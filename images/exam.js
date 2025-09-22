let questions = [];
let currentIndex = 0;
let userAnswers = [];

window.onload = function () {
    fetchQuestions();
    startTimer(300); // 5 minutes in seconds
    countUserSelections();
};

function fetchQuestions() {
    fetch('load_questions.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            questions = data;
            userAnswers = new Array(questions.length).fill(null);
            displayQuestion(currentIndex);
        })
        .catch(error => console.error('Error:', error));
}

function displayQuestion(index) {
    const question = questions[index];

    document.getElementById('questionText').innerHTML = question.question_Text;

    // Load image if exists
    const imageContainer = document.getElementById('questionImageContainer');
    if (question.question_Image && question.question_Image.trim() !== '') {
        imageContainer.innerHTML = `<img src="images/${question.question_Image}" alt="Question Image">`;
    } else {
        imageContainer.innerHTML = '';
    }

    document.getElementById('optionAText').innerText = question.OptionA;
    document.getElementById('optionBText').innerText = question.OptionB;
    document.getElementById('optionCText').innerText = question.OptionC;
    document.getElementById('optionDText').innerText = question.OptionD;

    const options = document.getElementsByName('option');
    options.forEach(opt => opt.checked = false);

    if (userAnswers[index]) {
        document.getElementById(userAnswers[index]).checked = true;
    }
}

function nextQuestion() {
    saveAnswer();
    if (currentIndex < questions.length - 1) {
        currentIndex++;
        displayQuestion(currentIndex);
    }
}

function previousQuestion() {
    saveAnswer();
    if (currentIndex > 0) {
        currentIndex--;
        displayQuestion(currentIndex);
    }
}

function saveAnswer() {
    const selectedOption = document.querySelector('input[name="option"]:checked');
    if (selectedOption) {
        userAnswers[currentIndex] = selectedOption.id;
    }
}

function submitAnswers() {
    saveAnswer();

    let score = 0;
    questions.forEach((q, i) => {
        if (userAnswers[i] && userAnswers[i] === q.Answer) {
            score++;
        }
    });

    fetch('save_scores.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `score=${score}`
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Exam submitted successfully. Score: ' + score);
                window.location.href = 'dashboard.php';
            } else {
                alert('Error saving score: ' + data.error);
            }
        });
}

// Countdown timer
function startTimer(duration) {
    let timer = duration;
    const display = document.getElementById('txtTime');
    const interval = setInterval(function () {
        const minutes = Math.floor(timer / 60);
        const seconds = timer % 60;
        display.textContent = `Time Left: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        if (--timer < 0) {
            clearInterval(interval);
            submitAnswers();
        }
    }, 1000);
}
  function countUserSelections() {
    let totalQuestions = questions.length;
    let answeredCount = userAnswers.filter(answer => answer !== undefined).length;
    let unansweredCount = totalQuestions - answeredCount;

    document.getElementById("answeredLabel").innerText = `You have answered ${answeredCount} out of ${totalQuestions} questions.`;
    document.getElementById("unansweredLabel").innerText = `You have ${unansweredCount} unanswered questions.`;
}
        // Ensure it updates when an answer is selected
document.querySelectorAll('input[type="radio"]').forEach((radio) => {
    radio.addEventListener('change', function() {
        saveAnswer();
        countUserSelections();
    });
});