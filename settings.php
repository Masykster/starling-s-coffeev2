<?php
require_once 'config.php';
requireUserLogin();

$pageTitle = "Pengaturan Akun";
$userId = getCurrentUserId();
$error = '';
$success = '';

// Get current user info
try {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT name, email, phone, address FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    $user = ['name' => '', 'email' => '', 'phone' => '', 'address' => ''];
}

// Handle update name
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_name'])) {
    $newName = trim($_POST['name'] ?? '');
    
    if (empty($newName)) {
        $error = 'Nama tidak boleh kosong.';
    } else {
        $result = updateUserName($userId, $newName);
        if ($result['success']) {
            $success = $result['message'];
            $user['name'] = $newName; // Update local variable
        } else {
            $error = $result['message'];
        }
    }
}

// Handle update password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($currentPassword) || empty($newPassword)) {
        $error = 'Semua field password harus diisi.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'Password baru minimal 6 karakter.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Password baru dan konfirmasi password tidak cocok.';
    } else {
        $result = updateUserPassword($userId, $currentPassword, $newPassword);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}

include 'includes/header.php';
?>

<main class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <h1 class="display-5 fw-bold mb-4 pb-3 border-bottom">Pengaturan Akun</h1>
                
                <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Berhasil!</strong> <?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Update Name Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-person"></i> Ubah Nama</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="settings.php">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Nama Lengkap</label>
                                <input type="text" class="form-control form-control-lg" id="name" name="name" 
                                    value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                            </div>
                            <button type="submit" name="update_name" class="btn btn-primary">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>
                
                <!-- Update Password Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-lock"></i> Ubah Password</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="settings.php">
                            <div class="mb-3">
                                <label for="current_password" class="form-label fw-bold">Password Saat Ini</label>
                                <input type="password" class="form-control form-control-lg" id="current_password" 
                                    name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label fw-bold">Password Baru</label>
                                <input type="password" class="form-control form-control-lg" id="new_password" 
                                    name="new_password" required minlength="6">
                                <small class="form-text text-muted">Minimal 6 karakter</small>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label fw-bold">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control form-control-lg" id="confirm_password" 
                                    name="confirm_password" required minlength="6">
                            </div>
                            <button type="submit" name="update_password" class="btn btn-primary">Ubah Password</button>
                        </form>
                    </div>
                </div>
                
                <!-- Account Info Section -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Akun</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" disabled>
                            <small class="form-text text-muted">Email tidak dapat diubah</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">No. Telepon</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? 'Tidak diisi'); ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alamat</label>
                            <textarea class="form-control" rows="3" disabled><?php echo htmlspecialchars($user['address'] ?? 'Tidak diisi'); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
