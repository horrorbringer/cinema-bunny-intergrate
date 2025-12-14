# How to Start Queue Worker (REQUIRED for Uploads)

## ⚠️ IMPORTANT: Queue Worker Must Be Running

Without the queue worker, videos will be saved locally but **NEVER uploaded to Bunny.net**.

---

## Quick Start

### Option 1: Using Docker (Recommended)

```bash
# Start queue worker container
docker-compose up -d queue

# Check if it's running
docker-compose ps queue

# View logs
docker-compose logs -f queue
```

### Option 2: Manual (If not using Docker)

```bash
# Start queue worker
php artisan queue:work --verbose --tries=3 --timeout=3600

# Keep it running in background (Linux/Mac)
nohup php artisan queue:work --verbose --tries=3 --timeout=3600 > storage/logs/queue.log 2>&1 &
```

---

## How It Works

1. **User uploads video** → File saved locally (2-5 seconds) ✅
2. **User gets immediate response** → "Upload queued!" ✅
3. **Queue worker processes job** → Uploads to Bunny.net in background ✅
4. **Video appears in Bunny.net** → Usually 5-30 minutes later ✅

---

## Check Queue Status

```bash
# Check pending jobs
php artisan queue:work --once

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

---

## Troubleshooting

### Queue Worker Not Processing Jobs?

1. **Check if worker is running:**
   ```bash
   docker-compose ps queue
   ```

2. **Check queue connection:**
   - Default: `database` (uses `jobs` table)
   - Make sure `jobs` table exists: `php artisan migrate`

3. **Check logs:**
   ```bash
   docker-compose logs queue
   # Or
   tail -f storage/logs/laravel.log
   ```

### Jobs Stuck?

```bash
# Clear stuck jobs
php artisan queue:clear

# Restart worker
docker-compose restart queue
```

---

## Why Async Uploads?

- ✅ **No timeout errors** - Upload happens in background
- ✅ **Fast response** - User gets immediate feedback
- ✅ **Reliable** - Automatic retries on failure
- ✅ **Scalable** - Multiple uploads simultaneously

**Without queue worker = Videos never upload to Bunny.net!**

