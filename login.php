<?php
require_once 'config.php';

// Jika sudah login, redirect sesuai role (sebelum include header)
if (isLoggedIn() || isAdmin()) {
    if (isAdmin()) {
        header('Location: admin/index.php');
        exit;
    } else {
        header('Location: index.php');
        exit;
    }
}

$pageTitle = "Starling's Coffee - Login";
$error = '';
$success = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email dan password harus diisi.';
    } else {
        $result = loginUser($email, $password);
        if ($result['success']) {
            // Redirect berdasarkan role (sebelum include header)
            if (isset($result['is_admin']) && $result['is_admin']) {
                // Admin redirect ke admin dashboard
                header('Location: admin/index.php');
            } else {
                // User redirect ke halaman yang diminta atau index
                $redirect = $_GET['redirect'] ?? 'index.php';
                header('Location: ' . $redirect);
            }
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

include 'includes/header.php';
?>

    <!-- Konten Halaman Login -->
    <main class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="content-page p-4 rounded shadow-sm">
                        <h1 class="display-5 fw-bold mb-4 pb-3 border-bottom text-center">Login</h1>
                        <p class="lead mb-4 text-center">Masuk ke akun Anda untuk melanjutkan</p>
                        
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
                        
                        <form method="POST" action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>">
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Email atau Username</label>
                                <input type="text" class="form-control form-control-lg" id="email" name="email" 
                                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                    placeholder="Email untuk user, email/username untuk admin" required>
                                <small class="text-muted">Gunakan email untuk user, atau email/username untuk admin</small>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Password</label>
                                <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                            </div>
                            <div class="mb-4">
                                <button type="submit" name="login" class="btn btn-success btn-lg w-100">Login</button>
                            </div>
                        </form>
                        
                        <div class="text-center">
                            <p class="mb-0">Belum punya akun? <a href="register.php" class="fw-bold">Daftar di sini</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>

