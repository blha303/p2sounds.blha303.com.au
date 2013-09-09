<?php
// http://p2sounds.blha303.com.au/portal2/3
$arr = explode("/", $_SERVER['REQUEST_URI']);
if (count($arr) <= 2 && $arr[1] == "") {
    echo json_encode(array("error" => "No request provided.", "source" => "https://github.com/blha303/p2sounds.blha303.com.au"));
    header($_SERVER["SERVER_PROTOCOL"]." 400 No Request Provided");
} else if ($arr[1] == "") {
    echo json_encode(array("error" => "No game provided."));
    header($_SERVER["SERVER_PROTOCOL"]." 400 No Game Provided");
} else if ($arr[1] == "search") {
    if (count($arr) >= 4) {
        $game = $arr[2];
        $term = urldecode($arr[3]);
        $data = array_values(json_decode(file_get_contents("data/".$arr[2].".txt"), true));
        $matches = array();
        foreach($data as $item) {
            if (strpos($arr[2], "music") !== false) {
                if (strpos($item["who"], $term) === 0) {
                    $matches[] = $item["id"];
                }
            } else {
                if (strpos($item["text"], $term) === 0) {
                    $matches[] = $item["id"];
                }
            }
        }
        $out = array();
        foreach($matches as $match) {
            $id = intval($match) - 1;
            if (array_key_exists($id, $data)) {
                $out[] = $data[$id];
            }
        }
        if (count($out) >= 1) {
            echo json_encode($out);
        } else {
            echo json_encode(array("error" => "No results."));
            header($_SERVER["SERVER_PROTOCOL"]." 404 No Results");
        }
    } else if (count($arr) == 2) {
        echo json_encode(array("error" => "No game provided."));
        header($_SERVER["SERVER_PROTOCOL"]." 400 No Game Provided");
    } else if (count($arr) == 3 && file_exists("data/".$arr[2].".txt")) {
        echo json_encode(array("error" => "No search term provided."));
        header($_SERVER["SERVER_PROTOCOL"]." 400 No Search Term Provided");
    } else if (!file_exists("data/".$arr[2].".txt")) {
        echo json_encode(array("error" => "No such game."));
        header($_SERVER["SERVER_PROTOCOL"]." 404 No Such Game");
    }
} else if ($arr[2] == "") {
/*  echo json_encode(array("error" => "No ID provided."));
    header($_SERVER["SERVER_PROTOCOL"]." 400 No ID Provided"); */
    if (file_exists("data/".$arr[1].".txt")) {
        echo file_get_contents("data/".$arr[1].".txt");
    } else {
        echo "{\"error\": \"No such game.\"}";
        header($_SERVER["SERVER_PROTOCOL"]." 404 No Such Game");
    }
} else if (!is_numeric($arr[2])) {
    echo json_encode(array("error" => "ID should be numeric."));
    header($_SERVER["SERVER_PROTOCOL"]." 400 ID Not Numeric");
} else {
    if (file_exists("data/".$arr[1].".txt")) {
        $id = intval($arr[2]) - 1;
        $data = array_values(json_decode(file_get_contents("data/".$arr[1].".txt"), true));
        if (array_key_exists($id, $data)) {
            echo json_encode($data[$id]);
        } else {
            echo json_encode(array("error" => "No such key."));
            header($_SERVER["SERVER_PROTOCOL"]." 404 No Such Key");
        }
    } else {
        echo json_encode(array("error" => "No such game."));
        header($_SERVER["SERVER_PROTOCOL"]." 404 No Such Game");
    }
}
