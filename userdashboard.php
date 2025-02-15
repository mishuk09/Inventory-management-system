<?php
session_start();
include('./config/config.php');

// Redirect if user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: /auth/userlogin.php");
    exit();
}


// Fetch available assets
$assetsQuery = "SELECT * FROM assets";
$assetsResult = mysqli_query($conn, $assetsQuery);



// Ensure user is logged in and email is set
if (!isset($_SESSION['user'])) {
    die("User not logged in");
}


$user_email = $_SESSION['user']; // Get the logged-in user's email
// Fetch issued items for the current user
$sql = "SELECT * FROM issued_items WHERE user_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$issuedresult = $stmt->get_result();

// Check if query executed successfully
if (!$issuedresult) {
    die("Query Failed: " . $conn->error);
}



//search functionality

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_query'])) {
    $search_query = mysqli_real_escape_string($conn, $_POST['search_query']);

    // Query the database to match `modal` field data
    $query = "SELECT * FROM assets WHERE model LIKE '%$search_query%'";
    $result = mysqli_query($conn, $query);

    // Return the search results
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='border-b py-2'>"; // Customize result layout
            echo "<strong>Modal:</strong> " . htmlspecialchars($row['model']) . "<br>";
            echo "<strong>Category:</strong> " . htmlspecialchars($row['category']) . "<br>";
            echo "<strong>Regi No:</strong> " . htmlspecialchars($row['registration_number']);
            echo "</div>";
        }
    } else {
        echo "<div class='text-gray-500'>No results found</div>";
    }
    exit; // Ensure no additional output is sent back
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | Inventory Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-blue-600 text-white p-4 shadow-md">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Inventory Management</h1>
            <a href="./auth/logout.php" class="bg-red-500 px-4 py-2 rounded-md hover:bg-red-600 transition">Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow">
        <h2 class="text-3xl font-bold text-gray-800 mb-10 text-center">Welcome,
            <?php echo htmlspecialchars($user_email); ?> 👋
        </h2>

        <!-- Available Assets -->
        <div class="bg-white p-6 mb-2   w-full rounded-lg shadow-md">
            <div class="flex gap-4 justify-end">
                <button onclick="openTakeItemModal()"
                    class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600 transition">📦 Take New
                    Item</button>

                <button onclick="openModal()"
                    class="bg-green-500 text-white px-6 py-2 rounded-md hover:bg-green-600 transition">📦 See All
                    Items</button>
            </div>
        </div>
        <div class="">
            <!-- Issued Items -->
            <div class="bg-white p-2 rounded-lg shadow-md">
                <div class="bg-white relative p-2 rounded-lg w-full max-h-[80vh] overflow-auto">
                    <h3 class="text-xl font-semibold mb-4 text-gray-700">Your Issued Items</h3>

                    <?php if ($issuedresult->num_rows > 0): ?>
                        <table class="min-w-full table-auto border">
                            <thead>
                                <tr class="bg-gray-200 text-left">
                                    <th class="py-2 px-4 border">#</th>
                                    <!-- <th class="py-2 px-4 border">User Email</th> -->
                                    <th class="py-2 px-4 border">Asset ID</th>
                                    <th class="py-2 px-4 border">Serial Number</th>
                                    <th class="py-2 px-4 border">Reg Number</th>
                                    <th class="py-2 px-4 border">Category</th>
                                    <th class="py-2 px-4 border">Model</th>
                                    <th class="py-2 px-4 border">Issued At</th>
                                    <th class="py-2 px-4 border">Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 1;
                                while ($row = $issuedresult->fetch_assoc()): ?>
                                    <tr class="border-t">
                                        <td class="py-2 px-4 border"><?php echo $counter++; ?></td>

                                        <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['asset_id']); ?></td>
                                        <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['serial_number']); ?></td>
                                        <td class="py-2 px-4 border">
                                            <?php echo htmlspecialchars($row['registration_number']); ?>
                                        </td>
                                        <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['category']); ?></td>
                                        <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['model']); ?></td>
                                        <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['issued_at']); ?></td>
                                        <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['due_date']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-gray-500">No assets available.</p>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Take New Item Modal -->
    <div id="takeItemModal" class="fixed  inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white  relative p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-xl font-bold text-gray-700 mb-4">Take New Item</h2>

            <label for="registration_number" class="block text-gray-600 font-semibold">Enter Registration
                Number:</label>
            <input type="text" id="registration_number"
                class="w-full px-3 py-2 border rounded-lg mt-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                placeholder="Enter Reg No">

            <button onclick="takeItem()"
                class="bg-green-500 text-white px-6 py-2 mt-4 rounded-md hover:bg-green-600 transition w-full">
                Submit
            </button>

            <button onclick="closeTakeItemModal()" class=" absolute top-2 right-2 text-xs">
                ❌
            </button>
        </div>
    </div>


    <!-- Modal -->
    <div id="assetModal" class="fixed inset-0 hidden bg-gray-800 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white relative p-6 rounded-lg w-3/4 max-h-[80vh] overflow-auto">
            <h3 class="text-2xl font-semibold mb-4 text-center w-full">All Available Items</h3>
            <button onclick="closeModal()" class="absolute top-2 right-4 text-black text-xl">&times;</button>
            <!-- Search Box -->
            <div
                class="bg-white flex  justify-end right-2 mb-4 rounded text-blue-600 font-semibold border border-blue-500 hover:border-blue-600 px-2 w-[350px] py-2">
                <form id="searchForm" class="flex w-full" onsubmit="return false;">
                    <input type="text" id="searchQuery" placeholder="Search assets..."
                        class="border rounded h-8 px-2 w-full" onkeyup="fetchSearchResults()">
                    <button type="button" onclick="fetchSearchResults()"
                        class="bg-blue-600 text-white px-4 ml-2 rounded">
                        Search
                    </button>
                </form>

                <div id="searchResults"
                    class="mt-4 w-[350px] rounded cursor-pointer text-gray-600 bg-white shadow-md px-2 absolute">
                </div>
            </div>
            <?php if (mysqli_num_rows($assetsResult) > 0): ?>

                <table class="min-w-full table-auto border">
                    <thead>
                        <tr class="bg-gray-200 text-left">
                            <th class="py-2 px-4 border">No</th>
                            <th class="py-2 px-4 border">Asset ID</th>
                            <th class="py-2 px-4 border">Serial Number</th>
                            <th class="py-3 px-4 text-gray-600">Reg Number</th>
                            <th class="py-2 px-4 border">Category</th>
                            <th class="py-2 px-4 border">Model</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 1;
                        while ($row = mysqli_fetch_assoc($assetsResult)): ?>
                            <tr class="border-t">
                                <td class="py-2 px-4 border"><?php echo $counter++; ?></td>
                                <td class="py-2 px-4 border"><?php echo $row['asset_id']; ?></td>
                                <td class="py-2 px-4 border"><?php echo $row['serial_number']; ?></td>
                                <td class="py-3 text-sm text-gray-700 px-4"><?php echo $row['registration_number']; ?>
                                </td>
                                <td class="py-2 px-4 border"><?php echo $row['category']; ?></td>
                                <td class="py-2 px-4 border"><?php echo $row['model']; ?></td>

                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-gray-500">No assets available.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white text-center py-4 mt-auto">
        &copy; <?php echo date("Y"); ?> Inventory Management | All Rights Reserved
    </footer>

    <!-- JavaScript for Modal -->
    <script>
        function openModal() {
            document.getElementById("assetModal").classList.remove("hidden");
        }
        function closeModal() {
            document.getElementById("assetModal").classList.add("hidden");
        }
        function openEditModal(assetId) {
            alert("Edit functionality for Asset ID: " + assetId);
        }



        function openTakeItemModal() {
            document.getElementById("takeItemModal").classList.remove("hidden");
        }

        function closeTakeItemModal() {
            document.getElementById("takeItemModal").classList.add("hidden");
        }


        function takeItem() {
            const regNumber = document.getElementById("registration_number").value;

            fetch("process_take_item.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "registration_number=" + encodeURIComponent(regNumber)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Add new row to the issued items table dynamically
                        const table = document.querySelector("tbody");
                        const newRow = document.createElement("tr");
                        newRow.classList.add("border-t");
                        newRow.innerHTML = `
                <td class="py-2 px-4 border">${data.counter}</td>
                <td class="py-2 px-4 border">${data.asset_id}</td>
                <td class="py-2 px-4 border">${data.serial_number}</td>
                <td class="py-2 px-4 border">${data.registration_number}</td>
                <td class="py-2 px-4 border">${data.category}</td>
                <td class="py-2 px-4 border">${data.model}</td>
                <td class="py-2 px-4 border">${data.issued_at}</td>
                <td class="py-2 px-4 border">${data.due_date}</td>
            `;
                        table.appendChild(newRow);

                        // Close the modal
                        closeTakeItemModal();
                    } else {
                        alert("Failed to take item: " + data.message);
                    }
                })
                .catch(error => console.error("Error:", error));
        }





        //search functionality js
        function fetchSearchResults() {
            var searchQuery = document.getElementById('searchQuery').value;
            var resultsDiv = document.getElementById('searchResults');

            if (searchQuery.length >= 1) { // Optional: start search after 3 characters
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'userdashboard.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        resultsDiv.innerHTML = xhr.responseText;
                    }
                };
                xhr.send('search_query=' + encodeURIComponent(searchQuery));
            } else {
                resultsDiv.innerHTML = ''; // Clear if search query is too short
            }
        }
    </script>

</body>

</html>