<?php

session_start();
include "db_conn.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable("../../");
$dotenv->load();
var_dump($_ENV);

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
    $mail->Subject = 'Email Verification from FanConstruct.com';
    $mail->Body    = '
        <h2>You have Registered with FanConstruct.com</h2>
        <h5>Verify your email adress to Login with by clicking the link provided below</h5>
        <br></br>
        <a href="'.$_ENV['SITE_URL'].'/php/verify-email.php?token='.$token.'"> Click me to verify! </a>
    ';
    
    $mail->send();

}

if (isset($_POST['fName']) && isset($_POST['lName']) && isset($_POST['uPhone']) && isset($_POST['uEmail']) && isset($_POST['uPass']) && isset($_POST['uRePass'])){

    $fName = mysqli_real_escape_string($conn, $_POST['fName']);
    $lName = mysqli_real_escape_string($conn, $_POST['lName']);
    $uPhone = mysqli_real_escape_string($conn, $_POST['uPhone']);
    $uEmail = mysqli_real_escape_string($conn, $_POST['uEmail']);
    $uPass = mysqli_real_escape_string($conn, $_POST['uPass']);
    $uRePass = mysqli_real_escape_string($conn, $_POST['uRePass']);
    $token = md5(rand());

    if(empty($fName)){

        header("location: ../index.php?error=First Name is required");
        exit();

    }else if(empty($lName)){

        header("location: ../index.php?error=Last Name is required");
        exit();

    }else if(empty($uPhone)){

        header("location: ../index.php?error=Phone Noumber is required");
        exit();

    }else if(empty($uEmail)){

        header("location: ../index.php?error=Email is required");
        exit();

    }else if(empty($uPass)){

        header("location: ../index.php?error=Password is required");
        exit();

    }else if(empty($uRePass)){

        header("location: ../index.php?error=Confirm Password");
        exit();

    }else if($uPass !== $uRePass){
        
        header("location: ../index.php?error=Passwords are not identical");
        exit();

    }else if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE Email='$uEmail'"))){

        header("location: ../index.php?error=Email is already used");
        exit();

    }else{

        $sql = "INSERT INTO users (id, prenume, nume, Email, telefon, parola , token) VALUES (NULL, '$fName', '$lName', '$uEmail', '$uPhone', '$uPass', '$token')";
        $sql_result = mysqli_query($conn, $sql);

        if($sql_result){

            sendmail_verify("$fName", "$lName", "$uEmail", "$token");
            $_SESSION['status']="Registration Successfull! Please verify your Email Address.";
            header("location: ../index.php");
            exit();
            
        }else{
            $_SESSION['status']="Registration Failed";
            header("location: ../index.php");
            exit();
        }

    }
}