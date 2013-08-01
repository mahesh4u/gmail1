<?php
/**
include("imap-library.php");
$imap = new imap_functions();
ini_set('display_errors', 'Off');
error_reporting(0);

 *	Uses PHP IMAP extension, so make sure it is enabled in your php.ini,
 *	extension=php_imap.dll
 */
set_time_limit(3000);



$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = 'mahesh.loginto@gmail.com';
$password = 'ocotlury';

function CountUnreadMail($host, $login, $passwd) {
    $mbox = imap_open($host, $login, $passwd);
    $count = 0;
    if (!$mbox) {
        echo "Error";
    } else {
        $headers = imap_headers($mbox);
        
        
        foreach ($headers as $mail) {
            $flags = substr($mail, 0, 4);
            $isunr = (strpos($flags, "U") !== false);
            if ($isunr)
            $count++;
        }
    }

    imap_close($mbox);
    return $count;
}


$count = CountUnreadMail($hostname, $username, $password);
echo $count.">>>>>>";
?>