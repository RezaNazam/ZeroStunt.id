# ZeroStunt.id - Stunting Prevention System

**Team Project:** Semester 2, ASTRATECH
**Deadline:** Mid-July 2024
**Status:** 🚀 In Development

---

## 📋 TABLE OF CONTENTS

- [Quick Start](#quick-start)
- [Git Workflow](#git-workflow)
- [Setup Database](#setup-database)
- [Branch Strategy](#branch-strategy)
- [FAQ](#faq)

---

## 🚀 QUICK START (5 MINUTES)

### 1️⃣ Clone Repository

```bash
git clone https://github.com/[TEAM]/zerostunt-id.git
cd zerostunt-id
```

### 2️⃣ Setup Local Database

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE zerostunt_db CHARACTER SET utf8mb4;"

# Copy environment file
cp .env.example .env

# Import database (wait for Person 1 to push database files)
mysql -u root -p zerostunt_db < database/01_schema.sql
mysql -u root -p zerostunt_db < database/02_triggers.sql
mysql -u root -p zerostunt_db < database/03_views.sql
mysql -u root -p zerostunt_db < database/04_test_data.sql
```

### 3️⃣ Verify Setup

```bash
# Check database
mysql -u root -p -e "SELECT COUNT(*) as tables FROM information_schema.tables WHERE table_schema='zerostunt_db';"
# Output harus: tables = 15 ✓

# Check Git
git branch -a
# Output harus: * develop, remotes/origin/main, remotes/origin/develop ✓
```

### 4️⃣ Run Local Server

```bash
# Make sure you're in project root
cd zerostunt-id

# Start PHP built-in server
php -S localhost:8000 -t public/

# Open browser: http://localhost:8000
```

---

## 🌳 GIT WORKFLOW (MOST IMPORTANT!)

### ⚠️ RULES (JANGAN LANGGAR!)

### 📍 BRANCH STRATEGY

**Ada 3 jenis branch:**

main (⚠️ JANGAN SENTUH!)
↑ (hanya dari release branch saat submit)
develop (integration branch)
↑ (hanya dari feature branch via PR)
feature/\* (per orang/per task)
└─ feature/person-1-database
└─ feature/person-2-ui
└─ feature/person-3-backend
└─ feature/person-4-testing

### 📖 STEP-BY-STEP WORKFLOW

**SETIAP HARI, IKUTI URUTAN INI:**

#### **STEP 1: Mulai Kerja**

```bash
# Pastikan di develop branch
git checkout develop

# Pull code terbaru dari GitHub
git pull origin develop

# Buat feature branch BARU dengan nama unik
git checkout -b feature/person-[name]-[task]

# Contoh:
# git checkout -b feature/person-1-database
# git checkout -b feature/person-2-navbar
# git checkout -b feature/person-3-auth
# git checkout -b feature/person-4-testing
```

#### **STEP 2: Kerja & Commit**

```bash
# Edit file...
# (pakai VS Code atau editor apapun)

# Cek status
git status

# Add file yang berubah
git add .

# Commit dengan pesan JELAS
git commit -m "feat: Describe what you did"

# Contoh pesan commit yang BAIK:
# git commit -m "feat: Create users table with password hashing"
# git commit -m "feat: Add Z-Score calculation trigger"
# git commit -m "fix: Fix form validation error message"
# git commit -m "docs: Add database setup guide"

# Contoh pesan commit yang BURUK:
# git commit -m "update"
# git commit -m "fix bugs"
# git commit -m "changes"
```

#### **STEP 3: Push & Create PR**

```bash
# Push ke GitHub (upload pekerjaan kalian)
git push origin feature/person-[name]-[task]

# Setelah push, GitHub akan show "Compare & pull request" button
# Klik button itu, atau:
# - Go to github.com/[team]/zerostunt-id
# - Click "Pull requests" tab
# - Click "New pull request"
# - Base: develop (PENTING!)
# - Compare: feature/person-[name]-[task]
# - Title: "Week 1: Database setup" (atau deskripsi task)
# - Description: List apa yang sudah dikerjakan
# - Click "Create pull request"
```

#### **STEP 4: Wait for Review & Merge**

```bash
# Tunggu team member approve di GitHub
# Setelah approved, click "Merge pull request"

# Setelah merged, pull code terbaru
git checkout develop
git pull origin develop

# Delete feature branch (cleanup)
git branch -d feature/person-[name]-[task]
git push origin --delete feature/person-[name]-[task]

# Sekarang siap kerja task berikutnya (ulangi dari STEP 1)
```

---

## 🗄️ SETUP DATABASE (MINGGU 1)

### Person 1: Push Database Files (Hari 2-5)

```bash
# Create SQL files (dari proposal schema)
# Files:
#  - database/01_schema.sql (15 tables)
#  - database/02_triggers.sql (5 triggers)
#  - database/03_views.sql (4 views)
#  - database/04_test_data.sql (sample data)

# Export from local database
mysqldump --no-data zerostunt_db > database/01_schema.sql
mysqldump zerostunt_db > database/02_full_backup.sql

# Commit & push
git add database/
git commit -m "feat: Add database schema + triggers + views"
git push origin feature/person-1-database

# Create PR di GitHub
```

### Person 2, 3, 4: Import Database (Setelah PR merged)

```bash
# Pull latest develop
git checkout develop
git pull origin develop

# Import SQL files (one time setup)
mysql -u root -p zerostunt_db < database/01_schema.sql
mysql -u root -p zerostunt_db < database/02_triggers.sql
mysql -u root -p zerostunt_db < database/03_views.sql
mysql -u root -p zerostunt_db < database/04_test_data.sql

# Verify
mysql -u root -p zerostunt_db -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='zerostunt_db';"
# Output: 15 tables ✓
```

### Database Changes (Minggu 2-4)

```bash
# JANGAN export full database setiap kali ada change!
# Instead: Create migration file

# Contoh: Person 1 add column ke users table
# File: database/migrations/20240707_add_phone_to_users.sql

ALTER TABLE users ADD COLUMN phone VARCHAR(15) DEFAULT NULL;
```

---

## 🛠️ TECH STACK & SETUP

### Requirements

PHP 8.0+ (verify: php -v)
MySQL 5.7+ (verify: mysql -V)
Node.js (for Tailwind CSS compilation only)

### Folder Structure

zerostunt-id/
├── config/ # Database config, constants
├── app/ # Models, Controllers, Helpers
│ ├── classes/
│ ├── models/
│ ├── controllers/
│ └── helpers/
├── public/ # Entry point, CSS, JS, images
├── views/ # HTML templates
├── database/ # SQL files, migrations
└── docs/ # Documentation

---

## 🐛 TROUBLESHOOTING

### Q: Git error "Your branch is ahead of 'origin/develop'"

```bash
A: Ini artinya ada local commit yang belum push
   git push origin feature/[your-branch]
```

### Q: "fatal: refusing to merge unrelated histories"

```bash
A: Clone ulang:
   git clone https://github.com/[team]/zerostunt-id.git
```

### Q: Accidentally push ke main?

```bash
A: Tell team immediately!
   This is fixable tapi butuh admin action
```

### Q: Merge conflict?

```bash
A: Don't panic:
   1. git status (lihat conflict files)
   2. Edit file, hapus conflict markers (<<<, ===, >>>)
   3. git add .
   4. git commit -m "fix: Resolve merge conflict"
   5. git push origin feature/[branch]
```

### Q: Forgot to pull before working?

```bash
A: Before pushing:
   git pull origin develop
   (fix any conflicts)
   git push origin feature/[branch]
```

---

## 📞 CONTACT & QUESTIONS

- **Database questions?** → Person 1
- **Frontend questions?** → Person 2
- **Backend questions?** → Person 3
- **Testing/QA questions?** → Person 4
- **Git/GitHub questions?** → Ask group chat

---

## 📅 TIMELINE

MINGGU 1: Database setup (Person 1 lead)
MINGGU 2: Backend logic + Tailwind (Person 3 + Person 2)
MINGGU 3: Transaksi + Frontend (Person 3 + Person 2)
MINGGU 4: Reports + Dashboard + UAT (All people)

## Daily standup: Every morning before class

---

## ✅ CHECKLIST (Baru Pertama Kali Clone)

- [ ] git clone
- [ ] Create database
- [ ] cp .env.example .env
- [ ] Import SQL files
- [ ] Verify 15 tables exist
- [ ] git checkout develop
- [ ] Understand branch strategy
- [ ] Ready untuk contribute!

---

## 🚀 READY TO START?

**Jika sudah setup semua:**

```bash
php -S localhost:8000 -t public/
# Buka: http://localhost:8000
```

**Selamat berkontribusi! 🎉**
