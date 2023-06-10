<?php


class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "password";
    private $db = "online_tutor";
    private $conn;

    public function connect() {
	$this->conn = new mysqli($this->host, $this->username, $this->password, $this->db);

        if ($this->conn->connect_error) {
            die("Connecton failed:". $this->conn->connect_error);
        }

        return $this->conn;
    }


}

?>
