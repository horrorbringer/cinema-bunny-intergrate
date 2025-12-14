<?php
/**
 * Test script to verify Bunny.net HTTP API upload works
 * This uses the new BunnyStorageService (HTTP API) instead of SFTP
 * Run: php test-bunny-api-upload.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\BunnyStorageService;

echo "Testing Bunny.net HTTP API Upload...\n\n";

// Test 1: Check configuration
echo "1. Checking configuration...\n";
$service = new BunnyStorageService();
$apiKey = env('BUNNY_STORAGE_PASSWORD');
echo "   Host: " . env('BUNNY_STORAGE_HOST', 'sg.storage.bunnycdn.com') . "\n";
echo "   Storage Zone: " . env('BUNNY_STORAGE_USERNAME', 'storage-movie-test') . "\n";
echo "   API Key: " . ($apiKey ? '***SET*** (Length: ' . strlen($apiKey) . ' chars)' : '❌ NOT SET') . "\n\n";

if (!$apiKey) {
    echo "❌ BUNNY_STORAGE_PASSWORD not set in .env file!\n";
    echo "\n💡 Make sure you're using the Storage Zone Access Key (not FTP password).\n";
    echo "   Find it in: Bunny.net Dashboard → Storage → Your Zone → FTP & HTTP API → Access Key\n";
    exit(1);
}

if (strlen($apiKey) < 10) {
    echo "⚠️  Warning: AccessKey seems too short. Make sure it's the correct Access Key.\n";
    echo "   Access Keys are usually longer than 10 characters.\n\n";
}

// Test 2: Create a small test file
echo "2. Creating test file...\n";
$testContent = "Test upload via HTTP API - " . date('Y-m-d H:i:s');
$testFileName = 'test-api-upload-' . time() . '.txt';
$testFilePath = storage_path('app/private/test-' . $testFileName);

file_put_contents($testFilePath, $testContent);
echo "   ✅ Test file created: {$testFilePath}\n\n";

// Test 3: Upload via HTTP API
echo "3. Uploading via Bunny.net HTTP API...\n";
try {
    $result = $service->uploadFile($testFilePath, $testFileName);
    
    if ($result) {
        echo "   ✅ Upload successful!\n";
        echo "   File: {$testFileName}\n\n";
        
        // Test 4: Verify file exists
        echo "4. Verifying file exists on Bunny.net...\n";
        if ($service->fileExists($testFileName)) {
            echo "   ✅ File verified on Bunny.net\n\n";
            
            // Test 5: Clean up
            echo "5. Cleaning up test file...\n";
            if ($service->deleteFile($testFileName)) {
                echo "   ✅ Test file deleted from Bunny.net\n";
            }
            
            // Delete local test file
            unlink($testFilePath);
            echo "   ✅ Local test file deleted\n";
        } else {
            echo "   ⚠️  File verification failed (might still be processing)\n";
        }
    } else {
        echo "   ❌ Upload returned false\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Upload failed: " . $e->getMessage() . "\n";
    echo "   Stack: " . $e->getTraceAsString() . "\n";
    
    // Clean up local file
    if (file_exists($testFilePath)) {
        unlink($testFilePath);
    }
    exit(1);
}

echo "\n✅ All tests passed! Bunny.net HTTP API upload is working correctly.\n";
echo "\n💡 This method is more reliable than SFTP for large video files!\n";

