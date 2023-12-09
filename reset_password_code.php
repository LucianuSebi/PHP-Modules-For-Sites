<?php

session_start();
include "db_conn.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable("../../");
$dotenv->load();

function sendmail_verify($fName, $lName, $uEmail, $token){
    
    $mail = new PHPMailer(true);
    $mail->isSMTP();                                                
    $mail->Host       = $_ENV['EMAIL_HOST'];                           
    $mail->SMTPAuth   = true;                                       
    $mail->Username   = $_ENV['EMAIL_USERNAME'];             
    $mail->Password   = $_ENV['EMAIL_PASSWORD'];                              
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;                
    $mail->Port       = $_ENV['EMAIL_PORT'];
    
    $mail->setFrom('amariei_sebastianl@yahoo.com', 'Amariei Sebastian-Lucian');
    $mail->addAddress($uEmail);

    $mail->isHTML(true);                                 
    $mail->Subject = 'Password Reset Link from FanConstruct.com';
    $mail->Body    = '
        <h2>You have requested a password reset link with FanConstrucy.com</h2>
        <h5>Reset your password by clicking the link provided below</h5>
        <br></br>
        <a href="'.$_ENV['SITE_URL'].'/change_password.php?token='.$token.'&uEmail='.$uEmail.'"> Click me to reset your password! </a>
    ';
    
    $mail->send();

}

if(isset($_POST['uEmail'])){

    $uEmail = mysqli_real_escape_string($conn, $_POST['uEmail']);
    $token = md5(rand());

    $sql = "SELECT Email FROM users where Email = '$uEmail'";
    $sql_result=mysqli_query($conn,$sql);

    if(mysqli_num_rows($sql_result)>0){

        $row = mysqli_fetch_array($sql_result);
        $fName = $row['FirstName'];
        $lName = $row['LastName'];

        $sql = "UPDATE users SET token='$token' WHERE Email='$uEmail'";
        $sql_result=mysqli_query($conn,$sql);

        if($sql_result){

            sendmail_reset_password($uEmail,$fName,$lName,$token);
            $_SESSION['status']="You have received a password reset link.";
            header("location: ../index.php");
            exit();

        }else{
            header("location: ../reset_password.php?error=Something went wrong");
            exit();
        }

    }else{

        header("location: ../reset_password.php?error=Email not found");
        exit();  

    }
}