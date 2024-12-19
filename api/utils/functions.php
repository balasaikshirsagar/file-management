<?php

function connect()
{
    $host = 'localhost';
    $port = '3306'; 
    $db_name = 'employeetask';
    $db_username = 'root'; 
    $db_password = ''; 

    try {
        $pdo = new \PDO("mysql:host=$host;port=$port;dbname=$db_name", $db_username, $db_password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (\PDOException $e) {
        sendResponse(false, "Database connection error: " . $e->getMessage());
    }
}

function sendResponse($status = false, $message = "", $data = null)
{
    echo json_encode(["status" => $status, "message" => $message, "data" => $data]);
    exit();
}
