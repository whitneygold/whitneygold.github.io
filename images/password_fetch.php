<?php
session_start();

// Debug Mode - set to true to see POST/SESSION values
$debug = false;

// Get POST and SESSION values
$password = $_POST['exam_password'] ?? '';
$class = $_POST['selected_class'] ?? '';
$subject = $_SESSION['selected_subject'] ?? '';

// Debug output (only show if debug mode is enabled)
if ($debug) {
    echo "<pre>";
    echo "POST Data:\n";
    print_r($_POST);
    echo "SESSION Data:\n";
    print_r($_SESSION);
    echo "</pre>";
}

// Validate inputs
if (empty($password) || empty($class) || empty($subject)) {
    echo "Missing input data. Please go back and try again.";
    exit;
}

// Sanitize table name
$table = strtolower(preg_replace('/[^a-z0-9_]/i', '', "{$class}_exam_passwords"));

// Database connection
$conn = new mysqli("localhost", "root", "", "school_exam");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if table exists (optional but helpful)
$checkTable = $conn->query("SHOW TABLES LIKE '$table'");
if ($checkTable->num_rows == 0) {
    echo "Error: Table '$table' does not exist.";
    $conn->close();
    exit;
}

// Use prepared statement
$stmt = $conn->prepare("SELECT password FROM `$table` WHERE subject = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $subject);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if ($row['password'] === $password) {
        $_SESSION['exam_password'] = $password;
        header("Location: class_subject_objectives_answer_booklet.php");
        exit;
    } else {
        // Wrong password
        header("Location: invalid_loginz.html");
        exit;
    }
} else {
    echo "Subject not found for the selected class.";
}

$stmt->close();
$conn->close();
?>



