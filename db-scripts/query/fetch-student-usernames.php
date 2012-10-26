<?php
require_once('../dbconn.php');

$result = $link->query("select username,fname,lname from coop_users where roles_id = 4");

$rows = $result->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $r) {
    $uname = $r['username'];
    $fname = $r['fname'];
    $lname = $r['lname'];
    echo "$uname - $fname $lname \n";
}

?>
