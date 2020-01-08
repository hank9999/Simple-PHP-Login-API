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

if (file_exists("install.lock")) {
	die("Don't Install Again!<br>If you want to reinstall, please delete install.lock and MySQL Table");
} else {
	require_once('./config.php');
	$conn = mysqli_connect($config['servername'], $config['username'], $config['password'], $config['dbname']); //Create Connect
	if (mysqli_connect_errno()) {
		die("MySQL Error: " . mysqli_connect_error() . "<br>"); //Print Error
	}
	echo "Installing...<br>";
	if (mysqli_query($conn, "CREATE TABLE `user` (`user_id` int not null primary key auto_increment, `permission` int, `username` varchar(255), `password` varchar(255))")) { //Create Table
		echo "Table 'user' Created Success<br>";
	} else {
		die("Table 'user' Created Error: " . mysqli_error($conn) . "<br>");
	}
	if (mysqli_query($conn, "CREATE TABLE `session` (`user_id` int not null primary key auto_increment, `session_id` varchar(255))")) { //Create Table
		echo "Table 'session' Created Success<br>";
	} else {
		die("Table 'session' Created Error: " . mysqli_error($conn) . "<br>");
	}
	if (mysqli_query($conn, "INSERT INTO `user` VALUES (NULL, 0, 'admin', '123456')")) { //Insert Data
		echo "Userdata Inserted Success<br>";
	} else {
		die("Userdata Inserted Error: " . mysqli_error($conn) . "<br>");
	}
	if (mysqli_query($conn, "INSERT INTO `session` VALUES (NULL, '')")) { //Insert Data
		echo "Session Data Inserted Success<br>";
	} else {
		die("Session Data Inserted Error: " . mysqli_error($conn) . "<br>");
	}
	$lock_file = fopen("install.lock", "w");
	fclose($lock_file);
	if (file_exists("install.lock")) {
		echo "Install Success";
	} else {
		die("Please check if you have permission to create files.<br>Install Error");
	}
	mysqli_close($conn);
}
?>