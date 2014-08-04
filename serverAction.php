<?php

require_once "AWSSDKforPHP/sdk.class.php";

$regionNames = array(
    "ae1",
    "aw1"
);

$serverArray = array(
    "ae1" => array(),
    "aw1" => array()
);

$volumeArray = array(
    "ae1" => array(),
    "aw1" => array()
);

$hosts = array(
    "ae1" => AmazonEC2::REGION_US_E1,
    "aw1" => AmazonEC2::REGION_US_W1
);

$tagNames = array(
    'platform',
    'vpc',
    'regZone',
    'makeReserved',
);

function is_iterable($var) {
    return (is_array($var) || $var instanceof Traversable);
}

function verification($mfa){
    if(is_numeric($mfa)){
        $tokenResponse = new AmazonSTS();
        $response = $tokenResponse->get_session_token(array(
            'DurationSeconds' => 1080,
            'SerialNumber' => 'GAKT000146C5',
            'TokenCode' => $mfa,
        ));
        return $response->isOK();
    }
    return false;
}

function displayServer($key, $server){
    $regZone = $server['regZone'];
    $regionArr = explode("-", $regZone);
    $region = $regionArr[0]."-".$regionArr[1];

    $stateClass = '';

    if($server['state'] == 'running'){
        $stateClass = "id = 'greenCellBlock' ";
    }else if($server['state'] == 'stopped'){
        $stateClass = "id = 'redCellBlock' ";
    }

    echo"<tr>".
        "<td><div class = 'serverCell'><div class = 'edit_name_".$region."' id='".$server['instanceID']."'>".$server['Name']."</div></div></td>".
        "<td><div class = 'serverCell'>".$server['instanceID']."</div></td>".
        "<td><div class = 'serverCell'>".$server['launchTime']."</div></td>".
        "<td><div class = 'serverCell'>".$server['type']."</div></td>".
        "<td><div class = 'serverCell'>".$server['IP']."</div></td>".
        "<td><div class = 'serverCell' ".$stateClass.">".$server['state']."</div></td>".
        "<td><div class = 'serverCell'>".$server['env']."</div></td>".
        "<td><div class = 'serverCell'>".$server['regZone']."</div></td>".
        "<td><div class = 'serverCell'>".$server['vpc']."</div></td>".
        "<td><div class = 'serverCell'>".$server['platform']."</div></td>".
        "<td><div class = 'serverCell'><div class = 'edit_makeres_".$region."' id='".$server['instanceID']."'>".$server['makeReserved']."</div></div></td>".
        "<td><div class = 'serverCell'>".$server['resStart']."</div></td>".
        "<td><div class = 'serverCell'>".$server['initialSpent']."</div></td>".
        "<td><div class = 'serverCell'>".$server['hourlyPrice']."</div></td>".
        "<td><div class = 'serverCell'>".$server['estimatedSpent']."</div></td>".
        "<td id = 'desc'><div class = 'serverCell'><div class = 'edit_des_".$region."' id='".$server['instanceID']."'>".$server['tagDescription']."</div></div></td>".
        "</tr>";
}

function displayVolume($key, $volume){
    echo"<tr>".
        "<td>".$volume['Name']."</td>".
        "<td>".$volume['volumeId']."</td>".
        "<td>".$volume['instanceId']."</td>".
        "<td>".$volume['serverIP']."</td>".
        "<td><div class = 'serverCell'>".$volume['serverPlatform']."</div></td>".
        "<td>".$volume['device']."</td>".
        "<td>".$volume['deviceSize']."</td>".
        "<td>".$volume['status']."</td>".
        "<td>".$volume['region']."</td>".
        "<td>".$volume['newName']."</td>".
        "</tr>";
}

function createTableTitleRow(){
    $headerStart = "<thead><tr>";
    $headerEnd   = "</tr></thead>";

    $titleList = array(
       'Name '                => 'Name',
       'Identication'         => 'ID',
       'LaunchTime'           => 'Launch Time',
       'InstanceType'         =>  'Instance Type',
       'IpAdress'             =>  'IP',
       'State'                =>  'State',
       'Environment'          =>  'Env',
       'Region Zone'          =>  'Region Zone',
       'VPC'                  =>  'VPC',
       'OperatingSystem'      =>  'OS',
       'MakeReserved'         =>  'Make Reserved',
       'ReservedStartingDate' =>  'Reserved Start',
       'InitialSpent'         =>  'Initial Spent',
       'HourlySpent'          =>  'Hourly Spent',
       'EstimatedTotalSpent'  =>  'Estimated Spent',
       'Description'          =>  'Description',
    );

    $titleHtmlTableTags = '';
    foreach($titleList as $title => $displayedTitle){
        $titleHtmlTableTags .= '<th>'.$displayedTitle.'</th>';
    }
    echo $headerStart.$titleHtmlTableTags.$headerEnd;
};

function displayServerTable($serverArray){

    echo '<div id="table_container">';
    echo '<table class="fancyTable tablesorter" id="myTable01" cellpadding="0" cellspacing="0">';

    createTableTitleRow();

    foreach($serverArray as $regKey => $region){
        foreach($serverArray[$regKey] as $key => $server){
            displayServer($key, $server);
        }
    }

   echo "</table>";
}

function displayVolumeTable($volumeArray){


    echo "<table border='1px' cellpadding='1' cellspacing='1' id='resultTable'>";
    echo "<thead>
    <tr>
    <th>Name</th>
    <th>volID</th>
    <th>InstanceID</th>
    <th>Server IP</th>
    <th>OS</th>
    <th>Device</th>
    <th>Size</th>
    <th>Status</th>
    <th>Region</th>
    </tr>
    </thead>";
    echo "<tbody>";
    foreach($volumeArray as $regKey => $region){
        foreach($volumeArray[$regKey] as $key => $volume){
            displayVolume($key, $volume);
        }
    }
    echo "</tbody>";
    echo "</table>";
}

function setReservedValues($resInstance){
    return array(
        'type' => (string)$resInstance->instanceType,
        'zone' => (string)$resInstance->availabilityZone,
        'start' => (string)$resInstance->start,
        'hourlyPrice' => (float)$resInstance->recurringCharges->item->amount,
        'initialSpent' => (float)$resInstance->fixedPrice,
        'instanceCount' => (int)$resInstance->instanceCount,
        'platformFull' => (string)$resInstance->productDescription,
    );
}

function platform($reservedInfo){
    $platformArr = explode(" ", $reservedInfo['platformFull']);

    if($platformArr[0] == "Windows"){
        return 'win';
    } elseif($platformArr[0] == "Linux/UNIX") {
        return 'linux';
    } else {
        return 'other';
    }
}

function reservedStart($start){
    $startArr = explode("T", $start);
    $dateArr = explode("-", $startArr[0]);
    $timeArr = explode(":", $startArr[1]);
    $startUnixArr =  array(
        'year' => (int)$dateArr[0],
        'month' => (int)$dateArr[1],
        'day' => (int)$dateArr[2],
        'hour' => (int)$timeArr[0],
        'min' => (int)$timeArr[1],
        'sec' => (int)$timeArr[2],
    );

    return array(
        'unix' => mktime($startUnixArr['hour'], $startUnixArr['min'], $startUnixArr['sec'], $startUnixArr['month'], $startUnixArr['day'], $startUnixArr['year']),
        'std' => $startUnixArr,
    );
}

function estimatedSpent($reservedInfo, $start){
    $currentTime = getdate()[0];
    $totalTime = $currentTime - $start;
    $min = $totalTime / 60;
    $hour = $min / 60;

    $estimatedSpent = ($reservedInfo['hourlyPrice'] * $hour) + $reservedInfo['initialSpent'];
    return money_format("%i", $estimatedSpent);
}

function reservedInstance($reservedInfo, $start, $estimatedSpent, $platform){
    return array(
        'type' => $reservedInfo['type'],
        'zone' => $reservedInfo['zone'],
        'initialSpent' => $reservedInfo['initialSpent'],
        'hourlyPrice' => $reservedInfo['hourlyPrice'],
        'start' => $start['month']."/".$start['year'],
        'estimatedSpent' => $estimatedSpent,
        'instanceCount' => $reservedInfo['instanceCount'],
        'platform' => $platform,
    );
}

function buildReservedInstanceArray($serverInstance){
    $resInstanceData = $serverInstance->describe_reserved_instances()->body->reservedInstancesSet->item;
    $reservedInstanceArray = array();
    foreach($resInstanceData as $resInstance){
        $reservedInfo = setReservedValues($resInstance);
        //if initial spent is 0 this is an indication that the server is retired and should not be taken into account
            if($reservedInfo['initialSpent'] > 0){
                $platform = platform($reservedInfo);
                $start = reservedStart($reservedInfo['start']);
                $estimatedSpent = estimatedSpent($reservedInfo, $start['unix']);
                $reservedInstance = reservedInstance($reservedInfo, $start['std'], $estimatedSpent, $platform);
                for($i = 0; $i < $reservedInfo['instanceCount']; $i++){
                    array_push($reservedInstanceArray, $reservedInstance);
                }
            }
    }

    return $reservedInstanceArray;
}

//build servers
function tags($tagSet){
    $tags = array();
    if(is_iterable($tagSet)){
        foreach($tagSet as $tag){
            if($tag->key == 'Name'){
                $tags['name'] = $tag->value;
            } elseif($tag->key == 'platform'){
                $tags['platform'] = $tag->value;
            } elseif($tag->key == 'vpc'){
                $tags['vpc'] = $tag->value;
            } elseif($tag->key == 'regZone'){
                $tags['regZone'] = $tag->value;
            } elseif($tag->key == 'description'){
                $tags['description'] = $tag->value;
            } elseif($tag->key == 'makeReserved'){
                $tags['makeReserved'] = $tag->value;
            }
        }
    }
    return $tags;
}

function serverValues($serverItem){
    $serverVal['type'] = $serverItem->instanceType;
    $serverVal['state'] = $serverItem->instanceState->name;
    $serverVal['platform'] = (isset($serverItem->platform))?'win':'linux';
    $serverVal['vpc'] = (isset($serverItem->vpcId)?'VPC':'noVPC');
    $serverVal['instanceID'] = $serverItem->instanceId;
    $serverVal['regZone'] = $serverItem->placement->availabilityZone;
    $serverVal['launchTime'] = $serverItem->launchTime;
    $serverVal['IP'] = $serverItem->privateIpAddress;
    return $serverVal;
}

function reservedStatus($tag, $reservedInstanceArray, $serverVal){
    $reservedStatus['initialSpent'] = $reservedStatus['hourlyPrice'] = $reservedStatus['estimatedSpent'] = $reservedStatus['start'] = "";
    $reservedStatus['makeReserved'] = "-----";
    $toBuy = null;
    if(isset($tag['makeReserved'])){
        $reservedStatus['makeReserved'] = $tag['makeReserved'];
        if($tag['makeReserved'] == 'purchased'){
            foreach($reservedInstanceArray as $key => $reservedInstance){
                if($serverVal['type'] == $reservedInstance['type'] &&
                    $serverVal['regZone'] == $reservedInstance['zone'] &&
                    $serverVal['platform'] == $reservedInstance['platform']){
                        $reservedStatus['initialSpent'] = $reservedInstance['initialSpent'];
                        $reservedStatus['hourlyPrice'] = $reservedInstance['hourlyPrice'];
                        $reservedStatus['estimatedSpent'] = $reservedInstance['estimatedSpent'];
                        $reservedStatus['start'] = $reservedInstance['start'];
                        unset($reservedInstanceArray[$key]);
                        break;
                }
            }
        } elseif($tag['makeReserved'] == 'yes'){
            $toBuy = array(
                'name' =>$tag['name'],
                'instanceID' =>$serverVal['instanceID'],
                'type' =>$serverVal['type'],
                'regZone' =>$serverVal['regZone'],
                'platform' =>$serverVal['platform'],
            );
            foreach($reservedInstanceArray as $key => $reservedInstance){
                if($serverVal['type'] == $reservedInstance['type'] &&
                    $serverVal['regZone'] == $reservedInstance['zone'] &&
                    $serverVal['platform'] == $reservedInstance['platform']){
                    $reservedStatus['start'] = "available";
                }
            }
        }
    }
    return array('status' => $reservedStatus, 'instance' => $reservedInstanceArray, 'toBuy' => $toBuy);
}

function checkTags($tag){
    $tag['name'] = (isset($tag['name'])?$tag['name']:"----------");
    $tag['platform'] = (isset($tag['platform'])?$tag['platform']:"");
    $tag['vpc'] = (isset($tag['vpc'])?$tag['vpc']:"");
    $tag['regZone'] = (isset($tag['regZone'])?$tag['regZone']:"");
    $tag['description'] = (isset($tag['description'])?$tag['description']:"----------");
    return $tag;
}

function setEnviroment($name){
    $nameArr = explode("-", $name);
    if(isset($nameArr[2])){
        return $nameArr[2];
    } else {
        return "";
    }
}

function setServerData($tag, $serverVal, $reservedStatus){
    return array(
        'Name' => $tag['name'],
        'instanceID' => $serverVal['instanceID'],
        'regZone' => $serverVal['regZone'],
        'type' => $serverVal['type'],
        'IP' => $serverVal['IP'],
        'state' => $serverVal['state'],
        'env' => $tag['env'],
        'vpc' => $serverVal['vpc'],
        'platform' => $serverVal['platform'],
        'tagRegZone' => $tag['regZone'],
        'tagVPC' => $tag['vpc'],
        'tagPlatform' => $tag['platform'],
        'tagDescription' => $tag['description'],
        'makeReserved' => $tag['makeReserved'],
        'resStart' => $reservedStatus['start'],
        'initialSpent' => $reservedStatus['initialSpent'],
        'hourlyPrice' => $reservedStatus['hourlyPrice'],
        'estimatedSpent' => $reservedStatus['estimatedSpent'],
        'launchTime' => explode("T",$serverVal['launchTime'])[0],
    );
}

function buildServerArray($serverArray, $severResponse, $serverZone, $reservedInstanceArray){
    $toBuy = array();
    foreach($severResponse->body->reservationSet->item as $server){
        $tag = array();
        $tagSet = $server->instancesSet->item->tagSet->item;
        $tag = tags($tagSet);
        $serverVal = serverValues($server->instancesSet->item);
        $tag = checkTags($tag);
        $tag['env'] = setEnviroment($tag['name']);
        $reserved = reservedStatus($tag, $reservedInstanceArray, $serverVal);
        $reservedInstanceArray = $reserved['instance'];
        $tag['makeReserved'] = $reserved['status']['makeReserved'];
        $serverData = setServerData($tag, $serverVal, $reserved['status']);
        if($reserved['toBuy'] != null){
            array_push($toBuy, $reserved['toBuy']);
        }
        array_push($serverArray[$serverZone], $serverData);
    }
    $serversAndReserved = array(
        'serverArray' => $serverArray,
        'reservedInstanceArray' => $reservedInstanceArray,
        'toBuy' => $toBuy,
    );
    return $serversAndReserved;
}

function buildVolumeArray($volumeArray, $serverArray, $volResponse, $regionName){
    foreach($volResponse->body->volumeSet->item as $volume){
        $name = null;
        $newName = false;
        $volumeId = $volume->volumeId;
        $deviceSize = $volume->size."Gb";

        if(isset($volume->tagSet)){
            foreach($volume->tagSet->item as $tag){
                if($tag->key == 'name'){
                    $newName = true;
                    $name = $tag->value;
                }
                if($tag->key == 'Name'){
                    $name = $tag->value;
                }
            }
        }

        $status = $volume->status;
        if($status == 'in-use'){
            $instanceId = $volume->attachmentSet->item->instanceId;
            $deviceFull = $volume->attachmentSet->item->device;
            $deviceType = explode("/", $deviceFull);
            $serverName = null;

            if($deviceType[0] == "")
                $device = $deviceType[2];
            else
                $device = $deviceType[0];
            foreach($serverArray[$regionName] as $server){
                if((string)$instanceId == (string)$server['instanceID']){
                    $serverName = $server['Name'];
                    $serverIP = $server['IP'];
                    $serverPlatform = $server['platform'];
                    break;
                }
            }
            if($name===null){
                $serNameArr = explode("-", $serverName);
                $lastName = array_pop($serNameArr);
                if($lastName==$device)
                    $name = $serverName;
                else
                    $name = (string)implode("-", $serNameArr)."-".(string)$device;
                $newName = true;
            }
        } elseif($status == 'available') {
            $instanceId = $device = $serverIP = $serverPlatform = '----------';
        } else {
            $instanceId = $device = $serverIP = $serverPlatform = '----------';
        }
        if($name===null){
            $name = '----------';
        }
        $volumeData = array(
            'Name' => $name,
            'volumeId' => $volumeId,
            'instanceId' => $instanceId,
            'serverIP' => $serverIP,
            'serverPlatform' => $serverPlatform,
            'device' => $device,
            'deviceSize' => $deviceSize,
            'status' => $status,
            'region' => $regionName,
            'newName'=> $newName,
        );
        array_push($volumeArray[$regionName], $volumeData);
    }
    return $volumeArray;
}

function setTag($ec2_region, $ID, $value, $tagName){
    $result = $ec2_region->create_tags(
        $ID, array(
            'Key' => $tagName,
            'Value' => $value
        )
    );
    if($result->isOK()){
        return true;
    } else {
        return false;
    }
}

function deleteTag($ec2_region, $instanceID, $tagName){
    $result = $ec2_region->delete_tags(
        $instanceID, array( "Tag" => array( array( "Key"=>$tagName ) ) )
    );
    if($result->isOK()){
        return true;
    } else {
        return false;
    }
}

function outputCSV($servers) {
    $outputBuffer = fopen("php://output", 'w');
    foreach($servers as $val) {
        fputcsv($outputBuffer, $val);
    }
    fclose($outputBuffer);
}

$regionEC2 = array(
    "ae1" => array(
        "instance" => new AmazonEC2()
    ),
    "aw1" => array(
        "instance" => new AmazonEC2()
    )
);

$reservedInstanceArray = array();

foreach($regionNames as $regionName){

    $regionEC2[$regionName]['instance']->set_region($hosts[$regionName]);
    $response = array('response' => $regionEC2[$regionName]['instance']->describe_instances());


    $regionEC2[$regionName] = array_merge($regionEC2[$regionName], $response);

    //this contains number of reservations made for current region
    $reservedInstanceArrayNew = buildReservedInstanceArray($regionEC2[$regionName]['instance']);

    $serversAndReserved = buildServerArray($serverArray, $regionEC2[$regionName]['response'], $regionName, $reservedInstanceArrayNew);
    $serverArrayNew = $serversAndReserved['serverArray'];
    $serverArray    = array_merge($serverArray, $serverArrayNew);

    $reservedToBuy = $serversAndReserved['toBuy'];

    $reservedInstanceArrayNew = $serversAndReserved['reservedInstanceArray'];
    $reservedInstanceArray    = array_merge($reservedInstanceArray, $reservedInstanceArrayNew);

    $volResponse    = $regionEC2[$regionName]['instance']->describe_volumes();
    $serverArrayNew = buildVolumeArray($volumeArray, $serverArray, $volResponse, $regionName);
    $volumeArray    = array_merge($volumeArray, $serverArrayNew);
}

$listOfReservationsMade = array_merge(
    buildReservedInstanceArray($regionEC2['ae1']['instance']),
    buildReservedInstanceArray($regionEC2['aw1']['instance'])
);

