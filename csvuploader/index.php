<?php

// Ensure the script is only executed in the Moodle context.
define('MOODLE_INTERNAL', true);
define('MOODLE_ROOT', dirname(dirname(dirname(__FILE__))));
require_once(MOODLE_ROOT . '/config.php');
global $CFG, $DB;
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/csvlib.class.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/uploaduser/locallib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/uploaduser/user_form.php');

require_login();

$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/local/csvuploader/index.php'));
$PAGE->set_title(get_string('csvuploader', 'local_csvuploader'));
$PAGE->set_heading(get_string('csvuploader', 'local_csvuploader'));

$action = optional_param('action', '', PARAM_ALPHA);

switch ($action) {
    case 'upload':
        $mform1 = new admin_uploaduser_form1();

        if ($formdata = $mform1->get_data()) {
            $iid = csv_import_reader::get_new_iid('uploaduser');
            $cir = new csv_import_reader($iid, 'uploaduser');

            $content = $mform1->get_file_content('userfile');

            $readcount = $cir->load_csv_content($content, $formdata->encoding, $formdata->delimiter_name);
            $csvloaderror = $cir->get_error();
            unset($content);

            if (!is_null($csvloaderror)) {
                throw new \moodle_exception('csvloaderror', '', $returnurl, $csvloaderror);
            }

            $columns = $cir->get_columns();
            $columns = array_map('clean_param', $columns, PARAM_ALPHANUMEXT);

            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('csvuploader', 'local_csvuploader'));
            echo html_writer::start_tag('ul');
            foreach ($columns as $column) {
                echo html_writer::tag('li', $column);
            }
            echo html_writer::end_tag('ul');
            echo $OUTPUT->footer();
            die;
        } else {
            echo $OUTPUT->header();

            echo $OUTPUT->heading_with_help(get_string('uploadusers', 'tool_uploaduser'), 'uploadusers', 'tool_uploaduser');

            $mform1->display();
            echo $OUTPUT->footer();
            die;
        }
        break;

    case 'send_sample_email':
        $users = $DB->get_records('csv_data', array());
        foreach ($users as $user) {
            $email = $user->email;
            $subject = 'Sample Email Subject';
            $message = 'Sample Email Content';
            email_to_user($user, get_admin(), $subject, $message);
            $sent_email = new stdClass();
            $sent_email->userid = $user->id;
            $sent_email->datesent = time();
            $DB->insert_record('sent_emails', $sent_email);
        }
        redirect(new moodle_url('/local/csvuploader/index.php'), get_string('randomemailsent', 'local_csvuploader'));
        break;

    case 'view_sent_emails':
        $sent_emails = $DB->get_records('sent_emails', array());
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('viewsentemails', 'local_csvuploader'));
        if (!empty($sent_emails)) {
            echo html_writer::start_tag('ul');
            foreach ($sent_emails as $sent_email) {
                $user = $DB->get_record('user', array('id' => $sent_email->userid));
                echo html_writer::tag('li', get_string('user', 'local_csvuploader') . ': ' . fullname($user) . ', ' .
                    get_string('sentdate', 'local_csvuploader') . ': ' . userdate($sent_email->datesent));
            }
            echo html_writer::end_tag('ul');
        } else {
            echo html_writer::tag('p', get_string('nosentemails', 'local_csvuploader'));
        }
        echo $OUTPUT->footer();
        die;
        break;

    default:
        // Display any default content or redirect as needed.
        break;
}
