<?php
include("imap-library.php");
$imap = new imap_functions();
ini_set('display_errors', 'Off');
error_reporting(0);
set_time_limit(3000);
/* connect to gmail with your credentials */
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = 'mail4jiva'; 
$password = 'Jiva@123';
/* try to connect */
$inbox = imap_open($hostname, $username."@gmail.com", $password) or die('Cannot connect to Gmail: ' . imap_last_error());

// How far back in time do you want to search for messages - one month = 0 , two weeks = 1, one week = 2, three days = 3,
// one day = 4, six hours = 5 or one hour = 6 e.g.--> $m_t = 6;
$m_t = 5;
// unix time gone by or is it bye.....its certanly not bi.......or is it? ......I dunno fooker
$m_gunixtp = array(2592000, 1209600, 604800, 259200, 86400, 21600, 3600, 172800);
// Date to start search
$m_gdmy = date('d-M-Y', time() - $m_gunixtp[$m_t]);
echo $m_gdmy;
//$emails=imap_search ($inbox, 'UNSEEN SINCE ' . $m_gdmy . '');
$emails=imap_search ($inbox, 'ON "'.$m_gdmy.'"');
/* if any emails found, iterate through each email */
if ($emails) {
    $count = 1;
    /* put the newest emails on top */
    rsort($emails);
    echo "<pre>";
    /* for every email... */
    foreach ($emails as $email_number) {
        /* get mail message */
        $data = $imap->retrieve_message($inbox, $email_number);
            print_r($data);
    }
}
/* close the connection */
imap_close($inbox);
?>