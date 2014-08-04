<?php
require_once "head.php";

/////////////////////////////////////////////////////////////////////////////////////////////////
// BUCKETS
/////////////////////////////////////////////////////////////////////////////////////////////////

$all = array();
for($i=0; $i<count($reservedInstanceArray); $i++){
    $type = $reservedInstanceArray[$i]['type'];
    $zone = substr(str_replace('.','',$reservedInstanceArray[$i]['zone']), 3, 4);
    $platform = strtolower(substr($reservedInstanceArray[$i]['platform'], 0, 3));
    $all[] = $type.' '.$zone.' '.$platform;
}

//set each reserved type as a key
//and the value will be a list of each reserved instance that matches
foreach($all as $val){
    $allReservedInstances[$val] = array();
}

$serversByReservedType = array();
$descriptionsOfReservedTypes = array();

$testServerList1 = array();
$testServerList2 = array();


$correctlyAdded = 0;

//for each server, go through the list of reserved instances and
//put the server into the correct category
foreach($serverArray as $serverList){

    foreach($serverList as $key => $server){

        $testServerList1[] = $server['Name'];
        $found = false;
        $t = $server['type'];
        $z = substr( str_replace('.','',$server['regZone']) , 3, 4 );
        $p = strtolower(substr($server['platform'], 0, 3));
        $final = $t .' '.$z.' '.$p;

        $allReservedInstances[$final][] = $server;

    }
}

echo "<div id='table_container'><table  class='fancyTable tablesorter' id='myTable01' border='1px' cellpadding='1' cellspacing='1'>
        <thead>
            <tr>
                <th>Zone</th>
                <th>OS</th>
                <th>Instance Type</th>
                <th># of Reservations made</th>
                <th>Running</th>
                <th>Difference</th>
            </tr>
        </thead><tbody>";

$totalServers  = 0;
$totalRunning  = 0;
$totalReserved = 0;


$resCountPerType = array();

$f = 0;

foreach($listOfReservationsMade as $reservation) {

    $t = $reservation['type'];
    $z = substr( str_replace('.','',$reservation['zone']) , 3, 4 );
    $p = strtolower(substr($reservation['platform'], 0, 3));
    $final = $t .' '.$z.' '.$p;

    if(!isset($resCountPerType[$final])){
        $resCountPerType[$final] = 0;
    }
    $resCountPerType[$final] += 1;
}


foreach($allReservedInstances as $type => $list){

    $typeArray = explode(' ', $type);

    $runningCount  = 0;
    $reservedCount = 0;

    if(isset($resCountPerType[$type])){
        $reservedCount = $resCountPerType[$type];
    }

    foreach($list as $server){
        if($server['state'] == "running") {
            $runningCount++;
        }
    }

    $totalRunning = $totalRunning + $runningCount;
    $totalReserved = $totalReserved + $reservedCount;
    $difference = $runningCount - $reservedCount;
    echo"<tr>".
        "<td><div class = 'serverCell'>".$typeArray[1]."</div></td>".
        "<td><div class = 'serverCell'>".$typeArray[2]."</div></td>".
        "<td><div class = 'serverCell'>".$typeArray[0]."</div></td>".
        "<td><div class = 'serverCell'>".$reservedCount."</div></td>".
        "<td><div class = 'serverCell'>".$runningCount."</div></td>".
        "<td><div class = 'serverCell'>".$difference."</div></td>".
        "</tr>";
}

echo"<tr>".
    "<td><div class = 'serverCell'></div></td>".
    "<td><div class = 'serverCell'></div></td>".
    "<td><div class = 'serverCell'></div></td>".
    "<td><div class = 'serverCell'>".$totalRunning."</div></td>".
    "<td><div class = 'serverCell'>".$totalReserved."</div></td>".
    "<td><div class = 'serverCell'></div></td>".
    "</tr>";
echo "</tbody>";
echo "</table></div>";

?>
</div>
</body>
</html>

