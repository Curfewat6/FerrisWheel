<?php
include 'inc/init.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Shopping Cart</title>
    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    ?>
    <?php
    include 'inc/head.inc.php';
    ?>
</head>
<body>
<?php
    include "inc/nav.inc.php";
    include "inc/header.inc.php";
?>
<main class="container">
    <h3>Your Cart</h3>
    <?php
    $config = parse_ini_file('/var/www/private/db-config.ini');
    if (!$config) {
        $errorMsg = "Failed to read database config file.";
        $success = false;
    } else {
        $conn = new mysqli(
            $config['servername'],
            $config['username'],
            $config['password'],
            $config['dbname']
        );

        if ($conn->connect_error) {
            $errorMsg = "Connection failed: " . $conn->connect_error;
            $success = false;
        } else {
            $user_id_cart_name = $_SESSION['userid' ];
                $stmt = $conn->prepare("SELECT
                c.cart_id, 
                pt.product_name,
                ut.username AS seller_name,
                pt.product_image,
                pt.price,
                pc.cat_name AS cat_name
            FROM
                cart_table c
            JOIN
                product_table pt ON c.product_id = pt.product_id
            JOIN
                user_table ut ON pt.user_id = ut.user_id
            JOIN
                product_category pc ON pt.cat_id = pc.cat_id
            WHERE
                c.user_id = ?");
            $stmt->bind_param("i", $user_id_cart_name);

            if (!$stmt->execute()) {
                $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                $success = false;
            } else {
                $result = $stmt->get_result();
                ?>
                
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col">Product Name</th>
                                <th scope="col">Price</th>
                                <th scope="col">Category</th>
                                <th scope="col">Seller Name</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $subtotal = 0;
                            $item_count = 0;
                            while ($row = $result->fetch_assoc()) {
                                $subtotal += $row["price"]; // Add the price of each product to the subtotal
                                $item_count++;
                                echo '<tr>';
                                echo '<td><img src="/images/' . $row["product_image"] . '" style="width: 50px; height: 50px;"> ' . $row["product_name"] . '</td>'; // Display the product image beside the name
                                echo '<td>$' . $row["price"] . '</td>';
                                echo '<td>' . $row["cat_name"] . '</td>';
                                echo '<td>' . $row["seller_name"] . '</td>';
                                echo '<td>
                                    <form action="removeFromCart.php" method="POST">
                                    <input type="hidden" name="cart_id" value="' . htmlspecialchars($row["cart_id"]) . '">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>';
                            echo '</tr>';
                            }
                            ?>
                        </tbody>
                        </table>
                    </div>
                    <div style="text-align: right;">
                    <div style="display: inline-block;">
                            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                        </div>
                        <div style="display: inline-block; margin-left: 20px;">
                            <p>Subtotal (<span id="item-count"><?php echo $item_count; ?></span> items): $<span><?php echo $subtotal; ?></span></p>
                            <button id="proceed-to-checkout" class="btn btn-primary">Proceed to Checkout</button>
                        </div>
                    </div>
                <?php
                $stmt->close();
            }
            $conn->close();
        }
    }
    ?>
</main>
<?php
include "inc/footer.inc.php";
?>

<!-- Bootstrap Modal -->
<div class="modal fade" id="emptyCartModal" tabindex="-1" aria-labelledby="emptyCartModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="emptyCartModalLabel">Empty Cart</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Your cart is empty. Please add some items before checking out.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<script>
document.getElementById('proceed-to-checkout').addEventListener('click', function() {
    var itemCount = document.getElementById('item-count').innerText;
    if (itemCount === '0') {
        var myModal = new bootstrap.Modal(document.getElementById('emptyCartModal'), {});
        myModal.show();
    } else {
        window.location.href = 'checkout.php'; // Proceed to checkout page
    }
});
</script>
</body>
</html>