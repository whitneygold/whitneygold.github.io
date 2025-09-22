
<?php
session_start(); // Start the session
header('Content-Type: application/json'); // Ensure JSON response

// Include DB connection
include 'db_connect.php'; // Assumes this file sets up $conn

$class = $_SESSION['selected_class'] ?? '';
$subject = $_SESSION['selected_subject'] ?? '';
$type = $_SESSION['selected_type'] ?? '';

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['studentname']) || !isset($data['studentsscore'])) {
    echo json_encode(['success' => false, 'error' => 'Missing student name or score']);
    exit();
}
if ($class && $subject && $type) {
     $table = strtolower("{$class}_exam_records");
   
     $subject = strtolower("{$subject}");
   
    $table = preg_replace('/[^a-z0-9_]/', '', $table);

    $studentname = $conn->real_escape_string(trim($data['studentname']));
    $studentsscore = (int) $data['studentsscore']; // Ensure integer

    // Check if student exists
    $checkSql = "SELECT * FROM $table WHERE `student_name` = '$studentname'";
    $checkResult = $conn->query($checkSql);

    if ($checkResult->num_rows > 0) {
        // Update score
        $sql = "UPDATE $table SET $subject = '$studentsscore' WHERE `student_name` = '$studentname'";
    } else {
        // Insert new record
        $sql = "INSERT INTO $table (`student_name`, $subject) VALUES ('$studentname', '$studentsscore')";
    }

    // Execute query
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);

     } else {
             echo json_encode(['error' => 'Session values missing']);
            }

} else {
        echo json_encode(['success' => false, 'error' => "Database error: " . $conn->error]);
       }

$conn->close();
?>

