# Digital Announcement System

Sistem pengumuman digital berbasis web dengan menggunakan Docker, Laravel (API), Next.js (Frontend), MySQL (Database), Nginx (Gateway & Asset Server).

## 📋 Daftar Isi

- [Arsitektur Sistem](#arsitektur-sistem)
- [Teknologi Stack](#teknologi-stack)
- [Struktur Project](#struktur-project)
- [Prerequisites](#prerequisites)
- [Instalasi & Setup](#instalasi--setup)
- [Cara Menjalankan](#cara-menjalankan)
- [Fitur Aplikasi](#fitur-aplikasi)
- [API Documentation](#api-documentation)
- [Troubleshooting](#troubleshooting)

## 🏗️ Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────┐
│                      User Browser                        │
└─────────────────────┬───────────────────────────────────┘
                      │
                      ▼
         ┌────────────────────────┐
         │  Gateway (Nginx)       │
         │  pengumuman.test       │
         └────┬──────────┬────────┘
              │          │
    ┌─────────┘          └─────────┐
    ▼                              ▼
┌─────────┐                  ┌──────────┐
│ UI      │◄─────────────────┤ API      │
│ Next.js │   API Calls      │ Laravel  │───┐
└────┬────┘                  └────┬─────┘   │
     │                            │         │
     │                            ▼         │
     │                       ┌─────────┐    │
     │                       │ MySQL   │    │
     │                       │ DB      │    │
     │                       └─────────┘    │
     │                                      │
     └──────────────┬───────────────────────┘
                    ▼
              ┌──────────┐
              │ Assets   │
              │ Nginx    │ (Serve uploaded images)
              └──────────┘
```

**Alur Kerja:**
1. **User** → Akses via **Gateway** (pengumuman.test)
2. **Gateway** → Route ke **UI** (page) atau **API** (endpoint)
3. **UI** ←→ **API** → Komunikasi untuk CRUD data
4. **API** → **Database** → Simpan/ambil data announcements
5. **API** → **Assets** → Upload gambar announcements
6. **UI** → **Assets** → Load gambar untuk ditampilkan

## 🛠️ Teknologi Stack

### Backend (API)
- **Laravel 11** - PHP Framework
- **MySQL 8** - Database
- **Sanctum** - API Authentication

### Frontend (UI)
- **Next.js 16** - React Framework
- **React 19** - UI Library
- **TypeScript** - Type Safety
- **Tailwind CSS** - Styling
- **Axios** - HTTP Client
- **React-Quill-New** - Rich Text Editor

### Infrastructure
- **Docker & Docker Compose** - Containerization
- **Nginx** - Reverse Proxy & Static File Server
- **phpMyAdmin** - Database Management

## 📁 Struktur Project

```
tgs-p13/
├── api-digital-announce-system/          # Laravel API Backend
│   └── digital-announce-system/
|   └── nginx/
|       └── default.conf
|   └── php/
|   └── docker-compose.yml
│       
│
├── ui-digital-announce-system/           # Next.js Frontend
│
├── database/                             # MySQL Database
│   └── docker-compose.yml
│
├── gateway-digital-announce-system/                              # Nginx Gateway
│   ├── nginx/ 
|   |   |── certs/
│   │   └── default.conf    
│   └── docker-compose.yml
│
└── assets-digital-announce-system/       # Static Assets Server
    ├── nginx/
    │   └── default.conf    
    └── docker-compose.yml
```

## 📦 Prerequisites

- **Docker Desktop** (sudah terinstall dan running)
- **Git** (untuk clone repository)
- Port yang diperlukan:
  - `80` (Gateway)
  - `3000` (UI - internal)
  - `9000` (API - internal)
  - `3306` (MySQL - internal)
  - `8080` (phpMyAdmin)

## ⚙️ Instalasi & Setup

### 1. Clone Repository

```bash
cd c:\Users\fauza\Documents\Kuliah\semester-7\WST\Pertemuan-13
git clone <repository-url> tgs-p13
cd tgs-p13
```

### 2. Setup Hosts File

Tambahkan domain lokal ke file hosts Windows:

**File:** `C:\Windows\System32\drivers\etc\hosts`

```
127.0.0.1 pengumuman.test
127.0.0.1 db.pengumuman.test
```

### 3. Buat Docker Network

Semua services menggunakan **external network** `pengumuman-net`. Network ini harus dibuat terlebih dahulu sebelum menjalankan service apapun.

```bash
docker network create pengumuman-net
```

**Penjelasan:**
- Semua `docker-compose.yml` menggunakan `external: true` untuk network
- Artinya network harus sudah ada sebelumnya (tidak dibuat otomatis)
- Semua container akan terhubung dalam satu network yang sama
- Memungkinkan komunikasi antar services menggunakan container name

**Verifikasi network:**
```bash
docker network ls | findstr pengumuman-net
```

### 4. Setup Environment Variables

**API (Laravel):**
```bash
cd api-digital-announce-system/digital-announce-system
cp .env.example .env
# Edit .env sesuai kebutuhan
```

**Key environment variables:**
```env
DB_HOST=mysql_pengumuman
DB_DATABASE=pengumuman_db
DB_USERNAME=user_pengumuman
DB_PASSWORD=user123
```

## 🚀 Cara Menjalankan

### Urutan Start Services (PENTING!)

**1. Start Database**
```bash
cd database
docker-compose up -d
```

**2. Start API (Laravel)**
```bash
cd ../api-digital-announce-system/digital-announce-system
docker-compose up -d --build
```

**3. Install Dependencies & Migrate Database**
```bash
# Masuk ke container
docker exec -it laravel_app bash

# Generate key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed data (optional)
php artisan db:seed

# Exit container
exit
```

**4. Start Assets Server**
```bash
cd ../../assets-digital-announce-system
docker-compose up -d
```

**5. Start UI (Next.js)**
```bash
cd ../ui-digital-announce-system/digital-announce-system-fe
docker-compose up -d
```

**6. Start Gateway (Nginx)**
```bash
cd ../../gateway
docker-compose up -d
```

### Verifikasi Semua Services Running

```bash
docker ps
```

Pastikan semua container berikut running:
- `nextjs_ui`
- `laravel_app`
- `db_pengumuman`
- `phpmyadmin_pengumuman`
- `gateway_nginx`
- `assets_nginx`

## 🌐 Akses Aplikasi

| Service | URL | Credentials |
|---------|-----|-------------|
| **Website** | http://pengumuman.test | - |
| **Admin Login** | http://pengumuman.test/login | Email: `admin@example.com`<br>Password: `password` |
| **phpMyAdmin** | http://localhost:8080 | Server: `db_pengumuman`<br>User: `pengumuman_user`<br>Pass: `pengumuman_pass` |

## ✨ Fitur Aplikasi

### Public Features
- ✅ Lihat daftar pengumuman (dengan pagination)
- ✅ Baca detail pengumuman
- ✅ Search pengumuman
- ✅ Filter berdasarkan status aktif
- ✅ Responsive design (mobile-friendly)

### Admin Features
- ✅ Login/Logout dengan authentication
- ✅ Dashboard statistik
- ✅ CRUD Announcements
  - Create dengan rich text editor
  - Upload gambar
  - Set publish date
  - Toggle status aktif/inaktif
- ✅ Preview gambar saat edit
- ✅ Search & pagination
- ✅ Real-time validation

## 📚 API Documentation

### Authentication Endpoints

**Login**
```http
POST /api/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```

**Logout**
```http
POST /api/logout
Authorization: Bearer {token}
```

### Announcements Endpoints

**Get All Announcements**
```http
GET /api/announcements?page=1&per_page=10&sort_by=created_at&sort_order=desc&search=keyword
```

**Get Single Announcement**
```http
GET /api/announcements/{id}
```

**Create Announcement** (Protected)
```http
POST /api/announcements
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "title": "string",
  "content": "html string",
  "publish_at": "2025-12-14",
  "is_active": "1",
  "image": file
}
```

**Update Announcement** (Protected)
```http
POST /api/announcements/{id}
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "_method": "PUT",
  "title": "string",
  "content": "html string",
  ...
}
```

**Delete Announcement** (Protected)
```http
DELETE /api/announcements/{id}
Authorization: Bearer {token}
```

## 🔧 Development Workflow

### Install Dependency Baru (UI)

```bash
# Jangan install di host Windows!
# Install HANYA di container:
docker exec -it nextjs_ui npm install <package-name>
```

### Lihat Logs

```bash
# UI logs
docker logs nextjs_ui -f

# API logs
docker logs laravel_app -f

# Database logs
docker logs db_pengumuman -f
```

### Restart Services

```bash
# Restart specific service
docker-compose restart

# Rebuild container (jika ada perubahan Dockerfile)
docker-compose up -d --build
```

## 🐛 Troubleshooting

### Error: Network pengumuman-net not found

Jika muncul error saat `docker-compose up`:
```
ERROR: Network pengumuman-net declared as external, but could not be found
```

**Solusi:**
```bash
# Buat network terlebih dahulu
docker network create pengumuman-net

# Lalu jalankan ulang
docker-compose up -d
```

### Container tidak bisa start

```bash
# Check logs
docker logs <container-name>

# Remove dan rebuild
docker-compose down
docker-compose up -d --build
```

### Port sudah digunakan

```bash
# Check port usage
netstat -ano | findstr :80
netstat -ano | findstr :3306

# Kill process atau ganti port di docker-compose.yml
```

### Database connection error

1. Pastikan database service sudah running
2. Check environment variables di `.env`
3. Pastikan container terhubung ke network yang sama

```bash
docker network inspect pengumuman-net
```

### Hot reload tidak bekerja (UI)

Sudah dikonfigurasi dengan:
- `WATCHPACK_POLLING=true`
- `CHOKIDAR_USEPOLLING=true`
- Webpack polling mode

Jika masih tidak bekerja:
```bash
docker-compose restart
```

## 📝 Notes

- **Node modules:** Disimpan di Docker volume, tidak perlu ada di host
- **Gambar:** Disimpan di `api-digital-announce-system/digital-announce-system/storage/app/public/announcements/`
- **Database:** Data persistent di Docker volume `pengumuman_db_data`

---

Made with ❤️
