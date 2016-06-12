<?php
$path = '../lib';
require_once("$path/class/AJAXChatDataBase.php");
require_once("$path/class/AJAXChatMySQLDataBase.php");
require_once("$path/class/AJAXChatMySQLQuery.php");
require_once("$path/class/AJAXChatMySQLiDataBase.php");
require_once("$path/class/AJAXChatMySQLiQuery.php");

if (!require_once("$path/config.php")) {
    header('Location: ../');
    exit;
}

$userId = isset($_REQUEST['userId']) ? (int) $_REQUEST['userId'] : null;

if (!is_numeric($userId)) {
    $userId = null;
}

$table = $config['dbTableNames']['bans'];
$db = new AJAXChatDataBase($config);

if(!$config['dbConnection']['link']) {
    $db->connect($config['dbConnection']);
    if($db->error()) {
        echo $this->db->getError();
        die();
    }
    $db->select($config['dbConnection']['name']);
    if($db->error()) {
        echo $db->getError();
        die();
    }
}
unset($config['dbConnection']);

$sql = "SELECT userName, dateTime, NOW() as currentTime FROM $table WHERE userID = $userId";
$result = $db->sqlQuery($sql);

$row = $result->fetch();

if (is_null($row)) {
    header('Location: ../');
    exit;
} else {
    $banExpiration = new DateTime($row['dateTime']);
    $now = new DateTime($row['currentTime']);

    if ($banExpiration < $now) {
        header('Location: ../');
        exit;
    }

    $interval = $banExpiration->diff($now);
    $banDuration = '';

    if ($interval->format('%d') > 0) {
        $banDuration .= $interval->format('%d').' Days ';
    }

    if ($interval->h > 0) {
        $banDuration .= $interval->format('%h').' Hours ';
    }

    if ($interval->i > 0) {
        $banDuration .= $interval->format('%i').' Minutes ';
    }

    if ($interval->s > 0) {
        $banDuration .= $interval->format('%s').' Seconds';
    }
}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
    <title>PIMPNITES POKEMON CHAT</title>
    <link rel="stylesheet" type="text/css" href="../css/black.css" title="black"/>
    <link rel="alternate stylesheet" type="text/css" href="../css/Cobalt.css" title="Cobalt"/>
    <link rel="alternate stylesheet" type="text/css" href="../css/Mercury.css" title="Mercury"/>
    <link rel="alternate stylesheet" type="text/css" href="../css/Uranium.css" title="Uranium"/>
</head>
<body class="ajax-chat" onload="initializeLoginPage();">
<div id="loginContent">
    <h2>You have been Banned for <?php echo $banDuration; ?></h2>
    <h3>Enjoy your Trip to the Moon</h3>
    <script>
        setTimeout(function () {
            window.location.href = "https://www.youtube.com/watch?v=sntg94bgH5M&feature=player_embedded";
        }, 7000);
    </script>
</div>
</body>
</html>
