# Troubleshooting: Videos Not Appearing in Bunny.net

## Quick Fixes

### 1. **Check if Queue Worker is Running**

The queue worker must be running to process uploads. Check:

```bash
# If using Docker:
docker-compose ps queue

# Check queue logs:
docker-compose logs queue

# Start queue worker if not running:
docker-compose up -d queue
```

### 2. **Run Queue Worker Manually**

If queue worker isn't running, start it:

```bash
# Docker:
docker-compose exec queue php artisan queue:work --verbose

# Or locally:
php artisan queue:work --verbose
```

### 3. **Check for Failed Jobs**

```bash
php artisan queue:failed
```

### 4. **Process Pending Jobs**

```bash
php artisan queue:work --once
```

### 5. **Use Direct Upload (Temporary Solution)**

If queue isn't working, you can upload directly by modifying the form to use direct upload method.

---

## Why Videos Aren't Uploading

### Common Issues:

1. **Queue Worker Not Running**
   - Jobs are queued but never processed
   - Solution: Start queue worker

2. **Database Queue Not Set Up**
   - Check if `jobs` table exists
   - Run: `php artisan migrate`

3. **File Not Found**
   - Local file was deleted before upload
   - Check: `storage/app/uploads/videos/`

4. **Bunny.net Connection Failed**
   - Check credentials in `.env`
   - Test connection manually

5. **Memory/Timeout Issues**
   - Large files may timeout
   - Check PHP limits

---

## Manual Upload Test

To test if upload works, try uploading directly:

1. Go to admin upload form
2. Upload a small test video
3. Check Bunny.net dashboard
4. Check Laravel logs: `storage/logs/laravel.log`

---

## Check Logs

```bash
# View Laravel logs:
tail -f storage/logs/laravel.log

# Or in Docker:
docker-compose exec app tail -f storage/logs/laravel.log
```

Look for:
- "Video uploaded successfully"
- "Video upload failed"
- Connection errors

---

## Quick Test Command

Run this to test the upload job manually:

```bash
php artisan tinker
```

Then:
```php
$movie = App\Models\Movie::whereNull('cdn_path')->first();
if ($movie) {
    $job = new App\Jobs\UploadVideoToBunnyJob($movie, 'path/to/file', null, null);
    $job->handle();
}
```

