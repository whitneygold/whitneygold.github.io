<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
include 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

file_put_contents('debug_log.txt', "Received POST data: " . print_r($data, true) . "\n", FILE_APPEND);

// Update session variables from POST if available
if (isset($data['selectedClass'])) {
    $_SESSION['selected_class'] = $data['selectedClass'];
}
if (isset($data['selectedSubject'])) {
    $_SESSION['selected_subject'] = $data['selectedSubject'];
}
if (isset($data['selectedExamType'])) {
    $_SESSION['selected_exam_type'] = $data['selectedExamType'];
}

$selected_class = $_SESSION['selected_class'] ?? '';
$selected_subject = $_SESSION['selected_subject'] ?? '';
$selected_exam_type = $_SESSION['selected_exam_type'] ?? '';

file_put_contents('debug_log.txt', "Session values - class: $selected_class, subject: $selected_subject, exam_type: $selected_exam_type\n", FILE_APPEND);

if (!$selected_class || !$selected_subject || !$selected_exam_type) {
    echo json_encode(['success' => false, 'error' => 'Missing session values for class, subject, or exam type.']);
    exit();
}

$table = strtolower($selected_class . '_exam_records');
$table = preg_replace('/[^a-z0-9_]/', '', $table);
$subject = strtolower(preg_replace('/[^a-z0-9_]/', '', $selected_subject));

if (!isset($data['studentname']) || !isset($data['studentsscore'])) {
    echo json_encode(['success' => false, 'error' => 'Missing student name or score']);
    exit();
}

$studentname = $conn->real_escape_string(trim($data['studentname']));
$studentsscore = (int)$data['studentsscore'];

file_put_contents('debug_log.txt', "Inserting/updating student: $studentname with score: $studentsscore for subject: $subject in table: $table\n", FILE_APPEND);

$checkSql = "SELECT * FROM `$table` WHERE `student_name` = '$studentname'";
$checkResult = $conn->query($checkSql);

if ($checkResult && $checkResult->num_rows > 0) {
    $sql = "UPDATE `$table` SET `$subject` = '$studentsscore' WHERE `student_name` = '$studentname'";
} else {
    $sql = "INSERT INTO `$table` (`student_name`, `$subject`) VALUES ('$studentname', '$studentsscore')";
}

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    $errorMsg = "Database error: " . $conn->error;
    file_put_contents('debug_log.txt', $errorMsg . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
}

$conn->close();
?>