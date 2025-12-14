<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'cdn_path', // Main video path (backward compatible)
        'video_qualities', // Optional: JSON {"1080p": "file.mp4", "720p": "file-720p.mp4"}
        'thumbnail',
        'poster',
        'year',
        'duration',
        'rating',
        'age_rating',
        'is_featured',
        'is_trending',
        'views',
        'rental_expires_at',
    ];

    protected $casts = [
        'rental_expires_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
        'rating' => 'decimal:1',
        'video_qualities' => 'array', // Auto-cast JSON to array
    ];

    /**
     * Get available video qualities
     * Returns array of quality => CDN URL
     */
    public function getAvailableQualities(): array
    {
        $qualities = [];
        $bunnyDomain = env('BUNNY_CDN_DOMAIN', env('BUNNY_STORAGE_HOST', 'sg.storage.bunnycdn.com'));
        $storageZone = env('BUNNY_STORAGE_USERNAME', 'storage-movie-test');
        
        // If video_qualities exists, use it
        if ($this->video_qualities && is_array($this->video_qualities)) {
            foreach ($this->video_qualities as $quality => $path) {
                if (str_contains($bunnyDomain, 'b-cdn.net')) {
                    $qualities[$quality] = "https://{$bunnyDomain}/{$path}";
                } else {
                    $qualities[$quality] = "https://{$bunnyDomain}/{$storageZone}/{$path}";
                }
            }
        }
        
        // Fallback to single cdn_path (backward compatible)
        if (empty($qualities) && $this->cdn_path) {
            if (str_contains($bunnyDomain, 'b-cdn.net')) {
                $qualities['auto'] = "https://{$bunnyDomain}/{$this->cdn_path}";
            } else {
                $qualities['auto'] = "https://{$bunnyDomain}/{$storageZone}/{$this->cdn_path}";
            }
        }
        
        return $qualities;
    }

    /**
     * Get CDN URL for a specific quality (or default)
     */
    public function getQualityUrl(?string $quality = null): ?string
    {
        $qualities = $this->getAvailableQualities();
        
        if ($quality && isset($qualities[$quality])) {
            return $qualities[$quality];
        }
        
        // Return first available quality
        return !empty($qualities) ? reset($qualities) : null;
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'movie_genre');
    }

    public function watchHistory(): HasMany
    {
        return $this->hasMany(WatchHistory::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get full CDN URL for thumbnail
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail) {
            return null;
        }

        $bunnyDomain = env('BUNNY_CDN_DOMAIN', env('BUNNY_STORAGE_HOST', 'sg.storage.bunnycdn.com'));
        
        // Pull Zone format: https://{pull-zone-domain}/{filename}
        // Storage Zone format: https://{storage-domain}/{storage-zone}/{filename}
        if (str_contains($bunnyDomain, 'b-cdn.net')) {
            // Pull Zone - no storage zone name needed
            return "https://{$bunnyDomain}/{$this->thumbnail}";
        } else {
            // Storage Zone - include storage zone name
            return "https://{$bunnyDomain}/" . env('BUNNY_STORAGE_USERNAME', 'storage-movie-test') . "/{$this->thumbnail}";
        }
    }

    /**
     * Get full CDN URL for poster
     */
    public function getPosterUrlAttribute(): ?string
    {
        if (!$this->poster) {
            return null;
        }

        $bunnyDomain = env('BUNNY_CDN_DOMAIN', env('BUNNY_STORAGE_HOST', 'sg.storage.bunnycdn.com'));
        
        // Pull Zone format: https://{pull-zone-domain}/{filename}
        // Storage Zone format: https://{storage-domain}/{storage-zone}/{filename}
        if (str_contains($bunnyDomain, 'b-cdn.net')) {
            // Pull Zone - no storage zone name needed
            return "https://{$bunnyDomain}/{$this->poster}";
        } else {
            // Storage Zone - include storage zone name
            return "https://{$bunnyDomain}/" . env('BUNNY_STORAGE_USERNAME', 'storage-movie-test') . "/{$this->poster}";
        }
    }
}
