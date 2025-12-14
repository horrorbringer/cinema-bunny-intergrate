# 🎬 How CDN Streaming Works in Your System

## 📋 Complete Flow: Upload → Storage → CDN → Playback

---

## 1️⃣ **Upload Process**

### **Step 1: Admin Uploads Video**
```
Admin → /admin/movies/create → Upload video file
```

### **Step 2: File Saved Locally**
```php
// File saved to: storage/app/private/uploads/videos/
$file->storeAs('uploads/videos', $fileName);
```

### **Step 3: Queue Job Dispatched**
```php
UploadVideoToBunnyJob::dispatch($movie, $localPath);
```

### **Step 4: Background Upload to Bunny.net**
```php
// Uses Guzzle to upload via HTTP API
$bunnyService->uploadFile($localPath, $fileName);
```

### **Step 5: File on Bunny.net Storage**
```
✅ File stored at: sg.storage.bunnycdn.com/storage-movie-test/video.mp4
```

---

## 2️⃣ **CDN Distribution**

### **Automatic Process:**
```
Bunny.net Storage → CDN Edge Network → Global Distribution
```

### **What Happens:**
1. **File Uploaded** → Stored in Bunny.net Storage Zone
2. **CDN Activated** → Automatically distributed to edge servers
3. **Global Caching** → Cached in 100+ locations worldwide
4. **Ready to Stream** → Available from nearest edge server

---

## 3️⃣ **Video Playback**

### **User Requests Video:**
```
User → /movies/{slug}/watch
```

### **Controller Generates CDN URL:**
```php
// app/Http/Controllers/MovieController.php
$bunnyDomain = env('BUNNY_CDN_DOMAIN');
$cdnPath = $movie->cdn_path;
$url = "https://{$bunnyDomain}/storage-movie-test/{$cdnPath}";
```

### **CDN URL Example:**
```
https://sg.storage.bunnycdn.com/storage-movie-test/video-filename.mp4
```

### **HTML5 Video Player:**
```html
<video controls autoplay>
    <source src="{{ $url }}" type="video/mp4">
</video>
```

---

## 4️⃣ **CDN Delivery Process**

### **When User Plays Video:**

```
1. User clicks play
   ↓
2. Browser requests: https://sg.storage.bunnycdn.com/.../video.mp4
   ↓
3. CDN routes to nearest edge server
   ↓
4. Edge server checks cache
   ↓
5a. [Cache Hit] → Instant delivery (< 50ms)
   OR
5b. [Cache Miss] → Fetch from origin → Cache → Deliver
   ↓
6. Video streams to user
```

### **Geographic Routing:**
```
User in USA → CDN Edge in USA → Fast delivery
User in Asia → CDN Edge in Asia → Fast delivery
User in Europe → CDN Edge in Europe → Fast delivery
```

---

## 5️⃣ **Complete Code Flow**

### **Upload (Background):**
```php
// 1. Admin uploads
AdminController::store()
    → storeAsync()
    → Save locally
    → Dispatch UploadVideoToBunnyJob

// 2. Queue processes
UploadVideoToBunnyJob::handle()
    → BunnyStorageService::uploadFile()
    → Guzzle HTTP PUT
    → File on Bunny.net
    → Update movie.cdn_path
```

### **Playback (Real-time):**
```php
// 1. User requests video
MovieController::watch($slug)
    → Check movie exists
    → Generate CDN URL
    → Return view with URL

// 2. Browser plays video
<video src="CDN_URL">
    → Browser requests from CDN
    → CDN delivers from edge
    → Video streams
```

---

## 6️⃣ **Key Components**

### **Files Involved:**

1. **Upload:**
   - `app/Http/Controllers/AdminController.php` - Handles upload form
   - `app/Jobs/UploadVideoToBunnyJob.php` - Background upload job
   - `app/Services/BunnyStorageService.php` - HTTP API upload service

2. **Playback:**
   - `app/Http/Controllers/MovieController.php` - Generates CDN URLs
   - `resources/views/movies/watch.blade.php` - Video player view

3. **Configuration:**
   - `.env` - CDN domain and credentials
   - `config/filesystems.php` - Storage configuration

---

## 7️⃣ **Environment Variables**

### **Required:**
```env
# Storage Zone
BUNNY_STORAGE_USERNAME=storage-movie-test
BUNNY_STORAGE_PASSWORD=your-access-key
BUNNY_STORAGE_HOST=sg.storage.bunnycdn.com

# CDN Domain (for playback)
BUNNY_CDN_DOMAIN=sg.storage.bunnycdn.com
```

### **Optional:**
```env
# For signed URLs (security)
BUNNY_API_KEY=your-api-key
```

---

## 8️⃣ **URL Generation**

### **Current Implementation:**
```php
// app/Http/Controllers/MovieController.php (line 99-109)
$bunnyDomain = env('BUNNY_CDN_DOMAIN', env('BUNNY_STORAGE_HOST'));
$cdnPath = $movie->cdn_path;
$url = "https://{$bunnyDomain}/" . 
       env('BUNNY_STORAGE_USERNAME') . "/{$cdnPath}";
```

### **Result:**
```
https://sg.storage.bunnycdn.com/storage-movie-test/video.mp4
```

---

## 9️⃣ **Watch Progress Tracking**

### **How It Works:**
```javascript
// resources/views/movies/watch.blade.php
video.addEventListener('play', function() {
    // Save progress every 10 seconds
    setInterval(() => {
        fetch('/watch/{slug}/progress', {
            method: 'POST',
            body: JSON.stringify({ progress: video.currentTime })
        });
    }, 10000);
});
```

### **Backend:**
```php
// app/Http/Controllers/MovieController.php
MovieController::updateProgress()
    → Save to watch_history table
    → Resume from saved position
```

---

## 🔟 **Complete Example**

### **Full Flow:**

```
1. Admin uploads "movie.mp4" (100MB)
   ↓
2. File saved: storage/app/private/uploads/videos/movie.mp4
   ↓
3. Queue job: UploadVideoToBunnyJob
   ↓
4. Upload via HTTP API: BunnyStorageService
   ↓
5. File on Bunny.net: sg.storage.bunnycdn.com/storage-movie-test/movie.mp4
   ↓
6. CDN distributes globally (automatic)
   ↓
7. User visits: /movies/movie-slug/watch
   ↓
8. Controller generates: https://sg.storage.bunnycdn.com/.../movie.mp4
   ↓
9. Browser requests from CDN
   ↓
10. CDN edge server delivers (nearest location)
   ↓
11. Video plays instantly! 🎬
```

---

## ✅ **Summary**

### **Upload:**
1. Admin uploads → Local storage
2. Queue job → Background upload
3. Bunny.net Storage → File stored
4. CDN → Automatic distribution

### **Playback:**
1. User requests → Controller
2. CDN URL → Generated
3. Browser → Requests from CDN
4. Edge server → Delivers video
5. User → Watches video

### **Advantages:**
- ⚡ Fast - Global edge network
- 💰 Cheap - Pay per use
- 📈 Scalable - Unlimited viewers
- 🔒 Secure - HTTPS delivery

---

**Your system is fully functional!** 🚀

