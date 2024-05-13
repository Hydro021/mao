<?php
    include '../components/connection.php';

    session_start();

    $admin_id = $_SESSION['admin_id'];

    if (!isset($admin_id)) {
        header('location: login.php');
    }
   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hydrogen- Registered User's Admin Page</title>
    <link rel="stylesheet" type="text/css" href="admin_style.css?v=<?php echo time(); ?>">
    <!-- boxicon cdn link -->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include '../components/admin_header.php'; ?>
    <div class="main">
        <div class="banner">
            <h1>Registered User's And Admin's</h1>
        </div>
        <div class="title2">
            <a href="dashboard.php">Dashboard </a><span> / Registered User's And Admin's</span>
        </div>
        <section class="accounts">
           <h1 class="heading">Registered User's</h1>
           <div class="box-container">
            <?php 
               $select_users = $conn->prepare("SELECT * FROM `users`");     
               $select_users->execute();
               
               if ($select_users->rowCount()> 0){
                while($fetch_users = $select_users->fetch(PDO::FETCH_ASSOC)) {
                    $user_id = $fetch_users['id'];
            ?>
            <div class="box">
                <p>User ID: <span><?= $user_id; ?></span></p>
                <p>User Name: <span><?= $fetch_users['name']; ?></span></p>
                <p>User Email: <span><?= $fetch_users['email']; ?></span></p>

            </div>
            <?php 
                }
            }else 
            echo '<div class="empty"> 
            <p>No User Registered Yet</p>
        </div>';
            ?>
           </div>
        </section>
        <section class="accounts">
           <h1 class="heading">Admin User's</h1>
           <div class="box-container">
            <?php 
               $select_admins = $conn->prepare("SELECT * FROM `admin`");     
               $select_admins->execute();
               
               if ($select_admins->rowCount()> 0){
                while($fetch_admins = $select_admins->fetch(PDO::FETCH_ASSOC)) {
                    $admin_id = $fetch_admins['id'];
            ?>
            <div class="box">
                <p>Admin ID: <span><?= $admin_id; ?></span></p>
                <p>Admin Name: <span><?= $fetch_admins['name']; ?></span></p>
                <p>Admin Email: <span><?= $fetch_admins['email']; ?></span></p>

            </div>
            <?php 
                }
            }else 
            echo '<div class="empty"> 
            <p>No Admin Registered Yet</p>
        </div>';
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