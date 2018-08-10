(function (window, document, $) {
  let defaultQuizConfig = {
    "channel": "individual",
    "company": null,
    "partner": null,
    "team": null,
    "language": "nl",
    "imageDirectory": "https://s3-eu-west-1.amazonaws.com/verkeersquiz/"
  };
  let translations = {
    nl: {
      START_QUIZ: 'Start de quiz',
      QUESTION: 'Vraag',
      TIME_LEFT: 'Resterende tijd',
      NEXT_QUESTION: 'Volgende vraag',
      VIEW_SCORE: 'Bekijk score',
      SCORE: 'Score',
      PLAY_AGAIN: 'Speel nog eens',
      ANSWERED_CORRECT: 'Juist',
      ANSWERED_WRONG: 'Fout',
      ANSWERED_LATE: 'Te laat'
    },
    fr: {
      START_QUIZ: 'Commencer le quiz',
      QUESTION: 'Question',
      TIME_LEFT: 'Temps restant',
      NEXT_QUESTION: 'Question suivante',
      VIEW_SCORE: 'Voir mon score',
      SCORE: 'Score',
      PLAY_AGAIN: 'Jouer encore une fois',
      ANSWERED_CORRECT: 'Correct',
      ANSWERED_WRONG: 'Faux',
      ANSWERED_LATE: 'Trop tard'
    }
  };
  let cachedConfig = {};

  function loadTemplate(name, language) {
    let template = $('div[data-template="'+name+'"]')
    template
      .find('[data-translate]')
      .each(function () {
        let translatable = $(this);
        let reference = translatable.attr('data-translate');
        translatable.text(translations[language][reference]);
      })

    return template.prop('outerHTML');
  }

  function Quiz (quizConfig) {
    quizConfig = Object.assign({}, defaultQuizConfig, quizConfig || cachedConfig);
    cachedConfig = quizConfig;
    let view = $('#gvq-quiz .gvq-quiz-view');
    let oldView = $('#gvq-quiz .gvq-quiz-old-view');

    function renderView(viewName, ...args) {
      let oldContent = view.children();
      let newContent = $(views[viewName].template).hide();

      function showNewContent() {
        oldContent.remove();
        newContent.show();
      }

      oldView.append(oldContent);
      view.append(newContent);
      views[viewName].controller(...args).done(showNewContent);
    }

    function setViewValue(name, value) {
      view.find('[data-value="'+name+'"]').text(value);
    }

    let views = {
      participationForm: {
        controller: function () {
          function start(email) {
            $.post('/quiz', JSON.stringify(Object.assign({}, quizConfig, {email: email})))
              .done(function( data ) {
                renderView('askQuestion', data.id, 1);
              });
          }

          view.find('button.gvq-start-button').on('click', function () {
            start($('input.gvq-participant-email').val());
          });

          return $.Deferred().resolve().promise();
        },
        template: loadTemplate('participation-form', quizConfig.language)
      },
      askQuestion: {
        controller: function (quizId, questionNr) {
          let deferredRender = $.Deferred();
          let counterInterval;

          function startCountdown() {
            let counter = view.find('.gvq-time-left');
            counterInterval = window.setInterval(function () {
              let secondsLeft = parseInt(counter.text(),10) - 1;
              if (0 === secondsLeft) {
                clearInterval(counterInterval);
                view.find('.gvq-countdown').addClass('finished');
              }
              counter.text(secondsLeft);
            }, 1000);
          }

          function renderQuestion(data) {
            let imageLocation = quizConfig.imageDirectory + data.question.imageFileName;
            let questionImage = new Image();
            setViewValue('questionText', data.question.text);
            $.each(data.question.answers, function (index, answer) {
              setViewValue('answer'+answer.index, answer.text);
            });

            view
              .find('ul.gvq-answers')
              .on('click', 'li', function () {
                let chosenAnswer = data.question.answers[$(this).index()];
                clearInterval(counterInterval);
                renderView('showAnswer', quizId, questionNr, chosenAnswer.id);
              });

            questionImage.onload = function () {
              view
                .find('.gvq-question-image')
                .attr('src', imageLocation);
              deferredRender.resolve();
              startCountdown();
            };
            questionImage.src = imageLocation;
          }

          setViewValue('questionNr', questionNr);
          $.get('/quiz/'+quizId+'/question').done(renderQuestion);
          return deferredRender.promise();
        },
        template: loadTemplate('ask-question', quizConfig.language)
      },
      showAnswer: {
        controller: function(quizId, questionNr, answerId) {
          let deferredRender = $.Deferred();

          function renderAnsweredQuestion(data) {
            let answerResult = 'correct';
            if (false === data.answeredTooLate) answerResult = 'wrong';
            if (true === data.answeredTooLate) answerResult = 'late';

            setViewValue('questionText', data.question.text);
            $.each(data.question.answers, function (index, answer) {
              setViewValue('answer'+answer.index, answer.text);
              view.find('[data-value="answer'+answer.index+'"]')
                .toggleClass('selected-answer', answer.id === answerId)
                .toggleClass('is-correct', answer.correct);
              });

            view
              .find('.gvq-answer-result')
              .filter(function () {
                return false === $(this).hasClass(answerResult);
              })
              .remove();

            view
              .find('.gvq-question-image')
              .attr('src', quizConfig.imageDirectory + data.question.imageFileName);

            setViewValue('questionText', data.question.text);
            setViewValue('feedback', data.question.feedback);

            if (typeof data.score === 'number') {
              view.find('button.gvq-view-score')
                .on('click', function () {
                  renderView('showResult', quizId, data.score, questionNr);
                })
                .show();
            } else {
              view.find('button.gvq-next-question')
                .on('click', function () {
                  renderView('askQuestion', quizId, ++questionNr);
                })
                .show();
            }

            deferredRender.resolve();
          }

          setViewValue('questionNr', questionNr);
          $.post('/quiz/'+quizId+'/question/'+answerId).done(renderAnsweredQuestion);

          return deferredRender.promise();
        },
        template: loadTemplate('show-answer', quizConfig.language)
      },
      showResult: {
        controller: function (quizId, score, totalQuestions) {
          setViewValue('score', score);
          setViewValue('totalQuestions', totalQuestions);

          view.find('button.gvq-play-again').on('click', function () {
            Quiz();
          })

          return $.Deferred().resolve().promise();
        },
        template: loadTemplate('show-result', quizConfig.language)
      }
    };

    renderView('participationForm');
  };

  window.Quiz = Quiz;
}(window, document, jQuery));