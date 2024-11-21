<?php
class Event {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addEvent($name, $date, $location) {
        $stmt = $this->conn->prepare("INSERT INTO events (name, date, location) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $date, $location);
        return $stmt->execute();
    }

    public function getAllEvents() {
        $stmt = $this->conn->prepare("SELECT * FROM events");
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getEventById($event_id) {
        $stmt = $this->conn->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateEvent($event_id, $name, $date, $location) {
        $stmt = $this->conn->prepare("UPDATE events SET name = ?, date = ?, location = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $date, $location, $event_id);
        return $stmt->execute();
    }

    public function deleteEvent($event_id) {
        $stmt = $this->conn->prepare("DELETE FROM events WHERE id = ?");
        $stmt->bind_param("i", $event_id);
        return $stmt->execute();
    }
}
?>
