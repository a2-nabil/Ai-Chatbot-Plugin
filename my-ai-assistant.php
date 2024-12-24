<?php
/*
Plugin Name: My AI Chatbot
Description: A simple chatbot integrated with Gemini AI API.
Version: 1.0
Author: Nabil
*/

// Enqueue styles and scripts
function example_plugin_enqueue_assets()
{
    wp_enqueue_style('plugin-style', plugin_dir_url(__FILE__) . 'assets/css/style.css', [], '1.0.1', 'all');
    wp_enqueue_script('plugin-script', plugin_dir_url(__FILE__) . 'assets/js/script.js', ['jquery'], '1.0.1', true);

    // Localize script for AJAX URL
    wp_localize_script('plugin-script', 'aiChatbotAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
}
add_action('wp_enqueue_scripts', 'example_plugin_enqueue_assets');

// Hook to add a chatbox to your site
function ai_chatbot_ui()
{
    ?>
    <div id="ai-chatbox" class="ai-chatbox">
        <button id="ai-chat-btn" class="ai-chat-btn">Chat with AI</button>
        <div id="ai-chat-area" class="ai-chat-area" style="display: none;">
            <div id="ai-messages" class="ai-messages"></div>
            <input type="text" id="user-input" class="user-input" placeholder="Type your question...">
            <button id="send-btn" class="send-btn">Send</button>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'ai_chatbot_ui');

// AJAX handler for AI response
require "vendor/autoload.php";
use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;
function ai_chatbot_get_response()
{
    if (!isset($_POST['user_input'])) {
        wp_send_json_error(['message' => 'No input received.']);
        wp_die();
    }

    $user_input = sanitize_text_field($_POST['user_input']);

    try {
        $client = new \GeminiAPI\Client("AIzaSyC34WQ4O-GX-GecTabr0cMLDcHTqLDBn4U");
        $response = $client->geminiPro()->generateContent(
            new \GeminiAPI\Resources\Parts\TextPart($user_input)
        );
        wp_send_json_success(['response' => $response]);
    } catch (Exception $e) {
        error_log($e->getMessage());
        wp_send_json_error(['message' => 'API error: ' . $e->getMessage()]);
    }
    wp_die();
}
add_action('wp_ajax_get_ai_response', 'ai_chatbot_get_response');
add_action('wp_ajax_nopriv_get_ai_response', 'ai_chatbot_get_response');