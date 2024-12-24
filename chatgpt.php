<?php


// Hook to add a chatbox to your site
function ai_chatbot_ui()
{
    ?>
    <div id="ai-chatbox" class="ai-chatbox">
        <button id="ai-chat-btn" class="ai-chat-btn">Chat with AI</button>
        <div id="ai-chat-area" class="ai-chat-area">
            <div id="ai-messages" class="ai-messages"></div>
            <input type="text" id="user-input" class="user-input" placeholder="Type your question...">
            <button id="send-btn" class="send-btn">Send</button>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'ai_chatbot_ui');

// Enqueue necessary styles and scripts
function ai_chatbot_scripts()
{
    ?>
    <style>
        .ai-chatbox {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 10px;
            width: 300px;
            z-index: 9999;
        }

        .ai-chat-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
        }

        .ai-chat-btn:hover {
            background-color: #0056b3;
        }

        .ai-chat-area {
            display: none;
            padding-top: 10px;
        }

        .ai-messages {
            max-height: 200px;
            overflow-y: auto;
            margin-bottom: 10px;
            padding: 5px;
        }

        .user-input {
            width: calc(100% - 70px);
            padding: 8px;
            margin-right: 10px;
            border-radius: 5px;
        }

        .send-btn {
            background-color: #28a745;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
        }

        .send-btn:hover {
            background-color: #218838;
        }

        .ai-message,
        .user-message {
            margin: 5px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .ai-message {
            background-color: #f1f1f1;
        }

        .user-message {
            background-color: #d1e7dd;
            text-align: right;
        }
    </style>

    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script>
        jQuery(document).ready(function () {
            // Show chat window
            jQuery('#ai-chat-btn').click(function () {
                jQuery('#ai-chat-area').toggle();
            });

            // Send user message and get AI response
            jQuery('#send-btn').click(function () {
                var userInput = jQuery('#user-input').val().trim();
                if (userInput) {
                    jQuery('#ai-messages').append('<div class="user-message">' + userInput + '</div>');
                    jQuery('#user-input').val('');

                    // Call the AJAX function to get AI response
                    jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                        action: 'get_ai_response',
                        user_input: userInput
                    }, function (response) {
                        jQuery('#ai-messages').append('<div class="ai-message">' + response + '</div>');
                        jQuery('#ai-messages').scrollTop(jQuery('#ai-messages')[0].scrollHeight);
                    });
                }
            });

            // Enter key for sending message
            jQuery('#user-input').keypress(function (e) {
                if (e.which == 13) {
                    jQuery('#send-btn').click();
                }
            });
        });
    </script>
    <?php
}
add_action('wp_footer', 'ai_chatbot_scripts');

// Handle the AJAX request
function ai_chatbot_get_response()
{
    $user_input = sanitize_text_field($_POST['user_input']);

    // OpenAI API request
    $api_key = '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'; // Replace with your API key
    $url = 'https://api.openai.com/v1/chat/completions'; // Updated API endpoint

    // Prepare the request body for the chat-based model
    $args = array(
        'body' => json_encode(array(
            'model' => 'gpt-4o-mini', // Using the latest model
            'messages' => array(
                array(
                    'role' => 'user',
                    'content' => $user_input,
                ),
            ),
            'max_tokens' => 150,
        )),
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ),
    );

    $response = wp_remote_post($url, $args);

    if (is_wp_error($response)) {
        echo 'Error: ' . $response->get_error_message();
        wp_die();
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    // Check if the response has valid choices
    if (isset($data['choices'][0]['message']['content'])) {
        echo $data['choices'][0]['message']['content'];
    } else {
        echo 'No valid response from OpenAI.';
    }

    wp_die();
}


// Action to get the AI response via AJAX
add_action('wp_ajax_get_ai_response', 'ai_chatbot_get_response');
add_action('wp_ajax_nopriv_get_ai_response', 'ai_chatbot_get_response');

?>