<?php
// http://p2sounds.blha303.com.au/portal2/3
$arr = explode("/", $_SERVER['REQUEST_URI']);
if (count($arr) <= 1) {
  echo "{\"error\": \"No request provided.\"}";
  http_response_code(400);
} else if ($arr[1] == "") {
  echo "{\"error\": \"No game provided.\"}";
  http_response_code(400);
} else if ($arr[2] == "") {
  echo "{\"error\": \"No ID provided.\"}";
  http_response_code(400);
} else if (!is_numeric($arr[2])) {
  echo "{\"error\": \"ID should be numeric.\"}";
  http_response_code(400);
} else {

}
