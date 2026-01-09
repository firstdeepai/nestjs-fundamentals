<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include '../db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    if (isset($_GET['id'])) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $stmt = $conn->query("SELECT * FROM products ORDER BY id DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    if ($data) {
        echo json_encode(["status" => "success", "data" => $data]);
    } else {
        echo json_encode(["status" => "error", "message" => "No products found"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid Request Method"]);
}
?>