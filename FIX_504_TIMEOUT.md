# Fixed: 504 Gateway Timeout Error

## What Was Fixed

### 1. **Nginx Timeouts Increased**
- `client_body_timeout`: 300s → **3600s** (1 hour)
- `fastcgi_read_timeout`: 300s → **3600s** (1 hour)
- `fastcgi_send_timeout`: 300s → **3600s** (1 hour)
- Added proxy timeouts: **3600s**

### 2. **PHP Timeouts Increased**
- `max_execution_time`: 600s → **3600s** (1 hour)
- `max_input_time`: 600s → **3600s** (1 hour)
- Added `set_time_limit(3600)` in upload controller

### 3. **Better Error Handling**
- Added try-catch for upload failures
- Better logging
- Streaming upload with fallback

---

## ⚠️ IMPORTANT: Restart Docker Containers

After these changes, you **MUST restart** your Docker containers for changes to take effect:

```bash
# Restart nginx and app containers
docker-compose restart web app

# Or rebuild if needed
docker-compose down
docker-compose up -d
```

---

## Why This Happened

Large video files (especially 1GB+) take a long time to upload:
- **Small file (100MB)**: ~2-5 minutes
- **Medium file (500MB)**: ~10-20 minutes  
- **Large file (2GB+)**: ~30-60+ minutes

The default nginx timeout (60-300 seconds) was too short for large files.

---

## Alternative Solution: Use Async Uploads

If you still get timeouts with very large files, switch to async uploads:

1. **Start queue worker:**
   ```bash
   docker-compose up -d queue
   ```

2. **Change AdminController.php line 63:**
   ```php
   // Change from:
   return $this->storeDirect($request, $file, $fileName, $uniqueId);
   
   // To:
   return $this->storeAsync($request, $file, $fileName, $uniqueId);
   ```

This way:
- User gets immediate response (2-5 seconds)
- Upload happens in background
- No timeout issues

---

## Test the Fix

1. Restart containers: `docker-compose restart web app`
2. Try uploading a video again
3. Should work without timeout errors

If you still get timeouts, the file might be too large. Use async uploads instead.

