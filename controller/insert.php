<?php

session_start();
include '../Config/ConnectionObjectOriented.php';
include '../Config/DB.php';
$_POST["role"] = "user";
$db = new DB($conn);
$auto = array();
$name = $_POST["name"];
$key = $db->apiKey($name);
$userid = $db->userId($name);
array_push($auto, $key);
array_push($auto, $userid);
$_POST["api_key"] = $key;
$_POST["userid"] = $userid;
$_POST["type"] = "user";
$_POST["tbname"] = "user";
$useridExist = "yes";
while ($useridExist != "no") {
    $data = $db->select($_POST["tbname"], "*", array("userid" => $_POST["userid"]));
    if ($data->num_rows > 0) {
        $useridExist = "yes";
        $_POST["userid"] = $db->userId($name);
    } else {
        $useridExist = "no";
    }
}
$info1 = "";
if (isset($_POST["info"])) {
    $info1 = $_POST["info"];
}
$info = array();
if ($useridExist == "no") {
    if ($db->exist($_POST["tbname"], array("email" => $_POST["email"])) == "no") {
        $info = $db->insert($_POST, $_POST["tbname"]);
        if ($info[0] == 1) {
            $info["status"] = "success";
            $info["key"] = $key;
            $info["uniqueid"] = $userid;
            $info["message"] = "Data  saved";
            $info["userid"] = $_SESSION["recentinsertedid"];
            $db->sendBack($_SERVER, $info1);
        } else if ($info[0] == 0) {
            $info["status"] = "failed";
            $info["message"] = "Data not saved ! server error";
            $db->sendBack($_SERVER, "?info=Data not saved ! server error");
        }
    } else {
        $info["status"] = "failed";
        $info["message"] = "This email is already exist";
        $db->sendBack($_SERVER, "?info=This email is already exist ! failed");
    }
}
