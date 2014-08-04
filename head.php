<!DOCTYPE html>
<html>
<head>
    <script src='http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.js'></script>
    <script src='js/filter.js'></script>

    <link href="css/960.css" rel="stylesheet" media="screen" />
    <link href="css/defaultTheme.css" rel="stylesheet" media="screen" />
    <link href="css/myTheme.css" rel="stylesheet" media="screen" />
    <link href="css/style.css" rel="stylesheet" media="screen" />

    <script src='http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.js'></script>
    <script src="js/jquery.fixedheadertable.min.js"></script>
    <script src="js/demo.js"></script>

    <script type="text/javascript" src="js/jquery.tablesorter.js"></script>

    <script type="text/javascript" >

    /**
     * http://www.jquerybyexample.net/2012/10/export-table-data-to-excel-using-jquery.html
     */
    $(document).ready(function() {

        $("#btnExport").click(
            function(e) {
                window.open('data:application/vnd.ms-excel,' + encodeURIComponent($('#table_container').html()));
                e.preventDefault();
        });
    });

    </script>


</head>
<body>
<?php require_once "header.php"; ?>
</body>
</html>