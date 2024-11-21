<?php
class Admin {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllUsers() {
        $query = "SELECT * FROM users";
        return $this->conn->query($query);
    }

    public function addNewUser($username, $password, $role) {
        $password = md5($password); 
        $query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $username, $password, $role);
        return $stmt->execute();
    }

    public function getUserById($user_id) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateUser($user_id, $username, $password, $role) {
        if (!empty($password)) {
            $password = md5($password); 
        } else {
            $password = null;
        }

        if ($password) {
            $query = "UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sssi", $username, $password, $role, $user_id);
        } else {
            $query = "UPDATE users SET username = ?, role = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssi", $username, $role, $user_id);
        }

        return $stmt->execute();
    }

    public function deleteUser($user_id) {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }
}
?>
