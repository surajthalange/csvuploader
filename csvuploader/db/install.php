<?php

function xmldb_local_csvuploader_install() {
    global $DB;

    // Create the user_email table
    $user_email_table = new xmldb_table('user_email');

    // Add columns to user_email table
    $user_email_table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $user_email_table->add_field('username', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $user_email_table->add_field('email', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $user_email_table->add_field('datesent', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

    // Add keys to user_email table
    $user_email_table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    // Create the user_email table
    $DB->create_table($user_email_table);

    // Create the csv_data table
    $csv_data_table = new xmldb_table('csv_data');

    // Add columns to csv_data table
    $csv_data_table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $csv_data_table->add_field('username', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $csv_data_table->add_field('email', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $csv_data_table->add_field('firstname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $csv_data_table->add_field('lastname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

    // Add keys to csv_data table
    $csv_data_table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    // Create the sent_emails table
    $sent_emails_table = new xmldb_table('sent_emails');

    // Add columns to sent_emails table
    $sent_emails_table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $sent_emails_table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $sent_emails_table->add_field('datesent', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

    // Add keys to sent_emails table
    $sent_emails_table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    // Create the sent_emails table
    $DB->create_table($sent_emails_table);

    // Perform any additional installation tasks here
    // For example, add capabilities, settings, etc.
}

function xmldb_local_csvuploader_upgrade($oldversion) {
    // Perform upgrade tasks here
    // Check the current version and apply any necessary changes
    return true;
}

function xmldb_local_csvuploader_uninstall() {
    global $DB;

    // Drop the user_email table on uninstallation
    $DB->drop_table('user_email');

    // Drop the csv_data table on uninstallation
    $DB->drop_table('csv_data');

    // Drop the sent_emails table on uninstallation
    $DB->drop_table('sent_emails');

    // Perform any additional uninstallation tasks here
    // For example, remove capabilities, settings, etc.
}

// Add any other functions or modifications specific to your plugin
