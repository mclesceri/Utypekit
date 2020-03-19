<?php

require_once('mail/class.phpmailer.php');

class Email
{
    
    public function _mail($vars) {
        // vars = array(recipient_email(s),reply_to,sender_email,sender_name,subject,message)
        $recipients = explode(',',$vars['recipient_email']);
        $from_name = $vars['sender_name'];
        $from_email = $vars['sender_email'];
        $subject = $vars['subject'];
        $message = $vars['message'];
        
        $mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

        $mail->IsSMTP(); // telling the class to use SMTP
        $mail->WordWrap         = 50; // set word wrap
        $mail->IsHTML(true);
        
        //$mail->SMTPDebug      = 2;// enables SMTP debug information (for testing)
        $mail->SMTPAuth     = true;// enable SMTP authentication
        //$mail->SMTPSecure   = "ssl";// sets the prefix to the server
        $mail->Host         = "mail.dornbos.com"; // sets the SMTP server
        $mail->Port         = 25;// set the SMTP port for the GMAIL server
        $mail->Username     = "cookbook@dornbos.com"; // SMTP account username
        $mail->Password     = "ROPB4014nrsf";// SMTP account password
        
        //$mail->SetFrom('info@dev.cbp.ctcsdev.com','Cookbook Publishers U-Type-It&#153; Online');
        $mail->SetFrom($from_email,$from_name);
        $mail->addReplyTo($from_email,$from_name);
        
        $mail->Subject = $subject;
        $mail->MsgHTML($message);   
        
        foreach($recipients AS $r) {
            $tmp = $r;
            $to_name = substr($tmp,0,(strpos($tmp,'<')-1));
            trim($to_name);
            $tmp = str_replace($to_name,'',$tmp);
            trim($tmp);
            $to_email = substr($tmp,2,-1);
            $mail->AddAddress($to_email,$to_name);
        }
        
        if($mail->Send()) {
            return(true);
        } else {
            return($mail->ErrorInfo);
        }
    }
}

?>
