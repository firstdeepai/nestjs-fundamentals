<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$subtotal = 0;

foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$discount = 0;
if (isset($_SESSION['coupon'])) {
    if ($_SESSION['coupon']['type'] == 'percent') {
        $discount = $subtotal * ($_SESSION['coupon']['value'] / 100);
    } else {
        $discount = $_SESSION['coupon']['value'];
    }
}

$final_total = max(0, $subtotal - $discount);
$status = 'Pending';
$payment_method = $_POST['paymentMethod'] ?? 'COD';

if ($payment_method == 'ONLINE' && !empty($_POST['razorpay_payment_id'])) {
    $status = 'Processing';
}

try {
    $conn->beginTransaction();

    foreach ($_SESSION['cart'] as $pid => $item) {
        $check = $conn->prepare("SELECT stock, name FROM products WHERE id = ? FOR UPDATE");
        $check->execute([$pid]);
        $product = $check->fetch();

        if (!$product || $product['stock'] < $item['quantity']) {
            throw new Exception("Stock unavailable for " . $product['name']);
        }
    }

    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $final_total, $status]);
    $order_id = $conn->lastInsertId();

    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

    foreach ($_SESSION['cart'] as $pid => $item) {
        $stmt_item->execute([$order_id, $pid, $item['quantity'], $item['price']]);
        $stmt_stock->execute([$item['quantity'], $pid]);
    }

    $conn->commit();
    unset($_SESSION['cart']);
    unset($_SESSION['coupon']);
    
    header("Location: orders.php?placed=1");
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    $_SESSION['error'] = $e->getMessage();
    header("Location: cart.php");
    exit;
}
?>