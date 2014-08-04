<?php
echo "<a href = '/'>home</a><br />";

require_once "serverAction.php";

$tagName = 'tagMakeReserved';
foreach($serverArray as $regKey => $region){
    foreach($region as $serKey => $server){
        if(false){
            if(deleteTag($regionEC2[$regKey]['instance'], $server['instanceID'], $tagName)){
                echo "success: ".$serKey."-".$tagName."-".$server['instanceID']."<br />";
            } else {
                echo "failure: ".$serKey." ".$server['instanceID']." ".$tagName."<br />";
            }
        } else {
            echo "not a server I want<br />";
        }
    }
}