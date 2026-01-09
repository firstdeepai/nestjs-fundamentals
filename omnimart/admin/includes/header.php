<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../login.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - OmniMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --admin-dark: #0f172a; --admin-accent: #f97316; }
        body { background-color: #f1f5f9; font-family: 'Outfit', sans-serif; display: flex; overflow-x: hidden; }
        
        .sidebar { 
            width: 260px; 
            background: var(--admin-dark); 
            color: white; 
            height: 100vh; 
            position: fixed; 
            transition: all 0.3s ease; 
            z-index: 1000; 
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #334155 transparent;
        }
        
        .sidebar::-webkit-scrollbar { width: 5px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background-color: #334155; border-radius: 20px; }

        .sidebar .brand { padding: 25px; font-size: 1.5rem; font-weight: 700; border-bottom: 1px solid #1e293b; position: sticky; top: 0; background: var(--admin-dark); z-index: 10; }
        .sidebar a { color: #94a3b8; text-decoration: none; padding: 15px 25px; display: block; font-weight: 500; border-left: 4px solid transparent; transition: all 0.2s; }
        
        .sidebar a:hover, .sidebar a.active { background: #1e293b; color: #fff; border-left-color: var(--admin-accent); }
        .sidebar i { width: 25px; text-align: center; margin-right: 10px; }
        
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 30px; transition: all 0.3s ease; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .table thead th { font-weight: 600; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }

        .mobile-header { display: none; }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; }

        @media (max-width: 991px) {
            .sidebar { left: -260px; }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0; width: 100%; padding: 20px; margin-top: 60px; }
            
            .mobile-header { 
                display: flex; align-items: center; justify-content: space-between;
                position: fixed; top: 0; left: 0; right: 0; 
                height: 60px; background: var(--admin-dark); color: white; padding: 0 20px; 
                z-index: 998; 
            }
            .sidebar-overlay.active { display: block; }
        }
    </style>
</head>
<body>

<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<div class="mobile-header">
    <div class="fw-bold fs-5">Omni<span style="color: var(--admin-accent);">Mart</span></div>
    <button class="btn border-0 p-0 text-white" onclick="toggleSidebar()">
        <i class="fa-solid fa-bars fs-4"></i>
    </button>
</div>

<div class="sidebar" id="sidebar">
    <div class="brand d-flex justify-content-between align-items-center">
        <span>Omni<span style="color: var(--admin-accent);">Mart</span></span>
        <i class="fa-solid fa-xmark d-lg-none cursor-pointer" onclick="toggleSidebar()" style="cursor: pointer;"></i>
    </div>
    <div class="mt-4 pb-5">
        <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':''; ?>">
            <i class="fa-solid fa-gauge"></i> Dashboard
        </a>
        <a href="categories.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='categories.php'?'active':''; ?>">
            <i class="fa-solid fa-layer-group"></i> Categories
        </a>
        <a href="products.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='products.php'?'active':''; ?>">
            <i class="fa-solid fa-box"></i> Products
        </a>
        <a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='orders.php'?'active':''; ?>">
            <i class="fa-solid fa-cart-shopping"></i> Orders
        </a>
        <a href="vendors.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='vendors.php'?'active':''; ?>">
            <i class="fa-solid fa-store"></i> Vendors
        </a>
        <a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='users.php'?'active':''; ?>">
            <i class="fa-solid fa-users"></i> Users
        </a>
        <a href="coupons.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='coupons.php'?'active':''; ?>">
            <i class="fa-solid fa-gift"></i> Coupons
        </a>
        
        <a href="reports.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='reports.php'?'active':''; ?>">
            <i class="fa-solid fa-chart-pie"></i> Reports
        </a>

        <div class="mt-5 border-top border-secondary pt-3">
            <a href="../index.php" target="_blank"><i class="fa-solid fa-eye"></i> View Site</a>
            <a href="../logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
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