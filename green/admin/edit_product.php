<?php
    include '../components/connection.php';

    session_start();

    $admin_id = $_SESSION['admin_id'];

    if (!isset($admin_id)) {
        header('location: login.php');
    }  
    if (isset($_POST['update'])) {
        $post_id = $_GET['id'];

        $name = $_POST['name'];
        $name = filter_var($name, FILTER_SANITIZE_STRING);
  
        $price = $_POST['price'];
        $price = filter_var($price, FILTER_SANITIZE_STRING);
  
        $content = $_POST['content'];
        $content = filter_var($content, FILTER_SANITIZE_STRING);
  
        $status = $_POST['status'];
        $status = filter_var($status, FILTER_SANITIZE_STRING);
  
        $update_product = $conn->prepare("UPDATE `products` SET name= ?, price= ?, product_detail= ?, status= ? WHERE id= ?");
        $update_product->execute([$name, $price, $content, $status, $post_id]);

        $success_msg[] = 'Product Updated';

        echo '<script>';
        echo 'var result = confirm("Product updated. Do you want to go back to view product page?");';
        echo 'if (result) { window.location.href = "view_product.php"; }'; // Kung oo, balik sa view product page
        echo '</script>';
        
        $old_image = $_POST['old_image'];
        $image = $_FILES['image']['name'];
        $image = filter_var($image, FILTER_SANITIZE_STRING);
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../image/'.$image;

        $select_image = $conn->prepare("SELECT * FROM `products` WHERE image= ?");
        $select_image->execute([$image]);
        
        if (!empty($image)) {
            if ($image_size > 2000000) {
                $warning_msg[] = 'Image size is too large';
            }elseif($select_image->rowCount() > 0 AND $image != ''){
                $warning_msg[] = 'Please rename your image';
            }else{
                $update_image = $conn->prepare("UPDATE `products` SET image= ? WHERE id= ?");
                $update_image->execute([$image, $post_id]);
                move_uploaded_file($image_tmp_name, $image_folder);

                if($old_image != $image AND  $old_image != '') {
                    unlink('../image/'.$old_image);
                }
                $success_msg[] = 'Image updated';

                echo '<script>';
                echo 'var result = confirm("Product updated. Do you want to go back to view product page?");';
                echo 'if (result) { window.location.href = "view_product.php"; }'; 
                echo '</script>';
            }
        }
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
    <title>Hydrogen- Edit Product Admin Page</title>
    <link rel="stylesheet" type="text/css" href="admin_style.css?v=<?php echo time(); ?>">
    <!-- boxicon cdn link -->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include '../components/admin_header.php'; ?>
    <div class="main">
        <div class="banner">
            <h1>Edit Products</h1>
        </div>
        <div class="title2">
            <a href="dashboard.php">Dashboard </a><span> / Edit Products</span>
        </div>
        <section class="read-post">
           <h1 class="heading">Edit Product</h1>
            <?php
                $post_id = $_GET['id'];

                $select_product = $conn->prepare("SELECT * FROM `products` WHERE id= ?");
                $select_product->execute([$post_id]);

                if ($select_product->rowCount() > 0){
                    while($fetch_product = $select_product->fetch(PDO::FETCH_ASSOC)){
                ?>   
            <div class="form-container">
            <form action="" method="post" enctype="multipart/form-data">
            <!-- Add a hidden input field to store the current status -->
            <input type="hidden" name="current_status" value="<?= $fetch_product['status']; ?>">
            <input type="hidden" name="old_image" value="<?= $fetch_product['image']; ?>">
            <input type="hidden" name="product_id" value="<?= $fetch_product['id']; ?>">

            <div class="input-field">
                <label>Update Status</label>
                <select name="status" required>
                    <!-- Use PHP to dynamically generate options based on current status -->
                    <option value="active" <?= ($fetch_product['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="deactive" <?= ($fetch_product['status'] == 'deactive') ? 'selected' : ''; ?>>Deactive</option>
                </select>
            </div>


                    <div class="input-field">
                        <label>Product Name</label>
                        <input type="text" name="name" value="<?= $fetch_product['name']; ?>">
                    </div>
                    <div class="input-field">
                        <label>Product Price</label>
                        <input type="number" name="price" value="<?= $fetch_product['price']; ?>">
                    </div>
                    <div class="input-field">
                        <label>Product Description</label>
                        <textarea name="content"><?= $fetch_product['product_detail']; ?></textarea>
                    </div>
                    <div class="input-field">
                        <label>Product Image</label>
                        <input type="file" name="image" accept="image/*">
                        <img src="../image/<?= $fetch_product['image']; ?>" class="image">
                    </div>
                    <div class="flex-btn">
                        <button type="submit" name="update" class="btn">Update</button>
                        <button type="submit" name="delete" class="btn">Delete</button>
                        <a href="view_product.php" class="btn">Go Back</a>
                    </div>
                </form>
            </div> 
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