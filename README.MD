# Digital Announcement System

A web-based digital announcement system using Docker, Laravel (API), Next.js (Frontend), MySQL (Database), and Nginx (Gateway & Asset Server).

## 📋 Table of Contents

- [System Architecture](#system-architecture)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Prerequisites](#prerequisites)
- [Installation & Setup](#installation--setup)
- [How to Run](#how-to-run)
- [Application Features](#application-features)
- [API Documentation](#api-documentation)
- [Troubleshooting](#troubleshooting)

## 🏗️ System Architecture

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

**Workflow:**
1. **User** → Access via **Gateway** (pengumuman.test)
2. **Gateway** → Route to **UI** (pages) or **API** (endpoints)
3. **UI** ←→ **API** → Communication for CRUD operations
4. **API** → **Database** → Store/retrieve announcements data
5. **API** → **Assets** → Upload announcement images
6. **UI** → **Assets** → Load images for display

## 🛠️ Technology Stack

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

## 📁 Project Structure

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

- **Docker Desktop** (installed and running)
- **Git** (to clone repository)
- Required ports:
  - `80` (Gateway)
  - `3000` (UI - internal)
  - `9000` (API - internal)
  - `3306` (MySQL - internal)
  - `8080` (phpMyAdmin)

## ⚙️ Installation & Setup

### 1. Clone Repository

```bash
cd c:\Users\fauza\Documents\Kuliah\semester-7\WST\Pertemuan-13
git clone <repository-url> tgs-p13
cd tgs-p13
```

### 2. Setup Hosts File

Add local domain to Windows hosts file:

**File:** `C:\Windows\System32\drivers\etc\hosts`

```
127.0.0.1 pengumuman.test
127.0.0.1 db.pengumuman.test
```

### 3. Create Docker Network

All services use an **external network** `pengumuman-net`. This network must be created first before running any service.

```bash
docker network create pengumuman-net
```

**Explanation:**
- All `docker-compose.yml` files use `external: true` for network
- This means the network must exist beforehand (not automatically created)
- All containers will be connected in the same network
- Enables communication between services using container names

**Verify network:**
```bash
docker network ls | findstr pengumuman-net
```

### 4. Setup Environment Variables

**API (Laravel):**
```bash
cd api-digital-announce-system/digital-announce-system
cp .env.example .env
# Edit .env as needed
```

**Key environment variables:**
```env
DB_HOST=mysql_pengumuman
DB_DATABASE=pengumuman_db
DB_USERNAME=user_pengumuman
DB_PASSWORD=user123
```

## 🚀 How to Run

### Service Start Order (IMPORTANT!)

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
# Enter container
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

### Verify All Services Running

```bash
docker ps
```

Ensure all the following containers are running:
- `nextjs_ui`
- `laravel_app`
- `db_pengumuman`
- `phpmyadmin_pengumuman`
- `gateway_nginx`
- `assets_nginx`

## 🌐 Application Access

| Service | URL | Credentials |
|---------|-----|-------------|
| **Website** | http://pengumuman.test | - |
| **Admin Login** | http://pengumuman.test/login | Email: `admin@example.com`<br>Password: `password` |
| **phpMyAdmin** | http://localhost:8080 | Server: `db_pengumuman`<br>User: `pengumuman_user`<br>Pass: `pengumuman_pass` |

## ✨ Application Features

### Public Features
- ✅ View announcement list (with pagination)
- ✅ Read announcement details
- ✅ Search announcements
- ✅ Filter by active status
- ✅ Responsive design (mobile-friendly)

### Admin Features
- ✅ Login/Logout with authentication
- ✅ Dashboard statistics
- ✅ CRUD Announcements
  - Create with rich text editor
  - Upload images
  - Set publish date
  - Toggle active/inactive status
- ✅ Image preview during edit
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

### Install New Dependencies (UI)

```bash
# Do NOT install on Windows host!
# Install ONLY in container:
docker exec -it nextjs_ui npm install <package-name>
```

### View Logs

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

# Rebuild container (if Dockerfile changes)
docker-compose up -d --build
```

## 🐛 Troubleshooting

### Error: Network pengumuman-net not found

If this error appears when running `docker-compose up`:
```
ERROR: Network pengumuman-net declared as external, but could not be found
```

**Solution:**
```bash
# Create network first
docker network create pengumuman-net

# Then run again
docker-compose up -d
```

### Container fails to start

```bash
# Check logs
docker logs <container-name>

# Remove and rebuild
docker-compose down
docker-compose up -d --build
```

### Port already in use

```bash
# Check port usage
netstat -ano | findstr :80
netstat -ano | findstr :3306

# Kill process or change port in docker-compose.yml
```

### Database connection error

1. Ensure database service is running
2. Check environment variables in `.env`
3. Ensure containers are connected to the same network

```bash
docker network inspect pengumuman-net
```

### Hot reload not working (UI)

Already configured with:
- `WATCHPACK_POLLING=true`
- `CHOKIDAR_USEPOLLING=true`
- Webpack polling mode

If still not working:
```bash
docker-compose restart
```

## 📝 Notes

- **Node modules:** Stored in Docker volume, no need on host
- **Images:** Stored in `api-digital-announce-system/digital-announce-system/storage/app/public/announcements/`
- **Database:** Persistent data in Docker volume `pengumuman_db_data`

---

Made with ❤️
