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

$user_name = $_GET["username"];
$pass_word = $_GET["password"];

session_start();   
$now_session_id = session_id(); 

function login($user_name, $pass_word, $config, $now_session_id) {
    if ($user_name == "") {
        echo '{"app":{"message":"Username Empty"}}';
    } else if ($pass_word == "") {
        echo '{"app":{"message":"Password Empty"}}';
    } else {
        $conn = mysqli_connect($config['servername'], $config['username'], $config['password'], $config['dbname']); //Create Connect
        if (mysqli_connect_errno()) {
            echo "MySQL Error: " . mysqli_connect_error() . "<br>"; //Print Error
        } else {
            $query = "SELECT `user_id`, `username`, `permission` FROM `user` WHERE `username` = '$user_name' AND `password` = '$pass_word'";
            $data = mysqli_query($conn,$query);
            if (mysqli_num_rows($data)==1) {
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
                    echo '{"app":{"message":"Login Success"}}';
                } else {
                    echo '{"app":{"message":"Unknown Error"}}';
                }
            } else {
                echo '{"app":{"message":"Username or Password Wrong"}}';
            }
        }
    }
}

if (!isset($_SESSION['user_id'])) {
    login($user_name, $pass_word, $config, $now_session_id);
} else {
    $conn = mysqli_connect($config['servername'], $config['username'], $config['password'], $config['dbname']); //Create Connect
    if (mysqli_connect_errno()) {
        echo "MySQL Error: " . mysqli_connect_error() . "<br>"; //Print Error
    } else {
        $user_id = $_SESSION["user_id"];
        $query = "SELECT `session_id` FROM `session` WHERE `user_id` = '$user_id'";
        $data = mysqli_query($conn,$query);
        if (mysqli_num_rows($data)==1) {
            $row = mysqli_fetch_array($data);
            if ($row["session_id"] == $now_session_id) {
                echo '{"app":{"message":"You have already logged in. Do not log again"}}';
            } else {
                login($user_name, $pass_word, $config, $now_session_id);
            }
        } else {
            echo '{"app":{"message":"Unknown Error"}}';
        }
    }
}