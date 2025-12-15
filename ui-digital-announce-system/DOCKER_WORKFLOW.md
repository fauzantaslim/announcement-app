# Docker Setup untuk Next.js

## Penjelasan Konfigurasi

### Volume Strategy
File `docker-compose.yml` menggunakan **named volume** untuk `node_modules`:
```yaml
volumes:
  - .:/app                              # Bind mount: sync code
  - node_modules:/app/node_modules      # Named volume: isolated node_modules
```

**Kenapa pakai named volume?**
- Node modules di Windows dan Linux **berbeda** (terutama binary dependencies)
- Named volume menyimpan node_modules **di dalam Docker** saja
- Tidak akan conflict dengan node_modules di host Windows

## Workflow Install Dependencies

### ❌ JANGAN Install di Host (Windows)
```bash
# SKIP ini kalau mau install dependency baru
npm install axios
```

### ✅ Install HANYA di Container
```bash
# 1. Masuk ke container
docker exec -it nextjs_ui sh

# 2. Install dependency di dalam container
npm install axios

# 3. Keluar dari container
exit
```

### Update package.json
Setelah install di container, `package.json` dan `package-lock.json` akan ter-update otomatis (karena di-bind mount). Commit file-file ini ke git.

## Best Practice

1. **Hapus node_modules di host** (opsional tapi recommended):
   ```bash
   rm -rf node_modules
   ```

2. **Install dependency hanya di container**:
   ```bash
   docker exec -it nextjs_ui npm install <package-name>
   ```

3. **Rebuild container** jika ada perubahan besar:
   ```bash
   docker-compose down
   docker-compose up --build
   ```

## Troubleshooting

### Container tidak ditemukan
Container name adalah `nextjs_ui` (bukan `digital-announce-system-fe-frontend`):
```bash
docker exec -it nextjs_ui sh   # ✅ Correct
```

### Install dependency langsung tanpa masuk container
```bash
docker exec -it nextjs_ui npm install <package-name>
```

## Summary
- ✅ Install dependency **HANYA di container**
- ✅ Node_modules **tidak perlu** ada di Windows host
- ✅ Named volume membuat node_modules **persistent** antar restart
- ✅ package.json updates akan ter-sync karena bind mount
