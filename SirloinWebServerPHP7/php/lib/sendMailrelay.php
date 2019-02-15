<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/Exception.php';
require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';
/**
 * sendEmail This function uses the mailing service from mailRelay
 * @param unknown $rcpt
 * @param unknown $body
 * @param unknown $subject
 */
function sendEmail($rcpt, $body, $subject) {
	$curl = curl_init ( 'https://programadepuntos-vip.ip-zone.com/ccm/admin/api/version/2/&type=json' );
	
	$postData = array (
			'function' => 'sendMail',
			'apiKey' => 'wmVxnabEgOCkABo5oKMnGcLTBpEhev2QYOEBofGL',
			'subject' => $subject,
			'html' => $body,
			'mailboxFromId' => 1,
			'mailboxReplyId' => 1,
			'mailboxReportId' => 1,
			'packageId' => 6,
			'emails' => $rcpt 
	);
	
	$post = http_build_query ( $postData );
	
	curl_setopt ( $curl, CURLOPT_POST, true );
	curl_setopt ( $curl, CURLOPT_POSTFIELDS, $post );
	curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
	
	$json = curl_exec ( $curl );
	if ($json === false) {
		die ( 'Request failed with error: ' . curl_error ( $curl ) );
	}
	
	$result = json_decode ( $json );
	return $result;
	if ($result->status == 0) {
		die ( 'Bad status returned. Error: ' . $result->error );
	}
}

function sendIndividualEmail($rcpt, $body, $subject) {
	$mail = new PHPMailer(true); // Passing `true` enables exceptions
	try {
		//Server settings
// 		$mail->SMTPDebug = 2; // Enable verbose debug output
		$mail->isSMTP(); // Set mailer to use SMTP
		$mail->Host = 'smtp.ip-zone.com'; // Specify main and backup SMTP servers mail.programadepuntos-vip.com
		$mail->SMTPAuth = true; // Enable SMTP authentication
		$mail->Username = 'programadepuntos-vip'; // SMTP username soporte@programadepuntos-vip.com
		$mail->Password = 'b683853d'; // SMTP password
		// $mail->SMTPSecure = 'tls'; // Enable TLS encryption,tls or `ssl` also accepted
		$mail->Port = 2525; // TCP port to connect to
		
		//Recipients
		$mail->setFrom('soporte@programadepuntos-vip.com', 'Soporte Sirloin');
		$mail->addAddress($rcpt); // Add a recipient
		$mail->addAddress("promociondepuntos@sirloindf.com"); // Add a recipient
		//$mail->addAddress('webmaster@soft-webcosmos.com.mx'); // Name is optional
		// $mail->addReplyTo('info@example.com', 'Information');
		// $mail->addCC('cc@example.com');
		$mail->addBCC('jcenteno.ibm@gmail.com');
		
		//Attachments
		// $mail->addAttachment('/var/tmp/file.tar.gz'); // Add attachments
		// $mail->addAttachment('/tmp/image.jpg', 'new.jpg'); // Optional name
		
		//Content
		$mail->isHTML(true); // Set email format to HTML
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->AltBody = $body;
		
		$mail->send();
		return 'Message has been sent';
	} catch (Exception $e) {
		return 'Message could not be sent. Mailer Error: '. $mail->ErrorInfo;
	}
}

// Create rcpt array to send emails to 2 rcpts
// $rcpt = array (
// array (
// 'name' => 'Jorge Centeno',
// 'email' => 'jorge.centeno@cc-datweb.com.mx'
// ),
// array (
// 'name' => "Ricardo Vargas Gomez",
// 'email' => 'ricardo.vargas@itprotec.com.mx'
// ),
// array (
// 'name' => "Ricardo Vargas Gomez",
// 'email' => 'rickvago@live.com.mx'
// ),
// array (
// 'name' => "Ricardo Vargas Gomez",
// 'email' => 'rickmar_98@yahoo.com'
// ),
// array (
// 'name' => "Miguel Angel Jaime Cruz",
// 'email' => 'angel.jaime@itprotec.com.mx'
// ),
// array (
// 'name' => "Miguel Angel Jaime Cruz",
// 'email' => 'jc_legn@hotmail.com'
// ),
// array (
// 'name' => "Miguel Angel Jaime Cruz",
// 'email' => 'soporte@itprotec.com.mx'
// ),
// array (
// 'name' => "Miguel Angel Jaime Cruz",
// 'email' => 'jclegn@gmail.com'
// )
// );

?>