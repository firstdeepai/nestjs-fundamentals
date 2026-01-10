<?php
include '../db.php';
include 'includes/header.php';

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (isset($_POST['add_user'])) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = $_POST['role'];

            $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check->execute([$email]);
            if($check->rowCount() > 0) {
                $error = "Email already exists!";
            } else {
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                if($stmt->execute([$name, $email, $password, $role])) {
                    $uid = $conn->lastInsertId();
                    if($role == 'vendor') {
                        $conn->prepare("INSERT INTO vendors (user_id, name) VALUES (?, ?)")->execute([$uid, $name]);
                    }
                    echo "<script>alert('User Added Successfully!'); window.location.href='users.php';</script>";
                }
            }
        }

        if (isset($_POST['update_user'])) {
            $id = $_POST['user_id'];
            $name = $_POST['name'];
            $email = $_POST['email'];
            $role = $_POST['role'];
            
            $sql = "UPDATE users SET name=?, email=?, role=? WHERE id=?";
            $conn->prepare($sql)->execute([$name, $email, $role, $id]);
            
            if (!empty($_POST['password'])) {
                $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $conn->prepare("UPDATE users SET password=? WHERE id=?")->execute([$pass, $id]);
            }
            echo "<script>alert('User Updated!'); window.location.href='users.php';</script>";
        }
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

if (isset($_GET['del'])) {
    $id = $_GET['del'];
    if ($id != $_SESSION['user_id']) {
        $conn->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
        echo "<script>window.location.href='users.php';</script>";
    } else {
        echo "<script>alert('You cannot delete yourself!'); window.location.href='users.php';</script>";
    }
}

$users = $conn->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h3 class="fw-bold mb-0">User Management</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal" style="background: var(--admin-accent); border:none;">
        <i class="fa-solid fa-plus me-2"></i> Add New User
    </button>
</div>

<?php if($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="card p-0 shadow-sm border-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">ID</th>
                    <th>User Details</th>
                    <th>Role</th>
                    <th>Joined At</th>
                    <th class="text-end pe-4">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr>
                    <td class="ps-4 text-muted">#<?php echo $u['id']; ?></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 40px; height: 40px; font-weight:bold; color: #f97316;">
                                <?php echo strtoupper(substr($u['name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($u['name']); ?></h6>
                                <small class="text-muted"><?php echo htmlspecialchars($u['email']); ?></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if($u['role'] == 'admin'): ?>
                            <span class="badge bg-danger">Admin</span>
                        <?php elseif($u['role'] == 'vendor'): ?>
                            <span class="badge bg-success">Vendor</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Customer</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small"><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-outline-primary me-2 edit-btn" 
                            data-id="<?php echo $u['id']; ?>"
                            data-name="<?php echo htmlspecialchars($u['name']); ?>"
                            data-email="<?php echo htmlspecialchars($u['email']); ?>"
                            data-role="<?php echo $u['role']; ?>"
                            data-bs-toggle="modal" data-bs-target="#editUserModal">
                            <i class="fa-solid fa-pen"></i> Edit
                        </button>

                        <?php if($u['role'] != 'admin'): ?>
                            <a href="?del=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this user?');">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="customer">Customer</option>
                            <option value="vendor">Vendor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_user" class="btn btn-primary" style="background: var(--admin-accent); border:none;">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="user_id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" id="edit_role" class="form-select">
                            <option value="customer">Customer</option>
                            <option value="vendor">Vendor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password (Optional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave empty to keep current">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_user" class="btn btn-primary" style="background: var(--admin-accent); border:none;">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editBtns = document.querySelectorAll('.edit-btn');
        editBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const email = this.getAttribute('data-email');
                const role = this.getAttribute('data-role');

                document.getElementById('edit_id').value = id;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_email').value = email;
                document.getElementById('edit_role').value = role;
            });
        });
    });
</script>

</body>
</html>