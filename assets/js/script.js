jQuery(document).ready(function ($) {
    // Show or hide the chat window
    $('#ai-chat-btn').click(function () {
        $('#ai-chat-area').toggle();
    });

    // Send user input and get AI response
    $('#send-btn').click(function () {
        var userInput = $('#user-input').val().trim();
        if (userInput) {
            $('#ai-messages').append('<div class="user-message">' + userInput + '</div>');
            $('#user-input').val('');

            // Show loading animation
            $('#ai-messages').append('<div class="ai-message loading"><span class="dot">.</span><span class="dot">.</span><span class="dot">.</span></div>');
            $('#ai-messages').scrollTop($('#ai-messages')[0].scrollHeight);

            // AJAX call to get AI response
            $.post(aiChatbotAjax.ajax_url, {
                action: 'get_ai_response',
                user_input: userInput
            })
            .done(function (response) {
                // Remove the loading animation
                $('#ai-messages .loading').remove();

                if (response.success) {
                    var text = response.data.response.candidates[0].content.parts[0].text;
                    $('#ai-messages').append('<div class="ai-message">' + text + '</div>');
                } else {
                    $('#ai-messages').append('<div class="ai-message error">Error: ' + response.data.message + '</div>');
                }
                $('#ai-messages').scrollTop($('#ai-messages')[0].scrollHeight);
            })
            .fail(function () {
                // Remove the loading animation
                $('#ai-messages .loading').remove();
                $('#ai-messages').append('<div class="ai-message error">Failed to get AI response.</div>');
            });
        }
    });

    // Handle enter key for sending messages
    $('#user-input').keypress(function (e) {
        if (e.which === 13) {
            $('#send-btn').click();
        }
    });
});
