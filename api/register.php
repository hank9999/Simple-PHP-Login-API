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

function register($user_name, $pass_word, $config) {
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
        $query = "SELECT `username` FROM `user` WHERE `username` = '$user_name'";
        $data = mysqli_query($conn,$query);
        if (mysqli_num_rows($data)>=1) {
            die(json_encode(array('status' => 'Fail', 'message' => 'Username is Unavailable'),JSON_UNESCAPED_UNICODE));
        } else {
            if (mysqli_query($conn, "INSERT INTO user VALUES (NULL,1 , '$user_name', '$pass_word')")) {
                if (mysqli_query($conn, "INSERT INTO `session` VALUES (NULL, '')")) {
                    exit(json_encode(array('status' => 'Success', 'message' => 'Register Success'),JSON_UNESCAPED_UNICODE));
                } else {
                    die(json_encode(array('status' => 'Fail', 'message' => 'Register Failed'),JSON_UNESCAPED_UNICODE));
                }
            } else {
                die(json_encode(array('status' => 'Fail', 'message' => 'Register Failed'),JSON_UNESCAPED_UNICODE));
            }
        }
        mysqli_close($conn);
    }
}
if(!isset($_SESSION['user_id'])) {  //No Session ID, then register it
    register($user_name, $pass_word, $config);
} else {
    $conn = mysqli_connect($config['servername'], $config['username'], $config['password'], $config['dbname']);
    if (mysqli_connect_errno()) {
        $message = array('status' => 'Fail','message' => 'MySQL Error: ' . mysqli_connect_error() . '<br>');
        die(json_encode($message,JSON_UNESCAPED_UNICODE));  //Print Error
    } else {
        $user_id = $_SESSION["user_id"];
        $query = "SELECT `session_id` FROM `session` WHERE `user_id` = '$user_id'";
        $data = mysqli_query($conn,$query);
        if (mysqli_num_rows($data)==1) {
            $row = mysqli_fetch_array($data);
            if ($row["session_id"] == $now_session_id) {  //Already Logged in
                die(json_encode(array('status' => 'Fail', 'message' => 'You have already registered.'),JSON_UNESCAPED_UNICODE));
            } else {  //If not,that account is logged in elsewhere.
                $_SESSION = array();
                if(isset($_COOKIE[session_name()])){
                    setcookie(session_name(),'',time()-3600);  //Clean Cookies
                }
                session_destroy();  //Clean Session
                register($user_name, $pass_word, $config);
            }
        } else {
            die(json_encode(array('status' => 'Fail', 'message' => 'Unknown Error'),JSON_UNESCAPED_UNICODE));
        }
        mysqli_close($conn);
    }
}