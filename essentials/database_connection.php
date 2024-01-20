<?php

$server = "localhost";
$username = "root";
$dbname = "api_with_php";
$password = "";

try {
    // Create a new mysqli connection
    $conn = new mysqli($server, $username, $password, $dbname);

    // Check for connection errors
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    } else {
        // echo "Connect successfully done";  // ? for testing purposes only
    }

    // Set charset to utf8mb4 for proper character encoding
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// TODO! Uncomment the line below for production to close the database connection when done
// $conn->close();
/**
 * Function to run prepared statements with binding
 *
 * @param mysqli $conn The database connection
 * @param string $sql The SQL query
 * @param array $params An array of parameters for binding
 * @return mysqli_result The result set
 * @throws Exception If an error occurs during query execution
 */
function runPreparedQuery($conn, $sql, $params = array())
{
    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);

    // Check if preparation was successful
    if ($stmt === false) {
        throw new Exception("Error preparing statement: " . $conn->error);
    }

    // Bind parameters if there are any
    if (!empty($params)) {
        // Determine the types of parameters and bind them
        $types = str_repeat('s', count($params)); // Assuming all parameters are strings
        $stmt->bind_param($types, ...$params);
    }

    // Execute the prepared statement
    $stmt->execute();

    // Check for errors during execution
    if ($stmt->error) {
        throw new Exception("Error executing statement: " . $stmt->error);
    }

    // Get the result set
    $result = $stmt->get_result();

    // Close the statement
    $stmt->close();

    // Return the result set
    return $result;
}
