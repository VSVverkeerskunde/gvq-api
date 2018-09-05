(function (window, document, $) {
  let defaultQuizConfig = {
    "channel": "individual",
    "company": null,
    "partner": null,
    "language": "nl",
    "imageDirectory": "/images/",
    "teams": {
      "922391c4-fc5b-4148-b69d-d347d48caaef": {
        "name": "Club Brugge KV",
        "primary": "#387cda",
        "secondary": "#3c3c3c"
      }
    },
    "apiUrl": "/api"
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
      ANSWERED_LATE: 'Te laat',
      CHOOSE_TEAM: 'Maak een keuze'
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
      ANSWERED_LATE: 'Trop tard',
      CHOOSE_TEAM: 'Faites un choix'
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
    let cupModeOn = ('cup' === quizConfig.channel);

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

    function setViewHtmlValue(name, value) {
      view.find('[data-value="'+name+'"]').html(value);
    }

    function renderTeamBanner(teamId) {
      let banner = $('#gvq-quiz .gvq-team-banner');
      let form = $('.participation-form');
      let team = false;

      if (false === teamId) {
        banner.remove();
        return;
      }

      if ('' !== teamId) {
        team = quizConfig['teams'][teamId];
      }

      let colorPrimary = team ? team['primary'] : 'white';
      let colorSecondary = team ? team['secondary'] : 'white';

      banner.find('img').attr('src', team ? (quizConfig.imageDirectory+'teams/'+teamId+'.png') : '');
      form.css({
        'background-color': colorSecondary,
        'background': 'linear-gradient(' + colorSecondary + ',' + colorPrimary + ')',
        'border': '1px solid' + colorSecondary + '!important',
      })
    }

    let views = {
      participationForm: {
        controller: function () {
          let startButton = view.find('button.gvq-start-button');
          let teamSelect = view.find('select[name="choose-team"]');

          function start(email, team) {
            $.post(quizConfig.apiUrl+'/quiz', JSON.stringify({
                channel: quizConfig['channel'],
                company: quizConfig['company'],
                partner: quizConfig['partner'],
                language: quizConfig['language'],
                email: email,
                team: team
            }))
            .done(function( data ) {
              renderView('askQuestion', data.id, 1);
            });
          }

          if (cupModeOn) {
            $.each(quizConfig['teams'], function (id, team) {
              teamSelect.append($('<option>', {value: id, text : team.name}));
            });

            teamSelect
              .on('change', function () {
                startButton.prop('disabled', this.value === '')
                renderTeamBanner(teamSelect.val());
              })
              .trigger('change');
          } else {
            renderTeamBanner(false);
            teamSelect.remove();
          }

          startButton.on('click', function () {
            start(
              $('input.gvq-participant-email').val(),
              cupModeOn ? teamSelect.val() : null
            );
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
          $.get(quizConfig.apiUrl+'/quiz/'+quizId+'/question').done(renderQuestion);
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

            //replace newlines with line breaks as pragmatic solution for bullet lists in feedback
            let feedbackWithLineBreaks = data.question.feedback.replace(/\n/g, '<br>');
            setViewHtmlValue('feedback', feedbackWithLineBreaks);

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
          $.post(quizConfig.apiUrl+'/quiz/'+quizId+'/question/'+answerId).done(renderAnsweredQuestion);

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