<?php
session_start();

include "db_conn.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable("../../");
$dotenv->load();

function sendmail_contact($fName, $lName,$uEmail,$oras,$mesaj){
    
    $mail = new PHPMailer(true);
    $mail->isSMTP();                                                
    $mail->Host       = $_ENV['EMAIL_HOST'];                           
    $mail->SMTPAuth   = true;                                       
    $mail->Username   = $_ENV['EMAIL_USERNAME'];             
    $mail->Password   = $_ENV['EMAIL_PASSWORD'];                              
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;                
    $mail->Port       = $_ENV['EMAIL_PORT'];
    
    $mail->setFrom('amariei_sebastianl@yahoo.com', 'Amariei Sebastian-Lucian');
    $mail->addAddress($_ENV['EMAIL_USERNAME']);

    $mail->isHTML(true);                                 
    $mail->Subject = 'Mesaj de la '.$uEmail.'';
    $mail->Body    = '
        <h2>Ati primit un mesaj de la '.$lName.' '.$fName.', din orasul '.$oras.', cu mail-ul '.$uEmail.'</h2>
        <h5>Mesajul este urmatorul:
        '.$mesaj.'</h5>
    ';
    
    $mail->send();

}

$fName = mysqli_real_escape_string($conn, $_POST['fName']);
$lName = mysqli_real_escape_string($conn, $_POST['lName']);
$oras = mysqli_real_escape_string($conn, $_POST['oras']);
$uEmail = mysqli_real_escape_string($conn, $_POST['uEmail']);
$mesaj = mysqli_real_escape_string($conn, $_POST['mesaj']);
sendmail_contact($fName, $lName,$uEmail,$oras,$mesaj);

header("location: ../contact.php");
exit();