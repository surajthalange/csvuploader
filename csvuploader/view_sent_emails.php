<?php
require_once('../../config.php'); // Adjust the path based on your Moodle installation
require_once($CFG->libdir . '/adminlib.php');
require_login();

// Ensure the user has the necessary capability to view sent emails
if (!has_capability('moodle/site:config', context_system::instance())) {
    print_error('insufficientpermissions', 'error');
}

// Set up the page
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/admin/tool/csvuploader/view_sent_emails.php')); // Adjust the URL as needed
$PAGE->set_title(get_string('viewsentemails', 'local_csvuploader'));
$PAGE->set_heading(get_string('viewsentemails', 'local_csvuploader'));

// Display the sent emails
echo $OUTPUT->header();

// Assuming you have a function named view_sent_emails that displays the list of sent emails
view_sent_emails();

echo $OUTPUT->footer();
?>
