<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
            $success = "Registration successful!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seminar Registration</title>
</head>
<body>
    <h1>Seminar Registration</h1>
    <?php
    if (!empty($error)) {
        echo "<p style='color: red;'>$error</p>";
    }
    if (!empty($success)) {
        echo "<p style='color: green;'>$success</p>";
    }
    ?>
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

        <input type="submit" value="Register">
    </form>
</body>
</html>
