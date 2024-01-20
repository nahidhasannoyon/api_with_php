<?php

include "essentials/database_connection.php";
include "essentials/api_key_validation.php";

header('Content-Type: application/json');

$apiKey = isset($_GET['api_key']) ? $_GET['api_key'] : '';
$securityCode = isset(getallheaders()['X-Security-Code']) ? getallheaders()['X-Security-Code'] : '';


try {
    validateApiKey($apiKey);
    validateSecurityCode($securityCode);

    $request = $_SERVER['REQUEST_METHOD'];

    switch ($request) {
        case 'GET':
            // Check if 'id' or 'ids' parameter is provided
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            $ids = isset($_GET['ids']) ? $_GET['ids'] : '';

            if (!empty($id)) {
                getMethod($conn, intval($id));
            } elseif (!empty($ids)) {
                getMethod($conn, array_map('intval', explode(',', $ids)));
            } else {
                getAllMethod($conn);
            }
            break;
        default:
            throw new Exception("Method not allowed", 404);
    }
} catch (Exception $e) {
    http_response_code($e->getCode());
    echo json_encode(array("error" => $e->getMessage()));
}

/* -------------------------------------------------------------------------- */
/*                           Data get part for all data                       */
/* -------------------------------------------------------------------------- */
// ? http://localhost/api_with_php/fetch_data.php
function getAllMethod($conn)
{
    try {
        $sql = "SELECT * FROM customers";
        $result = $conn->query($sql);

        if ($result === false) {
            throw new Exception("Internal Server Error", 500);
        }

        $rows = array();

        while ($r = $result->fetch_assoc()) {
            $rows[] = $r;
        }

        if (!empty($rows)) {
            echo json_encode($rows);
        } else {
            throw new Exception("No data found", 404);
        }
    } catch (Exception $e) {
        http_response_code($e->getCode());
        echo json_encode(array("error" => $e->getMessage()));
    }
}

/* -------------------------------------------------------------------------- */
/*                  Data get part for single or multiple IDs                  */
/* -------------------------------------------------------------------------- */
// ? http://localhost/api_with_php/fetch_data.php?id=1&api_key=your_secret_key
// ? http://localhost/api_with_php/fetch_data.php?ids=1,2&api_key=your_secret_key
function getMethod($conn, $ids)
{
    try {
        if (empty($ids)) {
            throw new Exception("ID(s) parameter is required", 400);
        }

        // Sanitize and validate the IDs
        if (is_array($ids)) {
            $ids = implode(',', $ids);
        } else {
            $ids = intval($ids);
        }

        $sql = "SELECT * FROM customers WHERE id IN ($ids)";
        $result = $conn->query($sql);

        if ($result === false) {
            throw new Exception("Internal Server Error", 500);
        }

        $rows = array();

        while ($r = $result->fetch_assoc()) {
            $rows[] = $r;
        }

        if (!empty($rows)) {
            echo json_encode($rows);
        } else {
            throw new Exception("No data found for the specified ID(s)", 404);
        }
    } catch (Exception $e) {
        http_response_code($e->getCode());
        echo json_encode(array("error" => $e->getMessage()));
    }
}

// todo! Uncomment the line below for production to close the database connection when done
// $conn->close();