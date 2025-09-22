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
$table4 = strtolower(preg_replace('/[^a-z0-9_]/i', '_', "{$session}_{$class}_{$term}_rating_key")) . ".csv";

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

// CSV Loader function
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
$ratingKey = loadCSVData($table4);

// Define subjects
$subjects = [
    'mathematics', 'english_language', 'basic_science', 'basic_technology',
    'home_economics', 'phe', 'computer_studies', 'music', 'crs', 'social_studies',
    'yoruba', 'civic_education', 'business_studies', 'diction'
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Report Sheet</title>
    <link rel="stylesheet" href="style.css">
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
            margin: 20px;
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
            margin-bottom: 10px;
        }
       
        th, td {
            border: 1px solid var(--table-border);
            padding: 4px;
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
            gap: 10px;
        }
       
        .table-small {
            width: 48%;
        }
       
        /* Added styles for rating key tables */
        .rating-key-table {
            width: 100%;
            margin-top: 10px;
            font-size: 8px;
        }
       
        .rating-key-table th {
            background-color: #f0f0f0;
            color: black;
        }
       
        .rating-key-table td {
            padding: 2px;
        }
       
        /* Color for character development ratings */
        .low-score {
            color: red;
            font-weight: bold;
        }
       
        /* Unified rating key container */
        .unified-rating-key {
            margin-top: 15px;
            border-top: 2px solid #006400;
            padding-top: 10px;
        }
       
        /* Subject score table adjustments */
        .score-table td:nth-child(2),
        .score-table td:nth-child(3),
        .score-table td:nth-child(4),
        .score-table td:nth-child(5),
        .score-table td:nth-child(6),
        .score-table td:nth-child(7),
        .score-table td:nth-child(8),
        .score-table td:nth-child(9) {
            font-weight: bold;
        }
       
        /* Adjust column widths for better fit */
        .score-table th:nth-child(1) { width: 15%; }
        .score-table th:nth-child(2) { width: 6%; }
        .score-table th:nth-child(3) { width: 6%; }
        .score-table th:nth-child(4) { width: 9%; }
        .score-table th:nth-child(5) { width: 6%; }
        .score-table th:nth-child(6) { width: 7%; }
        .score-table th:nth-child(7) { width: 8%; }
        .score-table th:nth-child(8) { width: 9%; }
        .score-table th:nth-child(9) { width: 10%; }
        .score-table th:nth-child(10) { width: 7%; }
        .score-table th:nth-child(11) { width: 7%; }
        .score-table th:nth-child(12) { width: 10%; }
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
   
    <div class="report-box">
        <h2>STUDENT REPORT SHEET</h2>
       
        <!-- BIO DATA Tables Side-by-Side -->
        <?php foreach ($bioData as $row) {
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
        } ?>
       
        <hr>
       
        <div class="report-box">
            <!-- Academic Scores Section -->
            <h3>Academic Scores</h3>
            <table class='score-table' id="subjectScores">
                <tr>
                    <th>Subject</th>
                    <th>CA1(10)</th>
                    <th>CA2(10)</th>
                    <th>Assignment(10)</th>
                    <th>Exam(70)</th>
                    <th>Total(100)</th>
                    <th>Cum_BF(100)</th>
                    <th>Cum_score(100)</th>
                    <th>Class Average(100)</th>
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
                if (!$found) echo "<tr><td colspan='12'>No subject data found for $username</td></tr>";
                ?>
            </table>
        </div>
       
        <h6>Letter Grading: 80 and above (A+: Excellent) 70-79 (A: Very Good) 60-69 (B: Good) 50-59 (C: Average) 40-49 (D: B-Average)Below 40 (F: Fail</h6>
        <hr>
       
        <!-- Character and Practical Skill Tables -->
        <div class="side-by-side">
            <!-- Character Development Table -->
            <div class="table-small">
                <table id="characterTable">
                    <tr><th>Character Development</th><th>Rating</th></tr>
                    <?php
                    foreach ($characterData as $row) {
                        if (strcasecmp($row['username'], $username) === 0) {
                            $character_keys = ['punctuality', 'attentiveness', 'neatness', 'honesty', 'relationship'];
                            foreach ($character_keys as $key) {
                                $rating = $row[$key];
                                $ratingClass = ($rating < 3) ? 'low-score' : '';
                                echo "<tr><td>" . ucwords($key) . "</td><td class='$ratingClass'>{$rating}</td></tr>";
                            }
                            break;
                        }
                    }
                    ?>
                </table>
            </div>
           
            <!-- Practical Skill Table -->
            <div class="table-small">
                <table id="practicalTable">
                    <tr><th>Practical Skills</th><th>Rating</th></tr>
                    <?php
                    foreach ($characterData as $row) {
                        if (strcasecmp($row['username'], $username) === 0) {
                            $skill_keys = ['sport', 'club', 'fluency', 'handwriting'];
                            foreach ($skill_keys as $key) {
                                $rating = $row[$key];
                                $ratingClass = ($rating < 3) ? 'low-score' : '';
                                echo "<tr><td>" . ucwords($key) . "</td><td class='$ratingClass'>{$rating}</td></tr>";
                            }
                            break;
                        }
                    }
                    ?>
                </table>
            </div>
        </div>
       
        <!-- Unified Rating Key -->
        <div class="unified-rating-key">
            <h3>Rating Key</h3>
            <table class="rating-key-table">
                <tr><th>Rating</th><th>Description</th></tr>
                <tr><td>5</td><td>Excellent</td></tr>
                <tr><td>4</td><td>Very Good</td></tr>
                <tr><td>3</td><td>Good</td></tr>
                <tr><td>2</td><td>Fair</td></tr>
                <tr><td>1</td><td>Poor</td></tr>
            </table>
        </div>
       
        <hr><br>
       
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
            fetch('logout.php', { method: 'POST' })
            .then(() => {
                localStorage.removeItem('username');
                window.location.href = "logout.php";
            })
            .catch(error => console.error('Logout error:', error));
        }
       
        const themeToggle = document.getElementById('themeToggle');
        const body = document.body;
       
        // Check for saved theme preference or respect OS preference
        if (localStorage.getItem('theme') === 'dark' || (window.matchMedia('(prefers-color-scheme: dark)').matches && !localStorage.getItem('theme'))) {
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
       
        // Color coding for subject scores
        function colorSubjectScores() {
            const table = document.getElementById('subjectScores');
            if (!table) return;
           
            for (let i = 1; i < table.rows.length; i++) {
                const cells = table.rows[i].cells;
               
                // CA1, CA2, Assignment (columns 2,3,4)
                for (let j = 1; j <= 3; j++) {
                    if (cells[j]) {
                        const value = parseFloat(cells[j].innerText);
                        if (!isNaN(value) && value < 5) {
                            cells[j].classList.add('low-score');
                        }
                    }
                }
               
                // Exam (column 5)
                if (cells[4]) {
                    const value = parseFloat(cells[4].innerText);
                    if (!isNaN(value) && value < 40) {
                        cells[4].classList.add('low-score');
                    }
                }
               
                // Total, Cum_BF, Cum_score, Class Average (columns 6,7,8,9)
                for (let j = 5; j <= 8; j++) {
                    if (cells[j]) {
                        const value = parseFloat(cells[j].innerText);
                        if (!isNaN(value) && value < 40) {
                            cells[j].classList.add('low-score');
                        }
                    }
                }
            }
        }
       
        // Color coding for character and practical ratings
        function colorRatings() {
            // Character development table
            const charTable = document.getElementById('characterTable');
            if (charTable) {
                for (let i = 1; i < charTable.rows.length; i++) {
                    const cell = charTable.rows[i].cells[1];
                    const value = parseFloat(cell.innerText);
                    if (!isNaN(value) && value < 3) {
                        cell.classList.add('low-score');
                    }
                }
            }
           
            // Practical skills table
            const practicalTable = document.getElementById('practicalTable');
            if (practicalTable) {
                for (let i = 1; i < practicalTable.rows.length; i++) {
                    const cell = practicalTable.rows[i].cells[1];
                    const value = parseFloat(cell.innerText);
                    if (!isNaN(value) && value < 3) {
                        cell.classList.add('low-score');
                    }
                }
            }
        }
       
        // Run when page loads
        window.onload = function() {
            colorSubjectScores();
            colorRatings();
        };
    </script>
</body>
</html>