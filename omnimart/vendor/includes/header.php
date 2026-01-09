<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'vendor') {
    header("Location: ../login.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Portal - OmniMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --vendor-dark: #1e293b; --vendor-accent: #10b981; }
        body { background-color: #f8fafc; font-family: 'Outfit', sans-serif; display: flex; overflow-x: hidden; }
        
        .sidebar { width: 260px; background: white; border-right: 1px solid #e2e8f0; min-height: 100vh; position: fixed; z-index: 1000; transition: all 0.3s ease; }
        .sidebar .brand { padding: 25px; font-size: 1.4rem; font-weight: 700; color: var(--vendor-dark); border-bottom: 1px solid #f1f5f9; }
        .sidebar a { color: #64748b; text-decoration: none; padding: 15px 25px; display: block; font-weight: 500; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: #f0fdf4; color: var(--vendor-accent); border-right: 3px solid var(--vendor-accent); }
        .sidebar i { width: 25px; }
        
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 30px; transition: all 0.3s ease; }

        .mobile-header { display: none; }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; }

        @media (max-width: 991px) {
            .sidebar { left: -260px; }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0; width: 100%; padding: 20px; margin-top: 60px; }
            
            .mobile-header { 
                display: flex; align-items: center; justify-content: space-between;
                position: fixed; top: 0; left: 0; right: 0; 
                height: 60px; background: white; padding: 0 20px; 
                z-index: 998; border-bottom: 1px solid #e2e8f0; 
            }
            .sidebar-overlay.active { display: block; }
        }
    </style>
</head>
<body>

<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<div class="mobile-header">
    <div class="fw-bold fs-5 text-dark"><i class="fa-solid fa-store me-2 text-success"></i>Vendor Panel</div>
    <button class="btn border-0 p-0" onclick="toggleSidebar()">
        <i class="fa-solid fa-bars fs-4 text-dark"></i>
    </button>
</div>

<div class="sidebar" id="sidebar">
    <div class="brand d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-store me-2 text-success"></i>Vendor Panel</span>
        <i class="fa-solid fa-xmark d-lg-none cursor-pointer" onclick="toggleSidebar()" style="cursor: pointer;"></i>
    </div>
    <div class="mt-4">
        <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':''; ?>">
            <i class="fa-solid fa-chart-line"></i> Overview
        </a>
        <a href="products.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='products.php'?'active':''; ?>">
            <i class="fa-solid fa-box-open"></i> My Products
        </a>
        <a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='orders.php'?'active':''; ?>">
            <i class="fa-solid fa-clipboard-list"></i> My Orders
        </a>
        
        <div class="mt-5 border-top pt-3">
            <a href="../index.php" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Go to Shop</a>
            <a href="../logout.php" class="text-danger"><i class="fa-solid fa-power-off"></i> Logout</a>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
        document.querySelector('.sidebar-overlay').classList.toggle('active');
    }
</script>

<div class="main-content">