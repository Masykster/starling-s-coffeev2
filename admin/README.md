# Admin Panel - Starling Coffee

## Akses Admin Panel

URL: `http://localhost/starling-coffee-main/admin/`

## Default Login Credentials

Setelah import `database_admin.sql`, gunakan kredensial berikut:

- **Username:** `admin`
- **Password:** `admin123`

⚠️ **PENTING:** Ganti password default setelah pertama kali login!

## Setup Database

1. Import `database.sql` (jika belum)
2. Import `database_update.sql` (jika belum)
3. Import `database_admin.sql` (untuk admin panel)

### Menggunakan phpMyAdmin:
1. Buka phpMyAdmin
2. Pilih database `starling_coffee`
3. Klik tab "Import"
4. Pilih file `database_admin.sql`
5. Klik "Go"

## Fitur Admin Panel

### 1. Dashboard (`index.php`)
- Statistik total orders, revenue, users, dan menu items
- Daftar recent orders
- Quick access ke semua fitur

### 2. Manage Orders (`orders.php`)
- Lihat semua pesanan
- Filter berdasarkan status (pending, processing, completed, cancelled)
- View detail pesanan lengkap
- Update status pesanan
- Informasi customer dan alamat pengiriman

### 3. Manage Menu Items (`menu.php`)
- **Add Menu Item:** Tambah menu baru dengan form lengkap
- **Edit Menu Item:** Edit menu yang sudah ada
- **Delete Menu Item:** Hapus menu item
- **Fields:**
  - Name (required)
  - Description
  - Image Path (required)
  - Category (minuman/makanan)
  - Price (required)
  - Status (active/inactive)

### 4. Manage Categories (`categories.php`)
- Lihat semua kategori yang ada
- Lihat menu items dalam setiap kategori
- Edit kategori melalui menu items (ubah category field)

**Note:** Kategori dibuat otomatis dari menu items. Untuk menambah kategori baru, buat menu item dengan nama kategori baru.

### 5. Manage Users (`users.php`)
- Lihat semua user yang terdaftar
- Edit informasi user (name, email, phone, address)
- Delete user
- Lihat tanggal registrasi

### 6. Site Settings (`settings.php`)
- **General Settings:**
  - Site Name
  - Site Description
  - Site Email
  - Phone
  - Address
  
- **Social Media:**
  - Facebook URL
  - Instagram URL
  - Twitter/X URL

## Struktur File

```
admin/
├── config.php              # Config admin (include main config)
├── login.php              # Halaman login admin
├── logout.php             # Logout handler
├── index.php              # Dashboard
├── orders.php             # Manage orders
├── menu.php               # Manage menu items
├── categories.php         # Manage categories
├── users.php              # Manage users
├── settings.php           # Site settings
├── includes/
│   ├── auth.php          # Admin authentication functions
│   ├── header.php        # Admin layout header
│   └── footer.php        # Admin layout footer
└── README.md             # Dokumentasi ini
```

## Security Features

- ✅ Session-based authentication
- ✅ Password hashing (bcrypt)
- ✅ SQL injection protection (prepared statements)
- ✅ XSS protection (htmlspecialchars)
- ✅ Admin-only access (requireAdminLogin)
- ✅ Role-based access (super_admin/admin)

## Cara Menggunakan

### Login
1. Buka `admin/login.php`
2. Masukkan username dan password
3. Klik "Login"

### Manage Orders
1. Klik "Orders" di sidebar
2. Klik "View" untuk melihat detail pesanan
3. Update status pesanan dari dropdown
4. Klik "Update Status" untuk menyimpan

### Add Menu Item
1. Klik "Menu Items" di sidebar
2. Isi form di panel kiri:
   - Name: Nama menu
   - Description: Deskripsi menu
   - Image Path: Path gambar (contoh: `images/menu-item.jpg`)
   - Category: Pilih minuman atau makanan
   - Price: Harga menu
   - Status: Active atau Inactive
3. Klik "Add Menu Item"

### Edit Menu Item
1. Klik "Menu Items" di sidebar
2. Klik icon edit (pensil) pada item yang ingin diedit
3. Ubah data yang diperlukan
4. Klik "Update Menu Item"

### Edit User
1. Klik "Users" di sidebar
2. Klik icon edit (pensil) pada user yang ingin diedit
3. Ubah informasi user
4. Klik "Update User"

### Update Settings
1. Klik "Settings" di sidebar
2. Ubah setting yang diinginkan
3. Klik "Update Settings"

## Troubleshooting

### Tidak bisa login
- Pastikan `database_admin.sql` sudah diimport
- Cek apakah tabel `admins` ada dan berisi data
- Default password: `admin123`

### Error "Table doesn't exist"
- Pastikan semua file SQL sudah diimport:
  1. `database.sql`
  2. `database_update.sql`
  3. `database_admin.sql`

### Menu tidak muncul di website
- Pastikan status menu item adalah "active"
- Cek path gambar sudah benar
- Refresh halaman menu di website

### Orders tidak muncul
- Pastikan ada user yang sudah melakukan checkout
- Cek apakah tabel `orders` dan `order_items` sudah dibuat

## Catatan Penting

1. **Password Default:** Ganti password admin setelah pertama kali login
2. **Backup Database:** Selalu backup database sebelum melakukan perubahan besar
3. **Image Path:** Pastikan path gambar relatif dari root website (contoh: `images/menu.jpg`)
4. **Status Menu:** Menu dengan status "inactive" tidak akan muncul di website
5. **Delete User:** Hapus user akan menghapus semua orders dan cart items user tersebut

## Support

Jika ada masalah atau pertanyaan, cek:
- Error log PHP
- Database connection di `config.php`
- File permissions untuk folder admin

