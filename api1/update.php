<?php
$client_version = $_GET["version"];
if ($client_version == "") {
    echo "Version_Empty";
} else {
    $version_file = fopen("../version", "r") or die("Unable to open file!");
    $server_version = fread($version_file,filesize("../version"));
    fclose($version_file);

    if ($server_version == $client_version) {
        echo "No Update";
    } else if ($server_version > $client_version) {
        echo "New Version! Please Update!";
    } else {
        echo "You are kidding!";
    }
}

?>