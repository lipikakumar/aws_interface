<?php
require_once "serverAction.php";
$regZone = $_GET["region"];
$tagName = $_GET["tagname"];

if(isset($_POST["value"])){
    $value = $_POST["value"];
} elseif(isset($_GET["value"])) {
    $value = $_GET["value"];
}

$instanceID = $_POST["elementid"];

$regionArr = explode("-", $regZone);
$region = $regionArr[0]."-".$regionArr[1];

if($value == ""){
    $value = "----------";
}
if($region == "us-east"){
    if(setTag($regionEC2['ae1']['instance'], $instanceID, $value, $tagName))
        echo $value;
    else
        echo "ERROR: Tag not set";
} else if($region == "us-west") {
    if(setTag($regionEC2['aw1']['instance'], $instanceID, $value, $tagName))
        echo $value;
    else
        echo "ERROR: Tag not set";
} else {
    echo "ERROR: Server Location is not set";
}