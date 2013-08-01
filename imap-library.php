<?php
class imap_functions {
    public function retrieve_message($mbox, $messageid) {
        $message = array();
    
        $header = imap_header($mbox, $messageid);
        $subject = $header->subject;
        
        $array = array();
        
        $message['body'] = imap_fetchbody($mbox, $messageid, "1"); ## GET THE BODY OF MESSAGE
           
        $findinsubject   = "Potential Lead";
        $pos = strpos($subject, $findinsubject);
        if ($pos !== false) {
            $result = $this->getpotentiallead($message['body'],$subject);            
        }else{
            $findinsubject   = "Response on Property";
            $pos = strpos($subject, $findinsubject);
            if ($pos !== false) {
                $result = $this->getresponcepropery($message['body'],$subject);            
            }else{
                $array['type'] = "";
                $message['property_id'] = "";
            }
        }
        //echo "<pre>";print_R($array);
        return $result;
    }
    
    public function getProperty($body,$key){
       $pos1 = strpos($body, $key);
       $string = ($key=="Mobile") ? "Email" : "=20";
       $pos2 = strpos($body, $string,$pos1);
       $differecne = $pos2-$pos1; 
       $result = substr($body, $pos1, $differecne); 
       $array = explode(":",$result);
       unset($array[0]);
       return trim($this->spectionchar(implode(" ",$array)));
    }
    
    public function getPropertyId($string, $start, $end){
        $string = " ".$string;
        $ini = strpos($string,$start);
        if ($ini == 0) return "";
        $ini += strlen($start);
        $len = strpos($string,$end,$ini) - $ini;
        return trim($this->spectionchar(substr($string,$ini,$len)));
    }
        
    public function getresponcepropery($message,$subject){
        
        $array = array();
        $forwardedmessage = $this->checkisforward($subject);
        if($forwardedmessage == true){
            $array['type'] = "Response on Property";
            $array['property_id'] = $this->getPropertyId($subject,"ID:","-");
            $array['name'] = trim($this->getPropertyId($message,"Sender's Name:","Mobile:"));
            $array['mobile'] = trim($this->getPropertyId($message,"Mobile:","Email:"));
            $array['email'] = trim(str_replace("*","",$this->getPropertyId($message,"Email:","Message:")));
            $array['message'] = trim($this->getPropertyId($message,"Message:","Click here"));
        }else{
            $array['type'] = "Response on Property";
            $array['property_id'] = trim($this->getPropertyId($subject,"ID:","-"));
            $array['name'] = trim($this->getProperty($message,"Sender's Name:"));
            $array['mobile'] = trim($this->getProperty($message,"Mobile:"));
            $array['email'] = trim(str_replace("*","",$this->getProperty($message,"Email:")));
            $array['message'] = trim($this->getProperty($message,"Message:"));
        }
        return $array;
    }
    
    public function getpotentiallead($message,$subject){
        $array = array();
        $forwardedmessage = $this->checkisforward($subject);
        if($forwardedmessage == true){
            $array['type'] = "Potential Lead";
            $array['property_id'] = trim($this->getPropertyId($subject,"-",":"));
            $array['name'] = trim($this->getPropertyId($message,"Name:","Mobile:"));
            $array['mobile'] = trim($this->getPropertyId($message,"Mobile:","Email:"));
            $array['email'] = trim(str_replace("*","",$this->getPropertyId($message,"Email:","Property")));
        }else{
            $array['type'] = "Potential Lead";
            $array['property_id'] = trim($this->getPropertyId($subject,"-",":"));
            $array['name'] = trim($this->getProperty($message,"Name:"));
            $array['mobile'] = trim($this->getProperty($message,"Mobile:"));
            $array['email'] = trim(str_replace("*","",$this->getProperty($message,"Email:")));
        }
        return $array;
        
    }
            
    public function checkisforward($subject){
        $pos = strpos($subject, "Fwd:");
        if ($pos !== false) {
            return true;
        }else{
            return false;
        }
    }
        
    public function spectionchar($string){
        return str_replace("=20","",$string);
    }
}
?>