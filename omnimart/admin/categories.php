<?php
include '../db.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cat_name'])) {
    $name = $_POST['cat_name'];
    $conn->prepare("INSERT INTO categories (name) VALUES (?)")->execute([$name]);
    echo "<script>window.location.href='categories.php';</script>";
}

if (isset($_GET['del'])) {
    $conn->prepare("DELETE FROM categories WHERE id=?")->execute([$_GET['del']]);
    echo "<script>window.location.href='categories.php';</script>";
}

$cats = $conn->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Categories</h3>
</div>

<div class="row g-4">
    <div class="col-12 col-md-4">
        <div class="card p-4">
            <h5 class="fw-bold mb-3">Add New</h5>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Category Name</label>
                    <input type="text" name="cat_name" class="form-control" required placeholder="e.g. Smart Watch">
                </div>
                <button class="btn btn-primary w-100" style="background: var(--admin-accent); border:none;">Save Category</button>
            </form>
        </div>
    </div>

    <div class="col-12 col-md-8">
        <div class="card p-0 overflow-hidden border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Name</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cats as $c): ?>
                        <tr>
                            <td class="ps-4">#<?php echo $c['id']; ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($c['name']); ?></td>
                            <td class="text-end pe-4">
                                <a href="?del=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?');"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>