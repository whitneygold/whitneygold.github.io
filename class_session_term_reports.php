<?php
session_start(); 
if (!isset($_SESSION['username'])) {    
    header("Location: login.html");    
    exit;
} 
$username = $_SESSION['username'];
$image = $_SESSION['image'] ?? ''; 

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'school_report';
$conn = new mysqli($host, $user, $password, $database); 
if ($conn->connect_error) {    
    die("Connection failed: " . $conn->connect_error);
}   

$class = $_SESSION['selected_class'];  
$session = $_SESSION['selected_subject']; 
$term = $_SESSION['selected_exam_type']; 

$table1 = strtolower(preg_replace('/[^a-z0-9_]/i', '_', "{$session}_{$class}_{$term}_student_reports")); 
$table2 = strtolower(preg_replace('/[^a-z0-9_]/i', '_', "{$session}_{$class}_{$term}_records")); 

$reportSql = "SELECT * FROM `$table1`  WHERE username = ?";
$reportStmt = $conn->prepare($reportSql);
$reportStmt->bind_param("s", $username);
$reportStmt->execute();
$reportResult = $reportStmt->get_result(); 

$scoreSql = "SELECT * FROM `$table2` WHERE username = ?";
$scoreStmt = $conn->prepare($scoreSql);
$scoreStmt->bind_param("s", $username);
$scoreStmt->execute();
$scoreResult = $scoreStmt->get_result(); 
?> 

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>cbt.petoaschools.org</title>
<link rel="icon" type="image/x-icon" href="fav_icon2.ico">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
 /*body {
    font-family: Arial, sans-serif;
    margin: 10px;
    font-size: 10px; /* smaller font globally */
  }*/
       body {
            font-family: Arial, sans-serif;
            margin: 40px;
            position: relative;
            z-index: 1;
            background-color: lime;
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
            background-color: lime;
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
    font-size: 9px; /* smaller for table */
  }
  th, td {
    border: 1px solid #999;
    padding: 3px; /* reduced padding */
    text-align: center;
  }
  th {
    background: #006400;
    color: white;
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
    border: 1px solid #ccc;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 5px;
  }
  @media print {
    body { font-size: 9px; margin: 5px; }
    table { font-size: 8px; }
    th, td { padding: 2px; }
  }
</style>
<div id="logout-container">
            <button id="logout" onclick="logout()" style="background-color: red; color: lime;">Logout</button>
        </div>
</head>
<body ><!--oncontextmenu="return false;"-->

<div class="logo">
    <img src="img/templogo.jpg" alt="School Logo">
    <h5>TERMINAL PROGRESS REPORT FOR SECONDARY SCHOOLS</h5>
    <img id="student-image" src="uploads/<?php echo $image; ?>" alt="Profile Picture">
            <h2>Welcome, <?php echo $username; ?>.</h2>
  
    <?php if (isset($_SESSION['selected_class'], $_SESSION['selected_subject'], $_SESSION['selected_exam_type'])): ?>
        <h3><?= strtoupper($_SESSION['selected_class'] . ' - ' . $_SESSION['selected_subject'] . ' (' . $_SESSION['selected_exam_type'] . ')') ?>  REPORT SHEET</h3>
    <?php endif; ?>
</div>

<?php if ($reportResult->num_rows > 0): $reportRow = $reportResult->fetch_assoc(); ?>
    <div class="report-box">
        <h3>Student Basic Information</h3>
        <table>
            <tr>
                <td>Full Name:</td><td><?= $reportRow['username'] ?></td>
                <td>Class:</td><td><?= $reportRow['class'] ?></td>
                <td>Term:</td><td><?= $reportRow['term'] ?></td>
            </tr>
            <tr>
                <td>Session:</td><td><?= $reportRow['year_session'] ?></td>
                <td>Hostel:</td><td><?= $reportRow['hostel'] ?></td>
                <td>Attendance:</td><td><?= $reportRow['attendance'] ?></td>
            </tr>
            <tr>
                <td>Out Of:</td><td><?= $reportRow['outof'] ?></td>
                <td>Number in Class:</td><td><?= $reportRow['number_in_class'] ?></td>
                <td></td><td></td>
            </tr>
        </table>
    </div>
<?php endif; ?>

<?php if ($scoreResult->num_rows > 0): $scoreRow = $scoreResult->fetch_assoc(); ?>
    <div class="report-box">
        <h3>Subject Scores</h3>
        <table >
            <tr>
                <th>Subject</th>
                <th>CA1</th>
                <th>CA2</th>
                <th>Assignment</th>
                <th>Exam</th>
                <th>Total</th>
                <th>Cumm_BF</th>
                <th>Cumm_Score</th>
                <th>Class Average</th>
                <th>Position</th>
                <th>Grade</th>
                <th>Remarks</th>
            </tr>
            <?php
            $subjects = ['mathematics', 'english_language', 'basic_science', 'home_economics', 'phe', 'basic_technology', 'computer_studies', 'music', 'crs', 'social_studies', 'yoruba', 'civic_education', 'business_studies', 'diction', 'agricultural_science'];
            foreach ($subjects as $subject) {
                echo "<tr>";
                echo "<td>" . ucfirst(str_replace("_", " ", $subject)) . "</td>";
                echo "<td>" . ($scoreRow[$subject . '_ca1'] ?? '') . "</td>";
                echo "<td>" . ($scoreRow[$subject . '_ca2'] ?? '') . "</td>";
                echo "<td>" . ($scoreRow[$subject . '_ass'] ?? '') . "</td>";
                echo "<td>" . ($scoreRow[$subject . '_exam'] ?? '') . "</td>";
                echo "<td>" . ($scoreRow[$subject . '_total'] ?? '') . "</td>";
                echo "<td>" . ($scoreRow[$subject . '_cumulative_bf'] ?? '') . "</td>";
                echo "<td>" . ($scoreRow[$subject . '_cumulative_score'] ?? '') . "</td>";
                echo "<td>" . ($scoreRow[$subject . '_class_average'] ?? '') . "</td>";
                echo "<td>" . ($scoreRow[$subject . '_postition'] ?? '') . "</td>";
                echo "<td>" . ($scoreRow[$subject . '_grade'] ?? '') . "</td>";
                echo "<td>" . ($scoreRow[$subject . '_remarks'] ?? '') . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
<?php endif; ?>

<?php if (isset($reportRow)): ?>
    <div class="report-box">
        <h3>Character Development & Practical Skills</h3>
        <table>
            <tr>
                <td>Punctuality</td><td><?= $reportRow['punctuality'] ?></td>
                <td>Attentiveness</td><td><?= $reportRow['attentiveness'] ?></td>
                <td>Neatness</td><td><?= $reportRow['neatness'] ?></td>
                <td>Honesty</td><td><?= $reportRow['honesty'] ?></td>
            </tr>
            <tr>
                <td>Relationship</td><td><?= $reportRow['relationship'] ?></td>
                <td>Skill</td><td><?= $reportRow['skill'] ?></td>
                <td>Sport</td><td><?= $reportRow['sport'] ?></td>
                <td>Club</td><td><?= $reportRow['club'] ?></td>
            </tr>
            <tr>
                <td>Fluency</td><td><?= $reportRow['fluency'] ?></td>
                <td>Handwriting</td><td><?= $reportRow['handwriting'] ?></td>
                <td></td><td></td><td></td><td></td>
            </tr>
        </table>
    </div>

    <div class="report-box">
        <h3>Remarks & Signatures</h3>
        <table>
          <tr>
                <td>Teacher's Remark</td><td><?= $reportRow['teacher_remark'] ?></td>
                <td style="color: red; font-family: Arial, sans-serif;">Signature / Date</td><td><img src="img/principal_signature1.png" alt="teachersig"></td>
            </tr>
            <tr>
                <td>Headmaster's Remark</td><td><?= $reportRow['h_master_remark'] ?></td>
                <td>Signature / Date</td><td><img src="img/principal_signature1.png" alt="sig"></td>
            </tr>
            <tr>
                <td>Principal's Remark</td><td><?= $reportRow['principal_remark'] ?></td>
                <td>Signature / Date</td><td><img src="img/principal_signature1.png" alt="sig"></td>
            </tr>
             <tr>
                <td>Next Term ::</td><td><?= $reportRow['next_term'] ?></td>
                <td>Signature / Date</td><td><img src="img/principal_signature1.png" alt="sig"></td>
            </tr>
            <tr>
                <td>Promoted To:</td><td><?= $reportRow['promoted_to'] ?></td>
                <td>Signature / Date</td><td> <img src="img/principal_signature1.png" alt="sig"></td>
            </tr>
        </table>
    </div>
<?php endif; ?>
<div class="btn-group">
    <button onclick="window.print()" style="color: white; background-color: rgb(50,125,50);">🖨️ Print</button>
    <button onclick="downloadPDF()" style="color: white; background-color: rgb(50,125,50);">⬇️ Download PDF</button>
</div>
  <div class="windows-activate">
            <center>
              <h3><span style="color: lime;">&copy www.</span><span style="color: red;">petoaschools.</span><span style="color: red;">org</span></h3>
            </center>
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



document.addEventListener("DOMContentLoaded", function () {
  // Subject table color rules
  const subjectTable = document.querySelector("#subjectScores table");
  if (subjectTable) {
    subjectTable.querySelectorAll("tr").forEach((row, idx) => {
      if (idx === 0) return; // skip header
      let cells = row.querySelectorAll("td");
      [1,2,3].forEach(i => {
        let val = parseFloat(cells[i].innerText);
        if (!isNaN(val)) {
          if (val < 5) cells[i].style.color = "red";
          else if (val >= 5 && val <= 10) cells[i].style.color = "blue";
        }
      });
      let examVal = parseFloat(cells[4].innerText);
      if (!isNaN(examVal) && examVal < 20) cells[4].style.color = "red";
      let totalVal = parseFloat(cells[5].innerText);
      if (!isNaN(totalVal) && totalVal < 40) cells[5].style.color = "red";
      let gradeText = cells[10].innerText.trim().toUpperCase();
      if (gradeText === "E") cells[10].style.color = "red";
      let remarkText = cells[11].innerText.trim().toLowerCase();
      if (remarkText.includes("poor")) cells[11].style.color = "red";
    });
  }
  // Character table color rules
  const charTable = document.querySelector("#characterTable table");
  if (charTable) {
    charTable.querySelectorAll("td:nth-child(even)").forEach(cell => {
      let val = parseFloat(cell.innerText);
      if (!isNaN(val)) {
        if (val < 3) cell.style.color = "red";
        else cell.style.color = "blue";
      }
    });
  }
});




 function colorScores() {
        let tables = document.querySelectorAll("table");

        tables.forEach(table => {
            for (let i = 1; i < table.rows.length; i++) {
                let cells = table.rows[i].cells;

                // ✅ CA1, CA2, Assignment (index 1,2,3)
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

                // ✅ Exam (index 4)
                if (cells[4]) {
                    let examVal = parseInt(cells[4].innerText);
                    if (!isNaN(examVal) && examVal < 20) {
                        cells[4].style.color = "red";
                    }
                }

                // ✅ Total (index 5)
                if (cells[5]) {
                    let totalVal = parseInt(cells[5].innerText);
                    if (!isNaN(totalVal) && totalVal < 40) {
                        cells[5].style.color = "red";
                    }
                }

                // ✅ Grade (index 10)
                if (cells[10]) {
                    let gradeText = cells[10].innerText.trim().toUpperCase();
                    if (gradeText === "E") {
                        cells[10].style.color = "red";
                    }
                }

                // ✅ Remarks (index 11)
                if (cells[11]) {
                    let remarkText = cells[11].innerText.trim().toLowerCase();
                    if (remarkText.includes("poor")) {
                        cells[11].style.color = "red";
                    }
                }
            }
        });
    }

      const characterTable = document.querySelector("#characterTable"); // Give second table an id="characterTable"
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
    }

    // 3️⃣ Development table (same rule as character table)
    const developmentTable = document.querySelector("#developmentTable"); // Give third table an id="developmentTable"
    if (developmentTable) {
        developmentTable.querySelectorAll("tr td:nth-child(2)").forEach(cell => {
            let value = parseFloat(cell.innerText);
            if (!isNaN(value)) {
                if (value < 3) {
                    cell.style.color = "red";
                } else {
                    cell.style.color = "blue";
                }
            }
        });
    }


    // Run when page loads
    window.onload = colorScores;


</script>
</body>
</html>