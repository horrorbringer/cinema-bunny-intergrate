# Upload Verification Guide

## Current Upload Approach

### ✅ **Async Upload (Current Implementation)**

**Flow:**
1. User uploads video → Saved to local storage (2-5 seconds)
2. Movie record created → `cdn_path = null` (uploading status)
3. Job queued → Background worker processes upload
4. Upload to Bunny.net → Happens in background (no timeout)
5. Database updated → `cdn_path` set when complete
6. Local file deleted → After successful upload

**Benefits:**
- ✅ No timeout errors
- ✅ Fast user response
- ✅ Automatic retries
- ✅ Reliable

---

## How to Verify Upload Works

### Step 1: Test Bunny.net Connection

```bash
php test-bunny-upload.php
```

This will:
- Test connection to Bunny.net
- Upload a small test file
- Verify it appears in your storage
- Clean up test file

### Step 2: Check Queue Worker

```bash
# Check if queue worker is running
docker-compose ps queue

# View queue logs
docker-compose logs -f queue
```

### Step 3: Check Laravel Logs

```bash
# View upload logs
tail -f storage/logs/laravel.log | grep -i "upload\|bunny"
```

Look for:
- ✅ "Video uploaded successfully"
- ✅ "Movie upload completed"
- ❌ Any error messages

### Step 4: Check Bunny.net Dashboard

1. Go to your Bunny.net storage zone
2. Check "File manager"
3. Videos should appear there after upload completes

---

## Troubleshooting

### Videos Not Appearing in Bunny.net?

1. **Check queue worker is running:**
   ```bash
   docker-compose up -d queue
   ```

2. **Check for failed jobs:**
   ```bash
   php artisan queue:failed
   ```

3. **Check logs for errors:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Manually process a job:**
   ```bash
   php artisan queue:work --once
   ```

### Common Issues

| Issue | Solution |
|-------|----------|
| Queue worker not running | `docker-compose up -d queue` |
| Connection failed | Check `.env` credentials |
| File not found | Check local storage path |
| Memory error | File too large (>2GB) |
| Timeout | Already fixed with async uploads |

---

## Upload Status Indicators

- **`cdn_path = null`** → Still uploading or failed
- **`cdn_path = "filename.mp4"`** → Upload successful ✅
- **Movie not visible on site** → Only shows when `cdn_path` is set

---

## Best Practices

1. ✅ **Always use async uploads** (current implementation)
2. ✅ **Keep queue worker running**
3. ✅ **Monitor logs** for errors
4. ✅ **Check Bunny.net dashboard** to verify files
5. ✅ **Use smaller files** if memory issues occur

---

## Next Steps

1. Run test script: `php test-bunny-upload.php`
2. Start queue worker: `docker-compose up -d queue`
3. Upload a test video
4. Check logs and Bunny.net dashboard
5. Verify video appears in storage

