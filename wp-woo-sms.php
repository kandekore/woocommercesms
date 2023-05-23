https://github.com/kandekore/woocommercesms.git<?php
/**
 * Plugin Name: WP Woo SMS
 * Description: Sends an SMS message to the customer when an order is completed.
 * Version: 1.0
 * Author: Darren kandekore
 */

// Add a custom menu item in the admin dashboard
function order_message_plugin_menu() {
    add_menu_page(
        'Order Message Plugin',
        'Order Message',
        'manage_options',
        'order-message-plugin',
        'order_message_plugin_settings_page',
        'dashicons-email',
        30
    );
}
add_action('admin_menu', 'order_message_plugin_menu');

// Render the settings page for the plugin
function order_message_plugin_settings_page() {
    ?>
    <div class="wrap">
        <h1>Order Message Plugin</h1>
        <p>Configure the settings for the order message plugin.</p>
        <form method="post" action="options.php">
            <?php settings_fields('order_message_plugin_settings'); ?>
            <?php do_settings_sections('order-message-plugin'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Register the plugin settings
function order_message_plugin_register_settings() {
    add_settings_section(
        'order_message_plugin_general',
        'General Settings',
        'order_message_plugin_general_section_callback',
        'order-message-plugin'
    );
    
    add_settings_field(
        'order_message_plugin_email_subject',
        'Email Subject',
        'order_message_plugin_email_subject_field_callback',
        'order-message-plugin',
        'order_message_plugin_general'
    );
    
    add_settings_field(
        'order_message_plugin_email_message',
        'Email Message',
        'order_message_plugin_email_message_field_callback',
        'order-message-plugin',
        'order_message_plugin_general'
    );
    
    register_setting('order_message_plugin_settings', 'order_message_plugin_email_subject');
    register_setting('order_message_plugin_settings', 'order_message_plugin_email_message');
}
add_action('admin_init', 'order_message_plugin_register_settings');

// Render the email subject field
function order_message_plugin_email_subject_field_callback() {
    $subject = get_option('order_message_plugin_email_subject');
    echo '<input type="text" name="order_message_plugin_email_subject" value="' . esc_attr($subject) . '" class="regular-text" />';
}

// Render the email message field
function order_message_plugin_email_message_field_callback() {
    $message = get_option('order_message_plugin_email_message');
    echo '<textarea name="order_message_plugin_email_message" class="large-text">' . esc_textarea($message) . '</textarea>';
}

// Render the general settings section
function order_message_plugin_general_section_callback() {
    echo '<p>Configure the general settings for the order message plugin.</p>';
}

// Hook the function to the order complete event
function send_message_on_order_complete($order_id) {
    // Get the order object
    $order = wc_get_order($order_id);
    
    // Get the client's phone number
    $phone = $order->get_billing_phone();
    
    // Construct the email address using the phone number
    $email = $phone . '@txtlocal.co.uk';
    
    // Get the email subject and message from the plugin settings
    $subject = get_option('order_message_plugin_email_subject');
    $message = get_option('order_message_plugin_email_message');
    
    // Replace placeholders in the message with actual order data
    $message = str_replace('%n', "\n", $message);
    $message = str_replace('{first_name}', $order->get_billing_first_name(), $message);
    $message = str_replace('{last_name}', $order->get_billing_last_name(), $message);
    $message = str_replace('{order_id}', $order_id, $message);
    
    // Send the email
    wp_mail($email, $subject, $message);
}
add_action('woocommerce_order_status_completed', 'send_message_on_order_complete');
