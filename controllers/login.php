<?php
include_once '../config/dbConf.php';

$db = new Database();
$dbConn = $db->connect();
session_start();

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true 
            && $_SESSION['role'] === 'Student') {
    header('Location: studDashboard.php');
    exit;
}

elseif(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true 
            && $_SESSION['role'] === 'Turor') {
    header('Location: turorDashboard.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, password, email FROM users WHERE email = ?";
    $stmt = $dbConn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1){
        $user = $result->fetch_assoc();

        if(password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            
            if($_POST['role'] === 'Student'){
                $_SESSION['role'] = 'Student';
                header('Location: studentDashboard.php');
            }

            elseif($_POST['role'] === 'Tutor'){
                $_SESSION['role'] = 'Tutor';
                header('Location: tuturDashboard.php');
            }


        }
    }
    
    $error = "Invalid credentials";

}

?>