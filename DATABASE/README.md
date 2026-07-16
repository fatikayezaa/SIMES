# Database SIMES

Folder ini berisi database yang digunakan pada aplikasi SIMES.

## File

```
simes_db.sql
```

## Cara Import

1. Jalankan MySQL melalui XAMPP.

2. Buka phpMyAdmin.

3. Buat database baru

```
simes_db
```

4. Pilih menu **Import**.

5. Upload file

```
simes_db.sql
```

6. Klik **Go**.

## Struktur Tabel

Database terdiri dari beberapa tabel utama:

| Tabel | Fungsi |
|--------|--------|
| users | Data pengguna |
| events | Data event |
| participants | Data peserta |
| budgets | Data anggaran |
| documentations | Dokumentasi kegiatan |
| reports | Data laporan |

## Relasi

```
users
   │
   └── events
          │
          ├── participants
          ├── budgets
          ├── documentations
          └── reports
```

## Catatan

- Database menggunakan MySQL.
- Charset UTF-8.
- Import menggunakan phpMyAdmin atau MySQL Workbench.