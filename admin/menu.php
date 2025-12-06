<?php
require_once 'config.php';
requireAdminLogin();

$pageTitle = "Manage Menu Items";
$message = '';
$messageType = '';

// Handle add/edit menu item
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $category = $_POST['category'] ?? 'minuman';
    $price = floatval($_POST['price'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    
    // Handle file upload if new file is uploaded
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image_file'];
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        
        if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxFileSize) {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'menu_' . time() . '_' . uniqid() . '.' . $extension;
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
    
    // If no new image and no existing image path, require image
    if (empty($image) && ($id == 0 || empty($editItem['image']))) {
        $message = 'Image is required!';
        $messageType = 'danger';
    } elseif (empty($name) || empty($category) || $price <= 0) {
        $message = 'Name, category, and price are required!';
        $messageType = 'danger';
    } else {
        try {
            $conn = getDBConnection();
            
            // Cek apakah field status ada
            $checkStatus = $conn->query("SHOW COLUMNS FROM menu_items LIKE 'status'");
            $hasStatusField = $checkStatus->num_rows > 0;
            
            if ($id > 0) {
                // Update
                if ($hasStatusField) {
                    $stmt = $conn->prepare("UPDATE menu_items SET name = ?, description = ?, image = ?, category = ?, price = ?, status = ? WHERE id = ?");
                    $stmt->bind_param("ssssdsi", $name, $description, $image, $category, $price, $status, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE menu_items SET name = ?, description = ?, image = ?, category = ?, price = ? WHERE id = ?");
                    $stmt->bind_param("ssssdi", $name, $description, $image, $category, $price, $id);
                }
            } else {
                // Insert
                if ($hasStatusField) {
                    $stmt = $conn->prepare("INSERT INTO menu_items (name, description, image, category, price, status) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssds", $name, $description, $image, $category, $price, $status);
                } else {
                    $stmt = $conn->prepare("INSERT INTO menu_items (name, description, image, category, price) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssd", $name, $description, $image, $category, $price);
                }
            }
            
            $stmt->execute();
            $stmt->close();
            $conn->close();
            
            $message = $id > 0 ? 'Menu item updated successfully!' : 'Menu item added successfully!';
            if (!$hasStatusField) {
                $message .= ' Note: Field "status" belum ada di database. Silakan jalankan fix_menu_status.php untuk menambahkan field status.';
            }
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
        $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        
        $message = 'Menu item deleted successfully!';
        $messageType = 'success';
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Get menu item for edit
$editItem = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $editItem = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        $editItem = null;
    }
}

// Get all menu items
try {
    $conn = getDBConnection();
    $result = $conn->query("SELECT * FROM menu_items ORDER BY category, name");
    $menuItems = [];
    while ($row = $result->fetch_assoc()) {
        $menuItems[] = $row;
    }
    $conn->close();
} catch (Exception $e) {
    $menuItems = [];
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
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><?php echo $editItem ? 'Edit' : 'Add'; ?> Menu Item</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="menu.php" enctype="multipart/form-data">
                    <?php if ($editItem): ?>
                    <input type="hidden" name="id" value="<?php echo $editItem['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" 
                            value="<?php echo $editItem ? htmlspecialchars($editItem['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"><?php echo $editItem ? htmlspecialchars($editItem['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Image <span class="text-danger">*</span></label>
                        <input type="hidden" name="image" id="image_path" 
                            value="<?php echo $editItem ? htmlspecialchars($editItem['image']) : ''; ?>" required>
                        
                        <div id="image-upload-area" class="border rounded p-3 text-center" 
                            style="min-height: 200px; cursor: pointer; border: 2px dashed #ccc !important;">
                            <div id="image-upload-placeholder">
                                <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="mt-2 mb-0">Drag & drop gambar di sini atau klik untuk memilih</p>
                                <small class="text-muted">Format: JPG, PNG, GIF, WebP (Max 5MB)</small>
                            </div>
                            <img id="image-preview" src="" alt="Preview" class="img-fluid d-none" 
                                style="max-height: 200px; border-radius: 4px;">
                        </div>
                        <input type="file" id="image_file" name="image_file" accept="image/*" 
                            class="d-none" onchange="handleFileSelect(event)">
                        
                        <?php if ($editItem && !empty($editItem['image'])): ?>
                        <div class="mt-2">
                            <small class="text-muted">Gambar saat ini: </small>
                            <a href="../<?php echo htmlspecialchars($editItem['image']); ?>" target="_blank" 
                                class="text-decoration-none">
                                <?php echo htmlspecialchars(basename($editItem['image'])); ?>
                            </a>
                            <img src="../<?php echo htmlspecialchars($editItem['image']); ?>" 
                                alt="Current" class="img-thumbnail ms-2" style="max-height: 50px;">
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select" name="category" required>
                            <option value="minuman" <?php echo ($editItem && $editItem['category'] == 'minuman') ? 'selected' : ''; ?>>Minuman</option>
                            <option value="makanan" <?php echo ($editItem && $editItem['category'] == 'makanan') ? 'selected' : ''; ?>>Makanan</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Price <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="price" step="0.01" min="0"
                            value="<?php echo $editItem ? $editItem['price'] : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <?php 
                            $currentStatus = $editItem ? ($editItem['status'] ?? 'active') : 'active';
                            ?>
                            <option value="active" <?php echo $currentStatus == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $currentStatus == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <?php echo $editItem ? 'Update' : 'Add'; ?> Menu Item
                    </button>
                    
                    <?php if ($editItem): ?>
                    <a href="menu.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Menu Items</h5>
            </div>
            <div class="card-body">
                <?php if (empty($menuItems)): ?>
                <p class="text-muted text-center py-4">No menu items found.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($menuItems as $item): ?>
                            <tr>
                                <td>
                                    <img src="../<?php echo htmlspecialchars($item['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars(substr($item['description'], 0, 50)); ?>...</small>
                                </td>
                                <td><span class="badge bg-info"><?php echo ucfirst($item['category']); ?></span></td>
                                <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                <td>
                                    <?php 
                                    $status = $item['status'] ?? 'active';
                                    ?>
                                    <span class="badge bg-<?php echo $status == 'active' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($status); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="menu.php?edit=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="menu.php?delete=<?php echo $item['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this item?');">
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
// Drag and drop functionality
const uploadArea = document.getElementById('image-upload-area');
const fileInput = document.getElementById('image_file');
const imagePreview = document.getElementById('image-preview');
const placeholder = document.getElementById('image-upload-placeholder');
const imagePathInput = document.getElementById('image_path');

<?php if ($editItem && !empty($editItem['image'])): ?>
// Show current image on edit
const currentImagePath = '../<?php echo htmlspecialchars($editItem['image']); ?>';
imagePreview.src = currentImagePath;
imagePreview.classList.remove('d-none');
placeholder.classList.add('d-none');
<?php endif; ?>

// Click to select file
uploadArea.addEventListener('click', () => fileInput.click());

// Drag and drop events
uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.style.borderColor = '#007bff';
    uploadArea.style.backgroundColor = '#f0f8ff';
});

uploadArea.addEventListener('dragleave', (e) => {
    e.preventDefault();
    uploadArea.style.borderColor = '#ccc';
    uploadArea.style.backgroundColor = '';
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.style.borderColor = '#ccc';
    uploadArea.style.backgroundColor = '';
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        handleFileSelect({ target: { files: files } });
    }
});

function handleFileSelect(event) {
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
        imagePreview.src = e.target.result;
        imagePreview.classList.remove('d-none');
        placeholder.classList.add('d-none');
        
        // Store filename for form submission
        // The actual upload will happen on form submit
        imagePathInput.value = 'temp_' + file.name;
    };
    reader.readAsDataURL(file);
}
</script>

<?php include 'includes/footer.php'; ?>

