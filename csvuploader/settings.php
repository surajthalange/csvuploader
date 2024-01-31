<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $ADMIN->add('root', new admin_category('local_csvuploader', get_string('pluginname', 'local_csvuploader')));

    $settings = new admin_settingpage('local_csvuploader_settings', get_string('csvuploader_settings', 'local_csvuploader'));

    // Add CSV uploader settings fields here
    $settings->add(new admin_setting_configtext('local_csvuploader/upload_path', get_string('uploadpath', 'local_csvuploader'), '/path/to/upload/directory', PARAM_TEXT));

    // Add additional settings as needed

    // Example: Add a link to view sent emails
    $settings->add(new admin_setting_configtext('local_csvuploader/view_sent_emails_link',
        get_string('viewsentemails', 'local_csvuploader'),
        get_string('viewsentemails_desc', 'local_csvuploader'),
        "$CFG->wwwroot/local/csvuploader/sent_emails.php",
        PARAM_TEXT));

    // Example: Add a checkbox for sending random emails
    $settings->add(new admin_setting_configcheckbox('local_csvuploader/send_random_email',
        get_string('sendrandomemail', 'local_csvuploader'),
        get_string('sendrandomemail_desc', 'local_csvuploader'), 0));

    // Example: Add a button to trigger sending random emails
    $settings->add(new admin_setting_configtext('local_csvuploader/send_random_email_button',
        get_string('sendrandomemail', 'local_csvuploader'),
        get_string('sendrandomemail_desc', 'local_csvuploader'),
        null, // default value
        PARAM_TEXT)); // change PARAM_TEXT to the appropriate data type for your button value

    $settings->add(new admin_setting_configcheckbox('local_csvuploader/random_email_sent',
        get_string('randomemailsent', 'local_csvuploader'),
        get_string('randomemailsent', 'local_csvuploader'),
        false)); // This checkbox can be used to indicate whether random emails were sent or not

    // Add a function to display a list of users who have been sent an email with the date
    $settings->add(new admin_setting_configtext('local_csvuploader/view_sent_emails',
        get_string('viewsentemails', 'local_csvuploader'),
        get_string('viewsentemails_desc', 'local_csvuploader'),
        'view_sent_emails',
        PARAM_TEXT));

    $ADMIN->add('local_csvuploader', $settings);
}
?>
