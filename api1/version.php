<?php
$version_file = fopen("../version", "r") or die("Unable to open file!");  //Read Version
echo fread($version_file,filesize("../version"));
fclose($version_file);
?>