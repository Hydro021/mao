<?php
    include '../components/connection.php';

    session_start();

    $admin_id = $_SESSION['admin_id'];

    if (!isset($admin_id)) {
        header('location: login.php');
    }
    $get_id = $_GET['post_id'];    

    if (isset($_POST['delete'])) {
        $p_id = $_POST['product_id'];
        $p_id = filter_var($p_id, FILTER_SANITIZE_STRING);
    
        // Get the image file name from the database
        $get_image_query = $conn->prepare("SELECT image FROM products WHERE id = ?");
        $get_image_query->execute([$p_id]);
        $image = $get_image_query->fetchColumn();
    
        // Check if there is an image associated with the product
        if ($image) {
            // Define the path to the image file
            $image_path = "../image/" . $image;
    
            // Check if the file exists
            if (file_exists($image_path)) {
                // Delete the image file
                unlink($image_path);
            } else {
                // If the file does not exist, display an error message
                $error_msg[] = 'Image Not Found!';
            }
        }
    
        // Delete the product record from the database
        $delete_product = $conn->prepare("DELETE FROM `products` WHERE id= ?");
        $delete_product->execute([$p_id]);
       header('location:view_product.php');
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hydrogen- Read Product Admin Page</title>
    <link rel="stylesheet" type="text/css" href="admin_style.css?v=<?php echo time(); ?>">
    <!-- boxicon cdn link -->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include '../components/admin_header.php'; ?>
    <div class="main">
        <div class="banner">
            <h1>Read Products</h1>
        </div>
        <div class="title2">
            <a href="dashboard.php">Dashboard </a><span> / Read Products</span>
        </div>
        <section class="read-post">
           <h1 class="heading">Read Product</h1>
          <?php
            $select_product = $conn->prepare("SELECT * FROM `products` WHERE id= ?");
            $select_product->execute([$get_id]);
            
            if ($select_product->rowCount() > 0) {
               while($fetch_product = $select_product->fetch(PDO::FETCH_ASSOC)){
          ?>
          <form action="" method="post">
            <input type="hidden" name="product_id" value="<?= $fetch_product['id']; ?>">

            <div class="status" style="color: <?php if($fetch_product['status']== 'active'){echo "green";}else{echo "red";}?>"><?= $fetch_product['status']; ?></div>
            <?php if($fetch_product['image'] != '') { ?>
                <img src="../image/<?= $fetch_product['image']; ?>" class="image">
            <?php } ?>
            <div class="price">₱<?= $fetch_product['price']; ?>/-</div>
            <div class="title"><?= $fetch_product['name']; ?></div>
            <div class="content"><?= $fetch_product['product_detail']; ?></div>
            <div class="flex-btn">
                <a href="edit_product.php?id=<?= $fetch_product['id']; ?>" class="btn">Edit</a>
                <button type="submit" name="delete" class="btn" onclick="return confirm('Delete This Product?');">Delete</button>
                <a href="view_product.php?id=<?= $get_id; ?>" class="btn">Go Back</a>
            </div>
          </form>
          <?php
               }
                 }else{
                    echo '<div class="empty"> 
                    <p>No Product Added Yet <br> <a href="add_product.php" style="margin-top:1.5rem;" class="btn2">Add Products</a></p>
                </div>';
                 }                
                ?>
        </section>
    </div>
 <!-- sweetalert cdn -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js link -->
<script type="text/javascript" src="script.js"></script>

<!-- alert -->
<?php include '../components/alert.php'; ?>
</body>
</html>