<?php
require_once 'config.php';
$pageTitle = "Starling's Coffee - Keranjang";
include 'includes/header.php';

requireLogin();

$message = '';
$messageType = '';

// Handle add to cart (from AJAX or form)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $menuItemId = intval($_POST['menu_item_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    
    if ($menuItemId > 0) {
        $result = addToCart(getCurrentUserId(), $menuItemId, $quantity);
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    }
}

// Handle update quantity
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quantity'])) {
    $cartItemId = intval($_POST['cart_item_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    
    if ($cartItemId > 0) {
        $result = updateCartItemQuantity($cartItemId, getCurrentUserId(), $quantity);
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    }
}

// Handle remove from cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_item'])) {
    $cartItemId = intval($_POST['cart_item_id'] ?? 0);
    
    if ($cartItemId > 0) {
        $result = removeFromCart($cartItemId, getCurrentUserId());
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    }
}

// Get cart items
$cartItems = getCartItems(getCurrentUserId());
$cartTotal = getCartTotal(getCurrentUserId());
?>

    <!-- Konten Halaman Cart -->
    <main class="py-5">
        <div class="container">
            <div class="content-page p-4 rounded shadow-sm">
                <h1 class="display-4 fw-bold mb-3 pb-3 border-bottom">Keranjang Belanja</h1>
                
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if (empty($cartItems)): ?>
                <div class="text-center py-5">
                    <h3 class="mb-3">Keranjang Anda kosong</h3>
                    <p class="lead mb-4">Mulai berbelanja dan tambahkan item ke keranjang!</p>
                    <a href="menu.php" class="btn btn-success btn-lg">Lihat Menu</a>
                </div>
                <?php else: ?>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="15%">Gambar</th>
                                <th>Nama Item</th>
                                <th width="15%">Harga</th>
                                <th width="15%">Jumlah</th>
                                <th width="15%">Subtotal</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cartItems as $item): 
                                $subtotal = $item['price'] * $item['quantity'];
                            ?>
                            <tr>
                                <td>
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                         class="img-fluid rounded" style="max-width: 80px;">
                                </td>
                                <td>
                                    <h5 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h5>
                                </td>
                                <td>
                                    <strong>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></strong>
                                </td>
                                <td>
                                    <form method="POST" action="cart.php" class="d-inline">
                                        <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                                        <div class="input-group">
                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                                   min="1" max="99" class="form-control" style="width: 70px;">
                                            <button type="submit" name="update_quantity" class="btn btn-sm btn-outline-secondary">Update</button>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <strong class="text-success">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></strong>
                                </td>
                                <td>
                                    <form method="POST" action="cart.php" class="d-inline" 
                                          onsubmit="return confirm('Yakin ingin menghapus item ini?');">
                                        <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" name="remove_item" class="btn btn-sm btn-danger">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total:</td>
                                <td class="fw-bold text-success fs-5">Rp <?php echo number_format($cartTotal, 0, ',', '.'); ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <a href="menu.php" class="btn btn-outline-secondary btn-lg">Lanjut Belanja</a>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="checkout.php" class="btn btn-success btn-lg">Checkout</a>
                    </div>
                </div>
                
                <?php endif; ?>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>

