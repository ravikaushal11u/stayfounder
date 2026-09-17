# StayFinder Free Deployment Guide (100% Free)

Aapka Laravel 12 project **Render.com** (aur Koyeb) par **100% FREE** deploy karne ke liye bilkul taiyar hai. Isme kisi credit card ki zaroorat nahi padti.

---

## 🛠️ Humne Kya Setup Kiya Hai:
1. **`Dockerfile`**: Node 20 (Vite asset build) + PHP 8.2 + Apache + Extensions (`pdo_sqlite`, `pdo_mysql`, `bcmath`, `curl`, `mbstring`, `zip`, `gd`, `intl`).
2. **`docker/entrypoint.sh`**:
   - Render ke dynamic `$PORT` ko handle karta hai.
   - SQLite database auto-create karta hai.
   - `php artisan migrate --force` aur `php artisan db:seed --force` chala kar demo data create karta hai.
   - Performance ke liye config/route caching optimize karta hai.
3. **`render.yaml`**: Render ke liye 1-click configuration.

---

## 🚀 Step 1: Code ko GitHub par Push Karein

Aapke system par terminal khol kar ye commands run karein (agar pehle se nahi kiya):

```bash
# 1. StayFinder folder me jayein
cd c:\Users\ravik\Downloads\stayfinderrr

# 2. Git initialize karein (agar nahi kiya)
git init -b main

# 3. Saari files add karein
git add .

# 4. Commit karein
git commit -m "feat: Add Docker and Render free deployment configuration"

# 5. Aapka GitHub repository link karein (ravikaushal11u/stayfounder)
git remote add origin https://github.com/ravikaushal11u/stayfounder.git

# Agar remote already exist bole:
# git remote set-url origin https://github.com/ravikaushal11u/stayfounder.git

# 6. Push karein
git push -u origin main --force
```

---

## 🌐 Step 2: Render.com par Free Deploy Karein (3 Clicks)

1. Apne browser me **[https://render.com](https://render.com)** kholein.
2. **"Sign In"** par click karein aur **"GitHub"** se login karein.
3. Dashboard me top-right par **"New +"** button dabayein aur **"Web Service"** select karein.
4. **"Build and deploy from a Git repository"** choose karein -> **Next**.
5. Apne GitHub repos ki list me se **`stayfounder`** select karein (agar nahi dikhe to "Configure GitHub App" par click karke access allow karein).
6. Settings page par:
   - **Name**: `stayfinder` (ya koi bhi pasand ka naam)
   - **Region**: `Singapore` (ya Frankfurt / Oregon)
   - **Branch**: `main`
   - **Runtime**: **`Docker`** (Render apne aap Dockerfile detect kar lega!)
   - **Instance Type**: **`Free`** ($0/month)
7. Niche **"Deploy Web Service"** par click kar dein!

---

## ⏱️ Deployment ke baad:
- Render aapka container build karega (~2-3 minutes).
- Build complete hote hi aapko ek live URL mil jayega, jaise:
  👉 **`https://stayfinder-xxxx.onrender.com`**
- Aapka StayFinder app live internet par chalne lagega!

---

## 🔑 Demo Logins (Already Seeded):
- **Admin Login**:
  - Email: `admin@stayfinder.com`
  - Password: `password123`
- **Provider Login**:
  - Email: `provider@stayfinder.com`
  - Password: `password123`
- **Student**:
  - Aap naye student ke roop me seedha Register kar sakte hain ya demo se login kar sakte hain.

---

## 💡 Optional: Koyeb.com Par Bhi Deploy Kar Sakte Hain (Alternative)
Agar aap Koyeb use karna chahein:
1. **[https://www.koyeb.com](https://www.koyeb.com)** par jayein aur GitHub se login karein.
2. **Create Service** -> **GitHub** -> **`stayfounder`** repo select karein.
3. **Builder**: Dockerfile -> **Free Nano** tier select karein -> **Deploy**!
