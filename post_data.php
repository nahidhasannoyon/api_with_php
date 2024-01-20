<?php

include "essentials/database_connection.php";
include "essentials/api_key_validation.php";

header('Content-Type: application/json');

$apiKey = isset($_GET['api_key']) ? $_GET['api_key'] : '';
$securityCode = isset(getallheaders()['X-Security-Code']) ? getallheaders()['X-Security-Code'] : '';

try {
    validateApiKey($apiKey);
    validateSecurityCode($securityCode);

    $requestMethod = $_SERVER['REQUEST_METHOD'];

    switch ($requestMethod) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            postMethod($conn, $data);
            break;
        default:
            throw new Exception("Method not allowed", 404);
    }
} catch (Exception $e) {
    http_response_code($e->getCode());
    echo json_encode(array("error" => $e->getMessage()));
}

/* -------------------------------------------------------------------------- */
/*                               Data post part                               */
/* -------------------------------------------------------------------------- */
// ? http://localhost/api_with_php/post_data.php
// {
//     "id": 3,
//     "name": "Noyon",
//     "address" : "Notun Bazar"
// }
function postMethod($conn, $data)
{
    try {
        // Validate and sanitize incoming data as needed
        // For example, check if required fields are present

        $id = $data['id'];
        $name = $data['name'];
        $address = $data['address'];

        // Sanitize and validate data (you may need to adjust this based on your specific requirements)
        $id = intval($id);
        $name = $conn->real_escape_string($name);
        $address = floatval($address);

        // Use runPreparedQuery for INSERT (POST)
        $sql = "INSERT INTO customers (id, name, address) VALUES (?, ?, ?)";
        $params = array($id, $name, $address);
        $affectedRows = runPreparedQuery($conn, $sql, $params, 'insert');

        if ($affectedRows === 0) {
            throw new Exception("Error inserting data into the database", 500);
        }

        echo json_encode(array("success" => "Data inserted successfully"));
    } catch (Exception $e) {
        http_response_code($e->getCode());
        echo json_encode(array("error" => $e->getMessage()));
    }
}
// todo! Uncomment the line below for production to close the database connection when done
// $conn->close();