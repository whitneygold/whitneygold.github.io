<?php
session_start();

// Paths to CSV files
$databaseDir = __DIR__ . "/database";
$registerFile = $databaseDir . "/register.csv";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    if (!file_exists($registerFile)) {
        die("Register file not found.");
    }

    $handle = fopen($registerFile, "r");
    $found = false;

    // Optional: skip header row if present
    $header = fgetcsv($handle);

    while (($data = fgetcsv($handle)) !== false) {
        // Expected CSV format: id,username,password,image
        $csvId       = $data[0] ?? '';
        $csvUsername = $data[1] ?? '';
        $csvPassword = $data[2] ?? '';
        $csvImage    = $data[3] ?? 'default.jpg';

        if ($csvUsername === $user && $csvPassword === $pass) {
            $_SESSION['username'] = $csvUsername;
            $_SESSION['image']    = $csvImage;
            $found = true;
            break;
        }
    }

    fclose($handle);

    if ($found) {
        header("Location: dashboard.php");
        exit;
    } else {
        header("Location: invalid_login.html");
        exit;
    }
}
?>
