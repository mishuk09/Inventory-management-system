<?php
// Start the session to store and retrieve session data
session_start();

// Include the database connection file
include('./config/config.php'); // Ensure this is the correct path

// Define success message variable
$successMessage = "";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture the form data and sanitize it
    $asset_id = mysqli_real_escape_string($conn, $_POST['asset_id']);
    $serial_number = mysqli_real_escape_string($conn, $_POST['serial_number']);
    $registration_number = mysqli_real_escape_string($conn, $_POST['registration_number']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $sub_category = mysqli_real_escape_string($conn, $_POST['sub_category']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $artificial = mysqli_real_escape_string($conn, $_POST['artificial']);
    $no_engine = mysqli_real_escape_string($conn, $_POST['no_engine']);

    // Prepare the SQL query to insert data into the database
    $query = "INSERT INTO assets (asset_id, serial_number, registration_number, category, sub_category, type, model, artificial, no_engine) 
              VALUES ('$asset_id', '$serial_number', '$registration_number', '$category', '$sub_category', '$type', '$model', '$artificial', '$no_engine')";

    // Execute the query and check if data is inserted
    if (mysqli_query($conn, $query)) {
        // If insertion is successful, set success message in session
        $_SESSION['successMessage'] = "Asset added successfully!";

        // Redirect to the same page to prevent resubmission of form data
        header("Location: add_item.php");
        exit(); // Always call exit after a header redirect
    } else {
        // If there is an error, display it
        echo "Error: " . mysqli_error($conn);
    }
}

// Retrieve the success message from session if set
if (isset($_SESSION['successMessage'])) {
    $successMessage = $_SESSION['successMessage'];
    // Clear the session variable after displaying the message to prevent it from showing again on refresh
    unset($_SESSION['successMessage']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script>
        // JavaScript to hide the success message after 3 seconds
        function hideMessage() {
            setTimeout(function () {
                document.getElementById("success-message").style.display = "none";
            }, 3000); // Hide after 3 seconds
        }
    </script>
</head>

<body class="bg-gray-50 font-sans antialiased">

    <!-- Main container -->
    <div class="max-w-7xl mx-auto px-6 sm:px-8 py-10">

        <!-- Home Button -->
        <div class="flex justify-between mt-2">
            <a href="index.php" class="text-3xl text-gray-600">🏠︎</a>
            <h1 class="text-3xl font-semibold text-center text-gray-600 mb-8">Add New Asset Item</h1>
        </div>

        <!-- Success Message -->
        <?php if ($successMessage != ""): ?>
            <div id="success-message" class="bg-green-500 text-white text-center p-4 rounded-lg mb-4">
                <?php echo $successMessage; ?>
                <script>hideMessage();</script>
            </div>
        <?php endif; ?>

        <!-- Form to Add Item -->
        <div class="bg-white shadow-md rounded-lg p-8">
            <form action="add_item.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Asset ID -->
                <div>
                    <label for="asset_id" class="font-semibold text-gray-700 ">Asset ID:</label>
                    <input type="number" id="asset_id" name="asset_id"
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- Serial Number -->
                <div>
                    <label for="serial_number" class="font-semibold text-gray-700 ">Serial Number:</label>
                    <input type="text" id="serial_number" name="serial_number"
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- Registration Number -->
                <div>
                    <label for="registration_number" class="font-semibold text-gray-700 ">Registration Number:</label>
                    <input type="text" id="registration_number" name="registration_number"
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="font-semibold text-gray-700 ">Category:</label>
                    <input type="text" id="category" name="category"
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- Sub-Category -->
                <div>
                    <label for="sub_category" class="font-semibold text-gray-700 ">Sub Category:</label>
                    <input type="text" id="sub_category" name="sub_category"
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="font-semibold text-gray-700 ">Type:</label>
                    <input type="text" id="type" name="type"
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- Model -->
                <div>
                    <label for="model" class="font-semibold text-gray-700 ">Model:</label>
                    <input type="text" id="model" name="model"
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- Artificial -->
                <div>
                    <label for="artificial" class="font-semibold text-gray-700 ">Artificial:</label>
                    <select id="artificial" name="artificial"
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <!-- No of Engines -->
                <div>
                    <label for="no_engine" class="font-semibold text-gray-700 ">Number of Engines:</label>
                    <input type="number" id="no_engine" name="no_engine"
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- Submit Button -->
                <div class="col-span-1 md:col-span-2 flex mx-auto items-center text-center justify-center">
                    <button type="submit"
                        class="text-white bg-blue-600 hover:bg-blue-700 px-8 py-3 rounded-lg font-semibold transition duration-300 transform hover:scale-105">
                        Add Asset
                    </button>
                </div>

            </form>
        </div>

    </div>

</body>

</html>