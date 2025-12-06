<?php
// File untuk fungsi points (poin member)

// Fungsi untuk mendapatkan total poin user (with session caching)
function getUserTotalPoints($userId, $forceRefresh = false) {
    // Check cache first (cache for 60 seconds)
    $cacheKey = 'user_points_' . $userId;
    if (!$forceRefresh && function_exists('getCachedData')) {
        $cached = getCachedData($cacheKey, 60);
        if ($cached !== null) {
            return $cached;
        }
    }
    
    try {
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("SELECT COALESCE(SUM(points), 0) as total FROM user_points WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $stmt->close();
        // Don't close connection - let pool handle it
        
        $points = intval($row['total'] ?? 0);
        
        // Cache the result
        if (function_exists('setCachedData')) {
            setCachedData($cacheKey, $points, 60);
        }
        
        return $points;
    } catch (Exception $e) {
        return 0;
    }
}

// Fungsi untuk menambahkan poin ke user
function addUserPoints($userId, $points, $orderId = null, $description = '') {
    try {
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("INSERT INTO user_points (user_id, points, earned_from_order_id, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $userId, $points, $orderId, $description);
        $stmt->execute();
        $stmt->close();
        // Don't close connection - let pool handle it
        
        // Clear user points cache
        if (function_exists('clearCache')) {
            clearCache('user_points_' . $userId);
        }
        
        return ['success' => true, 'message' => 'Poin berhasil ditambahkan!'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// Fungsi untuk menghitung poin dari total belanja (contoh: 1% dari total = poin)
function calculatePointsFromOrder($totalAmount) {
    // 1 poin per Rp 1.000
    return intval($totalAmount / 1000);
}

// Fungsi untuk mendapatkan semua rewards yang aktif
function getActiveRewards() {
    try {
        $conn = getDBConnection();
        
        $result = $conn->query("SELECT * FROM rewards WHERE status = 'active' ORDER BY points_required ASC");
        $rewards = [];
        while ($row = $result->fetch_assoc()) {
            $rewards[] = $row;
        }
        
        $conn->close();
        return $rewards;
    } catch (Exception $e) {
        return [];
    }
}

// Fungsi untuk mendapatkan reward by ID
function getRewardById($rewardId) {
    try {
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("SELECT * FROM rewards WHERE id = ? AND status = 'active'");
        $stmt->bind_param("i", $rewardId);
        $stmt->execute();
        $result = $stmt->get_result();
        $reward = $result->fetch_assoc();
        
        $stmt->close();
        $conn->close();
        
        return $reward;
    } catch (Exception $e) {
        return null;
    }
}

// Fungsi untuk tukar poin ke reward
function redeemReward($userId, $rewardId) {
    try {
        $conn = getDBConnection();
        
        // Get reward info
        $reward = getRewardById($rewardId);
        if (!$reward) {
            return ['success' => false, 'message' => 'Reward tidak ditemukan atau tidak aktif.'];
        }
        
        // Cek poin user
        $userPoints = getUserTotalPoints($userId);
        if ($userPoints < $reward['points_required']) {
            return ['success' => false, 'message' => 'Poin tidak cukup. Poin yang diperlukan: ' . $reward['points_required']];
        }
        
        // Cek stock jika ada
        if ($reward['stock'] !== null && $reward['stock'] <= 0) {
            return ['success' => false, 'message' => 'Reward sudah habis.'];
        }
        
        // Generate code unik
        $code = 'RW' . strtoupper(substr(uniqid(), -8)) . date('Ymd');
        
        // Set expiry (30 hari dari sekarang)
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        // Insert user reward
        $stmt = $conn->prepare("INSERT INTO user_rewards (user_id, reward_id, points_used, code, expires_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $userId, $rewardId, $reward['points_required'], $code, $expiresAt);
        $stmt->execute();
        
        // Kurangi poin user
        $negativePoints = -$reward['points_required'];
        $description = 'Tukar reward: ' . $reward['name'];
        addUserPoints($userId, $negativePoints, null, $description);
        
        // Kurangi stock jika ada
        if ($reward['stock'] !== null) {
            $stmt = $conn->prepare("UPDATE rewards SET stock = stock - 1 WHERE id = ?");
            $stmt->bind_param("i", $rewardId);
            $stmt->execute();
        }
        
        $stmt->close();
        $conn->close();
        
        return ['success' => true, 'message' => 'Reward berhasil ditukar!', 'code' => $code];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// Fungsi untuk mendapatkan user rewards (reward yang sudah ditukar)
function getUserRewards($userId, $status = null) {
    try {
        $conn = getDBConnection();
        
        if ($status) {
            $stmt = $conn->prepare("
                SELECT ur.*, r.name as reward_name, r.reward_type, r.discount_percent, 
                       r.discount_amount, r.free_item_name, r.cashback_amount
                FROM user_rewards ur
                INNER JOIN rewards r ON ur.reward_id = r.id
                WHERE ur.user_id = ? AND ur.status = ?
                ORDER BY ur.created_at DESC
            ");
            $stmt->bind_param("is", $userId, $status);
        } else {
            $stmt = $conn->prepare("
                SELECT ur.*, r.name as reward_name, r.reward_type, r.discount_percent, 
                       r.discount_amount, r.free_item_name, r.cashback_amount
                FROM user_rewards ur
                INNER JOIN rewards r ON ur.reward_id = r.id
                WHERE ur.user_id = ?
                ORDER BY ur.created_at DESC
            ");
            $stmt->bind_param("i", $userId);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $rewards = [];
        while ($row = $result->fetch_assoc()) {
            $rewards[] = $row;
        }
        
        $stmt->close();
        $conn->close();
        
        return $rewards;
    } catch (Exception $e) {
        return [];
    }
}

// Fungsi untuk mendapatkan points history
function getUserPointsHistory($userId, $limit = 20) {
    try {
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("
            SELECT * FROM user_points 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $history = [];
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }
        
        $stmt->close();
        $conn->close();
        
        return $history;
    } catch (Exception $e) {
        return [];
    }
}
?>

