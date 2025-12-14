# 🔧 WSL Migration Fix - Missing `genres` Table

## ❌ **Error:**
```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'cinema_app.genres' doesn't exist
```

## ✅ **Solution: Run Migrations in WSL**

The migrations exist but haven't been run in your WSL environment. Here's how to fix it:

---

## 🐳 **Option 1: If Using Docker (Recommended)**

### **Step 1: Start Docker Containers**
```bash
# In WSL terminal
docker-compose up -d
```

### **Step 2: Run Migrations**
```bash
# Run all pending migrations
docker-compose exec app php artisan migrate

# Or if you want to see what will run first:
docker-compose exec app php artisan migrate:status
```

### **Step 3: Seed Genres (Optional)**
```bash
# If you have a GenreSeeder, run it:
docker-compose exec app php artisan db:seed --class=GenreSeeder
```

---

## 💻 **Option 2: If Running PHP Directly (No Docker)**

### **Step 1: Make sure you're in the project directory**
```bash
cd /mnt/d/projects/laravel/cinema-bunny-intergrate
# or wherever your project is located
```

### **Step 2: Run Migrations**
```bash
php artisan migrate
```

### **Step 3: Seed Genres (Optional)**
```bash
php artisan db:seed --class=GenreSeeder
```

---

## 📋 **What Migrations Will Run:**

1. ✅ `2025_12_13_105710_create_genres_table.php` - Creates `genres` table
2. ✅ `2025_12_13_105721_create_movie_genre_table.php` - Creates `movie_genre` pivot table

---

## 🔍 **Verify It Worked:**

### **Check if tables exist:**
```bash
# In Docker:
docker-compose exec app php artisan tinker
>>> Schema::hasTable('genres')
=> true
>>> Schema::hasTable('movie_genre')
=> true
>>> exit

# Or directly:
docker-compose exec db mysql -u cinema_user -pcinema_password cinema_app -e "SHOW TABLES;"
```

### **Check migration status:**
```bash
docker-compose exec app php artisan migrate:status
```

---

## 🚨 **If Migrations Fail:**

### **Error: "Migration already ran"**
If you get an error saying migrations already ran, but tables don't exist:

```bash
# Reset migrations (⚠️ WARNING: This will drop all tables!)
docker-compose exec app php artisan migrate:fresh

# Or just refresh specific tables:
docker-compose exec app php artisan migrate:refresh --path=database/migrations/2025_12_13_105710_create_genres_table.php
docker-compose exec app php artisan migrate:refresh --path=database/migrations/2025_12_13_105721_create_movie_genre_table.php
```

### **Error: "Connection refused"**
Make sure your database is running:
```bash
docker-compose ps
# Should show 'cinema-db' as 'Up'
```

---

## 🔄 **Why This Happens:**

### **Windows vs WSL:**
- **Windows**: Migrations already ran → Tables exist ✅
- **WSL**: Migrations haven't run → Tables missing ❌

### **Different Database Instances:**
- Windows and WSL might be using different database instances
- Or WSL database was reset/recreated
- Migrations need to run in each environment separately

---

## ✅ **After Running Migrations:**

Your app should work! The `genres` and `movie_genre` tables will be created, and the error will be resolved.

---

## 📝 **Quick Command Reference:**

```bash
# Start Docker
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate

# Check status
docker-compose exec app php artisan migrate:status

# Seed genres (if seeder exists)
docker-compose exec app php artisan db:seed --class=GenreSeeder

# View logs if errors
docker-compose logs app
```

---

**Run the migrations in WSL and the error will be fixed!** 🚀

