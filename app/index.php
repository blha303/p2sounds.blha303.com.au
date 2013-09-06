<?php
// http://p2sounds.blha303.com.au/portal2/3
$arr = explode("/", $_SERVER['REQUEST_URI']);
if (count($arr) <= 1) {
    echo "{\"error\": \"No request provided.\"}";
    header($_SERVER["SERVER_PROTOCOL"]." 400 No Request Provided");
} else if ($arr[1] == "") {
    echo "{\"error\": \"No game provided.\"}";
    header($_SERVER["SERVER_PROTOCOL"]." 400 No Game Provided");
} else if ($arr[2] == "") {
    echo "{\"error\": \"No ID provided.\"}";
    header($_SERVER["SERVER_PROTOCOL"]." 400 No ID Provided");
} else if (!is_numeric($arr[2])) {
    echo "{\"error\": \"ID should be numeric.\"}";
    header($_SERVER["SERVER_PROTOCOL"]." 400 ID Not Numeric");
} else {
    if (file_exists("data/".$arr[1].".txt")) {
        $id = intval($arr[2]) - 1;
        $data = array_values(json_decode(file_get_contents("data/".$arr[1].".txt"), true));
        if (array_key_exists($id, $data)) {
            echo json_encode($data[$id]);
        } else {
            echo "{\"error\": \"No such key.\"}";
            header($_SERVER["SERVER_PROTOCOL"]." 404 No Such Key");
        }
    } else {
        echo "{\"error\": \"No such game.\"}";
        header($_SERVER["SERVER_PROTOCOL"]." 404 No Such Game");
    }
}
