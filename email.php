<?php

// enter gmail username below e.g.--> $m_username = "yourusername";
ini_set('display_errors', 'Off');
error_reporting(0);
set_time_limit(3000);
$m_username = "mail4jiva";

// enter gmail password below e.g.--> $m_password = "yourpword";
$m_password = "Jiva@123";

// enter the number of unread messages you want to display from mailbox or
//enter 0 to display all unread messages e.g.--> $m_acs = 0;
$m_acs = 15;

// How far back in time do you want to search for unread messages - one month = 0 , two weeks = 1, one week = 2, three days = 3,
// one day = 4, six hours = 5 or one hour = 6 e.g.--> $m_t = 6;
$m_t = 7;

//----------->Nothing More to edit below
//open mailbox..........please
$m_mail = imap_open ("{imap.gmail.com:993/imap/ssl}INBOX", $m_username . "@gmail.com", $m_password)

// or throw a freakin error............you pig
or die("ERROR: " . imap_last_error());

// unix time gone by or is it bye.....its certanly not bi.......or is it? ......I dunno fooker
$m_gunixtp = array(2592000, 1209600, 604800, 259200, 86400, 21600, 3600, 172800);

// Date to start search
$m_gdmy = date('d-M-Y', time() - $m_gunixtp[$m_t]);
echo $m_gdmy;
//search mailbox for unread messages since $m_t date
//$m_search=imap_search ($m_mail, 'UNSEEN SINCE ' . $m_gdmy . '');

$m_search=imap_search ($m_mail, 'ON "'.$m_gdmy.'"');

//If mailbox is empty......Display "No New Messages", else........ You got mail....oh joy
if($m_search < 1){
$m_empty = "No New Messages";}
else {

// Order results starting from newest message
rsort($m_search);

//if m_acs > 0 then limit results
if($m_acs > 0){
array_splice($m_search, $m_acs);
}

//loop it
foreach ($m_search as $what_ever) {

//get imap header info for obj thang
$obj_thang = imap_headerinfo($m_mail, $what_ever);
$obj_body = imap_body($m_mail, $what_ever);
//Then spit it out below.........if you dont swallow
echo "<body bgcolor=D3D3D3><div align=center><br /><font face=Arial size=2 color=#800000>Message ID# " . $what_ever . "</font>

<table bgcolor=#D3D3D3 width=700 border=1 bordercolor=#000000 cellpadding=0 cellspacing=0>
<tr>
<td><table width=100% border=0>
<tr>
<td><table width=100% border=0>
<tr>
<td bgcolor=#F8F8FF><font face=Arial size=2 color=#800000>Date:</font> <font face=Arial size=2 color=#000000>" . date("F j, Y, g:i a", $obj_thang->udate) . "</font></td>
<td bgcolor=#F8F8FF><font face=Arial size=2 color=#800000>From:</font> <font face=Arial size=2 color=#000000>" . $obj_thang->fromaddress . "</font></td>
<td bgcolor=#F8F8FF><font face=Arial size=2 color=#800000>To:</font> <font face=Arial size=2 color=#000000>" . $obj_thang->toaddress . " </font></td>
</tr>
<tr>
</table>
</td>
</tr><tr><td bgcolor=#F8F8FF><font face=Arial size=2 color=#800000>Subject:</font> <font face=Arial size=2 color=#000000>" . $obj_thang->Subject . "</font></td></tr><tr>
</tr>

<tr><td bgcolor=#F8F8FF><font face=Arial size=2 color=#800000>Message:</font> <font face=Arial size=2 color=#000000>" . $obj_body . "</font></td></tr><tr>
</tr>

</table></td>
</tr>
</table></font><br /></div></body>";
$subject=$obj_thang->Subject;
$data = retrieve_message($subject, $obj_body);
print_r($data);
}

} echo "<div align=center><font face=Arial size=4 color=#800000><b>" . $m_empty . "</b></font></div>";
//close mailbox bi by bye
imap_close($m_mail);


function retrieve_message($subject, $obj_body) {
    echo "";
        $message = array();
    
        //$header = imap_header($mbox, $messageid);
        //$subject = $subject;
        
        $array = array();
        
        //$message['body'] = imap_fetchbody($mbox, $messageid, "1"); ## GET THE BODY OF MULTI-PART MESSAGE
           
        $findinsubject1   = "Potential Lead";
        $pos = strpos($subject, $findinsubject1);
        echo $pos."<br>";
        if ($pos !== false) {
            $result = $this->getpotentiallead($obj_body,$subject);
            
        }else{
            $findinsubject1   = "Response on Property";
            $pos = strpos($subject, $findinsubject1);
            if ($pos !== false) {
                $result = $this->getresponcepropery($obj_body,$subject);
            
            }else{
                $array['type'] = "";
                $message['property_id'] = "";
            }
        }
    //echo "<pre>";print_R($array);
        return $result;
    }
    
    function getProperty($body,$key){
       $pos = strpos($body, $key);
       $string = ($key=="Mobile") ? "Email" : "=20";
       $pos1 = strpos($body, $string,$pos);
       $differecne = $pos1-$pos; 
       $result = substr($body, $pos, $differecne); 
       $array = explode(":",$result);
       unset($array[0]);
       return trim($this->spectionchar(implode(" ",$array)));
    }
    function getPropertyId($string, $start, $end){
        $string = " ".$string;
        $ini = strpos($string,$start);
        if ($ini == 0) return "";
        $ini += strlen($start);
        $len = strpos($string,$end,$ini) - $ini;
        return trim($this->spectionchar(substr($string,$ini,$len)));
    }
    function getresponcepropery($message,$subject){
        
        $array = array();
        $forwardedmessage = $this->checkisforward($subject);
        if($forwardedmessage == true){
            $array['type'] = "Response on Property";
            $array['property_id'] = $this->getPropertyId($subject,"ID:","-");
            $array['name'] = $this->getPropertyId($message,"Sender's Name:","Mobile");
            $array['mobile'] = $this->getPropertyId($message,"Mobile:","Email");
            $array['email'] = $this->getPropertyId($message,"Email:","Message");
            $array['message'] = $this->getPropertyId($message,"Message:","Click here");
        }else{
            $array['type'] = "Response on Property";
            $array['property_id'] = $this->getPropertyId($subject,"ID:","-");
            $array['name'] = $this->getProperty($message,"Sender's Name");
            $array['mobile'] = $this->getProperty($message,"Mobile");
            $array['email'] = $this->getProperty($message,"Email");
            $array['message'] = $this->getProperty($message,"Message:");
        }
        return $array;
    }
    
    function getpotentiallead($message,$subject){
        $array = array();
        $forwardedmessage = $this->checkisforward($subject);
        if($forwardedmessage == true){
            $array['type'] = "Potential Lead";
            $array['property_id'] = $this->getPropertyId($subject,"-",":");
            $array['name'] = $this->getPropertyId($message,"Name","Mobile");
            $array['mobile'] = $this->getPropertyId($message,"Mobile","Email");
            $array['email'] = $this->getPropertyId($message,"Email",".com");
        }else{
            $array['type'] = "Potential Lead";
            $array['property_id'] = $this->getPropertyId($subject,"-",":");
            $array['name'] = $this->getProperty($message,"Name");
            $array['mobile'] = $this->getProperty($message,"Mobile");
            $array['email'] = $this->getProperty($message,"Email");
        }
        return $array;
        
    }
    function checkisforward($subject){
            $pos = strpos($subject, "Fwd:");
            if ($pos !== false) {
                return true;
            }else{
                return false;
            }
    }
    function spectionchar($string){
        return str_replace("=20"," ",$string);
    }









//$yourEmail = "gmohan.reach@gmail.com";
//$yourEmailPassword = "savewater";

//$mailbox = imap_open("{imap.gmail.com:993/ssl}INBOX", $yourEmail, $yourEmailPassword);
//$mail = imap_search($mailbox, "ALL");
//$mail_headers = imap_headerinfo($mailbox);
//$body = imap_body($mailbox, $mail[0]);

//foreach($mail_headers as $head){
//$subject = $head->subject;
//$from = $head->fromaddress;

//}


//echo $subject ."<br>";
//echo $from."<br>";
//echo $body;

//imap_setflag_full($mailbox, $mail[0], "\\Seen \\Flagged");
//imap_close($mailbox);



	 	
//		$username = 'gmohan.reach@gmail.com';
//		$password = 'savewater';
//		
//	$mboxconnstr='{imap.gmail.com:993/imap/ssl}';
////    $mboxconnstr='{mail.bdways.com:143/imap/novalidate-cert}';
//    $mailbox = imap_open($mboxconnstr, $username, $password, OP_HALFOPEN) or die("can't connect: " . imap_last_error());
//
//    echo "<h1>Mailboxes</h1>\n";
//    $email = imap_fetchheader($mailbox); //get email header
//     $lines = explode("\n", $email);
// 
//     // data we are looking for
//     $from = "";
//     $subject = "";
//     $to = "";
//     $headers = "";
//     $splittingheaders = true;
//     
//     for ($i=0; $i < count($lines); $i++) {
//       if ($splittingheaders) {
//         // this is a header
//          $headers .= $lines[$i]."\n";
//         
//         // look out for special headers
//         if (preg_match("/^Subject: (.*)/", $lines[$i], $matches)) {
//          $subject = $matches[1];
//         }
//
//         if (preg_match("/^From: (.*)/", $lines[$i], $matches)) {
//          $from = $matches[1];
//         }
//
//         if (preg_match("/^To: (.*)/", $lines[$i], $matches)) {
//          $to = $matches[1];
//         }
//         
//       }//end of if statement
//     }// end of for loop
//     
//     //We can just display the relevant information in our browser, like below or write some method, that will put that information in a database
//      echo "FROM: ".$from."<br>";
//      echo "TO: ".$to."<br>";
//      echo "SUBJECT: ".$subject."<br>";
//   	  $st = imap_fetchstructure($mailbox, $num);
//
//   // echo('<pre>'); print_r($list_folders); echo('</pre>'); 
//		

	?>