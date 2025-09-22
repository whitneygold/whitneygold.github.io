<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit;
}

$username = $_SESSION['username'];
$image = $_SESSION['image'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class = $_POST['selected_class'] ?? '';
    $session = $_POST['selected_session'] ?? '';
    $term = $_POST['selected_term'] ?? '';

    if ($class && $session && $term) {
        $_SESSION['selected_class'] = $class;
        $_SESSION['selected_session'] = $session;
        $_SESSION['selected_term'] = $term;

        header("Location: class_session_term_report.php");
        exit();
    } else {
        echo "Please select all fields.";
    }
} 

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/x-icon" href="fav_icon2.ico">
    <title>www.college.org</title>
    <link rel="stylesheet" href="examstyle.css"> <!-- Your custom CSS file -->
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
    <div id="backModalOverlay" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.8); color: #fff; font-family: Arial, sans-serif; justify-content: center; align-items: center; flex-direction: column;">
                <div id="backModal" style="background: #222; padding: 30px; border-radius: 10px; text-align: center; width: 90%; max-width: 400px;">
                    <p style="font-size: 18px; margin: 0 0 10px;">Do you really want to go back?</p>

                    <div id="passwordContainer" style="display: none;">
                        <p style="margin-top: 10px;">Enter password to go back:</p>
                        <input type="password" id="backPassword" placeholder="Enter password"
                            style="padding: 10px; width: 80%; margin-top: 10px; border-radius: 5px; border: none; font-size: 16px;">
                    </div>

                    <div id="modalButtons" style="margin-top: 20px;">
                        <button onclick="handleYes()" style="padding: 10px 20px; margin: 5px; border: none; border-radius: 5px; background: limegreen; color: white; font-size: 16px; cursor: pointer;">Yes</button>
                        <button onclick="closeBackModal()" style="padding: 10px 20px; margin: 5px; border: none; border-radius: 5px; background: crimson; color: white; font-size: 16px; cursor: pointer;">No</button>
                    </div>
                </div>
            </div>

            <script>
            let backClickCount = 0;
            let allowBack = false;

            history.pushState(null, null, location.href);

            window.onpopstate = function () {
                if (!allowBack) {
                    backClickCount++;

                    if (backClickCount >= 50) {
                        allowBack = true;
                        alert("You have tried 50 times. Going back now...");
                        history.back();
                        return;
                    }

                    showBackModal();
                }
            };

            function showBackModal() {
                document.getElementById("backModalOverlay").style.display = "flex";
                document.getElementById("passwordContainer").style.display = "none";
                document.getElementById("backPassword").value = "";
               
                document.getElementById("modalButtons").innerHTML = `
                    <button onclick="handleYes()" style="padding: 10px 20px; margin: 5px; border: none; border-radius: 5px; background: limegreen; color: white; font-size: 16px; cursor: pointer;">Yes</button>
                    <button onclick="closeBackModal()" style="padding: 10px 20px; margin: 5px; border: none; border-radius: 5px; background: crimson; color: white; font-size: 16px; cursor: pointer;">No</button>
                `;
            }

            function closeBackModal() {
                document.getElementById("backModalOverlay").style.display = "none";
                history.pushState(null, null, location.href);
            }

            function handleYes() {
                document.getElementById("passwordContainer").style.display = "block";
                document.getElementById("modalButtons").innerHTML = `
                    <button onclick="checkPassword()" style="padding: 10px 20px; margin: 5px; border: none; border-radius: 5px; background: limegreen; color: white; font-size: 16px; cursor: pointer;">Submit</button>
                    <button onclick="closeBackModal()" style="padding: 10px 20px; margin: 5px; border: none; border-radius: 5px; background: crimson; color: white; font-size: 16px; cursor: pointer;">Cancel</button>
                `;
            }

            function checkPassword() {
                const correctPassword = "admin";
                const enteredPassword = document.getElementById("backPassword").value;

                if (enteredPassword === correctPassword) {
                    allowBack = true;
                    document.getElementById("backModalOverlay").style.display = "none";
                    history.back();
                } else {
                    alert("Incorrect password. Staying on this page.");
                    closeBackModal();
                }
            }
            </script>
<body oncontextmenu="return false;">
            <div id="logo">
                <img src="img/logo.jpg" alt="School Logo" class="logo">
            </div>    
    <div class="container">
                <img src="uploads/<?php echo $image; ?>" alt="Profile Picture" class="profile">
                <p class="username">WELCOME: <?php echo $_SESSION['username']; ?></p>
                <button class="logout-button" onclick="logout()">Logout</button>
                <h1>JUNIOR REPORT SHEET DASHBOARD</h1>

                     <div class="dashboard-container">
                    <div class="logout">
                        <a href="logout.php">Logout</a>
                    </div>

                    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
                    <form action="dashboard_password.php" method="post">
                                    <div class="form-group">
                                        <label>Select Class:</label>
                                        <select name="class">
                                            <option>JSS1</option>
                                            <option>JSS2</option>
                                            <option>JSS3</option>
                                        </select><br><br>
                                            </div>
                                    <div class="form-group">
                                        <label>Select Session:</label>
                                        <select name="session">
                                             <option>2024/2025</option>
                                                <option >2025/2026</option>
                                                <option >2026/2027</option>
                                                
                                        </select><br><br>
                                    </div>
                                    <div class="form-group"> 
                                        <label>Select Term
                                         <select name="term">
                                            <option value="First_term">First Term</option>
                                            <option value="Second_term">Second Term</option>
                                            <option value="Third_term">Third Term</option>
                                        </select><br><br>
                                    </div>
                            <button  type="submit">Next</button>
                    </form>
              <footer>
                    <p>&copy; cbt.schoolscollege.org</p>
                </footer>
            </div>
    </div>    
<script>

function logout() {
    window.location.href = "login.html";
}


    </script>
</body>



</html>