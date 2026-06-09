<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PhpmailerController extends Controller
{
    //
    function index(){

    	require __DIR__.'/../../../vendor/autoload.php';													// load Composer's autoloader

			$mail = new PHPMailer(true);                            // Passing `true` enables exceptions

			try {

				$mail = new PHPMailer();
	    
	    // SMTP configuration
	    $mail = new PHPMailer(); // create a new object
	    $mail->SMTPDebug = 2; //Alternative to above constant

	    //


// Encrypted connection: SSL, SSL/TLS or STARTTLS
//Incoming mail server i.e  IMAP server:  imap.zone.eu port 993
//Incoming mail server i.e  POP3 server:  pop3.zone.eu port 995
/// Outgoing mail server i.e SMTP server:  smtp.zone.eu port 465 SSL/TLS or 587 STARTTLS

	    $mail->isSMTP();
	    //$mail->Host = 'smtp.gmail.com';
	    $mail->Host = 'smtp.zone.eu';
	    $mail->SMTPAuth = true;
	    $mail->Username = (config("global.gmail_username"));
	    $mail->Password = (config("global.gmail_password"));
	    
	    $mail->SMTPSecure = 'TLS';
	    // $mail->Port = 465; // for europe email
	    $mail->Port = 587;
	    
	    
	    $mail->setFrom(config("global.gmail_username"), 'ScanBalt');
	    
	    // Add a recipient
	    
	    $mail->addAddress("srinath.venkata@gmail.com","Venkata Srinath");

	    $mail->Body = "Welcome. This is a test email";
	    
	    
	    
	    // Email subject
	    $mail->Subject = "Test Subject";
	    
	    // if($mailObj['cc']!='')
	    {
	        // $mail->addCC($mailObj['cc'],$mailObj['ccperson']);
	    }
	    
	    // if($mailObj['attachmentpath']!='' && (file_exists($mailObj['attachmentpath'])))
	    {
	        // $mail->addAttachment($mailObj['attachmentpath']);
	        
	    }
	    
	    // if(is_array($mailObj['otherattachments']) && count($mailObj['otherattachments'])>0)
	    {
	        // $otherAttachments = $mailObj['otherattachments'];
	        // for($k=0; $k < count($otherAttachments) ; $k++)
	        {
	            // $mail->addAttachment($otherAttachments[$k]['attachmentpath']);
	            
	        }
	    }
	    // $mail->addReplyTo('kishoreenterprises9@gmail.com', 'Kishore Enterprises');
	    // Set email format to HTML
	    $mail->isHTML(true);
	    $mail->CharSet = 'UTF-8';
	    
	    // if(1){
	    if(!$mail->send()){
	        // echo 'Message could not be sent.';
	        echo 'Mailer Error: ' . $mail->ErrorInfo;
	    }
	    
			} catch (Exception $e) {
				return back()->with('error','Message could not be sent.');
			}

    }
}
