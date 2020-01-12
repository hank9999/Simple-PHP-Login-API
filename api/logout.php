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

session_start();

if(!isset($_SESSION['user_id'])) {
    die(json_encode(array('status' => 'Fail', 'message' => 'You have not log yet.'),JSON_UNESCAPED_UNICODE));
} else {
    $_SESSION = array();
    if(isset($_COOKIE[session_name()])){
        setcookie(session_name(),'',time()-3600);  //Clean Cookies
    }
    session_destroy();  //Clean Session
    exit(json_encode(array('status' => 'Success', 'message' => 'Logout'),JSON_UNESCAPED_UNICODE));
}