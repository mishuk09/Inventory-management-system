<?php
session_start();
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM admins WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION['admin'] = $email;
        header("Location: ../admindashboard.php");
        exit();
    } else {
        $error = "Invalid Email or Password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-md p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Admin Login</h2>
        <?php if (isset($error))
            echo "<p class='text-red-500 text-center'>$error</p>"; ?>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" class="w-full p-2 mb-4 border rounded-lg" required>
            <input type="password" name="password" placeholder="Password" class="w-full p-2 mb-4 border rounded-lg"
                required>
            <button type="submit" class="w-full bg-gray-800 text-white py-2 rounded-lg hover:bg-gray-900">Login</button>
        </form>
    </div>
</body>

</html>