<?php

include_once '../config/dbConf.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");



$db = new Database();
$dbConn = $db->connect();

session_start();
session_unset();


/*if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && $_SESSION['role'] === 'Student') {
    $response = array('success': true, 'role': 'Student');
    echo json_encode($response);   	
    exit;
} elseif (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && $_SESSION['role'] === 'Tutor') {
    $response = array('success': true, 'role': 'Tutor');
    echo json_encode($response);
    exit;
}
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, password, email FROM users WHERE email = ?";
    $stmt = $dbConn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];

            if ($_POST['role'] === 'Student') {
                $_SESSION['role'] = 'Student';
                $response = array('success' => true, 'role' => 'Student');
                echo json_encode($response);
                exit;
            } elseif ($_POST['role'] === 'Tutor') {
                $_SESSION['role'] = 'Tutor';
                $response = array('success' => true, 'role' => 'Tutor');
                echo json_encode($response);
                exit;
            }
        }
    }
    $response = array('success' => false, 'error' => 'Invalid credentials');
    echo json_encode($response);
    exit;
}

?>
