<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit;
}

$username = $_SESSION['username'];
$image = $_SESSION['image'];
header("X-Content-Type-Options: nosniff");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="icon" type="image/x-icon" href="fav_icon2.ico">
    <title>cbt.petoacollege.org</title>
       <style>
        body {
            font-family: Arial, sans-serif;
            background-color:purple;
            margin: 0;
            padding: 20px;
            color: lime;
        }
          /* Style for the editor */
            #editor {
              width: 55vw; /* 75% of the viewport width */
              min-height: 200px;
              background-color: lime;
              color: red;
              font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
              font-size: 18px;
              padding: 15px;
              border: 2px solid darkgreen;
              border-radius: 10px;
              margin: 20px auto;
              outline: none;
              overflow-y: auto;
              box-shadow: 0 0 10px rgba(0, 128, 0, 0.5);
            }
        #exam-container {
            max-width: 800px;
            margin: auto;
            background-color: fuchsia;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        #question-text {
            font-size: 18px;
            margin-bottom: 10px;
            color: red;
        }
        textarea {
             width: 96%;
            height: 150px;
            resize: none;
            padding: 10px;
            font-size: 18px;
            color: red;
            background-color: lime;
            border: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
         /* Container styles for centering dropdown etc. */
        .toolbar {
          text-align: center;
          margin: 20px;
        }
        .btn {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-primary {
            background-color: orange;
            color: white;
        }
        .btn-success {
            background-color: purple;
            color: white;
        }
        .btn-cool {
            background-color: lime;
            color: red;
        }
        #header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }
                #student-info {
            text-align: center;  /* Center the text */
            margin: 20px auto;   /* Center the entire section */
            width: fit-content;  /* Adjust width to fit content */
        }

        #student-image {
            display: block;      /* Makes sure it's treated as a block element */
            margin: 0 auto;      /* Center the image */
            width: 80px;         /* Slightly bigger for better visibility */
            height: 80px;
            border-radius: 50%;
        }

        #welcome-message {
            font-size: 16px;
            font-weight: bold;
            text-align: center;  /* Ensure text is centered */
            margin-top: 10px;
        }

        #logo {
            display: block;
            margin: 10px auto;   /* Keep the logo centered */
            height: 100px;
            width: 250px;
        }

        #logo {
            display: block;
            margin: 20px auto;
            height: 100px;
            width: 250px;
            background-color: rgb(128, 200, 128); /* Placeholder color */
        }
        #logout-container {
            text-align: right;
        }
        #logout {
            background-color: red;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
        }
        #timer {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            color: lime;
        }
          #datetime {
            color: gold; /* Set text color to red */
            font-size: 18px;
            font-weight: bold;
        }
         /* Background overlay */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        /* Custom alert box */
        .custom-alert {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            width: 300px;
        }

        /* Alert title */
        .custom-alert h2 {
            margin: 0;
            font-size: 18px;
            color: black;
        }

        /* Buttons */
        .alert-buttons {
            margin-top: 15px;
        }

        .alert-buttons button {
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
            margin: 5px;
        }

        /* OK Button */
        .ok-btn {
            background: green;
            color: white;
        }

        /* Cancel Button */
        .cancel-btn {
            background: red;
            color: white;
        }
        /* Answered/Unanswered Count Box */
       /* Answered/Unanswered Count Box */
        #userAnswerCount {
            position: absolute;
            top: 20px;
            left: 20px; /* Moved to the left */
            background-color: rgba(0, 0, 0, 0.6); /* Transparent black */
            padding: 15px;
            border-radius: 8px;
            color: white;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            width: 200px;
        }
         #answerFeedback {
            position: absolute;
            top: 50%;
            right: 10%;
            transform: translateY(-50%);
            font-size: 20px;
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            width: 250px;
        }
        .correct {
            color: lime;
            border: 2px solid lime;
        }
        .incorrect {
            color: red;
            border: 2px solid red;
        }

        /* Answered/Unanswered Labels */
        #answeredLabel {
            color: lime;
            font-size: 20px;
            font-weight: bold;
        }

        #unansweredLabel {
            color: red;
            font-size: 20px;
            font-weight: bold;
        }

        }
        #question-container {
            margin: 0 auto;
            width: 60%;
        }
        /* Container to hold the moving text */
        .marquee-container {
            width: 100%;
            overflow: hidden;
            white-space: nowrap;
            background: fuchsia;
            padding: 10px 0;
        }

        /* Moving text animation */
        .marquee-text {
            display: inline-block;
            color: white;
            font-size: 24px;
            font-weight: bold;
            animation: marquee 10s linear infinite;
        }

        /* Keyframes for moving text */
        @keyframes marquee {
            from {
                transform: translateX(100%);
            }
            to {
                transform: translateX(-100%);
            }
        }
       
    </style>
    <div id="question-container">
  

</head>
<body oncontextmenu="return false">
    <div class="marquee-container">
        <span class="marquee-text">2024/2025 3RD TERM EXAMINATIONS </span>
    </div>
     
    <!-- Custom Confirmation Alert -->
    <div class="overlay" id="overlay">


        <div class="custom-alert">
            <h2>Hello and how do you do ?</h2>
            <div class="alert-buttons">
                <button class="ok-btn" onclick="confirmAction(true)">FINE</button>
                <button class="cancel-btn" onclick="confirmAction(false)">COOL</button>
            </div>
        </div>
    </div>
<div id="header">
    <div id="student-info">
          <p id="datetime"></p>
        <img id="student-image" src="uploads/<?php echo $image; ?>" alt="Profile Picture">
        <div id="welcome-message"><label id="usernameDisplay"></label></div>
          <h2>HELLO, <?php echo strtoupper($username); ?>.</h2>
          
        <h3><span style = "visibility: hidden; "> ....... </span>JSS1 EXAMINATION CBT  COMPUTER STUDIES </h3>
    </div>
    <div id="logout-container">
        <button id="logout" onclick="logout()">Logout</button>
    </div>

</div>

<div id="logo">
    <!-- Placeholder for the school logo -->
    <img id="school-logo" src="img/templogo.jpg" alt="PETOA COLLEGE CBT PORTAL">

</div>

<div id="question-container">
    <p id="datetime"></p>
    <div id="timer"></div>
    <div id="answerFeedback"></div>
     <p id = "copyright"></p>
    <body oncontextmenu="return false">
        <div id="exam-container">
            <button onclick="showAlert()">Click Me</button>
            <h2><span style="visibility: hidden;"> ......................... </span>COMPUTER JSS1 THEORY EXAM</h2>
            <div id="student-name"></div>
            <p id="question-text"></p>

            <div id="questionImageContainer"></div>
            <!-- Toolbar with the symbol dropdown -->
              <div class="toolbar">
                <select id="symbolDropdown" onchange="handleSymbolChange(this.value)">
                  <option value="">Select Symbol</option>
                  <option value="∆">∆</option>
                  <option value="°">°</option>
                  <option value="√">√</option>
                  <option value="π">π</option>
                  <option value="^`|%">^`|%</option>
                </select>
              </div>

            <!-- Formatting buttons -->
            <div class="format-buttons">
                <button onclick="document.execCommand('bold')">Bold</button>
                <button onclick="document.execCommand('underline')">Underline</button>
                <button onclick="document.execCommand('superscript')">X<sup>2</sup></button>
                <button onclick="document.execCommand('subscript')">Subscript</button>
            </div>

            <!-- Editable div replacing textarea -->
            <div id="editor" class="editor" contenteditable="true">Type your answer here...</div>

            <div class="nav-buttons">
                <button class="btn btn-primary" onclick="previousQuestion()">Previous</button>
                <button class="btn btn-cool" onclick="nextQuestion()">Next</button>
                <button class="btn btn-success" onclick="submitExam()">Submit Exam</button>
            </div>

        </div>
</div>
   
             <div class="marquee-container">
                <span class="marquee-text"> &copy PETOA INTERNATIONAL COLLEGE : IN PURSUIT OF ACADEMIC AND MORAL EXCELLENCE </span>
            </div>
                <!-- Answered/Unanswered Count -->
            <div id="userAnswerCount">
                <p>Questions Answered: <span id="answeredLabel">0</span></p>
                <p>Questions Unanswered: <span id="unansweredLabel">0</span></p>
            </div>


       
    </div>

    <script>
          if (self !== top) {
        top.location = self.location;
        }
          document.addEventListener('contextmenu', function(event) {
            event.preventDefault();
        });

        // Disable common Developer Tools keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            // Prevent F12
            if (event.key === 'F12') {
                event.preventDefault();
            }
            // Prevent Ctrl+Shift+I, Ctrl+Shift+J, and Ctrl+U
            if (event.ctrlKey && (event.shiftKey && (event.key === 'I' || event.key === 'J')) || event.key === 'U') {
                event.preventDefault();
            }
        });
         let currentYear = new Date().getFullYear();
        /*document.getElementById("copyright").innerHTML = "&copy;" + currentYear  + " www.cbt.petoacollege.org";*/
          
          function confirmAction(isConfirmed) {
            document.getElementById("overlay").style.display = "none";
            if (isConfirmed) {
                alert("Yeah that's perfectly fine!");
            } else {
                alert("Wow that's really cool!");
            }
        }
         function logoutx() {
                let confirmation = confirm("Do you want to logout?");
                
                if (confirmation) {
                    window.location.href = "login.html"; // Redirect to login page
                } 
             // If user clicks "Cancel", do nothing and stay on the page
         }
        // Detect if DevTools is open
        let devtoolsOpen = false;
        setInterval(function() {
            const widthThreshold = window.outerWidth - window.innerWidth > 100;
            const heightThreshold = window.outerHeight - window.innerHeight > 100;
            if (widthThreshold || heightThreshold) {
                devtoolsOpen = true;
                alert("Developer Tools are open. Please close them to continue.");
                window.location.href = 'logout.php'; // Redirect or take action
            } else {
                devtoolsOpen = false;
            }
        }, 1000);

        let randomize = false; 
        let questions = [];
        let currentQuestionIndex = 0;
        let userAnswers = [];
        let timer;
        let timerValue ;  // 5 minutes in seconds

         const username = "<?php echo $username; ?>";
        //localStorage.getItem('username');
        //document.getElementById('usernameDisplay').innerText = username;
        $name = username;
        const studentName = document.getElementById('usernameDisplay').innerText = "<?php echo $username; ?>";//prompt("Enter your name:");  // Capture student's name
        
         window.onload = function() {
         
            
            fetchQuestions();
            fetchTimer();
            countUserSelections();
            fetchCorrectAnswer();

              document.getElementById("student-name").innerText = "Student: " + "<?php echo $username; ?>";
              fetch('jss1_computer_randomize_questions.php')
            .then(response => response.json())
            .then(data => {
                randomize = data.randomize; // Set the randomize flag based on server response
                console.log("Randomization is " + (randomize ? "enabled!" : "disabled."));
                fetchQuestions(); // Fetch the questions after getting the randomize setting
            })
            .catch(error => console.error("Error fetching randomize state:", error));


        };

         function startTimer() {
            timer = setInterval(function() {
                let minutes = Math.floor(timerValue / 60);
                let seconds = timerValue % 60;
                document.getElementById('timer').innerText = `Time Left: ${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
                timerValue--;

                if (timerValue < 0) {
                    clearInterval(timer);
                    alert("Time's up! Submitting quiz.");
                    submitExam();
                }
            }, 1000);
        }

         function updateDateTime() {
            let now = new Date();

            // Get day, month, and year
            let day = now.getDate();
            let month = now.toLocaleString('default', { month: 'long' }); // Full month name (e.g., February)
            let year = now.getFullYear();

            // Add ordinal suffix to day (e.g., 1st, 2nd, 3rd, 4th)
            let suffix = "th";
            if (day === 1 || day === 21 || day === 31) suffix = "st";
            else if (day === 2 || day === 22) suffix = "nd";
            else if (day === 3 || day === 23) suffix = "rd";

            // Get time (hours, minutes, seconds)
            let hours = now.getHours();
            let minutes = now.getMinutes();
            let seconds = now.getSeconds();
            let ampm = hours >= 12 ? "PM" : "AM";

            // Convert to 12-hour format
            hours = hours % 12 || 12;

            // Add leading zero to minutes and seconds if needed
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            // Format the date and time
            let formattedDateTime = `${month} ${day}${suffix} ${year}, ${hours}:${minutes}:${seconds} ${ampm}`;

            // Display in the paragraph tag
            document.getElementById("datetime").innerText = formattedDateTime;
        }

        updateDateTime(); // Call function on page load
        setInterval(updateDateTime, 1000); // Update time every second


          function startCountdown() {
            let timerDisplay = document.getElementById("timer");
            let countdown = timervalue;

            const interval = setInterval(() => {
                if (countdown <= 0) {
                    clearInterval(interval);
                    timerDisplay.innerHTML = "Time's up!";
                    submitExam();
                    window.location.href = "login.html";
                } else {
                    // Convert seconds to minutes and seconds
                    let minutes = Math.floor(countdown / 60);
                    let seconds = countdown % 60;

                    // Format seconds to always have two digits
                    seconds = seconds < 10 ? "0" + seconds : seconds;

                    timerDisplay.innerHTML = `REMAINING TIME :  ${minutes} minutes:  ${seconds} seconds`;

                    countdown--;
                }
            }, 1000);
        }

          // Fetch the randomization setting from the server
        function Check_radomization(){
            fetch('jss1_computer_randomize_questions.php')
            .then(response => response.json())
            .then(data => {
                randomize = data.randomize; // Set the randomize flag based on server response
                console.log("Randomization is " + (randomize ? "enabled!" : "disabled."));
                fetchQuestions(); // Fetch the questions after getting the randomize setting
            })
            .catch(error => console.error("Error fetching randomize state:", error));         
        }
        function countUserSelections() {
            let totalQuestions = questions.length;
            let answeredCount = userAnswers.filter(answer => answer && answer.trim() !== "").length;
            let unansweredCount = totalQuestions - answeredCount;

            document.getElementById("answeredLabel").innerText = `You have answered ${answeredCount} out of ${totalQuestions} questions.`;
            document.getElementById("unansweredLabel").innerText = `You have ${unansweredCount} unanswered questions.`;
        }

        // Ensure it updates when a user types in a textarea
        document.querySelectorAll('textarea').forEach((textarea, index) => {
            textarea.addEventListener('input', function() {
                userAnswers[index] = textarea.value.trim(); // Save user input
                countUserSelections();
            });
        });

        


        function fetchQuestions() {
           fetch('jss1_computer_theory_load_questions.php')
                .then(response => response.json())
                .then(data => {
                    questions = data; // Store the questions
                    if (randomize) {
                        shuffleQuestions(); // Shuffle questions if randomization is enabled
                    }
                    loadQuestion(); // Load the first question
                })
                .catch(error => console.error('Error fetching questions:', error));
        }
        function shuffleQuestions() {
            for (let i = questions.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [questions[i], questions[j]] = [questions[j], questions[i]]; // Swap elements
            }
        }

         function fetchTimer() {
            fetch('jss1_computer_studies_retrieve_timer.php')
                .then(response => response.json())
                .then(data => {
                    if (data.timer) {
                        timervalue = data.timer; // Set the timer value
                        startCountdown();
                    } else {
                        console.error(data.error || 'Error fetching timer value');
                    }
                })
                .catch(error => console.error('Error:', error));
        }
           function showAlert() {
            document.getElementById("overlay").style.display = "block";
        }

        function confirmAction(isConfirmed) {
            document.getElementById("overlay").style.display = "none";
            if (isConfirmed) {
                alert("Yeah that's perfectly fine!");
            } else {
                alert("Wow that's really cool!");
            }
        }
        // Prevent Refresh using F5, Ctrl+R, and Right-Click Reload
        document.addEventListener("keydown", function (event) {
            if (event.key === "F5" || (event.ctrlKey && event.key === "r")) {
                event.preventDefault();
                alert("Page refresh is disabled during the exam!");
            }
        });

        // Prevent Refresh using Browser Back/Forward Navigation
        window.addEventListener("beforeunload", function (event) {
            event.preventDefault();
            event.returnValue = "Refreshing or leaving this page will end your exam session!";
        });

        // Optional: Hide the Reload Button on Mobile Browsers
        history.pushState(null, "", location.href);
        window.onpopstate = function () {
            history.pushState(null, "", location.href);
        };     

      
// Load question and restore editor content
        function loadQuestion() {
            const question = questions[currentQuestionIndex];
            document.getElementById("question-text").innerText = question.question_text;

            const editor = document.getElementById("editor");
            editor.innerHTML = userAnswers[currentQuestionIndex] || "Type your answer here...";

            // Load question image if available
            const imageContainer = document.getElementById('questionImageContainer');
            if (question.question_image) {
                imageContainer.innerHTML = `<img src="${question.question_image}" class="question-image" alt="Question Image" style="max-width:100%; height:auto; border:1px solid black; margin-top:10px;">`;
            } else {
                imageContainer.innerHTML = '';
            }
       }

        // Save user answer from contenteditable editor
        function saveUserAnswer() {
            const editorContent = document.getElementById("editor").innerHTML.trim();
            userAnswers[currentQuestionIndex] = editorContent;
        }

      // Go to next question
        function nextQuestion() {
            saveUserAnswer();
            if (currentQuestionIndex < questions.length - 1) {
                currentQuestionIndex++;
                loadQuestion();
            } else {
                alert("This is the last question.");
            }
        }

               // Go to previous question
        function previousQuestion() {
            saveUserAnswer();
            if (currentQuestionIndex > 0) {
                currentQuestionIndex--;
                loadQuestion();
            } else {
                alert("This is the first question.");
            }
        }
                
        document.getElementById('usernameDisplay').innerText = 'Welcome, ' + localStorage.getItem('username');
        

       function submitExam() {
            saveUserAnswer();
            let correctCount = 0;
            let scoreData = [];

            questions.forEach((question, index) => {
                let correctAnswer = question.correct_answer.trim().toLowerCase();
                let userAnswer = userAnswers[index]?.replace(/<[^>]*>?/gm, "").trim().toLowerCase(); // remove HTML

                let score = correctAnswer === userAnswer ? 1 : 0;
                correctCount += score;

                scoreData.push({
                    question_id: question.id,
                    user_answer: userAnswers[index],
                    score: score
                });
            });

            fetch('jss1_computer_theory_save_scores.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    student_name: studentName,
                    scores: scoreData
                })
            }).then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert("Exam submitted successfully!");
                    logout();
                } else {
                    alert("Failed to submit exam.");
                }
            });

            logout();
        }

        // Track changes in editor (real-time updates)
        document.getElementById('editor').addEventListener('input', function () {
            userAnswers[currentQuestionIndex] = this.innerHTML.trim();
        });

        // Remove placeholder when editor is focused
        document.getElementById('editor').addEventListener('focus', function () {
            if (this.innerText.trim() === "Type your answer here...") {
                this.innerHTML = "";
            }
        });

         function logout() {
            window.location.href = "login.html";
        }
         var currentTab = sessionStorage.getItem("currentTab");
                if (currentTab) {
                    alert("Duplicate tab detected! Closing this tab.");
                    window.close(); // Closes the duplicate tab
                } else {
                    sessionStorage.setItem("currentTab", "active");
                }

                window.addEventListener("beforeunload", function() {
                    sessionStorage.removeItem("currentTab");
                });


                if (sessionStorage.getItem("tabOpen")) {
                    alert("Duplicate tab detected! Redirecting...");
                    window.location.href = "error.php"; // Redirects to an error page
                } else {
                    sessionStorage.setItem("tabOpen", "true");
                }

                window.addEventListener("beforeunload", function() {
                    sessionStorage.removeItem("tabOpen");
                });

        document.addEventlistener('contextmenu',(event) => event.preventDefault());
        document.addEventlistener('Keydown',function(e){
            if (e.key === 'F12' ||  (e.ctrlKey && e.shiftkey && e.key === 'I')){
                e.preventDefault();
            }
    });

            /**
     * Inserts the given symbol into the editor at the current caret position.
     * @param {string} symbol - The symbol to insert.
     */
    function insertSymbol(symbol) {
      // Set focus to the editor.
      var editor = document.getElementById("editor");
      editor.focus();

      // Get the current selection and range.
      var sel, range;
      if (window.getSelection) {
        sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
          range = sel.getRangeAt(0);
          // Delete any selected content.
          range.deleteContents();

          // Create a text node for the symbol.
          var textNode = document.createTextNode(symbol);
          range.insertNode(textNode);

          // Move the caret immediately after the inserted symbol.
          range.setStartAfter(textNode);
          range.collapse(true);

          // Clear all selections and set the new range.
          sel.removeAllRanges();
          sel.addRange(range);
        }
      }
    }

    /**
     * Handles changes of the symbol dropdown.
     * Inserts the symbol and then resets the dropdown selection.
     * @param {string} symbol - The symbol selected by the user.
     */
    function handleSymbolChange(symbol) {
      if (symbol) {
        insertSymbol(symbol);
      }
      // Reset dropdown back to default option
      document.getElementById("symbolDropdown").selectedIndex = 0;
    }
    </script>
</body>
</html>




