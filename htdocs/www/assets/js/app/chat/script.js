
(function () {

    var $input = $('.chat-input');
    var $template = $('.template');
    var $messages = $('.messages');
    var $converse = $('.converse');
    var $messageLoading = $('.message-loading');
    var $recommendation = $('.chat-recommendation');
    var $proposal = $('.proposal');

    var template = {
        message: {
            oscar: $template.find('.message-oscar'),
            user: $template.find('.message-user')
        }
    };

    var lastSuggestion = null;

    var addReply = function (who, message, gif, movie, raw) {
        var $tpl = template.message[who];
        if ($tpl) {
            $content = $tpl.clone().find('.content');

            if (raw) {
                $content.html(message);
            } else {
                $content.text(message);
            }

            if (gif) {
                $content.append($('<img>').attr('src', gif).addClass('gif').on('load', gotoBottom));
            }

            if (movie) {
                lastSuggestion = movie;
                $recommendation.addClass('show');
                $proposal.show();
                $recommendation.find('.poster-bg').css('background-image', 'url(http://image.tmdb.org/t/p/w342' + movie.poster_path + ')');
                $recommendation.find('.movie-title').text(movie.title);
                $recommendation.find('.rating').text(movie.vote_average);
                $recommendation.find('.movie-poster').css('background-image', 'url(http://image.tmdb.org/t/p/w342' + movie.poster_path + ')');
                $recommendation.find('.movie-description').text(movie.overview);
                $recommendation.find('.genre-tag').text(movie.genre_name);
                $recommendation.find('.year-tag').text(movie.release_date.substr(0, 4));
                $recommendation.find('.country-tag').text(movie.original_language);
            } else {
                $proposal.hide();
                $recommendation.removeClass('show');
            }

            $content.end().appendTo($converse);
            gotoBottom();
        }
    };
    
    var gotoBottom = function () {
        $messages.scrollTop($messages.get(0).scrollHeight);
    };

    var reply = {
        user: function (message, gif, movie) { addReply('user', message, gif, movie, false); },
        oscar: function (message, gif, movie) { addReply('oscar', message, gif, movie, true); },
    };

    var movieAction = function (action) {
        $proposal.hide();
        $.ajax({
            url: location.href,
            data: {
                action: action,
                movie: lastSuggestion
            },
            'method': 'POST',
            'dataType': 'JSON',
            success: function (json) {
                if (json && json.success && json.data && (json.data.message || json.data.gif || json.data.movie)) {
                    reply.oscar(json.data.message, json.data.gif, json.data.movie);
                }
            },
            complete: function () {
                $messageLoading.hide();
            }
        });
    };

    $('.btn-accept').on('click', function () {
        movieAction('accept');
        $messageLoading.show();
    });

    $('.btn-deny').on('click', function () {
        movieAction('deny');
        $messageLoading.show();
    });

    $('.btn-already-seen').on('click', function () {
        movieAction('already-seen');
        $messageLoading.show();
    });

    $('.chat-box').on('submit', function (e) {
        e.preventDefault();
        var message = $input.val();
        var timeoutLoading = null;
        $input.val('');
        if (message.length) {
            reply.user(message);
            var replied = false;
            $.ajax({
                url: location.href,
                data: {
                    message: message,
                    action: 'converse'
                },
                'method': $(this).attr('method'),
                'dataType': 'JSON',
                success: function (json) {
                    replied = true;
                    if (json && json.success && json.data && (json.data.message || json.data.gif || json.data.movie)) {
                        reply.oscar(json.data.message, json.data.gif, json.data.movie);
                    } else {
                        reply.oscar('Sorry, something went wrong..');
                    }
                },
                error: function () {
                    replied = true;
                    reply.oscar('Sorry, something went wrong..');
                },
                complete: function () {
                    $messageLoading.hide();
                }
            });
            setTimeout(function () {
                if (replied === false) {
                    $messageLoading.show();
                    gotoBottom();
                }
            }, 1000);
        }
    });

})();
