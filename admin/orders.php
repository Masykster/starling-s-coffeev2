<?php
require_once 'config.php';
requireAdminLogin();

$pageTitle = "Manage Orders";
$message = '';
$messageType = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $orderId = intval($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    
    if ($orderId > 0 && in_array($status, ['pending', 'processing', 'completed', 'cancelled'])) {
        try {
            $conn = getDBConnection();
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $orderId);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            
            $message = 'Order status updated successfully!';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'danger';
        }
    }
}

// Get orders
try {
    $conn = getDBConnection();
    
    // Get single order if view parameter exists
    $viewOrderId = isset($_GET['view']) ? intval($_GET['view']) : 0;
    if ($viewOrderId > 0) {
        $stmt = $conn->prepare("
            SELECT o.*, u.name as user_name, u.email as user_email, u.phone as user_phone
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            WHERE o.id = ?
        ");
        $stmt->bind_param("i", $viewOrderId);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($order) {
            // Get order items
            $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $stmt->bind_param("i", $viewOrderId);
            $stmt->execute();
            $orderItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }
    }
    
    // Get all orders
    $statusFilter = $_GET['status'] ?? '';
    if ($statusFilter && in_array($statusFilter, ['pending', 'processing', 'completed', 'cancelled'])) {
        $stmt = $conn->prepare("
            SELECT o.*, u.name as user_name, u.email as user_email
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            WHERE o.status = ?
            ORDER BY o.created_at DESC
        ");
        $stmt->bind_param("s", $statusFilter);
    } else {
        $stmt = $conn->prepare("
            SELECT o.*, u.name as user_name, u.email as user_email
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            ORDER BY o.created_at DESC
        ");
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    $orders = [];
    $order = null;
    $orderItems = [];
}

include 'includes/header.php';
?>

<?php if ($message): ?>
<div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
    <?php echo htmlspecialchars($message); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (isset($order)): ?>
<!-- View Single Order -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Order Details: <?php echo htmlspecialchars($order['order_number']); ?></h5>
        <a href="orders.php" class="btn btn-sm btn-secondary">Back to Orders</a>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h6>Customer Information</h6>
                <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($order['user_name']); ?></p>
                <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($order['user_email']); ?></p>
                <p class="mb-1"><strong>Phone:</strong> <?php echo htmlspecialchars($order['user_phone'] ?? 'N/A'); ?></p>
            </div>
            <div class="col-md-6">
                <h6>Order Information</h6>
                <p class="mb-1"><strong>Status:</strong> 
                    <span class="badge bg-<?php 
                        echo $order['status'] == 'completed' ? 'success' : 
                            ($order['status'] == 'processing' ? 'warning' : 
                            ($order['status'] == 'cancelled' ? 'danger' : 'secondary')); 
                    ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </p>
                <p class="mb-1"><strong>Delivery Type:</strong> 
                    <?php 
                    $deliveryType = $order['delivery_type'] ?? 'pickup';
                    ?>
                    <span class="badge bg-<?php echo $deliveryType == 'delivery' ? 'primary' : 'info'; ?>">
                        <i class="bi bi-<?php echo $deliveryType == 'delivery' ? 'truck' : 'shop'; ?>"></i>
                        <?php echo $deliveryType == 'delivery' ? 'Delivery' : 'Ambil di Tempat'; ?>
                    </span>
                </p>
                <p class="mb-1"><strong>Date:</strong> <?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></p>
                <p class="mb-1"><strong><?php echo $deliveryType == 'delivery' ? 'Shipping Address' : 'Address'; ?>:</strong><br><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                <?php if ($order['notes']): ?>
                <p class="mb-1"><strong>Notes:</strong> <?php echo htmlspecialchars($order['notes']); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <h6>Order Items</h6>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orderItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['menu_item_name']); ?></td>
                        <td>Rp <?php echo number_format($item['menu_item_price'], 0, ',', '.'); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td><strong>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <form method="POST" action="orders.php">
            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Update Status</label>
                    <select name="status" class="form-select" required>
                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                        <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Orders List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Orders</h5>
        <div>
            <a href="orders.php" class="btn btn-sm btn-outline-secondary">All</a>
            <a href="orders.php?status=pending" class="btn btn-sm btn-outline-warning">Pending</a>
            <a href="orders.php?status=processing" class="btn btn-sm btn-outline-info">Processing</a>
            <a href="orders.php?status=completed" class="btn btn-sm btn-outline-success">Completed</a>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($orders)): ?>
        <p class="text-muted text-center py-4">No orders found.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Delivery</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $ord): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($ord['order_number']); ?></strong></td>
                        <td>
                            <?php echo htmlspecialchars($ord['user_name']); ?><br>
                            <small class="text-muted"><?php echo htmlspecialchars($ord['user_email']); ?></small>
                        </td>
                        <td>Rp <?php echo number_format($ord['total_amount'], 0, ',', '.'); ?></td>
                        <td>
                            <?php 
                            $deliveryType = $ord['delivery_type'] ?? 'pickup';
                            ?>
                            <span class="badge bg-<?php echo $deliveryType == 'delivery' ? 'primary' : 'info'; ?>">
                                <i class="bi bi-<?php echo $deliveryType == 'delivery' ? 'truck' : 'shop'; ?>"></i>
                                <?php echo $deliveryType == 'delivery' ? 'Delivery' : 'Pickup'; ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $ord['status'] == 'completed' ? 'success' : 
                                    ($ord['status'] == 'processing' ? 'warning' : 
                                    ($ord['status'] == 'cancelled' ? 'danger' : 'secondary')); 
                            ?>">
                                <?php echo ucfirst($ord['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d M Y H:i', strtotime($ord['created_at'])); ?></td>
                        <td>
                            <a href="orders.php?view=<?php echo $ord['id']; ?>" class="btn btn-sm btn-primary">
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

<?php include 'includes/footer.php'; ?>

