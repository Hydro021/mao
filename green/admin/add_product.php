<?php
    include '../components/connection.php';

    session_start();

    $admin_id = $_SESSION['admin_id'];

    if (!isset($admin_id)) {
        header('location: login.php');
    }

    if (isset($_POST['publish'])){
      $id = unique_id();

      $name = $_POST['name'];
      $name = filter_var($name, FILTER_SANITIZE_STRING);

      $price = $_POST['price'];
      $price = filter_var($price, FILTER_SANITIZE_STRING);

      $content = $_POST['content'];
      $content = filter_var($content, FILTER_SANITIZE_STRING);

      $status = 'active';

      $image = $_FILES['image']['name'];
      $image = filter_var($image, FILTER_SANITIZE_STRING);
      $image_size = $_FILES['image']['size'];
      $image_tmp_name = $_FILES['image']['tmp_name'];
      $image_folder = '../image/'.$image;

      $select_image = $conn->prepare("SELECT * FROM `products` WHERE image =  ?");
      $select_image->execute([$image]);

      if (isset($image)){
        if ($select_image->rowCount() > 0) {
          $warning_msg[] = 'Image Name Repeated';
        }elseif ($image_size > 2000000) {
          $warning_msg[] = 'Image Size Is To Large';
        }else{
          move_uploaded_file($image_tmp_name, $image_folder);
        }
      }else{
        $image = '';
      }
      if ($select_image->rowCount() > 0 AND $image != '') {
        $warning_msg[] = 'Please Rename Your Image';
      }else{
        $insert_product = $conn->prepare("INSERT INTO `products`(id, name, price, image, product_detail, status) VALUES(?,?,?,?,?,?)"); 
        $insert_product->execute([$id, $name, $price, $image, $content, $status]);
        $success_msg[] = 'Product Added Successfully';
      }
    }

    if (isset($_POST['draft'])){
      $id = unique_id();

      $name = $_POST['name'];
      $name = filter_var($name, FILTER_SANITIZE_STRING);

      $price = $_POST['price'];
      $price = filter_var($price, FILTER_SANITIZE_STRING);

      $content = $_POST['content'];
      $content = filter_var($content, FILTER_SANITIZE_STRING);

      $status = 'deactive';

      $image = $_FILES['image']['name'];
      $image = filter_var($image, FILTER_SANITIZE_STRING);
      $image_size = $_FILES['image']['size'];
      $image_tmp_name = $_FILES['image']['tmp_name'];
      $image_folder = '../image/'.$image;

      $select_image = $conn->prepare("SELECT * FROM `products` WHERE image =  ?");
      $select_image->execute([$image]);

      if (isset($image)){
        if ($select_image->rowCount() > 0) {
          $warning_msg[] = 'Image Name Repeated';
        }elseif ($image_size > 2000000) {
          $warning_msg[] = 'Image Size Is To Large';
        }else{
          move_uploaded_file($image_tmp_name, $image_folder);
        }
      }else{
        $image = '';
      }
      if ($select_image->rowCount() > 0 AND $image != '') {
        $warning_msg[] = 'Please Rename Your Image';
      }else{
        $insert_product = $conn->prepare("INSERT INTO `products`(id, name, price, image, product_detail, status) VALUES(?,?,?,?,?,?)"); 
        $insert_product->execute([$id, $name, $price, $image, $content, $status]);
        $success_msg[] = 'Product Saved';
      }
    }
   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hydrogen- Add Product Admin Page</title>
    <link rel="stylesheet" type="text/css" href="admin_style.css?v=<?php echo time(); ?>">
    <!-- boxicon cdn link -->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include '../components/admin_header.php'; ?>
    <div class="main">
        <div class="banner">
            <h1>Add Products</h1>
        </div>
        <div class="title2">
            <a href="dashboard.php">Dashboard </a><span> / Add Product</span>
        </div>
        <section class="form-container">
           <h1 class="heading">Add Product</h1>
           <form action="" method="post" enctype="multipart/form-data">
            <div class="input-field">
              <label>Product Name <sup>*</sup></label>
              <input type="text" name="name" maxlength="999" required placeholder="Product Name">
            </div>

            <div class="input-field">
              <label>Product Price <sup>*</sup></label>
              <input type="number" name="price" maxlength="999" required placeholder="Product Price">
            </div>
            <div class="input-field">
              <label>Product Detail <sup>*</sup></label>
              <textarea name="content" required maxlength="1000" required placeholder="Product Description"></textarea>
            </div>

            <div class="input-field">
              <label>Product Image <sup>*</sup></label>
              <input type="file" name="image" accept="image/*" required>
            </div>
            <div class="flex-btn">
              <button type="submit" name="publish" class="btn" >Publish Product</button>
              <button type="submit" name="draft" class="btn" >Save As Draft</button>
            </div>
           </form>
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