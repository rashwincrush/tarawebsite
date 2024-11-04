<?php
class Gallery {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO gallery (title, image_url, category, description)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['title'],
            $data['image_url'],
            $data['category'],
            $data['description']
        ]);
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM gallery ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCategory($category) {
        $stmt = $this->db->prepare("SELECT * FROM gallery WHERE category = ? ORDER BY created_at DESC");
        $stmt->execute([$category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE gallery
            SET title = ?, image_url = ?, category = ?, description = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['title'],
            $data['image_url'],
            $data['category'],
            $data['description'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM gallery WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
