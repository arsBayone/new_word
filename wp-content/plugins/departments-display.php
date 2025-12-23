<?php
/*
Plugin Name: Departments Display
Description: CRUD for departments table using shortcode
Version: 1.1
*/

if ( ! defined('ABSPATH') ) exit;

function wp_departments_shortcode() {
    global $wpdb;
    $table = $wpdb->prefix . 'departments';

    // Handle Add
    if ( isset($_POST['add_department']) && wp_verify_nonce($_POST['dept_nonce'], 'add_dept') ) {
        $wpdb->insert($table, [
            'department_name' => sanitize_text_field($_POST['department_name'])
        ]);
    }

    // Handle Delete
    if ( isset($_GET['delete']) ) {
        $wpdb->delete($table, ['department_id' => intval($_GET['delete'])]);
    }

    // Handle Update
    if ( isset($_POST['update_department']) && wp_verify_nonce($_POST['dept_nonce'], 'update_dept') ) {
        $wpdb->update(
            $table,
            ['department_name' => sanitize_text_field($_POST['department_name'])],
            ['department_id' => intval($_POST['department_id'])]
        );
    }

    $results = $wpdb->get_results("SELECT * FROM $table");

    ob_start();
    ?>

    <!-- Add Form -->
    <form method="post">
        <input type="text" name="department_name" placeholder="Department name" required>
        <?php wp_nonce_field('add_dept', 'dept_nonce'); ?>
        <button type="submit" name="add_department">Add Department</button>
    </form>

    <br>

    <!-- Table -->
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Department</th>
            <th>Actions</th>
        </tr>

        <?php foreach ($results as $row): ?>
            <tr>
                <td><?php echo esc_html($row->department_id); ?></td>

                <?php if ( isset($_GET['edit']) && $_GET['edit'] == $row->department_id ): ?>
                    <td>
                        <form method="post">
                            <input type="text" name="department_name" value="<?php echo esc_html($row->department_name); ?>" required>
                            <input type="hidden" name="department_id" value="<?php echo esc_html($row->department_id); ?>">
                            <?php wp_nonce_field('update_dept', 'dept_nonce'); ?>
                            <button name="update_department">Update</button>
                        </form>
                    </td>
                    <td></td>
                <?php else: ?>
                    <td><?php echo esc_html($row->department_name); ?></td>
                    <td>
                        <a href="?edit=<?php echo $row->department_id; ?>">Edit</a> |
                        <a href="?delete=<?php echo $row->department_id; ?>" onclick="return confirm('Delete this department?')">Delete</a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php
    return ob_get_clean();
}

add_shortcode('show_departments', 'wp_departments_shortcode');


function wp_employee_names_shortcode() {
    global $wpdb;

    $table = $wpdb->prefix . 'employees';

    $employees = $wpdb->get_results(
        "SELECT first_name, last_name FROM $table"
    );

    if ( empty($employees) ) {
        return '';
    }

    $names = [];

    foreach ( $employees as $emp ) {
        $names[] = esc_html($emp->first_name . ' ' . $emp->last_name);
    }

    return implode(', ', $names);
}

add_shortcode('employee_names', 'wp_employee_names_shortcode');


function wp_employee_names_list_shortcode() {
    global $wpdb;

    $table = $wpdb->prefix . 'employees';

    $employees = $wpdb->get_results(
        "SELECT first_name, last_name FROM $table ORDER BY first_name ASC"
    );

    if ( empty($employees) ) {
        return '';
    }

    $output = '<ul class="employee-list">';

    foreach ( $employees as $emp ) {
        $output .= '<li>' . esc_html($emp->first_name . ' ' . $emp->last_name) . '</li>';
    }

    $output .= '</ul>';

    return $output;
}

add_shortcode('employee_list', 'wp_employee_names_list_shortcode');
