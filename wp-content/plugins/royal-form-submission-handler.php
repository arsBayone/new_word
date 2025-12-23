add_action('royal_elementor_form_submit', 'save_main_form_data', 10, 2);

function save_main_form_data($form_id, $form_data) {

    // Match your form ID
    if ($form_id !== 'main_form') {
        return;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'contact_leads';

    // Get fields by Field ID
    $name   = sanitize_text_field($form_data['name'] ?? '');
    $mobile = sanitize_text_field($form_data['mobile'] ?? '');
    $email  = sanitize_email($form_data['email'] ?? '');

    // Validation
    if (empty($name) || empty($mobile) || empty($email)) {
        return;
    }

    // Insert into DB
    $wpdb->insert(
        $table,
        [
            'name'   => $name,
            'mobile' => $mobile,
            'email'  => $email,
        ],
        ['%s', '%s', '%s']
    );
}

<!-- add_action('royal_elementor_form_submit', function($form_id, $form_data) {
    error_log('FORM SUBMITTED: ' . $form_id);
    error_log(print_r($form_data, true));
}, 1, 2); -->
