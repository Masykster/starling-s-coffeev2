<?php
require_once 'config.php';
requireAdminLogin();

$pageTitle = "Dashboard";

// Get statistics
try {
    $conn = getDBConnection();
    
    // Total orders
    $result = $conn->query("SELECT COUNT(*) as total FROM orders");
    $totalOrders = $result->fetch_assoc()['total'];
    
    // Total users
    $result = $conn->query("SELECT COUNT(*) as total FROM users");
    $totalUsers = $result->fetch_assoc()['total'];
    
    // Total menu items
    $result = $conn->query("SELECT COUNT(*) as total FROM menu_items");
    $totalMenu = $result->fetch_assoc()['total'];
    
    // Total revenue (completed orders)
    $result = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'");
    $totalRevenue = $result->fetch_assoc()['total'] ?? 0;
    
    // Recent orders
    $result = $conn->query("
        SELECT o.*, u.name as user_name, u.email as user_email 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC 
        LIMIT 10
    ");
    $recentOrders = [];
    while ($row = $result->fetch_assoc()) {
        $recentOrders[] = $row;
    }
    
    $conn->close();
} catch (Exception $e) {
    $totalOrders = $totalUsers = $totalMenu = $totalRevenue = 0;
    $recentOrders = [];
}
include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-white-50">Total Orders</h6>
                        <h2 class="mb-0"><?php echo number_format($totalOrders); ?></h2>
                    </div>
                    <i class="bi bi-cart-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-white-50">Total Revenue</h6>
                        <h2 class="mb-0">Rp <?php echo number_format($totalRevenue, 0, ',', '.'); ?></h2>
                    </div>
                    <i class="bi bi-currency-dollar fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-white-50">Total Users</h6>
                        <h2 class="mb-0"><?php echo number_format($totalUsers); ?></h2>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-white-50">Menu Items</h6>
                        <h2 class="mb-0"><?php echo number_format($totalMenu); ?></h2>
                    </div>
                    <i class="bi bi-menu-button-wide fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Orders</h5>
                <a href="orders.php" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($recentOrders)): ?>
                <p class="text-muted text-center py-4">No orders yet.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order Number</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recentOrders as $order): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                                <td>
                                    <?php echo htmlspecialchars($order['user_name']); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($order['user_email']); ?></small>
                                </td>
                                <td>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $order['status'] == 'completed' ? 'success' : 
                                            ($order['status'] == 'processing' ? 'warning' : 
                                            ($order['status'] == 'cancelled' ? 'danger' : 'secondary')); 
                                    ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <a href="orders.php?view=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

