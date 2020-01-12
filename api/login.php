<?php
/*
Copyright (c) [2019] [hank9999]
[Simple-PHP-Login-API] is licensed under the Mulan PSL v1.
You can use this software according to the terms and conditions of the Mulan PSL v1.
You may obtain a copy of Mulan PSL v1 at:
    http://license.coscl.org.cn/MulanPSL
THIS SOFTWARE IS PROVIDED ON AN "AS IS" BASIS, WITHOUT WARRANTIES OF ANY KIND, EITHER EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO NON-INFRINGEMENT, MERCHANTABILITY OR FIT FOR A PARTICULAR
PURPOSE.
See the Mulan PSL v1 for more details.
*/
header('Content-Type:application/json; charset=utf-8'); 

require_once('../config.php');

$user_name = $_GET["username"];
$pass_word = $_GET["password"];

session_start();   
$now_session_id = session_id(); 

function login($user_name, $pass_word, $config, $now_session_id) {
    if ($user_name == "") {
        $message = array('status' => 'Fail','message' => 'Username Empty');
        die(json_encode($message,JSON_UNESCAPED_UNICODE));
    }
    if ($pass_word == "") {
        $message = array('status' => 'Fail','message' => 'Password Empty');
        die(json_encode($message,JSON_UNESCAPED_UNICODE));
    }
    $conn = mysqli_connect($config['servername'], $config['username'], $config['password'], $config['dbname']); //Create Connect
    if (mysqli_connect_errno()) {
        $message = array('status' => 'Fail','message' => 'MySQL Error: ' . mysqli_connect_error() . '<br>');
        die(json_encode($message,JSON_UNESCAPED_UNICODE));  //Print Error
    } else {
        $query = "SELECT `user_id`, `username`, `permission` FROM `user` WHERE `username` = '$user_name' AND `password` = '$pass_word'";  //Check Username and Password
        $data = mysqli_query($conn,$query);
        if (mysqli_num_rows($data)==1) {  //Right
            $row = mysqli_fetch_array($data);
            $user_id = $row['user_id'];
            $query2 = "UPDATE `session` SET `session_id` = '$now_session_id' WHERE `user_id` = '$user_id'";
            if (mysqli_query($conn,$query2)) {
                $_SESSION['user_id']=$user_id;
                $_SESSION['username']=$row['username'];
                if ($row['permission'] == 0) {
                    $_SESSION['permission']='Admin';
                } elseif ($row['permission'] == 2) {
                    $_SESSION['permission']='VIP';
                } else {
                    $_SESSION['permission']='User';
                }
                $message = array('status' => 'Success','message' => 'Login Success');
                exit(json_encode($message,JSON_UNESCAPED_UNICODE));
            } else {
                $message = array('status' => 'Fail','message' => 'Unknown Error');
                die(json_encode($message,JSON_UNESCAPED_UNICODE));  //Other Error
            }
        } else {
            $message = array('status' => 'Fail','message' => 'Username or Password Wrong');
            die(json_encode($message,JSON_UNESCAPED_UNICODE));  //Error
        }
        mysqli_close($conn);
    }
}

if (!isset($_SESSION['user_id'])) {
    login($user_name, $pass_word, $config, $now_session_id);  //Check Login. If no Session ID, not login in
} else {
    $conn = mysqli_connect($config['servername'], $config['username'], $config['password'], $config['dbname']); //Create Connect
    if (mysqli_connect_errno()) {
        echo "MySQL Error: " . mysqli_connect_error() . "<br>";  //Print Error
    } else {
        $user_id = $_SESSION["user_id"];
        $query = "SELECT `session_id` FROM `session` WHERE `user_id` = '$user_id'";
        $data = mysqli_query($conn,$query);
        if (mysqli_num_rows($data)==1) {
            $row = mysqli_fetch_array($data);
            if ($row["session_id"] == $now_session_id) {  //Check Session ID, If Session ID that saved before = Present Session ID, user had already logged in
                $message = array('status' => 'Fail', 'message' => 'You have already logged in. Do not log again');
                die(json_encode($message,JSON_UNESCAPED_UNICODE));
            } else {
                login($user_name, $pass_word, $config, $now_session_id);
            }
        } else {
            $message = array('status' => 'Fail','message' => 'Unknown Error');
            die(json_encode($message,JSON_UNESCAPED_UNICODE));  //Other Error
        }
        mysqli_close($conn);
    }
}