<?php
require_once 'config.php';
requireAdminLogin();

$pageTitle = "Site Settings";
$message = '';
$messageType = '';

// Handle update settings
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_settings'])) {
    try {
        $conn = getDBConnection();
        
        foreach ($_POST['settings'] as $key => $value) {
            $stmt = $conn->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->bind_param("ss", $value, $key);
            $stmt->execute();
            $stmt->close();
        }
        
        $conn->close();
        $message = 'Settings updated successfully!';
        $messageType = 'success';
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Get all settings
try {
    $conn = getDBConnection();
    $result = $conn->query("SELECT * FROM site_settings ORDER BY setting_key");
    $settings = [];
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row;
    }
    $conn->close();
} catch (Exception $e) {
    $settings = [];
}

include 'includes/header.php';
?>

<?php if ($message): ?>
<div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
    <?php echo htmlspecialchars($message); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Site Settings</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="settings.php">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Site Name</label>
                    <input type="text" class="form-control" name="settings[site_name]" 
                        value="<?php echo htmlspecialchars($settings['site_name']['setting_value'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Site Email</label>
                    <input type="email" class="form-control" name="settings[site_email]" 
                        value="<?php echo htmlspecialchars($settings['site_email']['setting_value'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Site Description</label>
                <textarea class="form-control" name="settings[site_description]" rows="2"><?php echo htmlspecialchars($settings['site_description']['setting_value'] ?? ''); ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" name="settings[site_phone]" 
                        value="<?php echo htmlspecialchars($settings['site_phone']['setting_value'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" class="form-control" name="settings[site_address]" 
                        value="<?php echo htmlspecialchars($settings['site_address']['setting_value'] ?? ''); ?>">
                </div>
            </div>
            
            <hr>
            <h6 class="mb-3">Social Media Links</h6>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Facebook</label>
                    <input type="url" class="form-control" name="settings[site_facebook]" 
                        value="<?php echo htmlspecialchars($settings['site_facebook']['setting_value'] ?? ''); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Instagram</label>
                    <input type="url" class="form-control" name="settings[site_instagram]" 
                        value="<?php echo htmlspecialchars($settings['site_instagram']['setting_value'] ?? ''); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Twitter/X</label>
                    <input type="url" class="form-control" name="settings[site_twitter]" 
                        value="<?php echo htmlspecialchars($settings['site_twitter']['setting_value'] ?? ''); ?>">
                </div>
            </div>
            
            <button type="submit" name="update_settings" class="btn btn-primary">Update Settings</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

