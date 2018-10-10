function getQuizHeight() {
    let quizElement = document.getElementById('gvq-quiz');
    return quizElement.offsetHeight;
}

function sendQuizHeight() {
    let height = getQuizHeight();
    let message = JSON.stringify({'quizHeight': height});
    parent.postMessage(message, '*');
}
