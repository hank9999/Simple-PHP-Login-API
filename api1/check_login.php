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

require_once('../config.php');

session_start();
$now_session_id = session_id();  //Get Present Session ID

if(!isset($_SESSION['user_id'])) {
    echo '{"app":{"message":"Not logged in"}}';  //Check Login. If no Session ID, not login in
} else {
    $conn = mysqli_connect($config['servername'], $config['username'], $config['password'], $config['dbname']);
    if (mysqli_connect_errno()) {
        echo "MySQL Error: " . mysqli_connect_error() . "<br>"; //Print Error
    } else {
        $user_id = $_SESSION["user_id"];
        $query = "SELECT `session_id` FROM `session` WHERE `user_id` = '$user_id'";
        $data = mysqli_query($conn,$query);
        if (mysqli_num_rows($data)==1) {
            $row = mysqli_fetch_array($data);
            if ($row["session_id"] == $now_session_id) {  //if Session ID that saved before = Present Session ID, user logged in
                echo '{"app":{"message":"Logged in"}}';
            } else {  //If not,that account is logged in elsewhere.
                $_SESSION = array();
                if(isset($_COOKIE[session_name()])){
                    setcookie(session_name(),'',time()-3600);  //Clean Cookies
                }
                session_destroy();  //Clean Session
                echo '{"app":{"message":"Your account is logged in elsewhere"}}';
            }
        } else {
            echo '{"app":{"message":"Unknown Error"}}';  //Other Error
        }
    }
}