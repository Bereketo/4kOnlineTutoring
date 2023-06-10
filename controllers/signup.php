<?php
include_once '../config/dbConf.php';

$db = new Database();
$dbConn = $db->connect();

if(isset($_POST['submit'])){
$role = $_POST['role'];
$firstname = $_POST['firstname'];
$lastname = $_POST['latname'];
$email = $_POST['email'];
$password = $_POST['password'];
$created_at = date('Y-m-d');
$updated_at = date('Y-m-d');
$profile_pic = $_POST['profile'];

$checkQuery = "SELECT * FROM users WHERE email = ?";
try {
$checkStmt = $dbConn->prepare($checkQuery);
$checkStmt->bind_param("s", $email);
$chekStme->execute();
$checkResult = $checkStmt->get_result();

}

catch(mysqli_sql_exception $e){
    die(("Error: $e"));

}

if($checkResult->num_rows > 0) {
    echo "Email already exists.";
}

else{
    $hassedPassword = password_hash($password, PASSWORD_DEFAULT);
    $userInsertQuery = "INSERT INTO users(firstname, lastname, email, password, role,
                                     created_at, updated_at, profile_pic) VALUES(?,?,?,?,?,?,?,?)";
    $userInsert = $dbConn->prepare($userInsertQuery);
    $userInsert->bind_param("sssssss", $firstname, $lastname, $email, $hassedPassword, $role,
                            $created_at, $updated_at, $profile_pic);
    $userInsert->execute();
    $userId = $userInsert->insert_id;

    if($role === 'Student'){
        $studQuery = "INSERT INTO students(user_id) VALUES(?)";
        $studeInsert = $dbConn->prepare($studQuery);
        $studeInsert->bind_param("i", $userId);
        $studInsert->execute();
        $studeInsert->close();
    }

    elseif($role === "Tutor") {
        $tutorQuery = "INSERT INTO tutors(user_id, bio, availability) VALUES(?,?,?)";
        $tutorInsert = $dbConn()->prepare($tutorQuery);
        $tutorInsert->bind_param("iss", $userId, $bio, $available);
        $tutorInsert->execute();
        $tutorInsert->close();
    }
}

}

?>