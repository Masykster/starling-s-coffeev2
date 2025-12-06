<?php
require_once 'config.php';
$pageTitle = "Starling's Coffee - Menu";
include 'includes/header.php';

// Ambil data menu dari database
$minuman = [];
$makanan = [];

$dbError = '';
try {
    $conn = getDBConnection();
    
    // Optimized: Single query instead of multiple queries
    // Check table exists and fetch all items in one query
    $sql = "SELECT * FROM menu_items ORDER BY category, name";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        // Separate items by category in PHP instead of multiple DB queries
        while($row = $result->fetch_assoc()) {
            if ($row['category'] == 'minuman') {
                $minuman[] = $row;
            } elseif ($row['category'] == 'makanan') {
                $makanan[] = $row;
            }
        }
    } elseif ($result === false) {
        // Check if table doesn't exist
        $checkTable = $conn->query("SHOW TABLES LIKE 'menu_items'");
        if (!$checkTable || $checkTable->num_rows === 0) {
            $dbError = 'Tabel menu_items belum ada. Silakan import file database.sql.';
        }
    }
    // Don't close connection - let pool handle it
} catch (Exception $e) {
    // Tampilkan error untuk membantu debugging
    $dbError = $e->getMessage();
    error_log("Error loading menu: " . $e->getMessage());
}
?>

    <!-- Konten Halaman Menu -->
    <main class="py-5">
        <div class="container">
            <div class="content-page p-4 rounded shadow-sm">
                <h1 class="display-4 fw-bold mb-3 pb-3 border-bottom">Menu Kami</h1>
                <p class="lead mb-4">Jelajahi minuman dan makanan favorit yang dibuat dengan bahan-bahan terbaik.</p>
                
                <?php if ($dbError): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Peringatan!</strong> <?php echo htmlspecialchars($dbError); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <div class="mb-4">
                    <input type="text" id="menuSearch" placeholder="Cari menu..." class="form-control form-control-lg">
                </div>

                <h2 class="h3 fw-bold mt-5 mb-4 pb-2 border-bottom">Minuman Unggulan</h2>
                <div class="row g-4 mb-5">
                    <?php foreach($minuman as $item): ?>
                    <div class="col-md-6 col-lg-4 menu-item">
                        <div class="card h-100 shadow-sm">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                loading="lazy" width="400" height="200" style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h3 class="card-title h5 fw-bold"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="card-text flex-grow-1"><?php echo htmlspecialchars($item['description']); ?></p>
                                <?php if($item['price']): ?>
                                <p class="menu-price text-success fw-bold fs-5 mb-3">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></p>
                                <?php endif; ?>
                                <?php if(isLoggedIn()): ?>
                                <form method="POST" action="cart.php" class="mt-auto">
                                    <input type="hidden" name="menu_item_id" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" name="add_to_cart" class="btn btn-success w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="display: inline-block; margin-right: 5px;">
                                            <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                        </svg>
                                        Tambah ke Keranjang
                                    </button>
                                </form>
                                <?php else: ?>
                                <a href="login.php" class="btn btn-outline-success w-100">Login untuk Beli</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <h2 class="h3 fw-bold mt-5 mb-4 pb-2 border-bottom">Makanan & Pastry</h2>
                <div class="row g-4">
                    <?php foreach($makanan as $item): ?>
                    <div class="col-md-6 col-lg-4 menu-item">
                        <div class="card h-100 shadow-sm">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                loading="lazy" width="400" height="200" style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h3 class="card-title h5 fw-bold"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="card-text flex-grow-1"><?php echo htmlspecialchars($item['description']); ?></p>
                                <?php if($item['price']): ?>
                                <p class="menu-price text-success fw-bold fs-5 mb-3">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></p>
                                <?php endif; ?>
                                <?php if(isLoggedIn()): ?>
                                <form method="POST" action="cart.php" class="mt-auto">
                                    <input type="hidden" name="menu_item_id" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" name="add_to_cart" class="btn btn-success w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="display: inline-block; margin-right: 5px;">
                                            <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                        </svg>
                                        Tambah ke Keranjang
                                    </button>
                                </form>
                                <?php else: ?>
                                <a href="login.php" class="btn btn-outline-success w-100">Login untuk Beli</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>

