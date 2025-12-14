<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            // Store multiple quality versions as JSON (optional - for future use)
            // Format: {"1080p": "movie-1080p.mp4", "720p": "movie-720p.mp4", "480p": "movie-480p.mp4"}
            // You can add lower qualities later without re-uploading the main video
            $table->json('video_qualities')->nullable()->after('cdn_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn('video_qualities');
        });
    }
};
