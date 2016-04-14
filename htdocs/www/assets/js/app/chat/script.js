
(function () {

    var $input = $('.chat-input');
    var $template = $('.template');
    var $messages = $('.messages');
    var $converse = $('.converse');
    var $messageLoading = $('.message-loading');

    var template = {
        message: {
            oscar: $template.find('.message-oscar'),
            user: $template.find('.message-user')
        }
    };

    var addReply = function (who, message, gif) {
        var $tpl = template.message[who];
        if ($tpl) {
            $content = $tpl.clone().find('.content').text(message);
            if (gif) $content.append($('<img>').attr('src', gif).addClass('gif').on('load', function (e) {
                gotoBottom();
            }));
            $content.end().appendTo($converse);
            gotoBottom();
        }
    };
    
    var gotoBottom = function () {
        $messages.scrollTop($messages.get(0).scrollHeight);
    };

    var reply = {
        user: function (message, gif) { addReply('user', message, gif); },
        oscar: function (message, gif) { addReply('oscar', message, gif); },
    };

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
                    message: message
                },
                'method': $(this).attr('method'),
                'dataType': 'JSON',
                success: function (json) {
                    replied = true;
                    if (json && json.success && json.data && (json.data.message || json.data.gif || json.data.movie)) {
                        reply.oscar(json.data.message, json.data.gif);
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
