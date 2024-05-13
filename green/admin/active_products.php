<?php
    include '../components/connection.php';

    session_start();

    $admin_id = $_SESSION['admin_id'];

    if (!isset($admin_id)) {
        header('location: login.php');
    }
   
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
                $error_msg[] = 'Image not found!';
            }
        }
    
        // Delete the product record from the database only if it's active
        $delete_product = $conn->prepare("DELETE FROM `products` WHERE id= ? AND status = 'active'");
        $delete_product->execute([$p_id]);
    
        $success_msg[] = 'Product Deleted';
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hydrogen- Active Product Admin Page</title>
    <link rel="stylesheet" type="text/css" href="admin_style.css?v=<?php echo time(); ?>">
    <!-- boxicon cdn link -->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include '../components/admin_header.php'; ?>
    <div class="main">
        <div class="banner">
            <h1>Active Products</h1>
        </div>
        <div class="title2">
            <a href="dashboard.php">Dashboard </a><span> / Active Products</span>
        </div>
        <section class="show-post">
           <h1 class="heading">Active Products</h1>
           <div class="box-container">
           <?php
                    $select_products = $conn->prepare("SELECT * FROM `products` WHERE status = 'active'");
                    $select_products->execute();

                    if ($select_products->rowCount() > 0){
                        while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC))
                            {
                ?>
                   <form action="" method="post" class="box">
                   <input type="hidden" name="product_id" value="<?= $fetch_products['id'];
                    ?>">
                    <?php if($fetch_products['image'] != ''){?>
                        <div class="image-container">
                          <img src="../image/<?= $fetch_products['image']; ?>" class="image">
                        </div>
                    <?php } ?>
                    <div class="status" style="color: <?php if ($fetch_products['status']=='active'){echo "green";}?>"><?= $fetch_products['status'] ?></div>
                    <div class="price">₱<?= $fetch_products['price']; ?>/-</div>
                    <div class="title"><?= $fetch_products['name']; ?></div>
                    <div class="flex-btn">
                        <a href="edit_product.php?id=<?= $fetch_products['id']; ?>" class="btn">Edit</a>
                        <button type="submit" name="delete" class="btn" onclick="return confirm('Delete This Product?')">Delete</button>
                        <a href="read_product.php?post_id=<?= $fetch_products['id']; ?>" class="btn">View</a>
                    </div>
                </form>
                <?php
               }
                 }else{
                    echo '<div class="empty"> 
                    <p>No Active Product Found</p>
                </div>';
                 }                
                ?>
           </div>
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
