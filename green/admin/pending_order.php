<?php
    include '../components/connection.php';
    session_start();
    $admin_id = $_SESSION['admin_id'];
    if (!isset($admin_id)) {
        header('location: login.php');
    }
    if (isset($_POST['delete_order'])) {
        $order_id = filter_var($_POST['order_id'], FILTER_SANITIZE_STRING);
        $verify_order = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
        $verify_order->execute([$order_id]);
        if ($verify_order->rowCount() > 0) {
            $order_message = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
            $order_message->execute([$order_id]);
            $success_msg[] = 'Order Deleted';
        } else {
            $warning_msg[] = 'Order Not Found';
        }
    }
    if (isset($_POST['update_order'])) {
        $order_id = filter_var($_POST['order_id'], FILTER_SANITIZE_STRING);
        $update_payment = filter_var($_POST['update_payment'], FILTER_SANITIZE_STRING);
        $update_pay = $conn->prepare("UPDATE `orders` SET payment_status= ? WHERE id= ?");
        $update_pay->execute([$update_payment, $order_id]);
        $success_msg[]= 'Order Updated';
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hydrogen- Pending Order Admin Page</title>
    <link rel="stylesheet" type="text/css" href="admin_style.css?v=<?php echo time(); ?>">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include '../components/admin_header.php'; ?>
    <div class="main">
        <div class="banner">
            <h1>Pending Order</h1>
        </div>
        <div class="title2">
            <a href="dashboard.php">Dashboard </a><span> / Pending Order</span>
        </div>
        <section class="order-container">
           <h1 class="heading">Total Pending Orders</h1>
           <div class="box-container">
              <?php
                  $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = 'pending'");
                  $select_orders->execute();
                  if($select_orders->rowCount() > 0){
                    while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
              ?>
              <div class="box">
                <div class="status" style="color: <?php echo ($fetch_orders['status']== 'in progress') ? 'green' : 'red'; ?>"><?php echo $fetch_orders['status']; ?></div>
                <div class="detail">
                    <p>User Name: <span><?php echo $fetch_orders['name']; ?></span></p>
                    <p>User ID: <span><?php echo $fetch_orders['id']; ?></span></p>
                    <p>Placed On: <span><?php echo $fetch_orders['date']; ?></span></p>
                    <p>User Number: <span><?php echo $fetch_orders['number']; ?></span></p>
                    <p>User Email: <span><?php echo $fetch_orders['email']; ?></span></p>
                    <p>Total Price: <span><?php echo $fetch_orders['price']; ?></span></p>
                    <p>Method: <span><?php echo $fetch_orders['method']; ?></span></p>
                    <p>Address: <span><?php echo $fetch_orders['address']; ?></span></p>
                </div>
                <form action="" method="post">
                    <input type="hidden" name="order_id" value="<?php echo $fetch_orders['id']; ?>">
                    <select name="update_payment">
                        <option disabled selected><?php echo $fetch_orders['payment_status']; ?></option>
                        <option value="pending">Pending</option>
                        <option value="complete">Complete</option>
                    </select>
                    <div class="flex-btn">
                        <button type="submit" name="update_order" class="btn">Update Payment</button>
                        <button type="submit" name="delete_order" class="btn">Delete Order</button>
                    </div>
                </form>
              </div>
                <?php 
                }
            } else {
                echo '<div class="empty"> 
                    <p>No Orders</p>
                </div>';
            }
            ?>
        </div>
        </section>
    </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script type="text/javascript" src="script.js"></script>
<?php include '../components/alert.php'; ?>
</body>
</html>
