# ⚡ Quick Setup: Bunny.net CDN (Pull Zone)

## 🎯 **5-Minute Setup:**

### **1. Create Pull Zone**
- Bunny.net Dashboard → Pull Zones → Add Pull Zone
- Name: `cinema-bunny-cdn`
- Origin: `https://sg.storage.bunnycdn.com/storage-movie-test`

### **2. Get CDN URL**
- You'll get: `cinema-bunny-cdn.b-cdn.net`

### **3. Update .env**
```env
BUNNY_CDN_DOMAIN=cinema-bunny-cdn.b-cdn.net
```

### **4. Done!**
- Your code already supports this
- Videos will use CDN automatically
- Better performance! 🚀

---

## 📋 **Detailed Steps:**

### **Step 1: Bunny.net Dashboard**
1. Go to https://bunny.net
2. Login
3. Click **"Pull Zones"** in sidebar

### **Step 2: Create Pull Zone**
1. Click **"Add Pull Zone"**
2. **Name**: `cinema-bunny-cdn`
3. **Origin URL**: `https://sg.storage.bunnycdn.com/storage-movie-test`
4. Click **"Add Pull Zone"**

### **Step 3: Configure**
1. **Origin Type**: Storage Zone
2. **Storage Zone**: Select `storage-movie-test`
3. **Access Key**: Your storage zone access key
4. **Save**

### **Step 4: Get Hostname**
- You'll see: `cinema-bunny-cdn.b-cdn.net`
- Copy this!

### **Step 5: Update Laravel**
```env
# Add to .env
BUNNY_CDN_DOMAIN=cinema-bunny-cdn.b-cdn.net
```

### **Step 6: Test**
- Upload a video
- Play it
- Check URL in browser (should use CDN domain)

---

## ✅ **That's It!**

Your videos will now stream from the optimized CDN! 🎬

