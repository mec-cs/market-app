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
        $mail->Host       = 'asmtp.bilkent.edu.tr';                   
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = EMAIL;                                       
        $mail->Password   = PASSWORD ;                     
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587; 
    
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
        echo '<p>Authentication mail has been sent to your mail account. Please check the code and provide it to login the system.</p>';
    } catch (Exception $e) {
        echo "<p>Authentication mail could not be sent, please try few seconds later.";
    }
   }
}