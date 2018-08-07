(function (window, document, $) {
  let defaultQuizConfig = {
    "channel": "individual",
    "company": null,
    "partner": null,
    "team": null,
    "language": "nl"
  };

  let Quiz =  function (quizConfig) {
    quizConfig = quizConfig || defaultQuizConfig;
    let view = $('#gvq-quiz .gvq-quiz-view');

    function renderView(viewName, ...args) {
      view.html(views[viewName].template);
      views[viewName].controller(...args);
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

          $('button.gvq-start-button').on('click', function () {
            start($('input.gvq-participant-email').val());
          })
        },
        template: $('div[data-template="participation-form"]').prop('outerHTML')
      },
      askQuestion: {
        controller: function (quizId, questionNr) {
          function renderQuestion(data) {
            setViewValue('questionText', data.text);
            $.each(data.answers, function (index, answer) {
              setViewValue('answer'+answer.index, answer.text);
            });

            view
              .find('ul.gvq-answers')
              .on('click', 'li', function () {
                let chosenAnswer = data.answers[$(this).index()];
                renderView('showAnswer', quizId, questionNr, chosenAnswer.id);
              })
          }

          setViewValue('questionNr', questionNr);
          $.get('/quiz/'+quizId+'/question').done(renderQuestion);
        },
        template: $('div[data-template="ask-question"]').prop('outerHTML')
      },
      showAnswer: {
        controller: function(quizId, questionNr, answerId) {
          function renderAnsweredQuestion(data) {
            setViewValue('questionText', data.text);
            $.each(data.answers, function (index, answer) {
              setViewValue('answer'+answer.index, answer.text);
              view.find('[data-value="answer'+answer.index+'"]')
                .toggleClass('selected-answer', answer.id === answerId)
                .toggleClass('is-correct', answer.correct);
            });

            setViewValue('questionText', data.text);
            setViewValue('feedback', data.feedback);
          }

          setViewValue('questionNr', questionNr);
          $.post('/quiz/'+quizId+'/question/'+answerId).done(renderAnsweredQuestion);
          $('button.gvq-next-question').on('click', function () {
            renderView('askQuestion', quizId, ++questionNr);
          })
        },
        template: $('div[data-template="show-answer"]').prop('outerHTML')
      }
    };

    renderView('participationForm');
  };

  Quiz();
}(window, document, jQuery));