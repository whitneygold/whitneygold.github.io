<?php
session_start();
$_SESSION['selected_class'] = $_POST['class'];
$_SESSION['selected_subject'] = $_POST['subject'];
$_SESSION['selected_exam_type'] = $_POST['exam_type'];
$image = $_SESSION['image'];


?>

<!DOCTYPE html>
<html>
<head>
    <title>Enter Password</title>
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

        .dashboard-container {
            max-width: 600px;
            margin: 0 auto;
            text-align: left;
            background-color: #f1f8e9;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .logout {
            text-align: right;
            margin-bottom: 20px;
        }

        .logout a {
            color: #d32f2f;
            font-weight: bold;
            text-decoration: none;
        }

        h1, h2 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 30px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 8px;
            color: #33691e;
        }

        select, input[type="password"] {
            padding: 12px;
            font-size: 16px;
            border: 1px solid #a5d6a7;
            border-radius: 6px;
            transition: border-color 0.3s ease;
        }

        select:focus, input[type="password"]:focus {
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

        footer {
            margin-top: 30px;
            text-align: center;
            color: #558b2f;
            font-weight: bold;
        }
    </style>
</head>
<body>
     <div id="logo">
                <img src="img/templogo.jpg" alt="School Logo" class="logo">
            </div>   
         
    <h2><?php echo $_SESSION['selected_class'] . " - " . $_SESSION['selected_subject'] . " - " . $_SESSION['selected_exam_type'] ; ?></h2>
     <div class="container">
        <img src="uploads/<?php echo $image; ?>" alt="Profile Picture" class="profile">
        <p class="username">WELCOME: <?php echo $_SESSION['username']; ?></p>
         <button class="logout-button" onclick="logout()">Logout</button>
            <form action="password_fetch.php" method="post">
                <label>Enter Exam Password:</label>
                <input type="password" name="exam_password" required>
                 <button type="submit">Start Exam</button>
            </form>
    </div>   
    <script>
         function logout() {
            window.location.href = "login.html";
        }
    
    </script>
        
</body>
</html>