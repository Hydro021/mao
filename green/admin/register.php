<?php
    include '../components/connection.php';

    if (isset($_POST['register'])) {

        $id = unique_id();

        $name = $_POST['name'];
        $name = filter_var($name, FILTER_SANITIZE_STRING);

        $email = $_POST['email'];
        $email = filter_var($email, FILTER_SANITIZE_STRING);

        $pass = sha1($_POST['password']);
        $pass = filter_var($pass, FILTER_SANITIZE_STRING);

        $cpass = sha1($_POST['cpassword']);
        $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

        $image = $_FILES['image']['name'];
        $image = filter_var($image, FILTER_SANITIZE_STRING);
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../image/'.$image;

        $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE email = ?");
        $select_admin->execute([$email]);

        if ($select_admin->rowCount() > 0 ) {
            $warning_msg[] = 'User Email Already Exist';
        }else {
            if ($pass != $cpass) {
                $warning_msg[] = 'Confirm Password Not Matched';
            }else {
                $insert_admin = $conn->prepare("INSERT INTO `admin` (id, name, email, password, profile) VALUES(?,?,?,?,?)");
                $insert_admin->execute([$id, $name, $email, $cpass, $image]);
                move_uploaded_file($image_tmp_name, $image_folder);
                $success_msg[] = 'New Admin Register';  
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hydrogen- Register Admin Page</title>
    <link rel="stylesheet" type="text/css" href="admin_style.css?v=<?php echo time(); ?>">
    <!-- boxicon cdn link -->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="main">
        <section>
            <div class="form-container" id="admin_login">
                <form action="" method="post" enctype="multipart/form-data">
                    <h3>Register Form</h3>
                    <div class="input-field">
                        <label>Username <sup>*</sup></label>
                        <input type="text" name="name" maxlength="900" required placeholder="Username" oninput="this.value.replace(/\s/g, '')">
                    </div>

                    <div class="input-field">
                        <label>Email <sup>*</sup></label>
                        <input type="email" name="email" maxlength="900" required placeholder="Email" oninput="this.value.replace(/\s/g, '')">
                    </div>

                    <div class="input-field">
                        <label>Password <sup>*</sup></label>
                        <input type="password" name="password" maxlength="900" required placeholder="Password" oninput="this.value.replace(/\s/g, '')">
                    </div>

                    <div class="input-field">
                        <label>Confirm Password <sup>*</sup></label>
                        <input type="password" name="cpassword" maxlength="900" required placeholder="Confirm Password" oninput="this.value.replace(/\s/g, '')">
                    </div>

                    <div class="input-field">
                        <label>Select Profile <sup>*</sup></label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                    <button type="submit" name="register" class="btn">Register</button>
                    <p>Already have an account? <a href="login.php">Log In Here</a></p>
                </form>
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