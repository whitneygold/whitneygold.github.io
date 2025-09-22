<?php
session_start();

// Debug log function
function logDebug($msg) {
    $logFile = __DIR__ . "/database/debug_log.txt";
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$timestamp] $msg\n", FILE_APPEND);
}

// Paths
$databaseDir = __DIR__ . "/database";

// Session values
$image    = $_SESSION['image'] ?? 'default.jpg';
$username = $_SESSION['username'] ?? 'Guest';

// POST values
$password = $_POST['exam_password'] ?? '';
$class    = $_POST['selected_class'] ?? '';
$session  = $_POST['selected_session'] ?? ($_SESSION['selected_session'] ?? '');
$term     = $_POST['selected_term'] ?? ($_SESSION['selected_term'] ?? '');

// Save session and term
if (!empty($_POST['selected_session'])) $_SESSION['selected_session'] = $_POST['selected_session'];
if (!empty($_POST['selected_term'])) $_SESSION['selected_term'] = $_POST['selected_term'];

logDebug("POST: " . json_encode($_POST));
logDebug("SESSION: " . json_encode($_SESSION));
logDebug("Checking password for user='$username' with exam_password='$password' class='$class'");

// Validate
if (empty($password) || empty($username) || empty($session) || empty($term) || empty($class)) {
    logDebug("❌ Missing input data");
    echo "Missing input data. Please go back and try again.";
    exit;
}

// ✅ Build class-specific file paths
$passwordFile = $databaseDir . "/" . strtolower($class) . "_result_passwords.csv";
$viewFile     = $databaseDir . "/" . strtolower($class) . "_result_view_attempt.csv";

// ✅ Check result password (from CSV)
$validPassword = false;
if (file_exists($passwordFile)) {
    $handle = fopen($passwordFile, "r");
    $header = fgetcsv($handle); // Skip header

    while (($data = fgetcsv($handle)) !== false) {
        // Format: id,username,password
        $csvId       = $data[0] ?? '';
        $csvUsername = $data[1] ?? '';
        $csvPassword = $data[2] ?? '';

        logDebug("Checking row: id=$csvId, username=$csvUsername, password=$csvPassword");

        if (trim($csvUsername) === trim($username) && trim($csvPassword) === trim($password)) {
            logDebug("✅ Password match found for $username in $class");
            $validPassword = true;
            break;
        }
    }
    fclose($handle);
} else {
    logDebug("❌ Password file missing: $passwordFile");
}

if (!$validPassword) {
    logDebug("❌ Invalid password for $username in $class");
    header("Location: invalid_password.html");
    exit;
}

// ✅ Check / update view attempts
$attempts = [];
if (file_exists($viewFile)) {
    $handle = fopen($viewFile, "r");
    $header = fgetcsv($handle);

    while (($data = fgetcsv($handle)) !== false) {
        // Format: id,username,view_attempt
        $csvId       = $data[0] ?? '';
        $csvUsername = $data[1] ?? '';
        $csvViews    = (int)($data[2] ?? 0);
        $attempts[$csvUsername] = $csvViews;
    }
    fclose($handle);
} else {
    logDebug("⚠️ View attempt file missing, will create new one: $viewFile");
}

// Current attempts
$currentViews = $attempts[$username] ?? 0;
logDebug("Current views for $username in $class = $currentViews");

if ($currentViews >= 50) {
    logDebug("❌ $username exceeded max attempts");
    echo "You have exceeded the number of allowed views (10 times). Please contact the administrator.";
    exit;
}

// Increment attempts
$attempts[$username] = $currentViews + 1;

// Save back to CSV (overwrite file with updated data)
$handle = fopen($viewFile, "w");
fputcsv($handle, ["id","username","view_attempt"]); // header

$idCounter = 1;
foreach ($attempts as $user => $count) {
    fputcsv($handle, [$idCounter++, $user, $count]);
}
fclose($handle);

logDebug("✅ Updated attempts for $username = " . $attempts[$username]);

// Success → Redirect
$_SESSION['exam_password'] = $password;
logDebug("✅ Login success, redirecting to report");
header("Location: result_csv_display.php");
exit;
?>