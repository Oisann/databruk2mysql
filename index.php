<?php
    date_default_timezone_set('Europe/Oslo');
    $servername = getenv("MYSQL_HOST");
    $username = getenv("MYSQL_USER");
    $password = getenv("MYSQL_PASSWORD");
    $dbname = getenv("MYSQL_DATABASE");

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    function HHMMSS2sec($time) {
        $split = explode(":", $time);
        $time = "";
        foreach($split as $viser) {
            $time .= explode(".", $viser)[0] . ":";
        }
        $split = explode(":", $time);
        
        $seconds = 0;
        
        for($i = count($split) - 1, $a = 0; $i > 0; $i--, $a++) {
            if($a == 3) {
                break;
            }
            $base = $a * 60;
            if($base == 0) {
                $base = 1;
            }
            $seconds += intval($split[$i - 1]) * $base;
        }
        
        return $seconds;
    }
    
    // PLEASE NOTE: This is not safe at all! Please don't use this in production!
    // This is a project I use on my LAN ONLY, it does not interact with the internet at all!
    // It gets data from another application and the data never gets to this application if it's not safe to put in the database this way!
    // It will be updated in the future, but I didn't want to spend the time to do it correctly when I made it. Sorry!
    $sql = "INSERT INTO datausage (DataUsageMB, DataLimitMB, RolloverLimitMB, TopupLimitMB, TotalRemainingDataMB, TalkUsage, TalkLimit, SmsUsage, SmsLimit, timestamp)
            VALUES (" . $_GET['DataUsageMB'] . ", " . $_GET['DataLimitMB'] . ", " . $_GET['RolloverLimitMB'] . ", " . $_GET['TopupLimitMB'] . ", " . $_GET['TotalRemainingDataMB'] . ", " . HHMMSS2sec($_GET['TalkUsage']) . ", " . HHMMSS2sec($_GET['TalkLimit']) . ", " . $_GET['SmsUsage'] . ", " . $_GET['SmsLimit'] . ", " . strtotime($_GET['DataDate']) . ");";
    
    header('Content-Type: application/json');
    if ($conn->query($sql) === TRUE) {
        echo "{ \"status\": \"success\" }";
    } else {
        $sql = "ALTER TABLE datausage AUTO_INCREMENT = 1";
        if ($conn->query($sql) === TRUE) {
            echo "{ \"status\": \"error\", \"AI_FIXED\": true }";
        } else {
            echo "{ \"status\": \"error\", \"AI_FIXED\": false }";
        }
    }
    
    $conn->close();
?>
