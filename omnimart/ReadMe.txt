Project: OmniMart (Assignment-6)

<--Main site link -->  
http://omnimart.42web.io/
http://omnimart.42web.io/
http://omnimart.42web.io/

1. PROJECT OVERVIEW
A ull dynamic e-commerce website where users can buy products, and vendors can sell them. 
I have tried to make it look like a real professional website (like Amazon/Flipkart).

Key Highlights:
- The Cart and Wishlist work without reloading the page (I used AJAX for this).
- Admin Panel to manage everything (Users, Products, Reports).
- Vendor Panel for sellers to manage their own products.
- Mobile responsive design using Bootstrap and custom CSS.

==================================================

This document contains login credentials and page routes for testing Admin, Vendor, and Customer panels.

--------------------------------------------------
1. ADMIN ACCESS (Full Control)
--------------------------------------------------
* Role: Manage Users, Vendors, Categories, and Reports.
* Login URL: http://localhost/omnimart/login.php
* Dashboard Route: /admin/dashboard.php

> Credentials:
  Email: admin@gmail.com
  Password: password123
* Key Routes:
  - /admin/users.php (User Management - CRUD)
  - /admin/vendors.php (Approve/Suspend Vendors)
  - /admin/products.php (Manage All Products)

--------------------------------------------------
2. VENDOR ACCESS (Seller Panel)
--------------------------------------------------
* Role: Add Products, View Orders, Check Earnings.
* Login URL: http://localhost/omnimart/login.php
* Dashboard Route: /vendor/dashboard.php

> Credentials (Demo Account):
  Email: deepak@gmail.com
  Password: password123

* Key Routes:
  - /vendor/products.php (Add/Edit own products)
  - /vendor/orders.php (View orders for own products)

--------------------------------------------------
3. CUSTOMER ACCESS (Shopping)
--------------------------------------------------
* Role: Browse, Add to Cart, Wishlist, Checkout.
* Login URL: http://localhost/omnimart/login.php
* Homepage Route: /index.php

> Credentials (Demo Account):
  Email: easytechdeepak@gmail.com
  Password: password123

* Key Routes:
  - /cart.php (Shopping Cart)
  - /wishlist.php (Saved Items)
  - /my_orders.php (Order History)

--------------------------------------------------
4. AUTHENTICATION LOGIC (CODE STRUCTURE)
--------------------------------------------------
If you want to check the code for authentication:

1. login.php: 
   - Handles login logic.
   - Checks user role and redirects to specific dashboards.
   - Checks if Vendor status is 'Suspended'.

2. signup.php:
   - Registers new Users and Vendors.
   - Vendors are created with 'Active' status by default (or Pending if configured).

3. Security (Middleware):
   - All Admin pages have a session check at the top:
     if ($_SESSION['user_role'] != 'admin') { header("Location: ../login.php"); }
   
   - All Vendor pages have a session check at the top:
     if ($_SESSION['user_role'] != 'vendor') { header("Location: ../login.php"); }

==================================================

I have focused on making the UI clean and the code secure (used prepared statements). 

Thank You.
