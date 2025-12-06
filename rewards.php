<?php
require_once 'config.php';
$pageTitle = "Starling's Coffee - Rewards & Poin";
include 'includes/header.php';

requireLogin();

$message = '';
$messageType = '';

// Handle redeem reward
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['redeem_reward'])) {
    $rewardId = intval($_POST['reward_id'] ?? 0);
    
    if ($rewardId > 0) {
        $result = redeemReward(getCurrentUserId(), $rewardId);
        if ($result['success']) {
            $message = $result['message'] . (isset($result['code']) ? ' Kode: ' . $result['code'] : '');
            $messageType = 'success';
        } else {
            $message = $result['message'];
            $messageType = 'danger';
        }
    }
}

// Get user points and rewards
$userPoints = getUserTotalPoints(getCurrentUserId());
$availableRewards = getActiveRewards();
$userRewards = getUserRewards(getCurrentUserId());
$pointsHistory = getUserPointsHistory(getCurrentUserId(), 10);
?>

    <!-- Konten Halaman Rewards -->
    <main class="py-5">
        <div class="container">
            <!-- Points Summary -->
            <div class="content-page p-4 rounded shadow-sm mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="display-5 fw-bold mb-3">Rewards & Poin Saya</h1>
                        <p class="lead mb-0">Kumpulkan poin setiap pembelian dan tukarkan dengan reward menarik!</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="p-4 bg-warning rounded-3">
                            <h3 class="mb-0 text-dark">Poin Saya</h3>
                            <h1 class="display-3 fw-bold text-dark mb-0"><?php echo number_format($userPoints, 0, ',', '.'); ?></h1>
                            <small class="text-muted">1 poin = Rp 1.000 pembelian</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <!-- Available Rewards -->
            <div class="content-page p-4 rounded shadow-sm mb-4">
                <h2 class="h3 fw-bold mb-4 pb-2 border-bottom">Rewards Tersedia</h2>
                
                <?php if (empty($availableRewards)): ?>
                <p class="text-muted text-center py-4">Belum ada rewards yang tersedia saat ini.</p>
                <?php else: ?>
                <div class="row g-4">
                    <?php foreach($availableRewards as $reward): 
                        $canRedeem = $userPoints >= $reward['points_required'];
                        $stockInfo = '';
                        if ($reward['stock'] !== null) {
                            if ($reward['stock'] <= 0) {
                                $canRedeem = false;
                                $stockInfo = '<span class="badge bg-danger">Habis</span>';
                            } else {
                                $stockInfo = '<span class="badge bg-info">Stock: ' . $reward['stock'] . '</span>';
                            }
                        }
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm <?php echo !$canRedeem ? 'opacity-75' : ''; ?>">
                            <?php if ($reward['image'] && file_exists($reward['image'])): ?>
                            <img src="<?php echo htmlspecialchars($reward['image']); ?>" class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($reward['name']); ?>" 
                                 style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-gift fs-1 text-muted"></i>
                            </div>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold"><?php echo htmlspecialchars($reward['name']); ?></h5>
                                <p class="card-text flex-grow-1"><?php echo htmlspecialchars($reward['description']); ?></p>
                                
                                <div class="mb-2">
                                    <?php if ($reward['reward_type'] == 'discount'): ?>
                                        <span class="badge bg-success">
                                            Diskon <?php echo number_format((float)($reward['discount_percent'] ?? 0), 0, ',', '.'); ?>%
                                        </span>
                                    <?php elseif ($reward['reward_type'] == 'free_item'): ?>
                                        <span class="badge bg-primary">
                                            <?php echo htmlspecialchars($reward['free_item_name'] ?? 'Free Item'); ?>
                                        </span>
                                    <?php elseif ($reward['reward_type'] == 'cashback'): ?>
                                        <span class="badge bg-info">
                                            Cashback Rp <?php echo number_format((float)($reward['cashback_amount'] ?? 0), 0, ',', '.'); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php echo $stockInfo; ?>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-bold text-warning">
                                        <i class="bi bi-star-fill"></i> <?php echo number_format($reward['points_required'], 0, ',', '.'); ?> poin
                                    </span>
                                </div>
                                
                                <form method="POST" action="rewards.php">
                                    <input type="hidden" name="reward_id" value="<?php echo $reward['id']; ?>">
                                    <button type="submit" name="redeem_reward" 
                                            class="btn btn-success w-100 <?php echo !$canRedeem ? 'disabled' : ''; ?>"
                                            <?php echo !$canRedeem ? 'disabled' : ''; ?>
                                            onclick="return confirm('Tukar <?php echo htmlspecialchars($reward['name']); ?> dengan <?php echo $reward['points_required']; ?> poin?');">
                                        <?php echo $canRedeem ? 'Tukar Sekarang' : 'Poin Tidak Cukup'; ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- My Rewards -->
            <div class="content-page p-4 rounded shadow-sm mb-4">
                <h2 class="h3 fw-bold mb-4 pb-2 border-bottom">Rewards Saya</h2>
                
                <?php if (empty($userRewards)): ?>
                <p class="text-muted text-center py-4">Anda belum memiliki reward. Tukar poin Anda untuk mendapatkan reward!</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Reward</th>
                                <th>Kode</th>
                                <th>Poin Digunakan</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Expires</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($userRewards as $reward): 
                                $isExpired = $reward['expires_at'] && strtotime($reward['expires_at']) < time();
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($reward['reward_name']); ?></strong><br>
                                    <?php if ($reward['reward_type'] == 'discount'): ?>
                                        <small class="text-muted">Diskon <?php echo $reward['discount_percent']; ?>%</small>
                                    <?php elseif ($reward['reward_type'] == 'free_item'): ?>
                                        <small class="text-muted"><?php echo htmlspecialchars($reward['free_item_name'] ?? 'Free Item'); ?></small>
                                    <?php elseif ($reward['reward_type'] == 'cashback'): ?>
                                        <small class="text-muted">Cashback Rp <?php echo number_format((float)($reward['cashback_amount'] ?? 0), 0, ',', '.'); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><code><?php echo htmlspecialchars($reward['code'] ?? 'N/A'); ?></code></td>
                                <td><?php echo number_format((int)($reward['points_used'] ?? 0), 0, ',', '.'); ?> poin</td>
                                <td>
                                    <?php 
                                    $statusBadge = 'secondary';
                                    $statusText = ucfirst($reward['status']);
                                    if ($reward['status'] == 'used') {
                                        $statusBadge = 'success';
                                    } elseif ($reward['status'] == 'expired' || $isExpired) {
                                        $statusBadge = 'danger';
                                        $statusText = 'Expired';
                                    } elseif ($reward['status'] == 'pending') {
                                        $statusBadge = 'warning';
                                    }
                                    ?>
                                    <span class="badge bg-<?php echo $statusBadge; ?>"><?php echo $statusText; ?></span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($reward['created_at'])); ?></td>
                                <td>
                                    <?php if ($reward['expires_at']): 
                                        $expireDate = strtotime($reward['expires_at']);
                                        $daysLeft = floor(($expireDate - time()) / (60 * 60 * 24));
                                    ?>
                                        <?php echo date('d M Y', $expireDate); ?>
                                        <?php if ($daysLeft > 0 && $daysLeft <= 7): ?>
                                            <br><small class="text-danger">(<?php echo $daysLeft; ?> hari lagi)</small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Points History -->
            <div class="content-page p-4 rounded shadow-sm">
                <h2 class="h3 fw-bold mb-4 pb-2 border-bottom">Riwayat Poin</h2>
                
                <?php if (empty($pointsHistory)): ?>
                <p class="text-muted text-center py-4">Belum ada riwayat poin.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Poin</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($pointsHistory as $history): ?>
                            <tr>
                                <td><?php echo date('d M Y H:i', strtotime($history['created_at'])); ?></td>
                                <td>
                                    <span class="fw-bold <?php echo ($history['points'] ?? 0) >= 0 ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo ($history['points'] ?? 0) >= 0 ? '+' : ''; ?><?php echo number_format((int)($history['points'] ?? 0), 0, ',', '.'); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($history['description'] ?? 'N/A'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>

