<?php require_once "serverAction.php"; ?>



<link href="css/960.css" rel="stylesheet" media="screen" />
<link href="css/defaultTheme.css" rel="stylesheet" media="screen" />
<link href="css/myTheme.css" rel="stylesheet" media="screen" />
<link href="css/style.css" rel="stylesheet" media="screen" />

<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.js'></script>
<script src="js/jquery.fixedheadertable.min.js"></script>
<script src="js/demo.js"></script>
<script src='js/filter.js'></script>

<div id="wrap">
    <div  id = 'nav'>
        <ul>
            <li>
                <a href = '/'>View All Servers</a>
            </li>

            <li>
                <a href = '/buckets.php' target="_blank">View Buckets</a>
            </li>

            <li>
                <input type="button" id="btnExport" value=" Export Table data into Excel " />
            </li>

            <li>
                <div id='search'>
                    <label for='filter'>Filter</label> <input type='text' name='filter' value='' id='filter' />
                </div>
            </li>

        </ul>
    </div>

