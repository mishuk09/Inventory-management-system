<?php
// Start the session to store and retrieve session data
session_start();

// Include the database connection file
include('./config/config.php'); // Ensure this is the correct path

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Get admin email from session
$admin_email = $_SESSION['admin'];

// Query to fetch issued items from the database
$query = "SELECT * FROM issued_items";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issued Items</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Function to return an item
        function returnItem(itemId) {
            if (confirm("Are you sure you want to return this item?")) {
                fetch('return_item.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + itemId
                })
                    .then(response => response.text())
                    .then(data => {
                        if (data === "success") {
                            document.getElementById("item-" + itemId).remove();
                        } else {
                            alert("Failed to return item. Please try again.");
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        }
    </script>
</head>

<body class="bg-gray-50 font-sans antialiased">

    <!-- Navbar -->
    <nav class="bg-blue-600 text-white p-4 shadow-md">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="admindashboard.php" class="text-3xl">🏠︎</a>
            <div>
                <?php echo htmlspecialchars($admin_email); ?>
                <a href="./auth/logout.php"
                    class="bg-red-500 px-4 ms-2 py-2 rounded-md hover:bg-red-600 transition">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto min-h-screen px-6 sm:px-8 py-10">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-10">📋 Return Issued Items</h1>

        <!-- Return by Registration Number -->
        <div class="flex justify-center mb-6">
            <input type="text" id="regNoInput" placeholder="Enter Registration No."
                class="border border-gray-300 px-4 py-2 rounded-md focus:outline-none focus:ring focus:ring-blue-400">
            <button onclick="returnByRegNo()"
                class="bg-blue-500 text-white px-4 py-2 rounded-md ml-2 hover:bg-blue-600 transition">
                Return
            </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto bg-white p-6 shadow-md rounded-lg">
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 px-4 py-2">ID</th>
                        <th class="border border-gray-300 px-4 py-2">User Email</th>
                        <th class="border border-gray-300 px-4 py-2">Regi No.</th>
                        <th class="border border-gray-300 px-4 py-2">Category</th>
                        <th class="border border-gray-300 px-4 py-2">Model</th>
                        <th class="border border-gray-300 px-4 py-2">Issued At</th>
                        <th class="border border-gray-300 px-4 py-2">Due Date</th>
                        <th class="border border-gray-300 px-4 py-2">Return</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr id="item-<?php echo $row['id']; ?>" class="hover:bg-gray-100">
                            <td class="border border-gray-300 px-4 py-2"><?php echo $row['id']; ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['user_email']); ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <?php echo htmlspecialchars($row['registration_number']); ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['category']); ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['model']); ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['issued_at']); ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['due_date']); ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <button onclick="returnItem(<?php echo $row['id']; ?>)"
                                    class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition">
                                    Return
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white text-center py-4 mt-10">
        &copy; <?php echo date("Y"); ?> Inventory Management | All Rights Reserved
    </footer>


    <script>
        function returnByRegNo() {
            let regNo = document.getElementById('regNoInput').value.trim();
            if (regNo === '') {
                alert('Please enter a registration number.');
                return;
            }
            if (confirm("Are you sure you want to return items for this registration number?")) {
                fetch('return_item.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'registration_number=' + encodeURIComponent(regNo)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            data.itemIds.forEach(itemId => {
                                let itemRow = document.getElementById("item-" + itemId);
                                if (itemRow) {
                                    itemRow.remove();
                                }
                            });
                            alert("Items successfully returned!");
                        } else {
                            alert("Failed to return items. Please check the registration number.");
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

    </script>
</body>

</html>