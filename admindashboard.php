<?php
// index.php

// Start the session to handle messages (if any)
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ./auth/adminlogin.php");
    exit();
}

//get email
$admin_email = $_SESSION['admin'];

// Include the database connection file
include('./config/config.php'); // Adjust the path if needed

// Query to fetch assets from the database
$query = "SELECT * FROM assets";
$result = mysqli_query($conn, $query);

// Check if the form for updating is submitted
if (isset($_POST['update_item'])) {
    $asset_id = $_POST['asset_id'];
    $serial_number = $_POST['serial_number'];
    $registration_number = $_POST['registration_number'];
    $category = $_POST['category'];
    $sub_category = $_POST['sub_category'];
    $type = $_POST['type'];
    $model = $_POST['model'];
    $artificial = $_POST['artificial'];
    $no_engine = $_POST['no_engine'];

    // Update the asset in the database
    $update_query = "UPDATE assets SET serial_number = '$serial_number', registration_number = '$registration_number', category = '$category', sub_category = '$sub_category', type = '$type', model = '$model', artificial = '$artificial', no_engine = '$no_engine' WHERE asset_id = '$asset_id'";

    if (mysqli_query($conn, $update_query)) {
        $_SESSION['message'] = "Asset updated successfully!";
    } else {
        $_SESSION['message'] = "Failed to update asset!";
    }
    header("Location: admindashboard.php");
}


// Delete item functionality
if (isset($_POST['delete_item'])) {
    $asset_id = $_POST['asset_id'];

    // Use prepared statements to delete the asset safely
    $delete_query = "DELETE FROM assets WHERE asset_id = ?";

    if ($stmt = mysqli_prepare($conn, $delete_query)) {
        // Bind the asset_id to the prepared statement
        mysqli_stmt_bind_param($stmt, "i", $asset_id);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Asset deleted successfully!";
        } else {
            $_SESSION['message'] = "Failed to delete asset!";
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    }

    // Redirect to the same page after deletion
    header("Location: admindashboard.php");
    exit();
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
    <title>Inventory Management Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style>
        #editModal {
            background-color: rgba(0, 0, 0, 0.5);
            transition: opacity 0.3s ease;
        }

        #editModal.hidden {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease;
        }
    </style>
</head>

<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4 shadow-md">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Inventory Management</h1>
            <div>
                <?php echo htmlspecialchars($admin_email); ?>

                <a href="./auth/logout.php"
                    class="bg-red-500 px-4 ms-2 py-2 rounded-md hover:bg-red-600 transition">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Main container -->
    <div class="max-w-7xl min-h-screen mx-auto px-4 sm:px-6 lg:px-8 py-14">


        <!-- Heading -->
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">👋 Welcome Admin Dashboard</h1>


        <!-- Heading -->



        <!-- Display Asset Data -->
        <div class="mt-10">
            <div>
                <div class="flex justify-between mt-20">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Asset List</h2>
                    </div>
                    <!-- Add Item Button -->
                    <div class="flex  gap-2 mb-2">
                        <div
                            class=" bg-white rounded border border-1 border-blue-500 hover:border-2 duration-75 transition delay-75 hover:border-blue-600 px-2 w-[200px] py-1">
                            <a href="returnitem.php" class=" text-blue-600  font-semibold ">
                                <!-- Icon and Text -->

                                <div class="flex text-center items-center justify-center">

                                    <div class="text-sm me-2">
                                        ↩️
                                    </div>
                                    <div>

                                        <span class="">Return Items</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div
                            class=" bg-white rounded border border-1 border-blue-500 hover:border-2 duration-75 transition delay-75 hover:border-blue-600 px-2 w-[200px] py-1">
                            <a href="add_item.php" class=" text-blue-600  font-semibold ">
                                <!-- Icon and Text -->

                                <div class="flex text-center items-center justify-center">

                                    <div class="text-sm me-2">
                                        ➕
                                    </div>
                                    <div>

                                        <span class="">Add Items</span>
                                    </div>
                                </div>
                            </a>
                        </div>



                        <!-- Search Item Button -->
                        <!-- Search Box -->
                        <div
                            class="bg-white rounded text-blue-600 font-semibold border border-blue-500 hover:border-blue-600 px-2 w-[350px] py-1">

                            <form id="searchForm" class="flex w-full" onsubmit="return false;">
                                <input type="text" id="searchQuery" placeholder="Search assets..."
                                    class="border text-xs rounded h-6 px-2 w-full" onkeyup="fetchSearchResults()">
                                <button type="button" onclick="fetchSearchResults()"
                                    class="bg-blue-600 text-white px-4 ml-2 rounded">
                                    Search
                                </button>
                            </form>

                            <div id="searchResults"
                                class="mt-4 w-[350px] rounded cursor-pointer text-gray-600 bg-white shadow-md px-2 absolute">
                            </div>
                        </div>



                        <div>
                            <!-- search result -->
                        </div>
                    </div>
                </div>

                <!-- Check if there are assets -->
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <!-- Table to display assets -->
                    <div class="overflow-x-auto bg-white shadow-md rounded-lg p-4">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 text-left">
                                    <th class="py-3 px-4 text-gray-600">No</th>
                                    <th class="py-3 px-4 text-gray-600">Asset ID</th>
                                    <th class="py-3 px-4 text-gray-600">Serial Number</th>
                                    <th class="py-3 px-4 text-gray-600">Reg Number</th>
                                    <th class="py-3 px-4 text-gray-600">Category</th>
                                    <th class="py-3 px-4 text-gray-600">Sub Cate</th>
                                    <th class="py-3 px-4 text-gray-600">Type</th>
                                    <th class="py-3 px-4 text-gray-600">Model</th>
                                    <th class="py-3 px-4 text-gray-600">Artificial</th>
                                    <th class="py-3 px-4 text-gray-600">Num of Engines</th>
                                    <th class="py-3 px-4 text-gray-600">Edit</th>
                                    <th class="py-3 px-4 text-gray-600">Delete</th> <!-- New Delete Column -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch data from the database
                                $result = mysqli_query($conn, "SELECT * FROM assets");

                                // Display rows dynamically
                                $counter = 1;
                                while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr class="border-t border-gray-200" data-asset-id="<?php echo $row['asset_id']; ?>">
                                        <td class="py-3 text-sm text-gray-700 px-4"><?php echo $counter++; ?></td>
                                        <td class="py-3 text-sm text-gray-700 px-4"><?php echo $row['asset_id']; ?></td>
                                        <td class="py-3 text-sm text-gray-700 px-4"><?php echo $row['serial_number']; ?></td>
                                        <td class="py-3 text-sm text-gray-700 px-4"><?php echo $row['registration_number']; ?>
                                        </td>
                                        <td class="py-3 text-sm text-gray-700 px-4"><?php echo $row['category']; ?></td>
                                        <td class="py-3 text-sm text-gray-700 px-4"><?php echo $row['sub_category']; ?></td>
                                        <td class="py-3 text-sm text-gray-700 px-4"><?php echo $row['type']; ?></td>
                                        <td class="py-3 text-sm text-gray-700 px-4"><?php echo $row['model']; ?></td>
                                        <td class="py-3 text-sm text-gray-700 px-4"><?php echo $row['artificial']; ?></td>
                                        <td class="py-3 text-sm text-gray-700 px-4"><?php echo $row['no_engine']; ?></td>
                                        <td class="py-3 px-4 text-center">
                                            <button onclick="openEditModal(<?php echo $row['asset_id']; ?>)"
                                                class="text-white bg-gray-200 hover:bg-gray-300 py-2 px-4 rounded-lg font-semibold transition duration-300">
                                                ✏️
                                            </button>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <form action="admindashboard.php" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this asset?');">
                                                <input type="hidden" name="asset_id" value="<?php echo $row['asset_id']; ?>">
                                                <button type="submit" name="delete_item"
                                                    class="text-white bg-red-500 hover:bg-red-600 py-2 px-4 rounded-lg font-semibold transition duration-300">
                                                    🗑️
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center text-gray-500">No assets found.</p>
                <?php endif; ?>
            </div>


            <!-- Edit Modal -->
            <div id="editModal"
                class="fixed inset-0 bg-gray-900 bg-transparent bg-opacity-75 hidden flex justify-center items-center">
                <div class="bg-white max-w-7xl  mx-auto px-4 sm:px-6 lg:px-8 py-10   rounded-md">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Edit Asset</h2>
                    <form action="admindashboard.php" method="POST">
                        <input type="hidden" id="editAssetId" name="asset_id">

                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                            <div class="mb-4">
                                <label for="serial_number" class="block text-gray-600">Serial Number</label>
                                <input type="text" id="editSerialNumber" name="serial_number"
                                    class="w-full p-2 border rounded" required>
                            </div>
                            <div class="mb-4">
                                <label for="registration_number" class="block text-gray-600">Registration Number</label>
                                <input type="text" id="editRegistrationNumber" name="registration_number"
                                    class="w-full p-2 border rounded" required>
                            </div>
                            <div class="mb-4">
                                <label for="category" class="block text-gray-600">Category</label>
                                <input type="text" id="editCategory" name="category" class="w-full p-2 border rounded"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label for="sub_category" class="block text-gray-600">Sub Category</label>
                                <input type="text" id="editSubCategory" name="sub_category"
                                    class="w-full p-2 border rounded" required>
                            </div>
                            <div class="mb-4">
                                <label for="type" class="block text-gray-600">Type</label>
                                <input type="text" id="editType" name="type" class="w-full p-2 border rounded" required>
                            </div>
                            <div class="mb-4">
                                <label for="model" class="block text-gray-600">Model</label>
                                <input type="text" id="editModel" name="model" class="w-full p-2 border rounded"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label for="artificial" class="block text-gray-600">Artificial</label>
                                <input type="text" id="editArtificial" name="artificial"
                                    class="w-full p-2 border rounded" required>
                            </div>
                            <div class="mb-4">
                                <label for="no_engine" class="block text-gray-600">Number of Engines</label>
                                <input type="number" id="editNoEngine" name="no_engine"
                                    class="w-full p-2 border rounded" required>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="button" onclick="closeEditModal()"
                                class="text-gray-600 bg-gray-200 hover:bg-gray-300 py-2 px-4 rounded-lg mr-4">
                                Close
                            </button>
                            <button type="submit" name="update_item"
                                class="text-white bg-blue-600 hover:bg-blue-700 py-2 px-4 rounded-lg">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>



        </div>


    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white text-center py-4 mt-auto">
        &copy; <?php echo date("Y"); ?> Inventory Management | All Rights Reserved
    </footer>
    <script>

        function openEditModal(assetId) {
            // Show the modal
            const modal = document.getElementById("editModal");
            modal.classList.remove("hidden");

            // Get the data from the row and fill the modal inputs
            const assetRow = document.querySelector(`[data-asset-id='${assetId}']`);
            document.getElementById("editAssetId").value = assetId;
            document.getElementById("editSerialNumber").value = assetRow.cells[2].textContent;
            document.getElementById("editRegistrationNumber").value = assetRow.cells[3].textContent;
            document.getElementById("editCategory").value = assetRow.cells[4].textContent;
            document.getElementById("editSubCategory").value = assetRow.cells[5].textContent;
            document.getElementById("editType").value = assetRow.cells[6].textContent;
            document.getElementById("editModel").value = assetRow.cells[7].textContent;
            document.getElementById("editArtificial").value = assetRow.cells[8].textContent;
            document.getElementById("editNoEngine").value = assetRow.cells[9].textContent;
        }

        function closeEditModal() {
            // Hide the modal
            document.getElementById("editModal").classList.add("hidden");
        }



        //search functionality js
        function fetchSearchResults() {
            var searchQuery = document.getElementById('searchQuery').value;
            var resultsDiv = document.getElementById('searchResults');

            if (searchQuery.length >= 1) { // Optional: start search after 3 characters
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'admindashboard.php', true);
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