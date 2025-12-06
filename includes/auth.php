<?php
// File untuk fungsi autentikasi (login, register, logout)

// Fungsi untuk memulai session
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Fungsi untuk mendaftar user baru
function registerUser($name, $email, $password, $phone = '', $address = '') {
    try {
        $conn = getDBConnection();
        
        // Cek apakah email sudah terdaftar
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $stmt->close();
            $conn->close();
            return ['success' => false, 'message' => 'Email sudah terdaftar.'];
        }
        $stmt->close();
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user baru
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $hashedPassword, $phone, $address);
        
        if ($stmt->execute()) {
            $userId = $conn->insert_id;
            $stmt->close();
            $conn->close();
            return ['success' => true, 'message' => 'Registrasi berhasil!', 'user_id' => $userId];
        } else {
            $stmt->close();
            $conn->close();
            return ['success' => false, 'message' => 'Terjadi kesalahan saat registrasi.'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// Fungsi untuk login (cek admin dulu, lalu user)
function loginUser($email, $password) {
    try {
        $conn = getDBConnection();
        startSession();
        
        // Cek apakah email adalah admin
        $stmt = $conn->prepare("SELECT id, username, email, password, name, role FROM admins WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $email);
        $stmt->execute();
        $adminResult = $stmt->get_result();
        
        if ($adminResult->num_rows > 0) {
            $admin = $adminResult->fetch_assoc();
            
            // Verifikasi password admin
            if (password_verify($password, $admin['password'])) {
                // Login sebagai admin
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_role'] = $admin['role'];
                $_SESSION['is_admin'] = true;
                
                $stmt->close();
                $conn->close();
                return ['success' => true, 'message' => 'Login berhasil!', 'is_admin' => true];
            } else {
                // Password salah untuk admin
                $stmt->close();
                $conn->close();
                return ['success' => false, 'message' => 'Password salah untuk admin.'];
            }
        }
        $stmt->close();
        
        // Jika bukan admin, cek sebagai user biasa
        $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            $conn->close();
            return ['success' => false, 'message' => 'Email atau password salah.'];
        }
        
        $user = $result->fetch_assoc();
        
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['is_admin'] = false;
            
            $stmt->close();
            $conn->close();
            return ['success' => true, 'message' => 'Login berhasil!', 'is_admin' => false];
        } else {
            $stmt->close();
            $conn->close();
            return ['success' => false, 'message' => 'Email atau password salah.'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// Fungsi untuk logout
function logoutUser() {
    startSession();
    session_unset();
    session_destroy();
    return ['success' => true, 'message' => 'Logout berhasil!'];
}

// Fungsi untuk cek apakah user yang login adalah admin
function isAdmin() {
    startSession();
    return isset($_SESSION['admin_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

// Fungsi untuk cek apakah user sudah login
function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']);
}

// Fungsi untuk mendapatkan user ID saat ini
function getCurrentUserId() {
    startSession();
    return $_SESSION['user_id'] ?? null;
}

// Fungsi untuk mendapatkan user name saat ini
function getCurrentUserName() {
    startSession();
    // Jika admin, return admin name
    if (isset($_SESSION['admin_name']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        return $_SESSION['admin_name'];
    }
    // Jika user biasa
    return $_SESSION['user_name'] ?? null;
}

// Fungsi untuk memerlukan login (redirect ke login jika belum login)
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

// Fungsi untuk memerlukan login user (bukan admin)
function requireUserLogin() {
    startSession();
    if (!isLoggedIn() || isAdmin()) {
        if (!isLoggedIn()) {
            header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        } else {
            header('Location: admin/index.php');
        }
        exit;
    }
}

// Fungsi untuk update nama user
function updateUserName($userId, $newName) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $newName, $userId);
        
        if ($stmt->execute()) {
            // Update session
            startSession();
            $_SESSION['user_name'] = $newName;
            
            $stmt->close();
            $conn->close();
            return ['success' => true, 'message' => 'Nama berhasil diubah!'];
        } else {
            $stmt->close();
            $conn->close();
            return ['success' => false, 'message' => 'Terjadi kesalahan saat mengubah nama.'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// Fungsi untuk update password user
function updateUserPassword($userId, $currentPassword, $newPassword) {
    try {
        $conn = getDBConnection();
        
        // Verifikasi password saat ini
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            $conn->close();
            return ['success' => false, 'message' => 'User tidak ditemukan.'];
        }
        
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if (!password_verify($currentPassword, $user['password'])) {
            $conn->close();
            return ['success' => false, 'message' => 'Password saat ini salah.'];
        }
        
        // Hash password baru
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Update password
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $userId);
        
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            return ['success' => true, 'message' => 'Password berhasil diubah!'];
        } else {
            $stmt->close();
            $conn->close();
            return ['success' => false, 'message' => 'Terjadi kesalahan saat mengubah password.'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}
?>

