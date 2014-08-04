<?php

echo "<a href = '/'>home</a><br />";

require_once "serverAction.php";
$mfa = $_POST['mfa'];
if(true){
    foreach($serverArray as $regKey => $region){
        foreach($region as $serKey => $server){
            foreach($tagNames as $tagName){
                if(setTag($regionEC2[$regKey]['instance'], $server['instanceID'], $server[$tagName], $tagName)){
                    echo $serKey." ".$server['instanceID']." ".$tagName."<br />";
                } else {
                    echo "failure: ".$serKey." ".$server['instanceID']." ".$tagName."<br />";
                }

            }
        }
    }

} else {
    echo "Wrong Value";
}