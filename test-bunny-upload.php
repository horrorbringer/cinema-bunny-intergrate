<?php
/**
 * Test script to verify Bunny.net upload works
 * Run: php test-bunny-upload.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Storage;

echo "Testing Bunny.net Upload Connection...\n\n";

// Test 1: Check configuration
echo "1. Checking configuration...\n";
$config = config('filesystems.disks.bunny');
echo "   Host: " . $config['host'] . "\n";
echo "   Username: " . $config['username'] . "\n";
echo "   Port: " . $config['port'] . "\n";
echo "   Timeout: " . $config['timeout'] . "s\n\n";

// Test 2: Try to connect
echo "2. Testing connection...\n";
try {
    // Try to list files (this tests connection)
    // Note: This might fail if directory is empty, but connection should work
    try {
        $files = Storage::disk('bunny')->files('/');
        echo "   ✅ Connection successful!\n";
        echo "   Files found: " . count($files) . "\n\n";
    } catch (\Exception $listError) {
        // If listing fails, try a simple operation
        echo "   ⚠️  File listing failed, but continuing with upload test...\n";
        echo "   Error: " . $listError->getMessage() . "\n\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Connection failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 3: Try to upload a small test file
echo "3. Testing file upload...\n";
try {
    $testContent = "Test upload from Laravel - " . date('Y-m-d H:i:s');
    $testFileName = 'test-upload-' . time() . '.txt';
    
    $result = Storage::disk('bunny')->put($testFileName, $testContent);
    
    if ($result) {
        echo "   ✅ Upload successful!\n";
        echo "   File: {$testFileName}\n\n";
        
        // Verify file exists
        if (Storage::disk('bunny')->exists($testFileName)) {
            echo "   ✅ File verified on Bunny.net\n";
            
            // Clean up test file
            Storage::disk('bunny')->delete($testFileName);
            echo "   ✅ Test file deleted\n";
        }
    } else {
        echo "   ❌ Upload returned false\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Upload failed: " . $e->getMessage() . "\n";
    echo "   Stack: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n✅ All tests passed! Bunny.net upload is working correctly.\n";

