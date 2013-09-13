<?php
if (isset($_GET['format'])) {
    if ($_GET['format'] == "json") {
        $json = true;
        $xml = false;
    } else {
        $xml = true;
        $json = false;
    }
} else if (strpos($accept, "application/json") !== false) {
    $json = true;
    $xml = false;
} else {
    $xml = true;
    $json = false;
}

if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    header("access-control-allow-methods: GET");
    die();
} else if ($_SERVER['REQUEST_METHOD'] != "GET") {
    header($_SERVER["SERVER_PROTOCOL"]." 405 Method Not Allowed");
    if ($json) {
        die(json_encode(array("error" => "Only GET requests are supported at this time.")));
    } else if ($xml) {
        die("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<p2sounds>\n    <error>Only GET requests are supported at this time.</error>\n</p2sounds>");
    }
}
if (strpos($_SERVER['REQUEST_URI'], "?") !== false) {
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "?"));
}
// http://p2sounds.blha303.com.au/portal2/3
$arr = explode("/", $_SERVER['REQUEST_URI']);
if (count($arr) <= 2 && $arr[1] == "") {
    $out = json_encode(array("error" => "No request provided.",
                             "source" => "https://github.com/blha303/p2sounds.blha303.com.au",
                             "JSON" => "Add ?format=json to the end of urls, or set Accept header to application/json",
                             "XML" => "Add ?format=xml to the end of urls, or set Accept header to text/xml"));
    header($_SERVER["SERVER_PROTOCOL"]." 400 No Request Provided");
} else if ($arr[1] == "") {
    $out = json_encode(array("error" => "No game provided."));
    header($_SERVER["SERVER_PROTOCOL"]." 400 No Game Provided");
} else if ($arr[1] == "search") {
    if (count($arr) >= 4) {
        $game = $arr[2];
        $term = urldecode($arr[3]);
        $data = array_values(json_decode(file_get_contents("data/".$arr[2].".txt"), true));
        $matches = array();
        foreach($data as $item) {
            if (strpos($arr[2], "music") !== false) {
                if (strpos($item["who"], $term) !== false) {
                    $matches[] = $item["id"];
                }
            } else {
                if (strpos($item["text"], $term) !== false) {
                    $matches[] = $item["id"];
                }
            }
        }
        $out = array();
        $out["items"] = array();
        foreach($matches as $match) {
            if (strpos($arr[2], "portal2music") !== false) {
                $id = intval($match) - 15;
            } else {
                $id = intval($match) - 1;
            }
            if (array_key_exists($id, $data)) {
                $out["items"][] = $data[$id];
            }
        }
        if (count($out) < 1) {
            $out = json_encode(array("error" => "No results."));
            header($_SERVER["SERVER_PROTOCOL"]." 404 No Results");
        }
    } else if (count($arr) == 2) {
        $out = json_encode(array("error" => "No game provided."));
        header($_SERVER["SERVER_PROTOCOL"]." 400 No Game Provided");
    } else if (count($arr) == 3 && file_exists("data/".$arr[2].".txt")) {
        $out = json_encode(array("error" => "No search term provided."));
        header($_SERVER["SERVER_PROTOCOL"]." 400 No Search Term Provided");
    } else if (!file_exists("data/".$arr[2].".txt")) {
        $out = json_encode(array("error" => "No such game."));
        header($_SERVER["SERVER_PROTOCOL"]." 404 No Such Game");
    }
} else if ($arr[2] == "") {
/*  $out = json_encode(array("error" => "No ID provided."));
    header($_SERVER["SERVER_PROTOCOL"]." 400 No ID Provided"); */
    if (file_exists("data/".$arr[1].".txt")) {
        echo file_get_contents("data/".$arr[1].".txt");
    } else {
        $out = json_encode(array("error" => "No such game."));
        header($_SERVER["SERVER_PROTOCOL"]." 404 No Such Game");
    }
} else if (!is_numeric($arr[2])) {
    $out = json_encode(array("error" => "ID should be numeric."));
    header($_SERVER["SERVER_PROTOCOL"]." 400 ID Not Numeric");
} else {
    if (file_exists("data/".$arr[1].".txt")) {
        $id = intval($arr[2]) - 1;
        $data = array_values(json_decode(file_get_contents("data/".$arr[1].".txt"), true));
        if (array_key_exists($id, $data)) {
            $out = json_encode($data[$id]);
        } else {
            $out = json_encode(array("error" => "No such key."));
            header($_SERVER["SERVER_PROTOCOL"]." 404 No Such Key");
        }
    } else {
        $out = json_encode(array("error" => "No such game."));
        header($_SERVER["SERVER_PROTOCOL"]." 404 No Such Game");
    }
}

foreach (apache_request_headers() as $header => $value) {
    if ($header == "Accept") {
        $accept = $value;
    }
}

if ($json) {
    header('Content-type: application/json');
    echo $out;
} else if ($xml) {
    include("XML/Serializer.php");
    $options = array(
    "indent"    => "    ",
    "linebreak" => "\n",
    "typeHints" => false,
    "addDecl"   => true,
    "encoding"  => "UTF-8",
    "rootName"   => "p2sounds"
    );
    $serializer = new XML_Serializer($options);
    $obj = json_decode($out);
    header('Content-type: text/xml');

    if ($serializer->serialize($obj)) {
        $output = $serializer->getSerializedData();
        if (strpos($output, "<p2sounds />") !== false) {
            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<p2sounds>\n    <error>Currently search only works with json. Sorry for the inconvenience.</error>\n</p2sounds>";
        } else {
            echo $output;
        }
    } else {
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<p2sounds>\n    <error>Couldn't get XML object. Please notify blha303 http://twitter.com/blha303</error>\n</p2sounds>";
    }
}