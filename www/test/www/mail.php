<?php
$to      = 'anton@dev4masses.com';
$subject = 'the subject';
$message = 'hello';
$headers = 'From: webmaster@oxford-test.com' . "\r\n" .
      'Reply-To: webmaster@oxford-test.com' . "\r\n" .
          'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);
?>
