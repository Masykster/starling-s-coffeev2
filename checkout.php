<?php
require_once 'config.php';
$pageTitle = "Starling's Coffee - Checkout";
include 'includes/header.php';

requireLogin();

$error = '';
$success = '';

// Get cart items
$cartItems = getCartItems(getCurrentUserId());
$cartTotal = getCartTotal(getCurrentUserId());

// Jika cart kosong, redirect ke cart
if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}

// Get user info
$userId = getCurrentUserId();
try {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT name, email, phone, address FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    // Don't close connection - let pool handle it
} catch (Exception $e) {
    $error = 'Error mengambil data user.';
}

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    $deliveryType = $_POST['delivery_type'] ?? 'pickup';
    $shippingAddress = trim($_POST['shipping_address'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    
    // Validasi alamat jika delivery
    if ($deliveryType == 'delivery' && empty($shippingAddress)) {
        $error = 'Alamat pengiriman harus diisi untuk delivery.';
    }
    
    // Jika tidak ada error, lanjutkan proses checkout
    if (empty($error)) {
        // Untuk pickup, jika alamat kosong, set alamat default
        if ($deliveryType == 'pickup' && empty($shippingAddress)) {
            $shippingAddress = 'Ambil di tempat (Starling Coffee)';
        }
        
        try {
            $conn = getDBConnection();
            
            // Generate order number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
            
            // Create order dengan delivery_type
            $stmt = $conn->prepare("INSERT INTO orders (user_id, order_number, total_amount, delivery_type, shipping_address, notes) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $userId, $orderNumber, $cartTotal, $deliveryType, $shippingAddress, $notes);
            $stmt->execute();
            $orderId = $conn->insert_id;
            $stmt->close();
            
            // Create order items
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, menu_item_name, menu_item_price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
            
            foreach ($cartItems as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $stmt->bind_param("iisdid", $orderId, $item['menu_item_id'], $item['name'], $item['price'], $item['quantity'], $subtotal);
                $stmt->execute();
            }
            $stmt->close();
            
            // Tambahkan poin untuk user (1 poin per Rp 1.000)
            $pointsEarned = calculatePointsFromOrder($cartTotal);
            if ($pointsEarned > 0) {
                $description = "Poin dari pesanan #$orderNumber";
                addUserPoints($userId, $pointsEarned, $orderId, $description);
            }
            
            // Clear cart
            clearCart($userId);
            
            // Don't close connection - let pool handle it
            
            $deliveryTypeText = $deliveryType == 'delivery' ? 'Delivery' : 'Ambil di Tempat';
            $pointsMessage = $pointsEarned > 0 ? " Anda mendapatkan $pointsEarned poin!" : "";
            $success = "Pesanan berhasil dibuat! Nomor pesanan: $orderNumber ($deliveryTypeText).$pointsMessage";
            // Redirect akan dilakukan via JavaScript setelah 3 detik
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan saat checkout: ' . $e->getMessage();
        }
    }
}
?>

    <!-- Konten Halaman Checkout -->
    <main class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="content-page p-4 rounded shadow-sm mb-4">
                        <h1 class="display-5 fw-bold mb-4 pb-3 border-bottom">Checkout</h1>
                        
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
                            // Redirect setelah 3 detik jika checkout berhasil
                            setTimeout(function() {
                                window.location.href = 'index.php';
                            }, 3000);
                        </script>
                        <?php endif; ?>
                        
                        <form method="POST" action="checkout.php" id="checkoutForm">
                            <h3 class="mb-3">Pilihan Pengiriman</h3>
                            <div class="mb-4">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="delivery_type" id="delivery_pickup" value="pickup" 
                                        <?php echo (!isset($_POST['delivery_type']) || $_POST['delivery_type'] == 'pickup') ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-bold" for="delivery_pickup">
                                        <i class="bi bi-shop"></i> Ambil di Tempat
                                    </label>
                                    <small class="d-block text-muted ms-4">Ambil pesanan langsung di kedai kami</small>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delivery_type" id="delivery_delivery" value="delivery"
                                        <?php echo (isset($_POST['delivery_type']) && $_POST['delivery_type'] == 'delivery') ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-bold" for="delivery_delivery">
                                        <i class="bi bi-truck"></i> Delivery (Pengiriman)
                                    </label>
                                    <small class="d-block text-muted ms-4">Pesanan akan dikirim ke alamat Anda</small>
                                </div>
                            </div>
                            
                            <h3 class="mb-3">Informasi Kontak</h3>
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Nama</label>
                                <input type="text" class="form-control form-control-lg" id="name" 
                                    value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control form-control-lg" id="email" 
                                    value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label fw-bold">No. Telepon</label>
                                <input type="tel" class="form-control form-control-lg" id="phone" 
                                    value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="shipping_address" class="form-label fw-bold">
                                    <span id="addressLabel">Alamat Pengiriman</span> 
                                    <span id="addressRequired" class="text-danger"></span>
                                </label>
                                <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3"><?php echo isset($_POST['shipping_address']) ? htmlspecialchars($_POST['shipping_address']) : htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                <small class="text-muted" id="addressHint">Untuk delivery, alamat wajib diisi</small>
                            </div>
                            <div class="mb-4">
                                <label for="notes" class="form-label fw-bold">Catatan (Opsional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2"><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" name="checkout" class="btn btn-success btn-lg">Konfirmasi Pesanan</button>
                                <a href="cart.php" class="btn btn-outline-secondary">Kembali ke Keranjang</a>
                            </div>
                        </form>
                        
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const deliveryPickup = document.getElementById('delivery_pickup');
                            const deliveryDelivery = document.getElementById('delivery_delivery');
                            const shippingAddress = document.getElementById('shipping_address');
                            const addressLabel = document.getElementById('addressLabel');
                            const addressRequired = document.getElementById('addressRequired');
                            const addressHint = document.getElementById('addressHint');
                            const checkoutForm = document.getElementById('checkoutForm');
                            
                            if (!deliveryPickup || !deliveryDelivery) return;
                            
                            function updateDeliveryFields() {
                                if (deliveryDelivery && deliveryDelivery.checked) {
                                    shippingAddress.required = true;
                                    addressRequired.textContent = '*';
                                    addressLabel.textContent = 'Alamat Pengiriman';
                                    addressHint.textContent = 'Untuk delivery, alamat wajib diisi';
                                } else {
                                    shippingAddress.required = false;
                                    addressRequired.textContent = '';
                                    addressLabel.textContent = 'Catatan Alamat (Opsional)';
                                    addressHint.textContent = 'Kosongkan jika ambil di tempat';
                                }
                            }
                            
                            deliveryPickup.addEventListener('change', updateDeliveryFields);
                            deliveryDelivery.addEventListener('change', updateDeliveryFields);
                            
                            checkoutForm.addEventListener('submit', function(e) {
                                // Validasi hanya untuk delivery
                                if (deliveryDelivery.checked && !shippingAddress.value.trim()) {
                                    e.preventDefault();
                                    alert('Alamat pengiriman harus diisi untuk delivery.');
                                    shippingAddress.focus();
                                    return false;
                                }
                                // Untuk pickup, alamat boleh kosong, jadi tidak perlu validasi tambahan
                            });
                            
                            updateDeliveryFields();
                        });
                        </script>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="content-page p-4 rounded shadow-sm sticky-top" style="top: 100px;">
                        <h3 class="fw-bold mb-4">Ringkasan Pesanan</h3>
                        
                        <div class="mb-3">
                            <?php foreach($cartItems as $item): 
                                $subtotal = $item['price'] * $item['quantity'];
                            ?>
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <span class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></span>
                                    <small class="text-muted d-block"><?php echo $item['quantity']; ?>x</small>
                                </div>
                                <div class="text-end">
                                    <strong>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></strong>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">Total:</span>
                            <span class="fw-bold text-success fs-5">Rp <?php echo number_format($cartTotal, 0, ',', '.'); ?></span>
                        </div>
                        
                        <?php 
                        $pointsEarned = calculatePointsFromOrder($cartTotal);
                        if ($pointsEarned > 0):
                        ?>
                        <div class="alert alert-info mb-0 mt-3">
                            <small><i class="bi bi-star-fill"></i> Anda akan mendapatkan <strong><?php echo $pointsEarned; ?> poin</strong> dari pesanan ini!</small>
                        </div>
                        <?php endif; ?>
                        
                        <?php 
                        $userPoints = getUserTotalPoints(getCurrentUserId());
                        if ($userPoints > 0):
                        ?>
                        <div class="alert alert-warning mb-0 mt-2">
                            <small><i class="bi bi-wallet2"></i> Poin Anda saat ini: <strong><?php echo number_format($userPoints, 0, ',', '.'); ?> poin</strong></small>
                            <br>
                            <small><a href="rewards.php" class="text-decoration-none">Lihat rewards yang bisa ditukar →</a></small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>

