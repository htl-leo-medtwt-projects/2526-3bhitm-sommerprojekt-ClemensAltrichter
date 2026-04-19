<?php
session_start();

$_db_host = 'db_server';
$_db_username = 'cinematch';
$_db_password = 'cinematchpassword';
$_db_datenbank = 'cinematch';

$conn = new mysqli($_db_host, $_db_username, $_db_password, $_db_datenbank);

function fetchData($data){
    $array = [];

    while($row = $data->fetch_assoc()){
        array_push($array, $row);
    }

    return $array;
}