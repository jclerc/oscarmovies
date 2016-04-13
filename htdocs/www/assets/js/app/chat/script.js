
(function () {

    var $input = $('.chat-input');
    var $template = $('.template');
    var $messages = $('.messages');

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
                $messages.scrollTop($messages.get(0).scrollHeight);
            }));
            $content.end().appendTo($messages);
            $messages.scrollTop($messages.get(0).scrollHeight);
        }
    };

    var reply = {
        user: function (message, gif) { addReply('user', message, gif); },
        oscar: function (message, gif) { addReply('oscar', message, gif); },
    };

    $('.chat-box').on('submit', function (e) {
        e.preventDefault();
        var message = $input.val();
        $input.val('');
        if (message.length) {
            reply.user(message);
            $.ajax({
                url: location.href,
                data: {
                    message: message
                },
                'method': $(this).attr('method'),
                'dataType': 'JSON',
                success: function (json) {
                    if (json && json.success && json.data && json.data.message) {
                        reply.oscar(json.data.message, json.data.gif);
                    }
                }
            });
        }
    });

})();
