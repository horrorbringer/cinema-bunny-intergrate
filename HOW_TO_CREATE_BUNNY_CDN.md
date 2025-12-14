# 🚀 How to Create CDN in Bunny.net

## 📋 Overview

Bunny.net has two main services:
1. **Storage Zone** (what you have now) - File storage
2. **Pull Zone** (CDN) - Optimized content delivery

**Current Setup:** You're using Storage Zone directly  
**Recommended:** Create a Pull Zone for better performance

---

## 🎯 **Step-by-Step Guide:**

### **Step 1: Log in to Bunny.net**

1. Go to: https://bunny.net
2. Log in to your account
3. Navigate to Dashboard

---

### **Step 2: Create a Pull Zone**

1. **Click "Pull Zones"** in the left menu
2. **Click "Add Pull Zone"** button
3. **Fill in the form:**

#### **Basic Settings:**

- **Name**: `cinema-bunny-cdn` (or your preferred name)
- **Origin URL**: 
  ```
  https://sg.storage.bunnycdn.com/storage-movie-test
  ```
  (Your storage zone URL)

- **Origin Shield**: Leave default (or enable for better caching)
- **Cache Expiry**: `30 days` (or your preference)

#### **Advanced Settings (Optional):**

- **Enable Cache Slice**: ✅ Enable (better for large files)
- **Enable Query String**: ✅ Enable (if using signed URLs)
- **Enable Compression**: ✅ Enable (faster delivery)
- **Enable HTTP/2**: ✅ Enable (faster connections)

---

### **Step 3: Configure Storage Zone Connection**

1. **In Pull Zone settings**, go to **"Origin"** tab
2. **Origin Type**: Select "Storage Zone"
3. **Storage Zone**: Select your storage zone (`storage-movie-test`)
4. **Access Key**: Enter your Storage Zone Access Key

---

### **Step 4: Get Your CDN URL**

After creating the Pull Zone, you'll get:

1. **CDN Hostname**: 
   ```
   cinema-bunny-cdn.b-cdn.net
   ```
   (or your custom domain if configured)

2. **CDN URL Format**:
   ```
   https://cinema-bunny-cdn.b-cdn.net/video-filename.mp4
   ```

---

### **Step 5: Update Your Laravel Configuration**

#### **Update `.env` file:**

```env
# Storage Zone (for uploads)
BUNNY_STORAGE_HOST=sg.storage.bunnycdn.com
BUNNY_STORAGE_USERNAME=storage-movie-test
BUNNY_STORAGE_PASSWORD=your-access-key

# CDN Pull Zone (for streaming)
BUNNY_CDN_DOMAIN=cinema-bunny-cdn.b-cdn.net
# OR use custom domain if you have one
# BUNNY_CDN_DOMAIN=cdn.yourdomain.com
```

#### **Your code already supports this!**

The `MovieController.php` already uses `BUNNY_CDN_DOMAIN`:

```php
$bunnyDomain = env('BUNNY_CDN_DOMAIN', env('BUNNY_STORAGE_HOST'));
$url = "https://{$bunnyDomain}/" . env('BUNNY_STORAGE_USERNAME') . "/{$cdnPath}";
```

**Just update `.env` and it will work!**

---

## 🔧 **Advanced Configuration:**

### **1. Custom Domain (Optional)**

1. **In Pull Zone**, go to **"Hostnames"** tab
2. **Click "Add Hostname"**
3. **Enter your domain**: `cdn.yourdomain.com`
4. **Add CNAME record** in your DNS:
   ```
   cdn.yourdomain.com → cinema-bunny-cdn.b-cdn.net
   ```

### **2. Enable Security Features**

1. **Token Authentication** (for signed URLs):
   - Go to **"Security"** tab
   - Enable **"Token Authentication"**
   - Set token key
   - Use in your code (already implemented)

2. **Hotlink Protection**:
   - Enable to prevent direct linking
   - Add allowed domains

3. **DDoS Protection**:
   - Already enabled by default
   - No configuration needed

### **3. Optimize Caching**

1. **Cache Expiry**: Set to 30 days (or your preference)
2. **Cache Slice**: Enable for large video files
3. **Query String**: Enable if using signed URLs
4. **Compression**: Enable for faster delivery

---

## 📊 **Pull Zone vs Storage Zone:**

### **Storage Zone (Current):**
```
https://sg.storage.bunnycdn.com/storage-movie-test/video.mp4
```
- ✅ Direct access
- ✅ Simple setup
- ⚠️ Less optimized
- ⚠️ No advanced features

### **Pull Zone (CDN - Recommended):**
```
https://cinema-bunny-cdn.b-cdn.net/video.mp4
```
- ✅ Optimized delivery
- ✅ Better caching
- ✅ More features
- ✅ Custom domain support
- ✅ Better analytics

---

## 🎯 **Benefits of Pull Zone:**

### **1. Better Performance:**
- ⚡ Faster delivery
- 📦 Better caching
- 🔄 Automatic optimization

### **2. More Features:**
- 📊 Analytics
- 🔒 Security options
- 🌐 Custom domain
- 📈 Better scaling

### **3. Cost:**
- 💰 Same pricing
- 📊 Better value
- 💵 No extra cost

---

## 🔄 **Migration Steps:**

### **Option 1: Keep Both (Recommended)**

1. **Keep Storage Zone** for uploads
2. **Use Pull Zone** for streaming
3. **Update `.env`** with Pull Zone domain
4. **No code changes needed!**

### **Option 2: Full Migration**

1. Create Pull Zone
2. Update `.env`
3. Test streaming
4. Monitor performance

---

## ✅ **Quick Setup Checklist:**

- [ ] Log in to Bunny.net
- [ ] Create Pull Zone
- [ ] Configure Storage Zone as origin
- [ ] Get CDN hostname
- [ ] Update `.env` file
- [ ] Test video playback
- [ ] Monitor performance

---

## 🧪 **Testing:**

### **1. Test CDN URL:**
```bash
curl -I https://cinema-bunny-cdn.b-cdn.net/test-video.mp4
```

### **2. Check Headers:**
Look for:
- `X-Cache: HIT` (cached) or `MISS` (first request)
- `Server: BunnyCDN`
- `Cache-Control` headers

### **3. Test from Your App:**
1. Upload a video
2. Play it from your website
3. Check network tab in browser
4. Verify CDN URL is used

---

## 📝 **Complete Example:**

### **Before (Storage Zone):**
```env
BUNNY_CDN_DOMAIN=sg.storage.bunnycdn.com
```
URL: `https://sg.storage.bunnycdn.com/storage-movie-test/video.mp4`

### **After (Pull Zone):**
```env
BUNNY_CDN_DOMAIN=cinema-bunny-cdn.b-cdn.net
```
URL: `https://cinema-bunny-cdn.b-cdn.net/video.mp4`

### **Code (No Changes Needed!):**
```php
// Already works with both!
$url = "https://{$bunnyDomain}/" . env('BUNNY_STORAGE_USERNAME') . "/{$cdnPath}";
```

**Note:** With Pull Zone, you might not need the storage zone name in URL. Check Bunny.net docs for exact format.

---

## 💡 **Pro Tips:**

1. **Start with Pull Zone** - Better performance
2. **Enable Cache Slice** - For large videos
3. **Use Custom Domain** - Professional look
4. **Monitor Analytics** - Track usage
5. **Enable Compression** - Faster delivery

---

## 🚨 **Important Notes:**

### **URL Format Difference:**

**Storage Zone:**
```
https://sg.storage.bunnycdn.com/storage-movie-test/video.mp4
```

**Pull Zone (may vary):**
```
https://cinema-bunny-cdn.b-cdn.net/video.mp4
```
(Storage zone name might not be needed)

**Check your Pull Zone settings for exact format!**

---

## ✅ **Summary:**

### **Steps:**
1. Create Pull Zone in Bunny.net
2. Connect to Storage Zone
3. Get CDN hostname
4. Update `.env` file
5. Test and enjoy!

### **Benefits:**
- ⚡ Better performance
- 📊 More features
- 🔒 Better security
- 💰 Same cost

---

## 🎬 **Your Code is Ready!**

Your Laravel code already supports CDN URLs. Just:
1. Create Pull Zone
2. Update `.env`
3. Done! ✅

---

**Need help? Check Bunny.net documentation or test with a small video first!** 🚀

