<?php
require_once 'config.php';

// Jika sudah login, redirect ke index (sebelum include header)
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$pageTitle = "Starling's Coffee - Daftar";
$error = '';
$success = '';

// Handle registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    
    // Validasi
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Nama, email, dan password harus diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Password dan konfirmasi password tidak cocok.';
    } else {
        $result = registerUser($name, $email, $password, $phone, $address);
        if ($result['success']) {
            // Auto login setelah registrasi
            loginUser($email, $password);
            $success = 'Registrasi berhasil! Anda akan diarahkan ke halaman utama.';
            // Redirect akan dilakukan via JavaScript setelah 2 detik
        } else {
            $error = $result['message'];
        }
    }
}

include 'includes/header.php';
?>

    <!-- Konten Halaman Register -->
    <main class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="content-page p-4 rounded shadow-sm">
                        <h1 class="display-5 fw-bold mb-4 pb-3 border-bottom text-center">Daftar Akun</h1>
                        <p class="lead mb-4 text-center">Buat akun baru untuk mulai berbelanja</p>
                        
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
                        <script>
                            setTimeout(function() {
                                window.location.href = 'index.php';
                            }, 2000);
                        </script>
                        <?php endif; ?>
                        
                        <form method="POST" action="register.php">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label fw-bold">Nama Lengkap</label>
                                    <input type="text" class="form-control form-control-lg" id="name" name="name" 
                                        value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-bold">Alamat Email</label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email" 
                                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label fw-bold">Password</label>
                                    <input type="password" class="form-control form-control-lg" id="password" name="password" required minlength="6">
                                    <small class="form-text text-muted">Minimal 6 karakter</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label fw-bold">Konfirmasi Password</label>
                                    <input type="password" class="form-control form-control-lg" id="confirm_password" name="confirm_password" required minlength="6">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label fw-bold">No. Telepon (Opsional)</label>
                                <input type="tel" class="form-control form-control-lg" id="phone" name="phone" 
                                    value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                            </div>
                            <div class="mb-4">
                                <label for="address" class="form-label fw-bold">Alamat (Opsional)</label>
                                <textarea class="form-control" id="address" name="address" rows="2"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                            </div>
                            <div class="mb-4">
                                <button type="submit" name="register" class="btn btn-success btn-lg w-100">Daftar</button>
                            </div>
                        </form>
                        
                        <div class="text-center">
                            <p class="mb-0">Sudah punya akun? <a href="login.php" class="fw-bold">Login di sini</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>

