<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/csvlib.class.php');

/**
 * Process uploaded CSV file.
 *
 * @param array $file The uploaded file array.
 */
function process_uploaded_csv($file) {
    global $DB, $CFG, $USER;

    // Check if $file is set and is an array
    if (isset($file) && is_array($file)) {
        // Process the CSV file (e.g., save to a temporary location)
        $tempdir = make_temp_directory('csvuploader');

        // Check if 'name' key is present in $file array
        if (isset($file['name'])) {
            $filepath = $tempdir . '/' . $file['name'];

            // Move the uploaded file to the temporary directory
            move_uploaded_file($file['tmp_name'], $filepath);

            // Example: Display the columns of the CSV file
            $csv_columns = get_csv_columns($filepath);
            echo '<h2>CSV Columns</h2>';
            echo '<ul>';
            foreach ($csv_columns as $column) {
                echo '<li>' . $column . '</li>';
            }
            echo '</ul>';

            // Example: Queue sample email and track sent emails
            queue_and_track_emails($filepath, $csv_columns);
        } else {
            echo 'File name not found in the uploaded file array.';
        }
    } else {
        echo 'Invalid file data.';
    }
}

/**
 * Queue sample email and track sent emails.
 *
 * @param string $filepath The path to the CSV file.
 * @param array $csv_columns Array of CSV column names.
 */
function queue_and_track_emails($filepath, $csv_columns) {
    global $DB, $USER;

    // Example: Queue a sample email to users and track sent emails (modify as needed)
    $admins = get_users_by_capability(context_system::instance(), 'moodle/site:doanything', 'u.id, u.email', 'u.id');
    foreach ($admins as $admin) {
        $email = $admin->email;
        send_sample_email($USER->firstname, $USER->lastname, $email);
        track_sent_emails($USER->id, $email);
        save_sent_email_to_database($USER->firstname, $USER->lastname, $email);
    }

    // Example: Save CSV data to the database
    save_csv_to_database($filepath, $csv_columns);
}

/**
 * Send a sample email.
 *
 * @param string $firstname User's first name.
 * @param string $lastname User's last name.
 * @param string $email User's email address.
 */
function send_sample_email($firstname, $lastname, $email) {
    // Implement logic to send a sample email to the specified user
    // You can use Moodle's email API or any other email sending method
    // For simplicity, we'll just display the information here
    echo "Sent email to: $firstname $lastname ($email)<br>";
}

/**
 * Track sent emails in the database.
 *
 * @param int $userid User ID.
 * @param string $email User's email address.
 */
function track_sent_emails($userid, $email) {
    global $DB;

    // Example: Track sent email in the database (modify as needed)
    $record = new stdClass();
    $record->userid = $userid;
    $record->email = $email;
    $record->datesent = time(); // Assuming 'datesent' is a timestamp field in your table
    $DB->insert_record('user_email', $record);
}

/**
 * Save sent email data to the database.
 *
 * @param string $firstname User's first name.
 * @param string $lastname User's last name.
 * @param string $email User's email address.
 */
function save_sent_email_to_database($firstname, $lastname, $email) {
    global $DB;

    // Define the data to be inserted
    $data = new stdClass();
    $data->username = "$firstname $lastname";
    $data->email = $email;
    $data->datesent = time(); // You can set the current timestamp as the date sent

    // Insert data into the 'user_email' table
    $DB->insert_record('user_email', $data);
}

/**
 * View sent emails.
 */
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

/**
 * Save CSV data to the database.
 *
 * @param string $filepath The path to the CSV file.
 * @param array $csv_columns Array of CSV column names.
 */
function save_csv_to_database($filepath, $csv_columns) {
    global $DB;

    // Assume that the CSV columns include 'firstname', 'lastname', and 'email'
    $firstname_column = array_search('firstname', $csv_columns);
    $lastname_column = array_search('lastname', $csv_columns);
    $email_column = array_search('email', $csv_columns);

    if ($firstname_column !== false && $lastname_column !== false && $email_column !== false) {
        // Open the CSV file for reading
        $file = fopen($filepath, 'r');
        
        // Skip the header row
        fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            $firstname = trim($row[$firstname_column]);
            $lastname = trim($row[$lastname_column]);
            $email = trim($row[$email_column]);

            // Example: Save CSV data to the database
            save_csv_row_to_database($firstname, $lastname, $email);
        }

        // Close the CSV file
        fclose($file);
    }
}

/**
 * Save a CSV row to the database.
 *
 * @param string $firstname User's first name.
 * @param string $lastname User's last name.
 * @param string $email User's email address.
 */
function save_csv_row_to_database($firstname, $lastname, $email) {
    global $DB;

    // Define the data to be inserted
    $data = new stdClass();
    $data->firstname = $firstname;
    $data->lastname = $lastname;
    $data->email = $email;

    // Insert data into the 'csv_data' table (adjust the table name as needed)
    $DB->insert_record('csv_data', $data);
}
?>
