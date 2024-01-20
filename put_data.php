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
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            putMethod($conn, $data, $id);
            break;
        default:
            throw new Exception("Method not allowed", 404);
    }
} catch (Exception $e) {
    http_response_code($e->getCode());
    echo json_encode(array("error" => $e->getMessage()));
}

/* -------------------------------------------------------------------------- */
/*                                Data put part                               */
/* -------------------------------------------------------------------------- */
// ? http://localhost/api_with_php/put_data.php?id=3
// {
//     "name": "Daya",
//     "address" : "Notun Bazar"
// }
function putMethod($conn, $data, $id)
{
    try {
        // Validate and sanitize incoming data as needed

        $name = $data['name'];
        $address = $data['address'];

        // Sanitize and validate data (you may need to adjust this based on your specific requirements)
        $name = $conn->real_escape_string($name);
        $address = floatval($address);

        // Use prepared statement for UPDATE (PUT)
        $sql = "UPDATE customers SET name=?, address=? WHERE id=?";
        $params = array($name, $address, $id);
        $affectedRows = runPreparedQuery($conn, $sql, $params, 'update');

        if ($affectedRows === 0) {
            throw new Exception("Error updating data in the database", 500);
        }

        echo json_encode(array("success" => "Data updated successfully"));
    } catch (Exception $e) {
        http_response_code($e->getCode());
        echo json_encode(array("error" => $e->getMessage()));
    }
}

// todo! Uncomment the line below for production to close the database connection when done
// $conn->close();