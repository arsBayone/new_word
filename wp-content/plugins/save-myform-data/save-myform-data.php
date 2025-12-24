<?php
/**
 * Plugin Name: Save MyForm Data to Database
 */

add_action('elementor_pro/forms/new_record', function ($record, $handler) {

    global $wpdb;

    // 1. Check form ID
    $form_id = $record->get_form_settings('form_id');
    if ($form_id !== 'myform') {
        return;
    }

    // 2. Get submitted fields
    $fields = $record->get('fields');

    $name  = sanitize_text_field($fields['name']['value'] ?? '');
    $email = sanitize_email($fields['email']['value'] ?? '');

    // 3. Insert into database
    $wpdb->insert(
        $wpdb->prefix . 'myform_entries',
        [
            'name'  => $name,
            'email' => $email,
        ],
        ['%s', '%s']
    );

});
