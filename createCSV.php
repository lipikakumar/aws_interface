<?php
require_once "serverAction.php";



header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=ServerData".getdate()[0].".csv");
header("Pragma: no-cache");
header("Expires: 0");

$allServers = array();
$allServers = array_merge($serverArray['ae1'], $serverArray['aw1']);
outputCSV($allServers);