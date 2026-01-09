<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OmniMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
            padding: 15px 0;
        }
        .nav-link {
            font-weight: 500;
            color: #0f172a !important;
            margin: 0 10px;
        }
        .nav-link:hover {
            color: #f97316 !important;
        }
        .search-form {
            position: relative;
            width: 300px;
        }
        .search-input {
            border-radius: 50px;
            padding: 8px 20px;
            border: 1px solid #e2e8f0;
            background: #f1f5f9;
            width: 100%;
            outline: none;
            padding-right: 50px;
        }
        .search-input:focus {
            background: #fff;
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }
        .search-btn {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: #64748b;
        }
        .badge-cart {
            background: #f97316;
            font-size: 0.7rem;
        }
        footer {
            margin-top: auto;
        }
        img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top navbar-custom navbar-custom">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="index.php">Omni<span style="color: #f97316;">Mart</span>.</a>
        
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navContent">
            <form action="search.php" method="GET" class="search-form mx-auto my-2 my-lg-0">
                <input type="text" name="q" class="search-input" placeholder="Search products..." required>
                <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>

            <ul class="navbar-nav align-items-center">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link p-1 border border-red rounded-circle" href="wishlist.php"><i class="fa-solid fa-heart text-danger px-1"></i></a></li>
                <li class="nav-item">
                    <a href="cart.php" class="nav-link position-relative">
                        <i class="fa-solid fa-bag-shopping fs-5"></i>
                        <?php if($cart_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill badge-cart">
                                <?php echo $cart_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown ms-3">
                        <a class="nav-link dropdown-toggle fw-bold" href="#" data-bs-toggle="dropdown">
                            <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2">
                            <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                            <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                            
                            <?php if($_SESSION['user_role'] == 'admin'): ?>
                                <li><a class="dropdown-item" href="admin/dashboard.php">Admin Panel</a></li>
                            <?php elseif($_SESSION['user_role'] == 'vendor'): ?>
                                <li><a class="dropdown-item" href="vendor/dashboard.php">Vendor Panel</a></li>
                            <?php endif; ?>

                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-3">
                        <a href="login.php" class="btn btn-dark rounded-pill px-4" style="background: #0f172a;">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div style="height: 80px;"></div>