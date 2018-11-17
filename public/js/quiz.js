(function (window, document, $) {
  var logoOffsetCounter = 0;

  let defaultQuizConfig = {
    'channel': 'individual',
    'company': null,
    'partner': null,
    'language': 'nl',
    'imageDirectory': 'https://s3-eu-west-1.amazonaws.com/verkeersquiz/',
    'teams': {
      '922391c4-fc5b-4148-b69d-d347d48caaef': {
        'name': 'Club Brugge KV',
        'primary': '#387cda',
        'secondary': '#3c3c3c'
      }
    },
    'apiUrl': '/api',
    'email': '',
    'team': ''
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
      PLAY_CONTEST: 'Neem deel aan de wedstrijd',
      ANSWERED_CORRECT: 'Juist',
      ANSWERED_WRONG: 'Fout',
      ANSWERED_LATE: 'Te laat',
      CHOOSE_TEAM: 'Selecteer een club naar keuze',
      RECRUITMENT_TITLE: 'Test je verkeerskennis en ga aan de haal met 1 van de vele topprijzen. 5 minuutjes, 15 vragen en misschien win jij wel een reischeque van 1000 euro!',
      EMAIL: 'E-mail',
      SHARE_TITLE: 'Goede of slechte score?',
      SHARE_TITLE_VERY_BAD: 'Oei, dat ziet er niet goed uit. Probeer het nog eens en fris je verkeerskennis op. Vanaf 11/15 maak je trouwens kans op 1 van de geweldige prijzen.',
      SHARE_TITLE_BAD: 'Dat kan beter. Probeer nog eens en ga voor een hogere score. Vanaf 11/15 maak je kans op een reischeque van 1000 euro. Wie niet waagt...',
      SHARE_TITLE_GOOD: 'Goed bezig! Maar alles kan beter 😉. Probeer nog eens en laat zien dat je een échte verkeersexpert bent.',
      SHARE_TITLE_VERY_GOOD: 'Wat een topscore! Jij hebt duidelijk een echt verkeersbrein.',
      SHARE_SUB_TITLE: 'Deel je score met je vrienden en daag hen uit om beter te doen.',
    },
    fr: {
      START_QUIZ: 'Commencer le quiz',
      QUESTION: 'Question',
      TIME_LEFT: 'Temps restant',
      NEXT_QUESTION: 'Question suivante',
      VIEW_SCORE: 'Voir mon score',
      SCORE: 'Score',
      PLAY_AGAIN: 'Rejouer',
      PLAY_CONTEST: 'Participer au concours',
      ANSWERED_CORRECT: 'Correct',
      ANSWERED_WRONG: 'Faux',
      ANSWERED_LATE: 'Trop tard',
      CHOOSE_TEAM: 'Choisissez votre club',
      RECRUITMENT_TITLE: 'Testez vos connaissances du code de la route et gagnez un de nos 200 prix! 5 minutes, 15 questions et vous gagnerez peut-être un chèque-voyage de 1000 euros!',
      EMAIL: 'Email',
      SHARE_TITLE: 'Bon ou mauvais score?',
      SHARE_TITLE_VERY_BAD: 'Aïe, retentez vite votre chance! Vous devez obtenir 11/15 minimum pour tenter de gagner un des prix mis en jeu.',
      SHARE_TITLE_BAD: 'Vous pouvez faire mieux! Retentez vite votre chance! Vous devez obtenir 11/15 minimum pour tenter de gagner un des prix mis en jeu.',
      SHARE_TITLE_GOOD: 'Bien joué ! C\'est suffisant pour participer au concours mais pas encore pour être un véritable expert! Serez-vous capable de faire mieux?',
      SHARE_TITLE_VERY_GOOD: 'Quel score!  Le code de la route n\'a pas de secret pour vous, bravo.',
      SHARE_SUB_TITLE: 'Partagez votre score avec vos amis et défiez-les de faire mieux!',
    }
  };
  let cachedConfig = {};

  function loadTemplate(name, language) {
    let template = $('div[data-template="'+name+'"]');
    template
      .find('[data-translate]')
      .each(function () {
        let translatable = $(this);
        let reference = translatable.attr('data-translate');
        translatable.text(translations[language][reference]);
      });

    return template.prop('outerHTML');
  }

  function Quiz (quizConfig) {
    quizConfig = $.extend({}, defaultQuizConfig, quizConfig || cachedConfig);
    cachedConfig = quizConfig;
    let view = $('#gvq-quiz .gvq-quiz-view');
    let oldView = $('#gvq-quiz .gvq-quiz-old-view');
    let cupModeOn = ('cup' === quizConfig.channel);

    function renderView (viewName, quizId, questionNr, answerId) {
      let oldContent = view.children();
      let newContent = $(views[viewName].template).hide();

      function showNewContent () {
        oldContent.remove();
        newContent.show();
        sendQuizHeight(50);
      }

      oldView.append(oldContent);
      view.append(newContent);
      views[viewName].controller(quizId, questionNr, answerId).done(showNewContent);
    }

    function setViewValue (name, value) {
      view.find('[data-value="' + name + '"]').text(value);
    }

    function setViewHtmlValue (name, value) {
      view.find('[data-value="' + name + '"]').html(value);
    }

    function renderTeamBanner (teamId) {
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
      var teamColor = colorPrimary;
      if(colorPrimary == '#fff') {
        teamColor = colorSecondary;
      }

      banner.find('img').attr('src', team ? (quizConfig.imageDirectory+'teams/'+teamId+'.png') : '');

      banner.css({
        'border-bottom': '10px solid ' + teamColor,
      });

      if (logoOffsetCounter < 2) {
        sendQuizHeight(150);
        logoOffsetCounter++;
      }
    }

      let views = {
      participationForm: {
        controller: function () {
          let startButton = view.find('button.gvq-start-button');

          let teamSelect = view.find('select[name="choose-team"]');

          let emailInput = view.find('input#gvq-participant-email');
          emailInput.attr('placeholder', translations[quizConfig['language']]['EMAIL']);
          emailInput.val(quizConfig['email']);

          let recruitment = view.find('#recruitment');

          function start (email, team) {
            $.post(quizConfig.apiUrl + '/quiz', JSON.stringify({
              channel: quizConfig['channel'],
              company: quizConfig['company'],
              partner: quizConfig['partner'],
              language: quizConfig['language'],
              email: email,
              team: team
            }))
              .done(function (data) {
                renderView('askQuestion', data.id, 1);
              })
              .fail(function (data) {
                alert(data.responseText);
              });
            quizConfig['email'] = email;
            quizConfig['team'] = team;
          }

          if (cupModeOn) {
            recruitment.remove();

            $.each(quizConfig['teams'], function (id, team) {
              teamSelect.append($('<option>', {value: id, text: team.name}));
            });
            teamSelect.val(quizConfig['team']);

            teamSelect
              .on('change', function () {
                startButton.prop('disabled', (checkTeamSelect() && checkEmail()) === false);
                renderTeamBanner(teamSelect.val());
              })
              .trigger('change');

            emailInput.on('keyup change', function () {
              startButton.prop('disabled', (checkTeamSelect() && checkEmail()) === false);
            }).trigger('change');

          } else {
            renderTeamBanner(false);
            teamSelect.remove();
            emailInput.on('keyup change', function () {
              startButton.prop('disabled', checkEmail() === false);
            })
              .trigger('change');
          }

          function checkEmail () {
            let emailRegex = new RegExp('^[a-zA-Z0-9_+&*-]+(?:\\.[a-zA-Z0-9_+&*-]+)*@(?:[a-zA-Z0-9-]+\\.)+[a-zA-Z]{2,7}$');
            return emailRegex.test(emailInput.val());
          }

          function checkTeamSelect () {
            return teamSelect.val() !== '';
          }

          startButton.on('click', function () {
            start(
              emailInput.val(),
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

          function startCountdown () {
            let counter = view.find('.gvq-time-left');
            counterInterval = window.setInterval(function () {
              let secondsLeft = parseInt(counter.text(), 10) - 1;
              if (0 === secondsLeft) {
                clearInterval(counterInterval);
                view.find('.gvq-countdown').addClass('finished');
                renderView('showAnswer', quizId, questionNr);
              }
              counter.text(secondsLeft);
            }, 1000);
          }

          function renderQuestion (data) {
            let imageLocation = quizConfig.imageDirectory + data.question.imageFileName;
            let questionImage = new Image();
            setViewValue('questionText', data.question.text);
            $.each(data.question.answers, function (index, answer) {
              setViewValue('answer' + answer.index, answer.text);
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

            questionImage.onerror = function () {
              deferredRender.resolve();
              startCountdown();
            };

            questionImage.src = imageLocation;
          }

          setViewValue('questionNr', questionNr);
          $.get(quizConfig.apiUrl + '/quiz/' + quizId + '/question').done(renderQuestion);
          return deferredRender.promise();
        },
        template: loadTemplate('ask-question', quizConfig.language)
      },
      showAnswer: {
        controller: function (quizId, questionNr, answerId) {
          let deferredRender = $.Deferred();

          function renderAnsweredQuestion (data) {
            let answerResult = 'wrong';

            setViewValue('questionText', data.question.text);
            $.each(data.question.answers, function (index, answer) {
              if (answerId && answer.correct && answerId === answer.id) {
                answerResult = 'correct';
              }
              setViewValue('answer' + answer.index, answer.text);
              view.find('[data-value="answer' + answer.index + '"]')
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
          answerId = answerId || 'late';
          $.post(quizConfig.apiUrl + '/quiz/' + quizId + '/question/' + answerId).done(renderAnsweredQuestion);

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
          });

          view.find('button.gvq-play-contest').hide();
          let contestUrl = quizConfig.language+'/view/contest/'+quizId;
          $.get(contestUrl).done(function () {
              view.find('button.gvq-play-contest').show();
              sendQuizHeight(50);
          });

          view.find('button.gvq-play-contest').on('click', function () {
              $(location).attr("href", contestUrl);
          });

          if (cupModeOn) {
            view.find('#share-quiz').remove();
          } else {
            view.find('#share-cup').remove();

            if (score <= 7) {
              view.find('#share-title-very-bad').show();
              view.find('#share-title-bad').hide();
              view.find('#share-title-good').hide();
              view.find('#share-title-very-good').hide();
            } else if (score <=10 ) {
              view.find('#share-title-very-bad').hide();
              view.find('#share-title-bad').show();
              view.find('#share-title-good').hide();
              view.find('#share-title-very-good').hide();
            } else if (score <= 13) {
              view.find('#share-title-very-bad').hide();
              view.find('#share-title-bad').hide();
              view.find('#share-title-good').show();
              view.find('#share-title-very-good').hide();
            } else {
              view.find('#share-title-very-bad').hide();
              view.find('#share-title-bad').hide();
              view.find('#share-title-good').hide();
              view.find('#share-title-very-good').show();
            }
          }

          return $.Deferred().resolve().promise();
        },
        template: loadTemplate('show-result', quizConfig.language)
      }
    };

    renderView('participationForm');
  }

  window.Quiz = Quiz;
}(window, document, jQuery));
