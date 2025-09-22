<?php
// Connect to the database
$host = "localhost";
$user = "root";
$password = "";
$dbname = "school_report";

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all view attempts
$sql = "SELECT * FROM class_result_view_attempt ORDER BY view_attempt DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Result View Attempts</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f0f9f0;
            padding: 20px;
        }
        h2 {
            color: #155724;
        }
        table {
            border-collapse: collapse;
            width: 60%;
            background-color: #ffffff;
        }
        th, td {
            border: 1px solid #c3e6cb;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #d4edda;
            color: #155724;
        }
        tr:hover {
            background-color: #f8fdf8;
        }
    </style>
</head>
<body>

    <h2>Student Result View Attempts</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Number of Views</th>
        </tr>

        <?php
        if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['username']); ?></td>
                <td><?= $row['view_attempt']; ?></td>
            </tr>
        <?php
            endwhile;
        else:
        ?>
            <tr><td colspan="3">No data found</td></tr>
        <?php endif; ?>

    </table>

</body>
</html>

<?php $conn->close(); ?>

