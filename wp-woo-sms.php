<?php
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

    // Add a submenu item for the top-up page
    add_submenu_page(
        'order-message-plugin',
        'Top-up Credits',
        'Top-up Credits',
        'manage_options',
        'topup-credits',
        'topup_credits_page'
    );
}
add_action('admin_menu', 'order_message_plugin_menu');

// Render the settings page for the plugin
function order_message_plugin_settings_page() {
    ?>
    <div class="wrap">
        <h1>Order Message Plugin</h1>
        <p>Configure the settings for the order message plugin.</p>
        <p><a href="<?php echo admin_url('admin.php?page=topup-credits'); ?>">Top-up Credits</a></p>
        <form method="post" action="options.php">
            <?php settings_fields('order_message_plugin_settings'); ?>
            <?php do_settings_sections('order-message-plugin'); ?>
            <?php submit_button(); ?>
        </form>
        <h2>Credit Balance</h2>
        <p id="credit-balance"></p>
        <script type="text/javascript">
            function fetch_credit_balance() {
                jQuery.post(ajaxurl, {action: 'get_user_credits'}, function(response) {
                    jQuery('#credit-balance').text('Your current credit balance: ' + response);
                });
            }
            setInterval(fetch_credit_balance, 5000); // Fetch every 5 seconds
        </script>
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

// Function to allocate initial credits for the user
function allocate_credits($user_id) {
    $initial_credits = 3; // Number of initial credits to allocate
    
    // Store the initial credit allocation in the user's meta data
    update_user_meta($user_id, 'credits', $initial_credits);
}

// Function to deduct credits when an SMS message is sent
function deduct_credit($user_id) {
    // Get the current credit balance for the user
    $current_credits = get_user_meta($user_id, 'credits', true);

    // Deduct one credit from the balance
    $updated_credits = $current_credits - 1;

    // Delete the current credits meta data to clear the cache
    delete_user_meta($user_id, 'credits');

    // Update the credit balance in the user's meta data
    update_user_meta($user_id, 'credits', $updated_credits);

    // Check if the credits are depleted
    if ($updated_credits <= 0 && $current_credits > 0) {
        // Credits depleted, send an email notification to the admin
        $admin_email = get_option('admin_email'); // Get the admin email address
        $subject = 'Credit Depletion Notification';
        $message = 'The credits for user ID ' . $user_id . ' have been depleted. Please update the credit allocation.';

        // Send the email notification
        wp_mail($admin_email, $subject, $message);

        // Disable the SMS message functionality
        remove_action('woocommerce_order_status_completed', 'send_message_on_order_complete');
    }
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
    
    // Get the user ID associated with the order
    $user_id = $order->get_customer_id();
    
    // Deduct one credit for the SMS message
    deduct_credit($user_id);
    
    // Send the email
    wp_mail($email, $subject, $message);
}
add_action('woocommerce_order_status_completed', 'send_message_on_order_complete');

// Hook the function to user registration event
function allocate_credits_on_registration($user_id) {
    allocate_credits($user_id);
}
add_action('user_register', 'allocate_credits_on_registration');

// Add a custom dashboard widget to display the credit balance
function order_message_plugin_dashboard_widget() {
    // Get the current user ID
    $user_id = get_current_user_id();

    // Get the credit balance for the user
    $credits = get_user_meta($user_id, 'credits', true);
    ?>
    <div class="dashboard-widget">
        <h2>Credit Balance</h2>
        <p>Your current credit balance: <?php echo $credits; ?></p>
    </div>
    <?php
}
function order_message_plugin_add_dashboard_widgets() {
    wp_add_dashboard_widget('order_message_plugin_dashboard_widget', 'Credit Balance', 'order_message_plugin_dashboard_widget');
}
add_action('wp_dashboard_setup', 'order_message_plugin_add_dashboard_widgets');



function topup_credits_page() {
    if (isset($_POST['submit'])) {
        $credits = intval($_POST['credits']); // Get the number of credits from the submitted form
        $user_id = get_current_user_id(); // Get the current user's ID
        $current_credits = get_user_meta($user_id, 'credits', true); // Get the current credit balance

        // Delete the current credits meta data to clear the cache
        delete_user_meta($user_id, 'credits');

        // Update the credit balance with the additional credits
        update_user_meta($user_id, 'credits', $current_credits + $credits);

        echo '<div class="notice notice-success"><p>Credits topped up successfully!</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>Top-up Credits</h1>
        <p>Enter the number of credits you want to top up:</p>
        <form method="post" action="">
            <input type="number" name="credits" min="1" required>
            <p><input type="submit" name="submit" value="Top-up"></p>
        </form>
    </div>
    <?php
}
function ajax_get_user_credits() {
    $user_id = get_current_user_id();
    echo get_user_meta($user_id, 'credits', true);
    wp_die(); // this is required to terminate immediately and return a proper response
}
add_action('wp_ajax_get_user_credits', 'ajax_get_user_credits');

