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

if(!isset($_SESSION['user_id'])) {
    echo '{"app":{"message":"You have not log yet."}}';
} else {
    $conn = mysqli_connect($config['servername'], $config['username'], $config['password'], $config['dbname']); //Create Connect
    if (mysqli_connect_errno()) {
        echo "MySQL Error: " . mysqli_connect_error() . "<br>"; //Print Error
    } else {
        $user_id = $_SESSION['user_id'];
        $query = "SELECT `permission` FROM `user` WHERE `user_id` = $user_id";
        $data = mysqli_query($conn,$query);
        if (mysqli_num_rows($data)==1) {
            $row = mysqli_fetch_array($data);
            if ($row['permission'] == 0) {
                echo '{"app":{"message":"Admin"}}';
            } elseif ($row['permission'] == 1) {
                echo '{"app":{"message":"User"}}';
            } elseif ($row['permission'] == 2) {
                echo '{"app":{"message":"VIP"}}';
            }
        }
    }
}