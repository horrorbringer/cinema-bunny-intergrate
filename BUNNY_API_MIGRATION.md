# 🚀 Bunny.net HTTP API Migration

## What Changed?

I've upgraded your upload system to use **Bunny.net's HTTP Storage API** instead of SFTP. This is much more reliable for large video files!

### ✅ Benefits:

1. **More Reliable** - HTTP API is more stable than SFTP for large files
2. **Better Error Messages** - Clear HTTP status codes and error responses
3. **Faster** - HTTP is generally faster than SFTP
4. **Better Timeout Handling** - HTTP timeouts are more predictable
5. **File Verification** - Can check if files exist after upload

---

## 📁 New Files:

1. **`app/Services/BunnyStorageService.php`**
   - New service class for Bunny.net HTTP API
   - Handles uploads, verification, and deletion
   - Better error handling

2. **`test-bunny-api-upload.php`**
   - Test script to verify HTTP API works
   - Run: `php test-bunny-api-upload.php`

---

## 🔄 Updated Files:

1. **`app/Jobs/UploadVideoToBunnyJob.php`**
   - Now uses `BunnyStorageService` instead of SFTP
   - Cleaner code
   - Better error messages

---

## 🧪 Testing:

### Test the new HTTP API:

```bash
# Inside Docker container
docker-compose exec app php test-bunny-api-upload.php
```

### Expected Output:
```
✅ All tests passed! Bunny.net HTTP API upload is working correctly.
💡 This method is more reliable than SFTP for large video files!
```

---

## 📝 Configuration:

Your `.env` file should have:

```env
BUNNY_STORAGE_HOST=sg.storage.bunnycdn.com
BUNNY_STORAGE_USERNAME=storage-movie-test
BUNNY_STORAGE_PASSWORD=your-access-key-here
```

**Note:** `BUNNY_STORAGE_PASSWORD` is your **Storage Zone Access Key** (not FTP password).

You can find it in:
- Bunny.net Dashboard → Storage → Your Storage Zone → FTP & HTTP API → Access Key

---

## 🎯 How It Works:

1. **Admin uploads video** → Saved locally
2. **Queue job dispatched** → Background processing
3. **BunnyStorageService** → Uploads via HTTP PUT
4. **File verified** → Checks if upload succeeded
5. **Database updated** → Sets `cdn_path`
6. **Local file deleted** → Cleanup

---

## 🚨 If Upload Still Fails:

### Check 1: Queue Worker Running?
```bash
docker-compose up -d queue
docker-compose logs -f queue
```

### Check 2: Correct Access Key?
- Make sure `BUNNY_STORAGE_PASSWORD` is the **Access Key**, not FTP password
- Access Key format: Usually starts with letters/numbers

### Check 3: Storage Zone Name?
- Make sure `BUNNY_STORAGE_USERNAME` matches your storage zone name exactly
- Case-sensitive!

### Check 4: Network/Firewall?
- HTTP API uses port 443 (HTTPS)
- Should work from anywhere
- No special firewall rules needed

---

## 📊 Comparison:

| Feature | SFTP (Old) | HTTP API (New) |
|---------|------------|----------------|
| Reliability | ⚠️ Can timeout | ✅ More stable |
| Speed | ⚠️ Slower | ✅ Faster |
| Error Messages | ⚠️ Generic | ✅ Clear HTTP codes |
| Large Files | ⚠️ Problematic | ✅ Better handling |
| Verification | ❌ Difficult | ✅ Easy |

---

## ✅ Next Steps:

1. **Test the API:**
   ```bash
   docker-compose exec app php test-bunny-api-upload.php
   ```

2. **Upload a video:**
   - Go to `/admin/movies/create`
   - Upload a video
   - Check queue logs: `docker-compose logs -f queue`

3. **Monitor uploads:**
   - Check Laravel logs: `docker-compose exec app tail -f storage/logs/laravel.log`
   - Look for "✅ File uploaded successfully via HTTP API"

---

## 🎉 Why This Should Work Better:

- **HTTP is more reliable** than SFTP for large files
- **Better timeout handling** - HTTP timeouts are clearer
- **Easier debugging** - HTTP status codes tell you exactly what went wrong
- **No connection issues** - HTTP doesn't have persistent connection problems like SFTP

**Try it now!** 🚀

