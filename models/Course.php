<?php
include_once '../config/dbConf.php';

class Course
{
    private $dbConn;

    public function __construct()
    {
        $db = new Database();
	$this->dbConn = $db->connect();
    }

    public function createCourse($tutorId, $courseData)
    {
        try {
            // Insert the new course
            $insertQuery = "INSERT INTO courses (name, description, tutor_id, created_at, updated_at, course_url, course_img) VALUES (?, ?, ?, NOW(), NOW(), ?, ?)";
            $insertStmt = $this->dbConn->prepare($insertQuery);
            $insertStmt->bind_param("ssiss", $courseData['name'], $courseData['description'], $tutorId, $courseData['course_url'], $courseData['course_img']);
            $insertResult = $insertStmt->execute();

            if (!$insertResult) {
                throw new Exception("Error creating course");
            }

            return "Course created successfully";
        } catch (Exception $e) {
            return "Error: ". $e->getMessage();
        }
    }
    
    public function getAllCourses(){

        try{
            $query = "SELECT * FROM courses";
            $stmt = $this->dbConn->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();

            $courses = [];

            while($row = $result->fetch_assoc()){
                $courses[] = $row;
            }
            return $courses;
        }
        
        catch(Exception $e){
            return null;
        }

    }
    
    public function enrollments($userId){
        try{

            $query = "SELECT * FROM enrollments WHERE id = ?";
            $stmt = $this->dbConn->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result->num_rows === 0) {
                throw new Exception("No student Enrolled");
            }
            $user = $result->fetch_assoc();
            return $user;
        } catch (Exception $e) {
            return null;
        }   
    }
    
    public function enrolledStudents($courseId){
        try{

            $query = "SELECT * FROM courses WHERE id = ?";
            $stmt = $this->dbConn->prepare($query);
            $stmt->bind_param("i", $courseId);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result->num_rows === 0) {
                throw new Exception("No student Enrolled");
            }
            $courses = $result->fetch_assoc();
            return $courses;
        } catch (Exception $e) {
            return null;
        }   


    }

    public function getCoursesByTutorId($tutorId)
    {
        try {
            $query = "SELECT * FROM courses WHERE tutor_id = ?";
            $stmt = $this->dbConn->prepare($query);
            $stmt->bind_param("i", $tutorId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $courses = [];
            while ($row = $result->fetch_assoc()) {
                $courses[] = $row;
            }
            
            return $courses;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getCourseById($courseId)
    {
        try {
            $query = "SELECT * FROM courses WHERE id = ?";
            $stmt = $this->dbConn->prepare($query);
            $stmt->bind_param("i", $courseId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("Course not found");
            }

            $course = $result->fetch_assoc();
            return $course;
        } catch (Exception $e) {
            return null;
        }
    }

    public function enroll($userId, $courseId) {
        try {
            $check = "SELECT * FROM enrollments WHERE course_id = ? and student_id = ?";
            $checkStmt = $this->dbConn->prepare($check);
            $checkStmt->bind_param("ii", $courseId, $userId);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if($checkResult->num_rows > 0){
                return "Already Enrolled";
            }
            else {
                $query = "INSERT INTO enrollments(course_id, student_id, enrollment_date)
                      VALUES(?, ?, NOW())";
                $stmt = $this->dbConn->prepare($query);
                $stmt->bind_param("ii",$courseId, $userId);
                $stmt->execute();
                return "Enrolled successfully";
            }
        }
         catch (Exception $e) {
            return null;
         }
    }

    public function updateCourse($courseId, $courseData)
    {
        try {
            // Check if the course exists
            $checkQuery = "SELECT * FROM courses WHERE id = ?";
            $checkStmt = $this->dbConn->prepare($checkQuery);
            $checkStmt->bind_param("i", $courseId);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows === 0) {
                throw new Exception("Course not found");
            }

            // Update the course
            $updateQuery = "UPDATE courses SET name = ?, description = ? WHERE id = ?";
            $updateStmt = $this->dbConn->prepare($updateQuery);
            $updateStmt->bind_param("ssi", $courseData['name'], $courseData['description'], $courseId);
            $updateResult = $updateStmt->execute();

            if (!$updateResult) {
                throw new Exception("Error updating course");
            }

            return "Course updated successfully";
        } catch (Exception $e) {
            return "Error: ". $e->getMessage();
        }
    }

    public function deleteCourse($courseId)
    {
        try {
            // Check if the course exists
            $checkQuery = "SELECT * FROM courses WHERE id = ?";
            $checkStmt = $this->dbConn->prepare($checkQuery);
            $checkStmt->bind_param("i", $courseId);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows === 0) {
                throw new Exception("Course not found");
            }

            // Delete the course
            $deleteQuery = "DELETE FROM courses WHERE id = ?";
            $deleteStmt = $this->dbConn->prepare($deleteQuery);
            $deleteStmt->bind_param("i", $courseId);
            $deleteResult = $deleteStmt->execute();

            if (!$deleteResult) {
                throw new Exception("Error deleting course");
            }

            // Return success message
            return "Course deleted successfully";
        } catch (Exception $e) {
            return "Error: ". $e->getMessage();
        }
    }

    
}

?>
