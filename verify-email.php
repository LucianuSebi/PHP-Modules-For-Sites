<?php
session_start();

include "db_conn.php";

if(isset($_GET['token'])){
    
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    $sql = "SELECT * FROM users WHERE token= '$token' LIMIT 1";
    $sql_result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($sql_result)>0){

        $row = mysqli_fetch_array($sql_result);
        if($row['verified'] == "0"){

            $sql ="UPDATE users SET verified='1' WHERE token='$token' LIMIT 1";
            $sql_result= mysqli_query($conn, $sql);

            if($sql_result){
                $_SESSION['status'] = "Your Account has been verified succesfully!";
                header("Location: ../index.php");
                exit();
            }else{
                $_SESSION['status'] = "Verification Failed";
                header("Location: ../index.php");
                exit();
            }

        }else{
            $_SESSION['status'] = "Email Already Verified. Please log in";
            header("Location: ../index.php");
            exit();
        }

    }else{
        $_SESSION['status'] = "Token does not exist";
        header("Location: ../index.php");
        exit();
    }
}
else{
    $_SESSION['status'] = "Not Allowed";
    header("Location: ../index.php");
    exit();
}