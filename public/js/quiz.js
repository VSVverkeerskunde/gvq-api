(function (window, document, $) {
  var logoOffsetCounter = 0;

  let defaultQuizConfig = {
    'channel': 'individual',
    'company': null,
    'partner': null,
    'language': 'nl',
    'hideCountdownOnMobile': 'no',
    'imageDirectory': 'https://s3-eu-west-1.amazonaws.com/verkeersquiz-2021/',
    'teams': {
      '922391c4-fc5b-4148-b69d-d347d48caaef': {
        'name': 'Club Brugge KV',
        'primary': '#387cda',
        'secondary': '#3c3c3c'
      }
    },
    'apiUrl': '/api',
    'email': '',
    'team': '',
    'startquestion': null,
    'contestClosed': false
  };
  let translations = {
    nl: {
      STARTING: 'De quiz start over',
      START_QUIZ: 'Start!',
      CONTINUE: 'Verdergaan',
      QUESTION: 'Vraag',
      TIME_LEFT: 'Resterende tijd',
      NEXT_QUESTION: 'Volgende vraag >',
      VIEW_SCORE: 'Bekijk score',
      SCORE: 'Score',
      PLAY_AGAIN: 'Speel nog eens',
      PLAY_CONTEST: 'Neem deel aan de wedstrijd',
      ANSWERED_CORRECT: 'Juist',
      ANSWERED_WRONG: 'Fout',
      ANSWERED_LATE: 'Te laat',
      CHOOSE_TEAM: 'Selecteer een club naar keuze',
      RECRUITMENT_TITLE: 'Test je verkeerskennis en ga aan de haal met 1 van de vele topprijzen. 5 minuutjes, 10 vragen en misschien win jij wel een reischeque van 1000 euro!',
      EMAIL: 'E-mail:',
      EMAIL_CONFIRM: 'Bevestiging e-mail:',
      SHARE_TITLE: 'Goede of slechte score?',
      SHARE_TITLE_VERY_BAD: 'Oei, dat ziet er niet goed uit. Probeer het nog eens en fris je verkeerskennis op. Vanaf 7/10 maak je trouwens kans op 1 van de geweldige prijzen.',
      SHARE_TITLE_BAD: 'Dat kan beter. Probeer nog eens en ga voor een hogere score. Vanaf 7/10 maak je kans op een reischeque van 1000 euro. Wie niet waagt...',
      SHARE_TITLE_GOOD: 'Goed bezig! Maar alles kan beter 😉. Probeer nog eens en laat zien dat je een échte verkeersexpert bent.',
      SHARE_TITLE_VERY_GOOD: 'Wat een topscore! Jij hebt duidelijk een echt verkeersbrein.',
      SHARE_SUB_TITLE: 'Deel je score met je vrienden en daag hen uit om beter te doen.',
    },
    fr: {
      STARTING: 'Vous êtes prêt ?',
      START_QUIZ: 'Commencer',
      CONTINUE: 'Continuez',
      QUESTION: 'Question',
      TIME_LEFT: 'Temps restant',
      NEXT_QUESTION: 'Question suivante >',
      VIEW_SCORE: 'Voir mon score',
      SCORE: 'Score',
      PLAY_AGAIN: 'Rejouer',
      PLAY_CONTEST: 'Participer au concours',
      ANSWERED_CORRECT: 'Correct',
      ANSWERED_WRONG: 'Faux',
      ANSWERED_LATE: 'Trop tard',
      CHOOSE_TEAM: 'Choisissez votre club',
      RECRUITMENT_TITLE: 'Testez vos connaissances du code de la route et gagnez un de nos 200 prix! 5 minutes, 10 questions et vous gagnerez peut-être un chèque-voyage de 1000 euros!',
      EMAIL: 'Email:',
      EMAIL_CONFIRM: 'Confirmation email:',
      SHARE_TITLE: 'Bon ou mauvais score?',
      SHARE_TITLE_VERY_BAD: 'Aïe, retentez vite votre chance! Vous devez obtenir 7/10 minimum pour tenter de gagner un des prix mis en jeu.',
      SHARE_TITLE_BAD: 'Vous pouvez faire mieux! Retentez vite votre chance! Vous devez obtenir 7/10 minimum pour tenter de gagner un des prix mis en jeu.',
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

    function renderView (viewName, quizId, questionNr, answerId, scroll) {
      let oldContent = view.children();
      let newContent = $(views[viewName].template).hide();

      function showNewContent () {
        oldContent.remove();
        newContent.show();
        sendQuizHeight(50, scroll);
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
        startTimer: {
          controller: function () {
            var sequences = [
              {pct: 20, label: '4'},
              {pct: 40, label: '3'},
              {pct: 60, label: '2'},
              {pct: 80, label: '1'},
              {pct: 100, label: quizConfig['language'] == 'nl' ? 'start!' : 'C’est parti', css_class: 'start'}
            ];

            function progress(val, label, css_class, bar, cont) {
              var r = bar.attr('r');
              var c = Math.PI*(r*2);

              if (val < 0) { val = 0;}
              if (val > 100) { val = 100;}

              var offset = ((100-val)/100)*c;
              console.log(offset);

              bar.css({ strokeDashoffset: offset});

              cont.attr('data-pct',label)

              if (css_class) {
                cont.addClass(css_class);
              }
            }

            function start () {
              $.post(quizConfig.apiUrl + '/quiz', JSON.stringify({
                channel: quizConfig['channel'],
                company: quizConfig['company'],
                partner: quizConfig['partner'],
                language: quizConfig['language'],
                first_question: quizConfig['startquestion'],
                team: null
              }))
                  .done(function (data) {
                    renderView('askQuestion', data.id, 1, null, true);
                  })
                  .fail(function (data) {
                    alert(data.responseText);
                  });
            }

            let cont = view.find('.cont');
            let bar = view.find('.bar');

            for (var i = 0; i < sequences.length; i++) {
              setTimeout(progress, 500 * (i+1), sequences[i].pct, sequences[i].label, sequences[i].css_class ?? null, bar, cont);
            }

            setTimeout(start, (500 * sequences.length+1) + 300);

            return $.Deferred().resolve().promise();
          },
          template: loadTemplate('start-timer', quizConfig.language)
        },
      participationForm: {
        controller: function () {
          let startButton = view.find('button.gvq-start-button');

          let teamSelect = view.find('select[name="choose-team"]');

          let recruitment = view.find('#recruitment');

          function start (team) {
            $.post(quizConfig.apiUrl + '/quiz', JSON.stringify({
              channel: quizConfig['channel'],
              company: quizConfig['company'],
              partner: quizConfig['partner'],
              language: quizConfig['language'],
              first_question: quizConfig['startquestion'],
              team: team
            }))
              .done(function (data) {
                renderView('askQuestion', data.id, 1, null, true);
              })
              .fail(function (data) {
                alert(data.responseText);
              });
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
                startButton.prop('disabled', (checkTeamSelect()) === false);
                renderTeamBanner(teamSelect.val());
              })
              .trigger('change');

          } else {
            renderTeamBanner(false);
            teamSelect.remove();
          }

          function checkTeamSelect () {
            return teamSelect.val() !== '';
          }

          startButton.on('click', function () {
            start(
              cupModeOn ? teamSelect.val() : null
            );
          });

          return $.Deferred().resolve().promise();
        },
        template: loadTemplate('participation-form', quizConfig.language)
      },
      askEmail: {
        controller: function (quizId, score, totalQuestions) {
          setViewValue('score', score);
          setViewValue('totalQuestions', totalQuestions);

          function checkEmail () {
            let emailRegex = new RegExp('^[a-zA-Z0-9_+&*-]+(?:\\.[a-zA-Z0-9_+&*-]+)*@(?:[a-zA-Z0-9-]+\\.)+[a-zA-Z]{2,7}$');
            var email = emailInput.val().trim();
            var email_confirmation = emailInputConfirmation.val().trim();

            return emailRegex.test(email) &&
              email === email_confirmation;
          }

          let registerEmailButton = view.find('button.gvq-register-email-button');

          let emailInput = view.find('input#gvq-participant-email');
          let emailInputConfirmation = view.find('input#gvq-participant-email-confirm');
          emailInput.val(quizConfig['email']);

          emailInput.on('keyup change', function () {
            registerEmailButton.prop('disabled', checkEmail() === false);
          }).trigger('change');

          emailInputConfirmation.on('keyup change', function () {
            registerEmailButton.prop('disabled', checkEmail() === false);
          }).trigger('change');

          function showResult() {
            if (score >= 7) {
              let contestUrl = quizConfig.language+'/view/contest/'+quizId;
              $(location).attr("href", contestUrl);
            }
            else {
              renderView('showResult', quizId, score, totalQuestions, null, true);
            }
          }

          registerEmailButton.on('click', function () {
            $.post(quizConfig.apiUrl + '/quiz/' + quizId + '/email/' + emailInput.val().toLowerCase().trim()).done(
                showResult
            );
          });

          return $.Deferred().resolve().promise();
        },
        template: loadTemplate(quizConfig['company'] ? 'ask-email-company' : 'ask-email', quizConfig.language)
      },
      askQuestion: {
        controller: function (quizId, questionNr) {
          let deferredRender = $.Deferred();
          let counterInterval;

          function startCountdown () {
            if (quizConfig.hideCountdownOnMobile === 'yes') {
              view.find('.gvq-countdown').addClass('hide-on-mobile');
            }
            let counter = view.find('.gvq-time-left');
            sendCounterState('started');
            counterInterval = window.setInterval(function () {
              let secondsLeft = parseInt(counter.text(), 10) - 1;
              if (0 === secondsLeft) {
                clearInterval(counterInterval);
                view.find('.gvq-countdown').addClass('finished');
                sendCounterState('finished');
                renderView('showAnswer', quizId, questionNr, null, false);
              }
              counter.text(secondsLeft);
              sendCounter(secondsLeft);
            }, 1000);
          }

          function renderQuestion (data) {
            let imageLocation = quizConfig.imageDirectory + data.question.imageFileName;
            let questionImage = new Image();
            setViewValue('questionText', data.question.text);

            for (var i = 1; i <= 3; i++) {
              view.find('[data-value="answer' + i + '"]').hide();
            }

            $.each(data.question.answers, function (index, answer) {
              setViewValue('answer' + answer.index, answer.text);
              view.find('[data-value="answer' + answer.index + '"]').show();
            });



            view
              .find('ul.gvq-answers')
              .on('click', 'li', function () {
                let chosenAnswer = data.question.answers[$(this).index()];
                clearInterval(counterInterval);
                sendCounterState('finished');
                renderView('showAnswer', quizId, questionNr, chosenAnswer.id, false);
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

            for (var i = 1; i <= 3; i++) {
              view.find('[data-value="answer' + i + '"]').hide();
            }

            setViewValue('questionText', data.question.text);
            $.each(data.question.answers, function (index, answer) {
              if (answerId && answer.correct && answerId === answer.id) {
                answerResult = 'correct';
              }
              setViewValue('answer' + answer.index, answer.text);
              view.find('[data-value="answer' + answer.index + '"]').show();
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
                  let viewName = ((quizConfig['company'] || data.score >= 7) && quizConfig['contestClosed'] === false) ? 'askEmail': 'showResult';
                  renderView(viewName, quizId, data.score, questionNr, null, true);
                })
                .show();
            } else {
              view.find('button.gvq-next-question')
                .on('click', function () {
                  renderView('askQuestion', quizId, ++questionNr, null, true);
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

          if (cupModeOn) {
            view.find('#share-quiz').remove();
          } else {
            view.find('#share-cup').remove();

            if (score <= 4) {
              view.find('#share-title-very-bad').show();
              view.find('#share-title-bad').hide();
              view.find('#share-title-good').hide();
              view.find('#share-title-very-good').hide();
            } else if (score <= 6) {
              view.find('#share-title-very-bad').hide();
              view.find('#share-title-bad').show();
              view.find('#share-title-good').hide();
              view.find('#share-title-very-good').hide();
            } else if (score <= 8) {
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

    renderView('startTimer', null, null, null, false);
  }

  window.Quiz = Quiz;
}(window, document, jQuery));
