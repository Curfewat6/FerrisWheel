<!DOCTYPE html>
<html lang="en">
<head>
<title>Ferris wheel</title>
    <?php
        include 'inc/head.inc.php';
        include 'inc/init.inc.php'
    ?>
</head>

<body>
<?php
include "inc/nav.inc.php";
include 'inc/header.inc.php'; 

?>
<main class="container">
    <div id="list-product">
    <h1>New Listing</h1>
    <p>
        Upload a new listing here!
    </p>
        <form action="process_listing.php" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 col-sm-12 listing-field">
                    <label for="pname" class="form-label">Product Name</label>
                    <input maxlength="255" type="text" class="form-control" id="pname" name="pname"
                    placeholder="Enter your product name">
                </div>
                    <div class="col-md-6 col-sm-12 listing-field">
                    <label for="price" class="form-label">Price (S$)</label>
                    <input type="price" class="form-control" id="price" name="price"
                    placeholder="How much does this sell for?">
                </div>
                <div class="col-md-6 col-sm-12 listing-field">
                    <label for="pimage" class="form-label">Upload a picture here</label>
                    <input type="file" class="form-control" id="pimage" name="pimage">
                </div>
                <div class="col-md-6 col-sm-12 listing-field">
                    <label for="cat" class="form-label">Category</label>
                    <select class="form-control" id="cat" name="cat">
                    <?php
                    include "db_con.php";
                    $stmt = $conn->prepare("SELECT * from product_category");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        // output data of each row
                        while($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row["cat_id"] . "'>" . $row["cat_name"] . "</option>";
                        }
                    } else {
                        echo "0 results";
                    }
                    $conn->close();
                    ?>
                    </select>
                </div>
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary" id="listing-button">List it!</button>
                </div>
            </div>
        </form>
    </div>
</main>
<?php
include "inc/footer.inc.php";
?>
</body>
