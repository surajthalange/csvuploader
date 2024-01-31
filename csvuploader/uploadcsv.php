<?php
require_once('../../config.php');
require_once('uploadcsv_form.php');
require_once('lib.php'); // Include lib.php for functions

$iid         = optional_param('iid', '', PARAM_INT);
$previewrows = optional_param('previewrows', 10, PARAM_INT);

core_php_time_limit::raise(60 * 60); // 1 hour should be enough.
raise_memory_limit(MEMORY_HUGE);

$returnurl = new moodle_url('/admin/tool/uploaduser/index.php');

if (empty($iid)) {
    $mform1 = new uploadcsv_form();

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

    } else {
        echo $OUTPUT->header();

        echo $OUTPUT->heading_with_help(get_string('uploadusers', 'tool_uploaduser'), 'uploadusers', 'tool_uploaduser');

        $mform1->display();
        echo $OUTPUT->footer();
        die;
    }
} else {
    $cir = new csv_import_reader($iid, 'uploaduser');
}

// Test if columns ok.
$process = new \tool_uploaduser\process($cir);
$filecolumns = $process->get_file_columns();

// Print the header.
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('uploaduserspreview', 'tool_uploaduser'));

// NOTE: this is JUST csv processing preview, we must not prevent import from here if there is something in the file!!
// this was intended for validation of csv formatting and encoding, not filtering the data!!!!
// we definitely must not process the whole file!

// Preview table data.
$table = new \tool_uploaduser\preview($cir, $filecolumns, $previewrows);

echo html_writer::tag('div', html_writer::table($table), ['class' => 'flexible-wrap']);

// Print the form if valid values are available.
if ($table->get_no_error()) {
    // Implement additional logic here if needed
}
echo $OUTPUT->footer();
die;

?>
