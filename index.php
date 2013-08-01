<?php
include("imap-library.php");
$imap = new imap_functions();
ini_set('display_errors', 'Off');
error_reporting(0);
/**
 *	Uses PHP IMAP extension, so make sure it is enabled in your php.ini,
 *	extension=php_imap.dll
 */
set_time_limit(3000);
/* connect to gmail with your credentials */
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = 'mail4jiva@gmail.com'; # e.g somebody@gmail.com
$password = 'Jiva@123';
/* try to connect */
$inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' .
    imap_last_error());
$emails = imap_search($inbox, 'ALL');
/* useful only if the above search is set to 'ALL' */
$max_emails = 16;
/* if any emails found, iterate through each email */
if ($emails) {
    $count = 1;
    /* put the newest emails on top */
    rsort($emails);
    /* for every email... */
    echo "<pre>";
    foreach ($emails as $email_number) {
        //print_r($email_number); die;
        /* get information specific to this email */
        $overview = imap_fetch_overview($inbox, $email_number, 0);
        /* get mail message */

        $message = imap_fetchbody($inbox, $email_number, 2);
        //echo $message;
        $data = $imap->retrieve_message($inbox, $email_number);
        if(!empty($data['property_id']))
            print_r($data);
        if ($count++ >= $max_emails)
            break;
    }
}
/* close the connection */
imap_close($inbox);
?>