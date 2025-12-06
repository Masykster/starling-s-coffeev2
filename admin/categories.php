<?php
require_once 'config.php';
requireAdminLogin();

$pageTitle = "Manage Categories";
$message = '';
$messageType = '';

// Get all categories from menu_items
try {
    $conn = getDBConnection();
    $result = $conn->query("SELECT DISTINCT category, COUNT(*) as count FROM menu_items GROUP BY category");
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    
    // Get menu items by category for editing
    if (isset($_GET['edit'])) {
        $category = $_GET['edit'];
        $result = $conn->query("SELECT * FROM menu_items WHERE category = '$category'");
        $itemsInCategory = [];
        while ($row = $result->fetch_assoc()) {
            $itemsInCategory[] = $row;
        }
    }
    
    $conn->close();
} catch (Exception $e) {
    $categories = [];
    $itemsInCategory = [];
}

include 'includes/header.php';
?>

<?php if ($message): ?>
<div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
    <?php echo htmlspecialchars($message); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (isset($_GET['edit'])): ?>
<!-- Edit Category -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Category: <?php echo ucfirst(htmlspecialchars($_GET['edit'])); ?></h5>
        <a href="categories.php" class="btn btn-sm btn-secondary">Back</a>
    </div>
    <div class="card-body">
        <p class="text-muted">To change category name, you need to update each menu item individually.</p>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($itemsInCategory as $item): 
                        $status = $item['status'] ?? 'active';
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $status == 'active' ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($status); ?>
                            </span>
                        </td>
                        <td>
                            <a href="menu.php?edit=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Categories</h5>
    </div>
    <div class="card-body">
        <p class="text-muted">Categories are automatically created from menu items. To edit a category, update the category field in menu items.</p>
        <div class="row">
            <?php foreach($categories as $cat): ?>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0"><?php echo ucfirst(htmlspecialchars($cat['category'])); ?></h5>
                                <small class="text-muted"><?php echo $cat['count']; ?> items</small>
                            </div>
                            <a href="categories.php?edit=<?php echo urlencode($cat['category']); ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="alert alert-info mt-4">
            <strong>Note:</strong> To add a new category, create a menu item with a new category name. 
            To rename a category, edit all menu items in that category and change their category field.
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

