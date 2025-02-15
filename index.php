<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-md p-6 bg-white shadow-lg rounded-lg">
        <!-- Header -->
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Login / Register</h2>
        </div>

        <!-- Buttons -->
        <div class="space-y-4">
            <a href="auth/userlogin.php"
                class="block w-full bg-blue-500 text-white text-center py-2 rounded-lg hover:bg-blue-600 transition">User</a>
            <a href="auth/adminlogin.php"
                class="block w-full bg-gray-800 text-white text-center py-2 rounded-lg hover:bg-gray-900 transition">Admin</a>
        </div>
    </div>
</body>

</html>