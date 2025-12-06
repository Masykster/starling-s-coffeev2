<?php
require_once 'config.php';
requireAdminLogin();

$pageTitle = "Manage Rewards";
$message = '';
$messageType = '';

// Handle add/edit reward
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_reward'])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $pointsRequired = intval($_POST['points_required'] ?? 0);
    $rewardType = $_POST['reward_type'] ?? 'discount';
    $discountPercent = null;
    $discountAmount = null;
    $freeItemName = null;
    $cashbackAmount = null;
    $image = trim($_POST['image'] ?? '');
    $status = $_POST['status'] ?? 'active';
    $stock = !empty($_POST['stock']) ? intval($_POST['stock']) : null;
    
    // Set nilai berdasarkan reward type
    if ($rewardType == 'discount') {
        $discountPercent = floatval($_POST['discount_percent'] ?? 0);
        $discountAmount = floatval($_POST['discount_amount'] ?? 0);
    } elseif ($rewardType == 'free_item') {
        $freeItemName = trim($_POST['free_item_name'] ?? '');
    } elseif ($rewardType == 'cashback') {
        $cashbackAmount = floatval($_POST['cashback_amount'] ?? 0);
    }
    
    // Handle file upload if new file is uploaded
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image_file'];
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        
        if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxFileSize) {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'reward_' . time() . '_' . uniqid() . '.' . $extension;
            $uploadDir = '../images/';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $uploadPath = $uploadDir . $filename;
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $image = 'images/' . $filename;
            }
        }
    }
    
    if (empty($name) || $pointsRequired <= 0) {
        $message = 'Name dan points required harus diisi!';
        $messageType = 'danger';
    } else {
        try {
            $conn = getDBConnection();
            
            if ($id > 0) {
                // Update
                $stmt = $conn->prepare("UPDATE rewards SET name = ?, description = ?, points_required = ?, reward_type = ?, discount_percent = ?, discount_amount = ?, free_item_name = ?, cashback_amount = ?, image = ?, status = ?, stock = ? WHERE id = ?");
                $stmt->bind_param("ssisddsssdsi", $name, $description, $pointsRequired, $rewardType, $discountPercent, $discountAmount, $freeItemName, $cashbackAmount, $image, $status, $stock, $id);
            } else {
                // Insert
                $stmt = $conn->prepare("INSERT INTO rewards (name, description, points_required, reward_type, discount_percent, discount_amount, free_item_name, cashback_amount, image, status, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssisddsssds", $name, $description, $pointsRequired, $rewardType, $discountPercent, $discountAmount, $freeItemName, $cashbackAmount, $image, $status, $stock);
            }
            
            $stmt->execute();
            $stmt->close();
            $conn->close();
            
            $message = $id > 0 ? 'Reward updated successfully!' : 'Reward added successfully!';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'danger';
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("DELETE FROM rewards WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        
        $message = 'Reward deleted successfully!';
        $messageType = 'success';
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Get reward for edit
$editReward = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT * FROM rewards WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $editReward = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        $editReward = null;
    }
}

// Get all rewards
try {
    $conn = getDBConnection();
    $result = $conn->query("SELECT * FROM rewards ORDER BY points_required ASC");
    $rewards = [];
    while ($row = $result->fetch_assoc()) {
        $rewards[] = $row;
    }
    $conn->close();
} catch (Exception $e) {
    $rewards = [];
}

include 'includes/header.php';
?>

<?php if ($message): ?>
<div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
    <?php echo htmlspecialchars($message); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><?php echo $editReward ? 'Edit' : 'Add'; ?> Reward</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="rewards.php" enctype="multipart/form-data">
                    <?php if ($editReward): ?>
                    <input type="hidden" name="id" value="<?php echo $editReward['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" 
                            value="<?php echo $editReward ? htmlspecialchars($editReward['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"><?php echo $editReward ? htmlspecialchars($editReward['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Points Required <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="points_required" min="1"
                            value="<?php echo $editReward ? $editReward['points_required'] : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Reward Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="reward_type" id="reward_type" required>
                            <option value="discount" <?php echo ($editReward && $editReward['reward_type'] == 'discount') ? 'selected' : ''; ?>>Discount</option>
                            <option value="free_item" <?php echo ($editReward && $editReward['reward_type'] == 'free_item') ? 'selected' : ''; ?>>Free Item</option>
                            <option value="cashback" <?php echo ($editReward && $editReward['reward_type'] == 'cashback') ? 'selected' : ''; ?>>Cashback</option>
                        </select>
                    </div>
                    
                    <div id="discount_fields" style="display: <?php echo (!$editReward || $editReward['reward_type'] == 'discount') ? 'block' : 'none'; ?>;">
                        <div class="mb-3">
                            <label class="form-label">Discount Percent (%)</label>
                            <input type="number" class="form-control" name="discount_percent" step="0.01" min="0" max="100"
                                value="<?php echo $editReward ? ($editReward['discount_percent'] ?? '') : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Discount Amount (Rp)</label>
                            <input type="number" class="form-control" name="discount_amount" step="0.01" min="0"
                                value="<?php echo $editReward ? ($editReward['discount_amount'] ?? '') : ''; ?>">
                        </div>
                    </div>
                    
                    <div id="free_item_fields" style="display: <?php echo ($editReward && $editReward['reward_type'] == 'free_item') ? 'block' : 'none'; ?>;">
                        <div class="mb-3">
                            <label class="form-label">Free Item Name</label>
                            <input type="text" class="form-control" name="free_item_name"
                                value="<?php echo $editReward ? htmlspecialchars($editReward['free_item_name'] ?? '') : ''; ?>">
                        </div>
                    </div>
                    
                    <div id="cashback_fields" style="display: <?php echo ($editReward && $editReward['reward_type'] == 'cashback') ? 'block' : 'none'; ?>;">
                        <div class="mb-3">
                            <label class="form-label">Cashback Amount (Rp)</label>
                            <input type="number" class="form-control" name="cashback_amount" step="0.01" min="0"
                                value="<?php echo $editReward ? ($editReward['cashback_amount'] ?? '') : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="hidden" name="image" id="reward_image_path" 
                            value="<?php echo $editReward ? htmlspecialchars($editReward['image'] ?? '') : ''; ?>">
                        
                        <div id="reward-image-upload-area" class="border rounded p-3 text-center" 
                            style="min-height: 200px; cursor: pointer; border: 2px dashed #ccc !important;">
                            <div id="reward-image-upload-placeholder">
                                <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="mt-2 mb-0">Drag & drop gambar di sini atau klik untuk memilih</p>
                                <small class="text-muted">Format: JPG, PNG, GIF, WebP (Max 5MB)</small>
                            </div>
                            <img id="reward-image-preview" src="" alt="Preview" class="img-fluid d-none" 
                                style="max-height: 200px; border-radius: 4px;">
                        </div>
                        <input type="file" id="reward_image_file" name="image_file" accept="image/*" 
                            class="d-none" onchange="handleRewardFileSelect(event)">
                        
                        <?php if ($editReward && !empty($editReward['image'])): ?>
                        <div class="mt-2">
                            <small class="text-muted">Gambar saat ini: </small>
                            <a href="../<?php echo htmlspecialchars($editReward['image']); ?>" target="_blank" 
                                class="text-decoration-none">
                                <?php echo htmlspecialchars(basename($editReward['image'])); ?>
                            </a>
                            <img src="../<?php echo htmlspecialchars($editReward['image']); ?>" 
                                alt="Current" class="img-thumbnail ms-2" style="max-height: 50px;">
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Stock (NULL = Unlimited)</label>
                        <input type="number" class="form-control" name="stock" min="0"
                            value="<?php echo $editReward ? ($editReward['stock'] ?? '') : ''; ?>" 
                            placeholder="Kosongkan untuk unlimited">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="active" <?php echo (!$editReward || $editReward['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($editReward && $editReward['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="save_reward" class="btn btn-primary w-100">
                        <?php echo $editReward ? 'Update' : 'Add'; ?> Reward
                    </button>
                    
                    <?php if ($editReward): ?>
                    <a href="rewards.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Rewards</h5>
            </div>
            <div class="card-body">
                <?php if (empty($rewards)): ?>
                <p class="text-muted text-center py-4">No rewards found.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Points</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($rewards as $reward): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($reward['name']); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars(substr($reward['description'], 0, 50)); ?>...</small>
                                </td>
                                <td>
                                    <?php if ($reward['reward_type'] == 'discount'): ?>
                                        <span class="badge bg-success">Discount</span>
                                    <?php elseif ($reward['reward_type'] == 'free_item'): ?>
                                        <span class="badge bg-primary">Free Item</span>
                                    <?php elseif ($reward['reward_type'] == 'cashback'): ?>
                                        <span class="badge bg-info">Cashback</span>
                                    <?php endif; ?>
                                </td>
                                <td><strong class="text-warning"><?php echo number_format($reward['points_required'], 0, ',', '.'); ?></strong></td>
                                <td>
                                    <?php echo $reward['stock'] !== null ? number_format($reward['stock'], 0, ',', '.') : 'Unlimited'; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $reward['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($reward['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="rewards.php?edit=<?php echo $reward['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="rewards.php?delete=<?php echo $reward['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this reward?');">
                                        <i class="bi bi-trash"></i>
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

<script>
document.getElementById('reward_type').addEventListener('change', function() {
    const type = this.value;
    document.getElementById('discount_fields').style.display = type === 'discount' ? 'block' : 'none';
    document.getElementById('free_item_fields').style.display = type === 'free_item' ? 'block' : 'none';
    document.getElementById('cashback_fields').style.display = type === 'cashback' ? 'block' : 'none';
});

// Drag and drop functionality for reward image
const rewardUploadArea = document.getElementById('reward-image-upload-area');
const rewardFileInput = document.getElementById('reward_image_file');
const rewardImagePreview = document.getElementById('reward-image-preview');
const rewardPlaceholder = document.getElementById('reward-image-upload-placeholder');
const rewardImagePathInput = document.getElementById('reward_image_path');

<?php if ($editReward && !empty($editReward['image'])): ?>
// Show current image on edit
const rewardCurrentImagePath = '../<?php echo htmlspecialchars($editReward['image']); ?>';
rewardImagePreview.src = rewardCurrentImagePath;
rewardImagePreview.classList.remove('d-none');
rewardPlaceholder.classList.add('d-none');
<?php endif; ?>

// Click to select file
if (rewardUploadArea) {
    rewardUploadArea.addEventListener('click', () => rewardFileInput.click());
    
    // Drag and drop events
    rewardUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        rewardUploadArea.style.borderColor = '#007bff';
        rewardUploadArea.style.backgroundColor = '#f0f8ff';
    });
    
    rewardUploadArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        rewardUploadArea.style.borderColor = '#ccc';
        rewardUploadArea.style.backgroundColor = '';
    });
    
    rewardUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        rewardUploadArea.style.borderColor = '#ccc';
        rewardUploadArea.style.backgroundColor = '';
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            rewardFileInput.files = files;
            handleRewardFileSelect({ target: { files: files } });
        }
    });
}

function handleRewardFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        alert('Format file tidak didukung! Gunakan JPG, PNG, GIF, atau WebP.');
        return;
    }
    
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('Ukuran file terlalu besar! Maksimal 5MB.');
        return;
    }
    
    // Show preview
    const reader = new FileReader();
    reader.onload = (e) => {
        rewardImagePreview.src = e.target.result;
        rewardImagePreview.classList.remove('d-none');
        rewardPlaceholder.classList.add('d-none');
        
        // Store filename for form submission
        rewardImagePathInput.value = 'temp_' + file.name;
    };
    reader.readAsDataURL(file);
}
</script>

<?php include 'includes/footer.php'; ?>

