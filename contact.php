<?php
require_once 'config.php';
$pageTitle = "Starling's Coffee - Kontak";
include 'includes/header.php';

$success = false;
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validasi
    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Semua field harus diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        // Simpan ke database
        try {
            $conn = getDBConnection();
            
            // Cek apakah tabel contact_messages ada
            $checkTable = $conn->query("SHOW TABLES LIKE 'contact_messages'");
            if ($checkTable && $checkTable->num_rows > 0) {
                $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $email, $message);
                
                if ($stmt->execute()) {
                    $success = true;
                } else {
                    $error = 'Terjadi kesalahan saat menyimpan pesan. Silakan coba lagi.';
                }
                
                $stmt->close();
            } else {
                $error = 'Tabel contact_messages belum ada. Silakan import file database.sql terlebih dahulu.';
            }
            
            $conn->close();
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}
?>

    <!-- Konten Halaman Contact -->
    <main class="py-5">
        <div class="container">
            <div class="content-page p-4 rounded shadow-sm">
                <h1 class="display-4 fw-bold mb-3 pb-3 border-bottom">Hubungi Kami</h1>
                <p class="lead mb-4">Ada pertanyaan atau masukan? Kami ingin mendengarnya dari Anda. Silakan isi formulir di bawah ini.</p>
                
                <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Berhasil!</strong> Terima kasih! Pesan Anda telah berhasil dikirim.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <form class="contact-form" id="contact-form" method="POST" action="contact.php">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" class="form-control form-control-lg" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                        <div class="error-message text-danger small mt-1" id="name-error" style="display: none;">Nama tidak boleh kosong.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Alamat Email</label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                        <div class="error-message text-danger small mt-1" id="email-error" style="display: none;">Format email tidak valid.</div>
                    </div>
                    <div class="mb-4">
                        <label for="message" class="form-label fw-bold">Pesan Anda</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        <div class="error-message text-danger small mt-1" id="message-error" style="display: none;">Pesan tidak boleh kosong.</div>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg w-100">Kirim Pesan</button>
                </form>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>

