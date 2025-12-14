<?php

namespace App\Jobs;

use App\Models\Movie;
use App\Services\BunnyStorageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UploadVideoToBunnyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour timeout for large files
    public $tries = 3; // Retry 3 times on failure

    public function __construct(
        public Movie $movie,
        public string $localVideoPath,
        public ?string $localThumbnailPath = null,
        public ?string $localPosterPath = null,
        public ?string $quality = null, // e.g., '720p', '480p'
        public ?string $qualityFileName = null // filename for quality version
    ) {}

    public function handle(): void
    {
        $bunnyService = new BunnyStorageService();
        
        Log::info("Starting upload job for movie: {$this->movie->id} - {$this->movie->title}");
        
        try {
            // Upload video file using Bunny.net Storage API (HTTP - More reliable)
            if (Storage::disk('local')->exists($this->localVideoPath)) {
                // Use quality-specific filename if provided, otherwise use basename
                $fileName = $this->qualityFileName ?? basename($this->localVideoPath);
                $localPath = Storage::disk('local')->path($this->localVideoPath);
                
                Log::info("Uploading video file via Bunny.net API: {$fileName} (Size: " . filesize($localPath) . " bytes)" . ($this->quality ? " [Quality: {$this->quality}]" : ""));
                
                // Use Bunny.net Storage API (HTTP) instead of SFTP
                $bunnyService->uploadFile($localPath, $fileName);
                
                // Verify upload
                if ($bunnyService->fileExists($fileName)) {
                    Log::info("✅ Upload verified - file exists on Bunny.net");
                }
                
                // If this is a quality version, update video_qualities JSON
                if ($this->quality && $this->qualityFileName) {
                    $qualities = $this->movie->video_qualities ?? [];
                    $qualities[$this->quality] = $fileName;
                    $this->movie->update(['video_qualities' => $qualities]);
                    Log::info("✅ Quality version added: {$this->quality} = {$fileName}");
                } else {
                    // Update main CDN path (original upload)
                    $this->movie->update(['cdn_path' => $fileName]);
                }
                
                // Delete local file after successful upload
                Storage::disk('local')->delete($this->localVideoPath);
                
                Log::info("✅ Video uploaded successfully to Bunny.net: {$fileName}");
            } else {
                Log::error("Local video file not found: {$this->localVideoPath}");
                throw new \Exception("Local video file not found: {$this->localVideoPath}");
            }

            // Upload thumbnail using API
            if ($this->localThumbnailPath && Storage::disk('local')->exists($this->localThumbnailPath)) {
                $thumbName = basename($this->localThumbnailPath);
                $thumbPath = Storage::disk('local')->path($this->localThumbnailPath);
                
                Log::info("Uploading thumbnail via API: {$thumbName}");
                $bunnyService->uploadFile($thumbPath, $thumbName);
                $this->movie->update(['thumbnail' => $thumbName]);
                Storage::disk('local')->delete($this->localThumbnailPath);
                
                Log::info("✅ Thumbnail uploaded: {$thumbName}");
            }

            // Upload poster using API
            if ($this->localPosterPath && Storage::disk('local')->exists($this->localPosterPath)) {
                $posterName = basename($this->localPosterPath);
                $posterPath = Storage::disk('local')->path($this->localPosterPath);
                
                Log::info("Uploading poster via API: {$posterName}");
                $bunnyService->uploadFile($posterPath, $posterName);
                $this->movie->update(['poster' => $posterName]);
                Storage::disk('local')->delete($this->localPosterPath);
                
                Log::info("✅ Poster uploaded: {$posterName}");
            }

            Log::info("✅ Movie upload completed successfully: {$this->movie->title}");
        } catch (\Exception $e) {
            Log::error("❌ Video upload failed: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            throw $e; // Re-throw to trigger retry
        }
    }


    public function failed(\Throwable $exception): void
    {
        Log::error("❌ Video upload job failed permanently after {$this->tries} attempts");
        Log::error("Error: " . $exception->getMessage());
        Log::error("Movie ID: {$this->movie->id}, Title: {$this->movie->title}");
        
        // Mark as failed but don't delete - admin can retry manually
        $this->movie->update(['cdn_path' => null]);
    }
}
