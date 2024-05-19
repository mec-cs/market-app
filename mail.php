<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once './vendor/autoload.php' ;

require "conf/config.php" ; 

class Mail {
    public static function send($to, $subject, $message) {
        $mail = new PHPMailer(true) ;
        try {
            //SMTP Server settings
            $mail->isSMTP();                                            
            $mail->Host       = 'smtp.gmail.com';                   
            $mail->SMTPAuth   = true;                                   
            $mail->Username   = EMAIL;                                       
            $mail->Password   = PASSWORD ;                     
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587; 
            $mail->SMTPDebug  = 1;
        
            $mail->setFrom(EMAIL, FULLNAME);
            
            //Recipients
            $mail->addAddress($to);     //Add a recipient
            // You can add more than one address
            // See further option of recipients cc, bcc in phpmailer docs.

            //Content
            $mail->isHTML(true);  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $message;
        
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
   }
}