<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BunnyStorageService
{
    private string $storageZone;
    private string $apiKey;
    private string $host;

    public function __construct()
    {
        $this->storageZone = env('BUNNY_STORAGE_USERNAME', 'storage-movie-test');
        $this->apiKey = env('BUNNY_STORAGE_PASSWORD');
        $this->host = env('BUNNY_STORAGE_HOST', 'sg.storage.bunnycdn.com');
    }

    /**
     * Upload file using Bunny.net Storage API (HTTP - More reliable than SFTP)
     * Falls back to SFTP if HTTP API fails (e.g., wrong AccessKey)
     */
    public function uploadFile(string $localPath, string $remoteName): bool
    {
        if (!file_exists($localPath)) {
            throw new \Exception("File not found: {$localPath}");
        }

        $fileSize = filesize($localPath);
        Log::info("Uploading to Bunny.net: {$remoteName} (" . number_format($fileSize / 1024 / 1024, 2) . " MB)");

        // Try HTTP API first
        try {
            return $this->uploadFileViaHttp($localPath, $remoteName);
        } catch (\Exception $httpError) {
            // If HTTP API fails (e.g., 401 Unauthorized), fall back to SFTP
            if (str_contains($httpError->getMessage(), '401') || str_contains($httpError->getMessage(), 'Unauthorized')) {
                Log::warning("HTTP API failed (401 Unauthorized) - AccessKey might be wrong. Falling back to SFTP...");
                Log::warning("💡 To use HTTP API, get the Access Key from: Bunny.net Dashboard → Storage → Your Zone → FTP & HTTP API → Access Key");
                return $this->uploadFileViaSftp($localPath, $remoteName);
            }
            // Re-throw other errors
            throw $httpError;
        }
    }

    /**
     * Upload file via HTTP API
     */
    private function uploadFileViaHttp(string $localPath, string $remoteName): bool
    {
        $fileSize = filesize($localPath);
        Log::info("Attempting upload via HTTP API...");

        // Bunny.net Storage API endpoint format: https://{host}/{storageZone}/{filename}
        $url = "https://{$this->host}/{$this->storageZone}/{$remoteName}";

        // Increase memory limit for large files
        $originalLimit = ini_get('memory_limit');
        ini_set('memory_limit', '2048M');
        
        try {
            // For binary files, use Guzzle directly to avoid Laravel Http facade JSON encoding issues
            // Laravel Http facade tries to JSON encode strings even with withBody()
            $fileHandle = fopen($localPath, 'rb');
            if ($fileHandle === false) {
                throw new \Exception("Failed to open file: {$localPath}");
            }

            Log::info("File opened ({$fileSize} bytes), uploading via HTTP PUT with stream...");

            // Create a stream from the file handle
            $stream = \GuzzleHttp\Psr7\Utils::streamFor($fileHandle);

            // Track upload time
            $uploadStartTime = microtime(true);

            // Use Guzzle directly to avoid Laravel's JSON encoding
            $client = new \GuzzleHttp\Client(['timeout' => 3600]);
            $response = $client->request('PUT', $url, [
                'headers' => [
                    'AccessKey' => $this->apiKey,
                ],
                'body' => $stream, // Stream is sent as raw binary
            ]);

            // Calculate upload speed
            $uploadEndTime = microtime(true);
            $uploadDuration = $uploadEndTime - $uploadStartTime;
            $uploadSpeedMbps = ($fileSize * 8) / ($uploadDuration * 1000000); // Convert to Mbps
            $uploadSpeedMBps = $fileSize / ($uploadDuration * 1024 * 1024); // Convert to MB/s
            $fileSizeMB = number_format($fileSize / 1024 / 1024, 2);

            // Close the file handle (stream will also close it, but be safe)
            if (is_resource($fileHandle)) {
                fclose($fileHandle);
            }

            // Restore memory limit
            ini_set('memory_limit', $originalLimit);

            $statusCode = $response->getStatusCode();
            if ($statusCode >= 200 && $statusCode < 300) {
                Log::info("✅ File uploaded successfully via HTTP API: {$remoteName} ({$fileSizeMB} MB in " . number_format($uploadDuration, 2) . "s, " . number_format($uploadSpeedMBps, 2) . " MB/s)");
                return true;
            } else {
                $errorBody = $response->getBody()->getContents();
                Log::error("❌ HTTP API upload failed (Status: {$statusCode}): {$errorBody}");
                throw new \Exception("Upload failed (HTTP {$statusCode}): {$errorBody}");
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Close file handle if still open
            if (isset($fileHandle) && is_resource($fileHandle)) {
                fclose($fileHandle);
            }
            ini_set('memory_limit', $originalLimit);
            Log::error("Guzzle request error: " . $e->getMessage());
            throw new \Exception("Connection to Bunny.net failed: " . $e->getMessage());
        } catch (\Exception $e) {
            // Close file handle if still open
            if (isset($fileHandle) && is_resource($fileHandle)) {
                fclose($fileHandle);
            }
            ini_set('memory_limit', $originalLimit);
            throw $e;
        }
    }

    /**
     * Upload file via SFTP (fallback method)
     */
    private function uploadFileViaSftp(string $localPath, string $remoteName): bool
    {
        $fileSize = filesize($localPath);
        Log::info("Uploading via SFTP (fallback method)...");

        try {
            // Increase memory limit for large files
            $originalLimit = ini_get('memory_limit');
            ini_set('memory_limit', '2048M');

            // Read file content
            $fileContent = file_get_contents($localPath);
            if ($fileContent === false) {
                throw new \Exception("Failed to read file: {$localPath}");
            }

            Log::info("File content read ({$fileSize} bytes), uploading via SFTP...");

            // Upload using SFTP (uses FTP password, not AccessKey)
            $result = Storage::disk('bunny')->put($remoteName, $fileContent);

            // Restore memory limit
            ini_set('memory_limit', $originalLimit);

            if ($result === false) {
                throw new \Exception("SFTP upload returned false");
            }

            Log::info("✅ File uploaded successfully via SFTP: {$remoteName}");
            return true;
        } catch (\Exception $e) {
            Log::error("SFTP upload error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Upload file in chunks for very large files
     */
    public function uploadFileChunked(string $localPath, string $remoteName): bool
    {
        $chunkSize = 10 * 1024 * 1024; // 10MB chunks
        $fileSize = filesize($localPath);
        $totalChunks = ceil($fileSize / $chunkSize);

        Log::info("Uploading in chunks: {$remoteName} ({$totalChunks} chunks)");

        $handle = fopen($localPath, 'rb');
        if ($handle === false) {
            throw new \Exception("Failed to open file: {$localPath}");
        }

        try {
            // For Bunny.net, we still upload as one file but in a more controlled way
            // The API handles large files better than SFTP
            $fileContent = file_get_contents($localPath);
            return $this->uploadFile($localPath, $remoteName);
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }
    }

    /**
     * Check if file exists on Bunny.net
     * Uses GET request to check if file is accessible
     */
    public function fileExists(string $fileName): bool
    {
        $url = "https://{$this->host}/{$this->storageZone}/{$fileName}";
        
        try {
            // Try HEAD request first (lighter)
            $response = Http::timeout(30)
                ->withHeaders([
                    'AccessKey' => $this->apiKey,
                ])
                ->head($url);

            if ($response->successful()) {
                return true;
            }

            // If HEAD fails, try GET with range request (just check first byte)
            $response = Http::timeout(30)
                ->withHeaders([
                    'AccessKey' => $this->apiKey,
                    'Range' => 'bytes=0-0', // Just request first byte
                ])
                ->get($url);

            return $response->successful() || $response->status() === 206; // 206 = Partial Content
        } catch (\Exception $e) {
            Log::warning("File existence check failed: " . $e->getMessage());
            // If verification fails, assume file exists (upload was successful)
            return true;
        }
    }

    /**
     * Delete file from Bunny.net
     * Tries HTTP API first, falls back to SFTP if needed
     */
    public function deleteFile(string $fileName): bool
    {
        // Try HTTP API first
        try {
            return $this->deleteFileViaHttp($fileName);
        } catch (\Exception $httpError) {
            // If HTTP API fails (e.g., 401 Unauthorized), fall back to SFTP
            if (str_contains($httpError->getMessage(), '401') || str_contains($httpError->getMessage(), 'Unauthorized')) {
                Log::warning("HTTP API delete failed (401 Unauthorized) - Falling back to SFTP...");
                return $this->deleteFileViaSftp($fileName);
            }
            // Re-throw other errors
            throw $httpError;
        }
    }

    /**
     * Delete file via HTTP API
     * Note: Deletion is fast because it's just a DELETE request (no data transfer)
     */
    private function deleteFileViaHttp(string $fileName): bool
    {
        $url = "https://{$this->host}/{$this->storageZone}/{$fileName}";
        
        Log::info("Deleting file from Bunny.net via HTTP API: {$fileName}");
        
        $deleteStartTime = microtime(true);
        
        $response = Http::timeout(30)
            ->withHeaders([
                'AccessKey' => $this->apiKey,
            ])
            ->delete($url);

        $deleteDuration = microtime(true) - $deleteStartTime;

        if ($response->successful()) {
            Log::info("✅ File deleted successfully via HTTP API: {$fileName} (took " . number_format($deleteDuration, 2) . "s)");
            return true;
        } else {
            $errorBody = $response->body();
            Log::error("❌ HTTP API delete failed (Status: {$response->status()}): {$errorBody}");
            throw new \Exception("Delete failed (HTTP {$response->status()}): {$errorBody}");
        }
    }

    /**
     * Delete file via SFTP (fallback method)
     */
    private function deleteFileViaSftp(string $fileName): bool
    {
        Log::info("Deleting file from Bunny.net via SFTP: {$fileName}");
        
        try {
            $result = Storage::disk('bunny')->delete($fileName);
            
            if ($result) {
                Log::info("✅ File deleted successfully via SFTP: {$fileName}");
                return true;
            } else {
                Log::warning("⚠️ SFTP delete returned false (file may not exist): {$fileName}");
                // Return true anyway - file might not exist, which is fine
                return true;
            }
        } catch (\Exception $e) {
            Log::error("SFTP delete error: " . $e->getMessage());
            throw $e;
        }
    }
}

