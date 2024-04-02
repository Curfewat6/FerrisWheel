<?php
session_start();
if ((!($_SESSION['role'] == 'a')) || ($_SESSION['loggedin'] != true)) {
    if (!($_SESSION['role'] == 'admin')) {
        header('Location: ../error.php');
    }
} else {
    include 'db_con.php';

    $sql = "SELECT * FROM user_table";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $user_table_results = $stmt->get_result();

    if ($user_table_results) {
        $user_table = $user_table_results->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "Error fetching user table";
    }

    $stmt->close();

    $query = "SELECT 
            pt.product_id,
            pt.product_name,
            pt.product_image,
            pt.price,
            ut.username,
            pc.cat_name
          FROM 
            product_table pt
          JOIN 
            user_table ut ON pt.user_id = ut.user_id
          JOIN 
            product_category pc ON pt.cat_id = pc.cat_id";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $product_table_results = $stmt->get_result();


    if ($product_table_results) {
        $product_table = $product_table_results->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "Error fetching product table";
    }

    $stmt->close();

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'inc/head.inc.php'; ?>
    <title>Admin Console</title>
</head>

<body>
    <?php include 'inc/nav.inc.php'; ?>
    <main class="container my-4">
        <h1 class="mb-4 text-center">Admin Console</h1>
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <h2>Users</h2>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr class="text-nowrap">
                                <th scope="col" class="col-1">User ID</th>
                                <th scope="col" class="col-2">Username</th>
                                <th scope="col" class="col-3">Email Address</th>
                                <th scope="col" class="col-1">Role</th>
                                <th scope="col" class="col-2">Created</th>
                                <th scope="col" class="col-1">Funds</th>
                                <th scope="col" class="col-1">Status</th>
                                <th scope="col" class="col-1">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($user_table as $user) : ?>
                                <tr class="text-nowrap" style="text-align: center;">
                                    <td class="col-1"><?= htmlspecialchars($user['user_id']) ?></td>
                                    <td class="col-2"><?= htmlspecialchars($user['username']) ?></td>
                                    <td class="col-3"><?= htmlspecialchars($user['email']) ?></td>
                                    <td class="col-1"><?= $user['user_role'] == 'a' ? 'Admin' : 'User' ?></td>
                                    <td class="col-2"><?= htmlspecialchars($user['created_at']) ?></td>
                                    <td class="col-1"><?= htmlspecialchars($user['funds']) ?></td>
                                    <td class="col-1"><?= $user['status'] == 1 ? 'Verified' : 'Not Verified' ?></td>
                                    <td class="col-1">
                                        <a href="edit_userconsole.php?user_id=<?= $user['user_id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                        <a href="delete_userconsole.php?user_id=<?= $user['user_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <h2>Products</h2>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr class="text-nowrap">
                                <th scope="col" class="col-1">Product ID</th>
                                <th scope="col" class="col-2">Product Name</th>
                                <th scope="col" class="col-3">Product Image</th>
                                <th scope="col" class="col-1">Price</th>
                                <th scope="col" class="col-2">Category</th>
                                <th scope="col" class="col-1">User</th>
                                <th scope="col" class="col-1">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($product_table as $product) : ?>
                                <tr class="text-nowrap" style="text-align: center;">
                                    <td class="col-1"><?= htmlspecialchars($product['product_id']) ?></td>
                                    <td class="col-2"><?= htmlspecialchars($product['product_name']) ?></td>
                                    <td class="col-3"><img src="/images/<?= htmlspecialchars($product['product_image']) ?>" class="img-fluid rounded" style="width: 50px; height: 50px;"></td>
                                    <td class="col-1"><?= htmlspecialchars($product['price']) ?></td>
                                    <td class="col-2"><?= htmlspecialchars($product['cat_name']) ?></td>
                                    <td class="col-1"><?= htmlspecialchars($product['username']) ?></td>
                                    <td class="col-1">
                                        <a href="edit_productconsole.php?product_id=<?= $product['product_id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                        <a href="delete_productconsole.php?product_id=<?= $product['product_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <?php include "inc/footer.inc.php"; ?>
</body>

</html>