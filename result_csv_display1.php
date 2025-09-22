<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit;
}

$username = $_SESSION['username'];
$image = $_SESSION['image'] ?? '';

// Load selected values
$class = $_SESSION['selected_class'] ?? '';
$session = $_SESSION['selected_session'] ?? '';
$term = $_SESSION['selected_term'] ?? '';

// Create safe CSV table names
$table1 = strtolower(preg_replace('/[^a-z0-9_]/i', '_', "{$session}_{$class}_{$term}_student_reports")) . ".csv";
$table2 = strtolower(preg_replace('/[^a-z0-9_]/i', '_', "{$session}_{$class}_{$term}_records")) . ".csv";
$table3 = strtolower(preg_replace('/[^a-z0-9_]/i', '_', "{$session}_{$class}_{$term}_character_remarks")) . ".csv";

// Handle POST for selecting session/class/term
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class = $_POST['selected_class'] ?? '';
    $session = $_POST['selected_session'] ?? '';
    $term = $_POST['selected_term'] ?? '';

    if ($class && $session && $term) {
        $_SESSION['selected_class'] = $class;
        $_SESSION['selected_session'] = $session;
        $_SESSION['selected_term'] = $term;

        header("Location: result_csv_display.php");
        exit();
    } else {
        echo "Please select all fields.";
    }
}

// CSV Loader
function loadCSVData($filename) {
    $rows = [];
    $filepath = "results/$filename";

    if (file_exists($filepath) && ($handle = fopen($filepath, "r")) !== false) {
        $headers = fgetcsv($handle);
        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = array_combine($headers, $data);
        }
        fclose($handle);
    }
    return $rows;
}

// Load CSV data using properly formed filenames
$bioData = loadCSVData($table1);
$academicData = loadCSVData($table2);
$characterData = loadCSVData($table3);

// Define subjects
$subjects = [
    'mathematics', 'english_language', 'basic_science', 'basic_technology',
    'home_economics', 'phe', 'computer_studies', 'music', 'crs',
    'social_studies', 'yoruba', 'civic_education', 'business_studies', 'diction'
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Report Sheet</title>
    <link rel="stylesheet" href="style.css">
<!--<style>
         body {
            font-family: Arial, sans-serif;
            margin: 40px;
            position: relative;
            z-index: 1;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('src/templogo.jpg') no-repeat center center;
            background-size: 300px 300px;
            opacity: 0.1;
            z-index: 0;
            pointer-events: none;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            height: 100px;
        }
        h2, h3 {
            color: #006400;
            margin-top: 20px;
        }
        .side-by-side {
            display: flex;
            justify-content: space-between;
        }
        .table-small {
            width: 48%;
        }
        table {
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
         .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            height: 100px;
        }
          #student-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: block;
            margin: 0 auto;
            margin-bottom: 10px;
        }

       
        #welcome-message {
            color: red; /* Make text red */
            font-size: 24px; /* Increase size for emphasis */
            font-weight: bold; /* Make it bold */
        }
    </style>-->
      <style>
        :root {
            --bg-color: rgb(250,200,250);
            --text-color: black;
            --table-border: #999;
            --th-bg: #006400;
            --th-color: white;
            --report-box-border: #ccc;
            --button-bg: rgb(50,125,50);
            --button-color: white;
        }

        body.dark-theme {
            --bg-color: #121212;
            --text-color: #f0f0f0;
            --table-border: #555;
            --th-bg: #003300;
            --th-color: #ddd;
            --report-box-border: #444;
            --button-bg: #2d5a2d;
            --button-color: #ddd;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            position: relative;
            z-index: 1;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s, color 0.3s;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('img/templogo.jpg') no-repeat center center;
            background-size: 300px 300px;
            opacity: 0.1;
            z-index: 0;
            pointer-events: none;
            background-color: var(--bg-color);
            transition: background-color 0.3s;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            height: 100px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        th, td {
            border: 1px solid var(--table-border);
            padding: 3px;
            text-align: center;
            transition: border-color 0.3s;
        }

        th {
            background: var(--th-bg);
            color: var(--th-color);
            transition: background-color 0.3s, color 0.3s;
        }

        h2, h3 {
            font-size: 12px;
            margin: 2px 0;
        }

        #student-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
        }

        .report-box {
            border: 1px solid var(--report-box-border);
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        .theme-toggle {
            position: fixed;
            top: 10px;
            right: 10px;
            background: var(--th-bg);
            color: var(--th-color);
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            z-index: 1000;
            transition: background-color 0.3s, color 0.3s;
        }

        .btn-group button {
            color: var(--button-color);
            background-color: var(--button-bg);
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }

        #logout {
            background-color: red;
            color: lime;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        @media print {
            body {
                font-size: 9px;
                margin: 5px;
                background-color: white;
                color: black;
            }
           
            body::before {
                opacity: 0.05;
                background-color: white;
            }
           
            table {
                font-size: 8px;
            }
           
            th, td {
                padding: 2px;
            }
           
            .theme-toggle {
                display: none;
            }
        }
           .side-by-side {
            display: flex;
            justify-content: space-between;
        }
        .table-small {
            width: 48%;
        }

    </style>
    </head>
<body>
    <button class="theme-toggle" id="themeToggle">🌙 Dark Mode</button>
   
    <div id="logout-container">
        <button id="logout" onclick="logout()">Logout</button>
    </div>

    <div class="logo">
        <img src="img/templogo.jpg" alt="School Logo">
        <h5>TERMINAL PROGRESS REPORT FOR SECONDARY SCHOOLS</h5>
        <img id="student-image" src="uploads/<?php echo $image; ?>" alt="Profile Picture">
        <h2>Welcome, <?php echo $username; ?>.</h2>
        <?php if (isset($_SESSION['selected_class'], $_SESSION['selected_subject'], $_SESSION['selected_exam_type'])): ?>
            <h3><?= strtoupper($_SESSION['selected_class'] . ' - ' . $_SESSION['selected_subject'] . ' (' . $_SESSION['selected_exam_type'] . ')') ?> REPORT SHEET</h3>
        <?php endif; ?>
    </div>
    <!--<div class="report-container">-->
    <div class="report-box">    
        <h2>STUDENT REPORT SHEET</h2>

        <!-- BIO DATA Tables Side-by-Side -->
        <?php
        foreach ($bioData as $row) {
            if (strcasecmp($row['username'], $username) === 0) {
                echo "<div class='side-by-side'>";
                echo "<table class='table-small'>";
                echo "<tr><th>Student's Name:</th><td>{$row['username']}</td></tr>";
                echo "<tr><th>Class</th><td>{$row['class']}</td></tr>";
                echo "<tr><th>Term</th><td>{$row['term']}</td></tr>";
                echo "<tr><th>Session</th><td>{$row['year_session']}</td></tr>";
                echo "</table>";

                echo "<table class='table-small'>";
               
                echo "<tr><th>Hostel</th><td>{$row['hostel']}</td></tr>";
                echo "<tr><th>Attendance </th><td>{$row['attendance']}</td></tr>";
                 echo "<tr><th>Out Of </th><td>{$row['outof']}</td></tr>";
                echo "<tr><th>Number in Class</th><td>{$row['number_in_class']}</td></tr>";
                echo "</table>";
                echo "</div>";
                break;
            }
        }
        ?>

        <hr>
          <hr>
    <div class="report-box">
        <!-- Academic Scores Section -->
        <h3>Academic Scores</h3>
        <table class='score-table'>
            <tr>
                <th>Subject</th>
                <th>CA1</th>
                <th>CA2</th>
                <th>Assignment</th>
                <th>Exam</th>
                <th>Total</th>
                <th>Cum_BF</th>
                <th>Cum_score</th>
                <th>Class Average</th>
                <th>Position</th>
                <th>Grade</th>
                <th>Remark</th>
            </tr>
            <?php
            $found = false;
            foreach ($academicData as $row) {
                if (strcasecmp($row['username'], $username) === 0) {
                    $found = true;
                    foreach ($subjects as $subject) {
                        echo "<tr>";
                        echo "<td>" . ucwords(str_replace("_", " ", $subject)) . "</td>";
                        echo "<td>" . htmlspecialchars($row[$subject . '_ca1']) . "</td>";
                        echo "<td>" . htmlspecialchars($row[$subject . '_ca2']) . "</td>";
                        echo "<td>" . htmlspecialchars($row[$subject . '_ass']) . "</td>";
                        echo "<td>" . htmlspecialchars($row[$subject . '_exam']) . "</td>";
                        echo "<td>" . htmlspecialchars($row[$subject . '_total']) . "</td>";
                        echo "<td>" . htmlspecialchars($row[$subject . '_cumulative_bf']) . "</td>";
                        echo "<td>" . htmlspecialchars($row[$subject . '_cumulative_score']) . "</td>";
                        echo "<td>" . htmlspecialchars($row[$subject . '_class_average']) . "</td>";
                        echo "<td>" . htmlspecialchars($row[$subject . '_postition']) . "</td>";
                        echo "<td>" . htmlspecialchars($row[$subject . '_grade']) . "</td>";
                        echo "<td>" . htmlspecialchars($row[$subject . '_remarks']) . "</td>";
                        echo "</tr>";
                    }
                    break;
                }
            }
            if (!$found) echo "<tr><td colspan='9'>No subject data found for $username</td></tr>";
            ?>
        </table>
</div>
        <hr>


        <!-- Character and Practical Skill Tables -->
        <div class="side-by-side">
            <!-- Character Development Table -->
            <table class="table-small">
                <tr><th>Category</th><th>Rating</th></tr>
                <?php
                foreach ($characterData as $row) {
                    if (strcasecmp($row['username'], $username) === 0) {
                        $character_keys = ['punctuality', 'attentiveness', 'neatness', 'honesty', 'relationship'];
                        foreach ($character_keys as $key) {
                            echo "<tr><td>" . ucwords($key) . "</td><td>{$row[$key]}</td></tr>";
                        }
                        break;
                    }
                }
                ?>
            </table>

            <!-- Practical Skill Table -->
            <table class="table-small">
                <tr><th>Category</th><th>Rating</th></tr>
                <?php
                foreach ($characterData as $row) {
                    if (strcasecmp($row['username'], $username) === 0) {
                        $skill_keys = ['sport', 'club', 'fluency', 'handwriting'];
                        foreach ($skill_keys as $key) {
                            echo "<tr><td>" . ucwords($key) . "</td><td>{$row[$key]}</td></tr>";
                        }
                        break;
                    }
                }
                ?>
            </table>
        </div>

        <hr>

        <!-- Remarks Table -->
        <h3>Remarks</h3>
        <table>
            <tr><th>Category</th><th>Remarks</th><th>Signature/Date</th></tr>
            <?php
            $remarks = [
                'Teacher Remark' => $row['teacher_remark'] ?? '',
                'House masters Remark' => $row['h_master_remark'] ?? '',
                'Principal Remark' => $row['principal_remark'] ?? '',
                'Next Term' => $row['next_term'] ?? '',
                'Promoted To' => $row['promoted_to'] ?? ''
            ];
            foreach ($remarks as $label => $value) {
                echo "<tr>";
                echo "<td>$label</td>";
                echo "<td>$value</td>";
                echo "<td>........................ / ....................</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
    <div class="btn-group">
    <button onclick="window.print()">🖨️ Print</button>
    <button onclick="downloadPDF()">⬇️ Download PDF</button>
</div>
<script>
    function downloadPDF() {
        import('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js').then(jsPDF => {
            const { jsPDF: PDF } = jsPDF;
            const doc = new PDF();
            doc.html(document.getElementById('report-content'), {
                callback: function (pdf) {
                    pdf.save("student_report.pdf");
                },
                x: 10,
                y: 10
            });
        });
    }

         function logout() {
        fetch('logout.php', { method: 'POST' }) // Call actual logout script
            .then(() => {
                localStorage.removeItem('username'); // Clear localStorage on logout
                window.location.href = "logout.php";
            })
            .catch(error => console.error('Logout error:', error));
    }

      const themeToggle = document.getElementById('themeToggle');
        const body = document.body;

        // Check for saved theme preference or respect OS preference
        if (localStorage.getItem('theme') === 'dark' ||
            (window.matchMedia('(prefers-color-scheme: dark)').matches && !localStorage.getItem('theme'))) {
            body.classList.add('dark-theme');
            themeToggle.textContent = '☀️ Light Mode';
        } else {
            body.classList.remove('dark-theme');
            themeToggle.textContent = '🌙 Dark Mode';
        }

        // Theme toggle button event listener
        themeToggle.addEventListener('click', () => {
            if (body.classList.contains('dark-theme')) {
                body.classList.remove('dark-theme');
                localStorage.setItem('theme', 'light');
                themeToggle.textContent = '🌙 Dark Mode';
            } else {
                body.classList.add('dark-theme');
                localStorage.setItem('theme', 'dark');
                themeToggle.textContent = '☀️ Light Mode';
            }
        });

        // Modified function to color scores based on table ID
        function colorScores(tableId) {
            let table = document.getElementById(tableId);
            if (!table) return;
           
            for (let i = 1; i < table.rows.length; i++) {
                let cells = table.rows[i].cells;
               
                // CA1, CA2, Assignment (index 1,2,3)
                [1, 2, 3].forEach(idx => {
                    if (cells[idx]) {
                        let value = parseInt(cells[idx].innerText);
                        if (!isNaN(value)) {
                            if (value < 5) {
                                cells[idx].style.color = "red";
                            } else if (value >= 5 && value <= 10) {
                                cells[idx].style.color = "blue";
                            } else if (value > 10) {
                                cells[idx].style.color = "green";
                            }
                        }
                    }
                });
               
                // Exam (index 4)
                if (cells[4]) {
                    let examVal = parseInt(cells[4].innerText);
                    if (!isNaN(examVal) && examVal < 20) {
                        cells[4].style.color = "red";
                    }
                }
               
                // Total (index 5)
                if (cells[5]) {
                    let totalVal = parseInt(cells[5].innerText);
                    if (!isNaN(totalVal) && totalVal < 40) {
                        cells[5].style.color = "red";
                    }
                }
               
                // Grade (index 10)
                if (cells[10]) {
                    let gradeText = cells[10].innerText.trim().toUpperCase();
                    if (gradeText === "E") {
                        cells[10].style.color = "red";
                    }
                }
               
                // Remarks (index 11)
                if (cells[11]) {
                    let remarkText = cells[11].innerText.trim().toLowerCase();
                    if (remarkText.includes("poor")) {
                        cells[11].style.color = "red";
                    }
                }
            }
        }

        // Run when page loads
        window.onload = function() {
            colorScores('subject_table'); // Apply coloring only to the subject table

        };

    /*const characterTable = document.querySelector("character_table"); // Give second table an id="characterTable"
                if (characterTable) {
                    characterTable.querySelectorAll("tr td:nth-child(2)").forEach(cell => {
                        let value = parseFloat(cell.innerText);
                        if (!isNaN(value)) {
                            if (value < 3) {
                                cell.style.color = "red";
                            } else {
                                cell.style.color = "blue";
                            }
                        }
                    });
    }*/
</script>
</body>
</html>