<?php
// File untuk fungsi cart (keranjang belanja)

// Fungsi untuk menambahkan item ke cart
function addToCart($userId, $menuItemId, $quantity = 1) {
    try {
        $conn = getDBConnection();
        
        // Cek apakah item sudah ada di cart
        $stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND menu_item_id = ?");
        $stmt->bind_param("ii", $userId, $menuItemId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update quantity jika sudah ada
            $cartItem = $result->fetch_assoc();
            $newQuantity = $cartItem['quantity'] + $quantity;
            $stmt->close();
            
            $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
            $stmt->bind_param("ii", $newQuantity, $cartItem['id']);
            $stmt->execute();
            $stmt->close();
        } else {
            // Insert item baru
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO cart_items (user_id, menu_item_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $userId, $menuItemId, $quantity);
            $stmt->execute();
            $stmt->close();
        }
        
        // Don't close connection - let pool handle it
        
        // Clear cart count cache
        if (function_exists('clearCache')) {
            clearCache('cart_count_' . $userId);
        }
        
        return ['success' => true, 'message' => 'Item ditambahkan ke keranjang!'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// Fungsi untuk mendapatkan cart items
function getCartItems($userId) {
    try {
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("
            SELECT ci.id, ci.menu_item_id, ci.quantity, mi.name, mi.price, mi.image
            FROM cart_items ci
            INNER JOIN menu_items mi ON ci.menu_item_id = mi.id
            WHERE ci.user_id = ?
            ORDER BY ci.created_at DESC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        $stmt->close();
        // Don't close connection - let pool handle it
        
        return $items;
    } catch (Exception $e) {
        return [];
    }
}

// Fungsi untuk update quantity cart item
function updateCartItemQuantity($cartItemId, $userId, $quantity) {
    try {
        $conn = getDBConnection();
        
        if ($quantity <= 0) {
            // Hapus item jika quantity 0 atau kurang
            return removeFromCart($cartItemId, $userId);
        }
        
        $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("iii", $quantity, $cartItemId, $userId);
        $stmt->execute();
        $success = $stmt->affected_rows > 0;
        $stmt->close();
        // Don't close connection - let pool handle it
        
        // Clear cart count cache on update
        if ($success && function_exists('clearCache')) {
            clearCache('cart_count_' . $userId);
        }
        
        return ['success' => $success, 'message' => $success ? 'Keranjang diperbarui!' : 'Item tidak ditemukan.'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// Fungsi untuk menghapus item dari cart
function removeFromCart($cartItemId, $userId) {
    try {
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cartItemId, $userId);
        $stmt->execute();
        $success = $stmt->affected_rows > 0;
        $stmt->close();
        // Don't close connection - let pool handle it
        
        // Clear cart count cache on remove
        if ($success && function_exists('clearCache')) {
            clearCache('cart_count_' . $userId);
        }
        
        return ['success' => $success, 'message' => $success ? 'Item dihapus dari keranjang!' : 'Item tidak ditemukan.'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// Fungsi untuk mendapatkan total cart
function getCartTotal($userId) {
    try {
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("
            SELECT SUM(ci.quantity * mi.price) as total
            FROM cart_items ci
            INNER JOIN menu_items mi ON ci.menu_item_id = mi.id
            WHERE ci.user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $stmt->close();
        // Don't close connection - let pool handle it
        
        return $row['total'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

// Fungsi untuk mendapatkan jumlah item di cart (with session caching)
function getCartCount($userId, $forceRefresh = false) {
    // Check cache first (cache for 30 seconds)
    $cacheKey = 'cart_count_' . $userId;
    if (!$forceRefresh && function_exists('getCachedData')) {
        $cached = getCachedData($cacheKey, 30);
        if ($cached !== null) {
            return $cached;
        }
    }
    
    try {
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("SELECT SUM(quantity) as count FROM cart_items WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $stmt->close();
        // Don't close connection - let pool handle it
        
        $count = intval($row['count'] ?? 0);
        
        // Cache the result
        if (function_exists('setCachedData')) {
            setCachedData($cacheKey, $count, 30);
        }
        
        return $count;
    } catch (Exception $e) {
        return 0;
    }
}

// Fungsi untuk mengosongkan cart
function clearCart($userId) {
    try {
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
        // Don't close connection - let pool handle it
        
        // Clear cart count cache
        if (function_exists('clearCache')) {
            clearCache('cart_count_' . $userId);
        }
        
        return ['success' => true, 'message' => 'Keranjang dikosongkan!'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}
?>

