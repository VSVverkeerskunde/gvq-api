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
      let oldContent = view.children();
      let newContent = $(views[viewName].template).hide();
      function showNewContent() {
        oldContent.remove();
        newContent.show();
      }

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

          $('button.gvq-start-button').on('click', function () {
            start($('input.gvq-participant-email').val());
          });

          return $.Deferred().resolve().promise();
        },
        template: $('div[data-template="participation-form"]').prop('outerHTML')
      },
      askQuestion: {
        controller: function (quizId, questionNr) {
          let deferredRender = $.Deferred();

          function startCountdown() {
            let counter = view.find('.gvq-time-left');
            let counterInterval = window.setInterval(function () {
              let secondsLeft = parseInt(counter.text(),10) - 1;
              if (0 === secondsLeft) {
                clearInterval(counterInterval);
                view.find('.gvq-countdown').addClass('finished');
              }
              counter.text(secondsLeft);
            }, 1000);
          }

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
              });

            deferredRender.resolve();
            startCountdown();
          }

          setViewValue('questionNr', questionNr);
          $.get('/quiz/'+quizId+'/question').done(renderQuestion);
          return deferredRender.promise();
        },
        template: $('div[data-template="ask-question"]').prop('outerHTML')
      },
      showAnswer: {
        controller: function(quizId, questionNr, answerId) {
          let deferredRender = $.Deferred();

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

            deferredRender.resolve();
          }

          setViewValue('questionNr', questionNr);
          $.post('/quiz/'+quizId+'/question/'+answerId).done(renderAnsweredQuestion);
          $('button.gvq-next-question').on('click', function () {
            renderView('askQuestion', quizId, ++questionNr);
          });

          return deferredRender.promise();
        },
        template: $('div[data-template="show-answer"]').prop('outerHTML')
      }
    };

    renderView('participationForm');
  };

  Quiz();
}(window, document, jQuery));