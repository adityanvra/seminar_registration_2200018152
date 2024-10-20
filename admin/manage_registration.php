<?php
session_start();
if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db.php';
require_once '../includes/functions.php';

$error = "";
$success = "";

// Add new registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $email = sanitize_input($_POST["email"]);
    $name = sanitize_input($_POST["name"]);
    $institution = sanitize_input($_POST["institution"]);
    $country = sanitize_input($_POST["country"]);
    $address = sanitize_input($_POST["address"]);

    if (check_email_exists($email)) {
        $error = "Email already registered!";
    } else {
        $sql = "INSERT INTO registration (email, name, institution, country, address) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $email, $name, $institution, $country, $address);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Registration added successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}

// Edit registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit"])) {
    $id = sanitize_input($_POST["id"]);
    $email = sanitize_input($_POST["email"]);
    $name = sanitize_input($_POST["name"]);
    $institution = sanitize_input($_POST["institution"]);
    $country = sanitize_input($_POST["country"]);
    $address = sanitize_input($_POST["address"]);

    $sql = "UPDATE registration SET email=?, name=?, institution=?, country=?, address=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $email, $name, $institution, $country, $address, $id);

    if (mysqli_stmt_execute($stmt)) {
        $success = "Registration updated successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Soft delete registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
    $id = sanitize_input($_POST["id"]);

    $sql = "UPDATE registration SET is_deleted=1 WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        $success = "Registration deleted successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Fetch all active registrations
$sql = "SELECT * FROM registration WHERE is_deleted = 0 ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Registrations</title>
</head>
<body>
    <h1>Manage Registrations</h1>
    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <?php
    if (!empty($error)) {
        echo "<p style='color: red;'>$error</p>";
    }
    if (!empty($success)) {
        echo "<p style='color: green;'>$success</p>";
    }
    ?>

    <h2>Add New Registration</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="name">Name:</label>
        <input type="text" name="name" required><br>

        <label for="institution">Institution:</label>
        <input type="text" name="institution" required><br>

        <label for="country">Country:</label>
        <input type="text" name="country" required><br>

        <label for="address">Address:</label>
        <textarea name="address" required></textarea><br>

        <input type="submit" name="add" value="Add Registration">
    </form>

    <h2>Registrations</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Name</th>
            <th>Institution</th>
            <th>Country</th>
            <th>Address</th>
            <th>Actions</th>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['institution'] . "</td>";
            echo "<td>" . $row['country'] . "</td>";
            echo "<td>" . $row['address'] . "</td>";
            echo "<td>
                    <form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>
                        <input type='hidden' name='id' value='" . $row['id'] . "'>
                        <input type='hidden' name='email' value='" . $row['email'] . "'>
                        <input type='hidden' name='name' value='" . $row['name'] . "'>
                        <input type='hidden' name='institution' value='" . $row['institution'] . "'>
                        <input type='hidden' name='country' value='" . $row['country'] . "'>
                        <input type='hidden' name='address' value='" . $row['address'] . "'>
                        <input type='submit' name='edit' value='Edit'>
                        <input type='submit' name='delete' value='Delete' onclick='return confirm(\"Are you sure you want to delete this registration?\");'>
                    </form>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>
