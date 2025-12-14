# 🔐 Bunny.net Authentication Fix

## ❌ Error: HTTP 401 Unauthorized

The 401 error means the **AccessKey** is incorrect or missing.

---

## 🔍 Common Issues:

### 1. **Wrong Key Type**
- ❌ Using **FTP Password** instead of **Access Key**
- ✅ Need **Storage Zone Access Key** (different from FTP password)

### 2. **Where to Find Access Key:**
1. Go to **Bunny.net Dashboard**
2. Click **Storage** → Your Storage Zone (`storage-movie-test`)
3. Click **FTP & HTTP API** tab
4. Look for **"Access Key"** (not FTP Password)
5. Copy the Access Key

### 3. **Check Your .env:**
```env
# Should be the Access Key (not FTP password)
BUNNY_STORAGE_PASSWORD=your-access-key-here
```

**Access Key format:**
- Usually a long string of letters/numbers
- Different from FTP password
- Case-sensitive

---

## 🧪 Debug Steps:

### Step 1: Verify Access Key
```bash
# Check if AccessKey is set
docker-compose exec app php -r "echo 'AccessKey length: ' . strlen(env('BUNNY_STORAGE_PASSWORD')) . PHP_EOL;"
```

### Step 2: Test with cURL (from your computer)
```bash
# Replace with your actual values
curl -X PUT \
  -H "AccessKey: YOUR_ACCESS_KEY_HERE" \
  -H "Content-Type: application/octet-stream" \
  --data-binary "@test.txt" \
  "https://sg.storage.bunnycdn.com/storage-movie-test/test-file.txt"
```

If this works, the AccessKey is correct. If not, you need to get the correct AccessKey.

---

## ✅ Alternative: Use SFTP (Current Method)

If HTTP API authentication is problematic, we can stick with SFTP which is already working:

1. **SFTP uses FTP password** (different from Access Key)
2. **Already configured** in `config/filesystems.php`
3. **Works with current setup**

The HTTP API is better, but SFTP will work if you can't get the Access Key.

---

## 🔄 Quick Fix Options:

### Option A: Get Correct Access Key
1. Bunny.net Dashboard → Storage → Your Zone → FTP & HTTP API
2. Copy **Access Key** (not FTP Password)
3. Update `.env`: `BUNNY_STORAGE_PASSWORD=correct-access-key`
4. Test again: `docker-compose exec app php test-bunny-api-upload.php`

### Option B: Revert to SFTP
If you can't get the Access Key, we can revert to SFTP which uses the FTP password.

---

## 📝 Notes:

- **Access Key** ≠ **FTP Password**
- Access Key is for HTTP API
- FTP Password is for SFTP/FTP
- Both are in the same section of Bunny.net dashboard

