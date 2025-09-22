<?php
session_start();

// Check for required POST data
if (!isset($_POST['class'], $_POST['session'], $_POST['term'])) {
    echo "Missing required data. Please go back and select class, subject, and term.";
    exit;
}

// Set session variables
$_SESSION['selected_class'] = $_POST['class'];
$_SESSION['selected_session'] = $_POST['session'];
$_SESSION['selected_term'] = $_POST['term'];

// Fallbacks if session values are missing
$image = $_SESSION['image'] ?? 'default.jpg';
$username = $_SESSION['username'] ?? 'Guest';
?>

<!DOCTYPE html>
<html>
<head>
   <link rel="icon" type="image/x-icon" href="fav_icon2.ico">
    <title>www.college.org</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e8f5e9;
            padding: 20px;
            margin: 0;
            color: #2e7d32;
        }

        #logo {
            display: block;
            margin: 20px auto;
            height: 100px;
            width: 250px;
        }

        .container {
            width: 80%;
            margin: 50px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            text-align: center;
        }

        .profile {
            width: 150px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 2px solid #2e7d32;
        }

        .username {
            font-weight: bold;
            margin-bottom: 20px;
            color: #1b5e20;
            font-size: 18px;
        }

        .logout-button {
            background-color: #d32f2f;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            position: absolute;
            right: 30px;
            top: 30px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .logout-button:hover {
            background-color: #9a0007;
        }

        h2 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 30px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        label {
            font-weight: bold;
            margin-bottom: 8px;
            color: #33691e;
        }

        input[type="password"] {
            padding: 12px;
            font-size: 16px;
            border: 1px solid #a5d6a7;
            border-radius: 6px;
        }

        input[type="password"]:focus {
            border-color: #2e7d32;
            outline: none;
        }

        button[type="submit"] {
            background-color: #2e7d32;
            color: white;
            font-weight: bold;
            padding: 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #1b5e20;
        }
    </style>
</head>
<body>

    <div id="logo">
        <img src="img/tlogo.jpg" alt="School Logo" class="logo">
    </div>

    <h2><?php echo $_SESSION['selected_class'] . " - " . $_SESSION['selected_session'] . " - " . $_SESSION['selected_term']; ?></h2>

    <div class="container">
        <img src="uploads/<?php echo htmlspecialchars($image); ?>" alt="Profile Picture" class="profile">
        <p class="username">WELCOME: <?php echo htmlspecialchars($username); ?></p>

        <button class="logout-button" onclick="logout()">Logout</button>

        <form action="password_fetchers.php" method="post">
            <label>Enter Your Pincode:</label>
            <input type="password" name="exam_password" required>

            <!-- Send selected_class as POST to password_fetch.php -->
            <input type="hidden" name="selected_class" value="<?php echo htmlspecialchars($_SESSION['selected_class']); ?>">

            <button type="submit">View Result</button>
        </form>
    </div>

    <script>
        function logout() {
            window.location.href = "login.html";
        }
    </script>

</body>
</html>