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
        case 'DELETE':
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            deleteMethod($conn, $id);
            break;
        default:
            throw new Exception("Method not allowed", 404);
    }
} catch (Exception $e) {
    http_response_code($e->getCode());
    echo json_encode(array("error" => $e->getMessage()));
}

/* -------------------------------------------------------------------------- */
/*                              Data delete part                              */
/* -------------------------------------------------------------------------- */
// ? http://localhost/api_with_php/delete_data.php?id=3
function deleteMethod($conn, $id)
{
    try {
        // Validate and sanitize incoming data as needed

        // Sanitize and validate ID (you may need to adjust this based on your specific requirements)
        $id = intval($id);

        // Use prepared statement for DELETE
        $sql = "DELETE FROM customers WHERE id=?";
        $params = array($id);
        $affectedRows = runPreparedQuery($conn, $sql, $params, 'delete');

        if ($affectedRows === 0) {
            throw new Exception("Error deleting data from the database", 500);
        }

        echo json_encode(array("success" => "Data deleted successfully"));
    } catch (Exception $e) {
        http_response_code($e->getCode());
        echo json_encode(array("error" => $e->getMessage()));
    }
}

// todo! Uncomment the line below for production to close the database connection when done
// $conn->close();