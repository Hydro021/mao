<?php
    include '../components/connection.php';

    session_start();

    $admin_id = $_SESSION['admin_id'];

    if (!isset($admin_id)) {
        header('location: login.php');
    }

    if (isset($_POST['delete'])) {
        $delete_id = $_POST['delete_id'];
        $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

        $verify_delete = $conn->prepare("SELECT * FROM `message` WHERE id = ?");
        $verify_delete->execute([$delete_id]);

        if ($verify_delete->rowCount() > 0) {
            $delete_message = $conn->prepare("DELETE FROM `message` WHERE id = ?");
            $delete_message->execute([$delete_id]);
            $success_msg[] = 'Message Deleted';
        } else {
            $warning_msg[] = 'Message Not Found';
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hydrogen- Unread Message's Admin Page</title>
    <link rel="stylesheet" type="text/css" href="admin_style.css?v=<?php echo time(); ?>">
    <!-- boxicon cdn link -->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include '../components/admin_header.php'; ?>
    <div class="main">
        <div class="banner">
            <h1>Unread Message's</h1>
        </div>
        <div class="title2">
            <a href="dashboard.php">Dashboard </a><span> / Unread Message's</span>
        </div>
        <section class="accounts">
           <h1 class="heading">Unread Message's</h1>
           <div class="box-container">
            <?php 
               $select_message = $conn->prepare("SELECT * FROM `message`");     
               $select_message->execute();
               
               if ($select_message->rowCount() > 0){
                while($fetch_message = $select_message->fetch(PDO::FETCH_ASSOC)) {
                    
            ?>
            <div class="box">
                <h3 class="name"><?= $fetch_message['name']; ?></h3>
                <h4><?= $fetch_message['subject'];?></h4>
                <p><?= $fetch_message['message'];?></p>
                <form action="" method="post" class="flex-btn">
                    <input type="hidden" name="delete_id" value="<?= $fetch_message['id']; ?>">
                    <button type="submit" name="delete" class="btn" onclick="return confirm('Delete this message?');">Delete Message</button>
                </form>

            </div>
            <?php 
                }
            } else {
                echo '<div class="empty"> 
                    <p>No Message Found</p>
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
