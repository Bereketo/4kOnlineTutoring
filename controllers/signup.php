<?php
include_once '../models/User.php';


$userModel = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $photoUrl = "";

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        // File upload code
        $photoName = $_FILES['profile_pic']['name'];
        $photoTemp = $_FILES['profile_pic']['tmp_name'];
        $photoDestination = "../public/uploads/" . $photoName;

        if (move_uploaded_file($photoTemp, $photoDestination)) {
            // File moved successfully
            $photoUrl = $photoDestination;
        } else {
            $response = array('success' => true, 'upload' => false, 'message' => 'Failed to move the file');
        }
    } else {
        $response = array('success' => true, 'upload' => false, 'message' => 'Can\'t upload');
    }

    $result = $userModel->createUser($role, $firstname, $lastname, $email, $password, $photoUrl);
   if ($result === 'Registration successful') {
        $response = array('success' => true, 'upload' => true, 'message' => 'Successfully registered');
        echo json_encode($response);
        exit;
    } else {
        $response = array('success' => false, 'upload' => false, 'message' => 'Error registering a user');
        echo json_encode($response);
        exit;
    }
}

$response = array('success' => false, 'upload' => false, 'message' => 'Invalid request');
echo json_encode($response);
?>
