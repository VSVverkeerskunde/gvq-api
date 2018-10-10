function getQuizHeight() {
    let quizElement = document.getElementById('gvq-quiz');
    return quizElement.offsetHeight;
}

function sendQuizHeight(offset = 0) {
    let height = getQuizHeight() + offset;
    let message = JSON.stringify({'quizHeight': height});
    parent.postMessage(message, '*');
}
