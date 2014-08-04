<?php

echo "<a href = '/'>home</a><br />";

require_once "serverAction.php";
//$mfa = $_POST['mfa'];
if(true){
    foreach($volumeArray as $regKey => $region){
        foreach($region as $volKey => $volume){
            $tagName = 'Name';
            if($volume['newName']){
                if(setTag($regionEC2[$regKey]['instance'], $volume['volumeId'], $volume[$tagName], $tagName)){
                    echo $volKey." ".$volume['volumeId']." ".$tagName."<br />";
                } else {
                    echo "failure: ".$volKey." ".$volume['instanceID']." ".$tagName."<br />";
                }
            }

        }
    }

} else {
    echo "Wrong Value";
}