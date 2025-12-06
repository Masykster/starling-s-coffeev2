<?php
// File untuk fungsi autentikasi admin

// Fungsi untuk memulai session admin (gunakan session yang sama)
function startAdminSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Fungsi untuk login admin
function adminLogin($username, $password) {
    try {
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("SELECT id, username, email, password, name, role FROM admins WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            $conn->close();
            return ['success' => false, 'message' => 'Username atau password salah.'];
        }
        
        $admin = $result->fetch_assoc();
        
        // Verifikasi password
        if (password_verify($password, $admin['password'])) {
            startAdminSession();
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_role'] = $admin['role'];
            
            $stmt->close();
            $conn->close();
            return ['success' => true, 'message' => 'Login berhasil!'];
        } else {
            $stmt->close();
            $conn->close();
            return ['success' => false, 'message' => 'Username atau password salah.'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// Fungsi untuk logout admin
function adminLogout() {
    startAdminSession();
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_username']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_role']);
    return ['success' => true, 'message' => 'Logout berhasil!'];
}

// Fungsi untuk cek apakah admin sudah login
function isAdminLoggedIn() {
    startAdminSession();
    return isset($_SESSION['admin_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

// Fungsi untuk mendapatkan admin ID saat ini
function getCurrentAdminId() {
    startAdminSession();
    return $_SESSION['admin_id'] ?? null;
}

// Fungsi untuk mendapatkan admin name saat ini
function getCurrentAdminName() {
    startAdminSession();
    return $_SESSION['admin_name'] ?? null;
}

// Fungsi untuk mendapatkan admin role saat ini
function getCurrentAdminRole() {
    startAdminSession();
    return $_SESSION['admin_role'] ?? null;
}

// Fungsi untuk memerlukan login admin (redirect ke login jika belum login)
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: ../login.php');
        exit;
    }
}

// Fungsi untuk memerlukan super admin
function requireSuperAdmin() {
    requireAdminLogin();
    if (getCurrentAdminRole() !== 'super_admin') {
        header('Location: index.php?error=access_denied');
        exit;
    }
}
?>

