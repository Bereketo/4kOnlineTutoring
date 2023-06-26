<?php


include_once '../config/dbConf.php';
include_once '../helpers/validation.php';

class User {
    private $dbConn;

    public function __construct() {
        $db = new Database();
        $this->dbConn = $db->connect();
    }

    public function getUserByEmail($email) {
        try {
            // Retrieve user data from the database
            $query = "SELECT * FROM users WHERE email = ?";
            $stmt = $this->dbConn->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows === 0) {
                throw new Exception("User not found");
            }
    
            // Fetch user data as an associative array
            $user = $result->fetch_assoc();
    
            // Return the user data
            return $user;
        } catch (Exception $e) {
            return null; 
        }
    }

    public function getUserById($userId) {
        try {
            // Retrieve user data from the database
            $query = "SELECT * FROM users WHERE id = ?";
            $stmt = $this->dbConn->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows === 0) {
                throw new Exception("User not found");
            }
    
            // Fetch user data as an associative array
            $user = $result->fetch_assoc();
    
            // Return the user data
            return $user;
        } catch (Exception $e) {
            return null; 
        }
    }
    

    public function createUser($role, $firstname, $lastname, $email, $password, $profile_pic) {
        try {
            // Input validation
            if (!validateEmail($email)) {
                throw new Exception("Invalid email format");
            }
    
            if (!validatePassword($password)) {
                throw new Exception("Invalid password");
            }
    
            // Check if email already exists
            $checkQuery = "SELECT * FROM users WHERE email = ?";
            $checkStmt = $this->dbConn->prepare($checkQuery);
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
    
            if ($checkResult->num_rows > 0) {
                throw new Exception("Email already exists");
            }
    
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
            // Insert user
            $createdAt = date('Y-m-d');
            $updatedAt = date('Y-m-d');
            $insertQuery = "INSERT INTO users (firstname, lastname, email, password, role, created_at, updated_at, profile_pic) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $insertStmt = $this->dbConn->prepare($insertQuery);
            if (!$insertStmt) {
                throw new Exception("Error in query preparation: " . $this->dbConn->error);
            }
            $insertStmt->bind_param("ssssssss", $firstname, $lastname, $email, $hashedPassword, $role, $createdAt, $updatedAt, $profile_pic); // Bind the profile_pic value
            $insertResult = $insertStmt->execute();
            if (!$insertResult) {
                throw new Exception("Error in user insertion: " . $insertStmt->error);
            }
            $userId = $insertStmt->insert_id;
    
            if ($role === 'Student') {
                // Insert student
                $studentQuery = "INSERT INTO students (user_id) VALUES (?)";
                $studentStmt = $this->dbConn->prepare($studentQuery);
                if (!$studentStmt) {
                    throw new Exception("Error in query preparation: " . $this->dbConn->error);
                }
                $studentStmt->bind_param("i", $userId);
                $studentResult = $studentStmt->execute();
                if (!$studentResult) {
                    throw new Exception("Error in student insertion: " . $studentStmt->error);
                }
                $studentStmt->close();
            } elseif ($role === "Tutor") {
                // Insert tutor
                $tutorQuery = "INSERT INTO tutors (user_id, bio, availability) VALUES (?, ?, ?)";
                $tutorStmt = $this->dbConn->prepare($tutorQuery);
                if (!$tutorStmt) {
                    throw new Exception("Error in query preparation: " . $this->dbConn->error);
                }
                $tutorStmt->bind_param("iss", $userId, $bio, $availability);
                $tutorResult = $tutorStmt->execute();
                if (!$tutorResult) {
                    throw new Exception("Error in tutor insertion: " . $tutorStmt->error);
                }
                $tutorStmt->close();
            }
    
            $insertStmt->close();
            return 'Registration successful';
        } catch (Exception $e) {
            return 'Error';
        }
    }
    



    public function deleteUserByEmail($email) {
        try {
            // Check if the user exists
            $checkQuery = "SELECT * FROM users WHERE email = ?";
            $checkStmt = $this->dbConn->prepare($checkQuery);
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows === 0) {
                throw new Exception("User not found");
            }

            // Delete the user
            $deleteQuery = "DELETE FROM users WHERE email = ?";
            $deleteStmt = $this->dbConn->prepare($deleteQuery);
            $deleteStmt->bind_param("s", $email);
            $deleteResult = $deleteStmt->execute();

            if (!$deleteResult) {
                throw new Exception("Error deleting user");
            }

            // Return success message
            return "User deleted successfully";
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }


    public function updateUser($email, $formData) {
        try {
            // Check if the user exists
            $checkQuery = "SELECT * FROM users WHERE email = ?";
            $checkStmt = $this->dbConn->prepare($checkQuery);
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
    
            if ($checkResult->num_rows === 0) {
                throw new Exception("User not found");
            }
    
            // Prepare the update query and values
            $updateQuery = "UPDATE users SET ";
            $values = [];
    
            foreach ($formData as $field => $value) {
                // Handle profile picture separately
                if ($field === 'profile_pic') {
                    $updateQuery .= "profile_pic = ?, ";
                    $values[] = $value;
                } else {
                    $updateQuery .= "$field = ?, ";
                    $values[] = $value;
                }
            }
    
            // Remove trailing comma and space
            $updateQuery = rtrim($updateQuery, ", ");
    
            // Add WHERE condition based on user email
            $updateQuery .= " WHERE email = ?";
            $values[] = $email;
    
            // Prepare and execute the update statement
            $updateStmt = $this->dbConn->prepare($updateQuery);
            $bindTypes = str_repeat("s", count($values)); // Assuming all values are strings
            $updateStmt->bind_param($bindTypes, ...$values);
            $updateResult = $updateStmt->execute();
    
            if (!$updateResult) {
                throw new Exception("Error updating user");
            }
    
            // Return success message
            return "User updated successfully";
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
    
}
?>

