<?php

session_start();
include "db_conn.php";

$uEmail = mysqli_real_escape_string($conn, $_POST['uEmail']);
$token = mysqli_real_escape_string($conn, $_POST['token']);
$uPass = mysqli_real_escape_string($conn, $_POST['uPass']);
$uRePass = mysqli_real_escape_string($conn, $_POST['uRePass']);


if(empty($uEmail)){

    header("location: ../change_password.php?error=Email is required");
    exit();

}else if(empty($uPass)){

    header("location: ../change_password.php?error=Password is required");
    exit();

}else if(empty($uRePass)){

    header("location: ../change_password.php?error=Confirm Password");
    exit();

}else if($uPass !== $uRePass){
    
    header("location: ../change_password.php?error=Passwords are not identical");
    exit();

}else if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE Email='$uEmail' AND token='$token'"))){

    $sql = "UPDATE users SET Password='$uPass' WHERE Email='$uEmail' AND token='$token';";
    $sql_result=mysqli_query($conn,$sql);

    if($sql_result){

        $token = md5(rand());
        $sql = "UPDATE users SET token='$token' WHERE Email='$uEmail'";
        $sql_result=mysqli_query($conn,$sql);

        $_SESSION['status']="You have succesfully changed your password.";
        header("location: ../index.php");
        exit();

    }else{
        header("location: ../change_password.php?error=Something went wrong");
        exit();
    }

}else{
    header("location: ../change_password.php?error=Something went wrong");
    exit();
}