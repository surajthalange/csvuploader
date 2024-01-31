<?php
require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_login();

// Ensure the user has the necessary capability to view sent emails
if (!has_capability('moodle/site:config', context_system::instance())) {
    print_error('insufficientpermissions', 'error');
}

// Set up the page
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/admin/tool/csvuploader/sent_emails.php'));
$PAGE->set_title(get_string('viewsentemails', 'local_csvuploader'));
$PAGE->set_heading(get_string('viewsentemails', 'local_csvuploader'));

// Display the sent emails
echo $OUTPUT->header();

// Function to view sent emails
function view_sent_emails() {
    global $DB;

    // Fetch data from the database (assuming your table is named 'user_email')
    $emails = $DB->get_records('user_email');

    // Display the list of users and the date when the email was sent
    echo '<h2>List of Users who received an Email</h2>';
    echo '<table border="1">';
    echo '<tr><th>User</th><th>Email</th><th>Date Sent</th></tr>';
    foreach ($emails as $email) {
        echo '<tr>';
        echo '<td>' . $email->username . '</td>';
        echo '<td>' . $email->email . '</td>';
        echo '<td>' . userdate($email->datesent) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}

// Call the function to display the list of sent emails
view_sent_emails();

echo $OUTPUT->footer();
?>
