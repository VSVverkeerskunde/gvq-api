function getQuizHeight() {
    let quizElement = document.getElementById('gvq-quiz');
    return quizElement.offsetHeight;
}

function sendQuizHeight(offset, scroll) {
    let height = getQuizHeight() + offset + 100;
    if (height < 900) {
        height = 900;
    }
    let message = JSON.stringify({'quizHeight': height, 'scroll': scroll});
    parent.postMessage(message, '*');
}

function sendCounterState(state) {
    let message = JSON.stringify({'counterState': state});
    parent.postMessage(message, '*');
}

function sendCounter(counter) {
    let message = JSON.stringify({'counter': counter});
    parent.postMessage(message, '*');
}
