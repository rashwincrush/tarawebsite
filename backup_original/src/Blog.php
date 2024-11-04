<?php
class Blog {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO blog_posts (title, content, author, image_url, meta_description)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['title'],
            $data['content'],
            $data['author'],
            $data['image_url'] ?? null,
            $data['meta_description'] ?? null
        ]);
    }
    
    public function getAll($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $total = $this->db->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
        
        $stmt = $this->db->prepare("
            SELECT * FROM blog_posts 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        
        return [
            'posts' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'pages' => ceil($total / $limit)
        ];
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM blog_posts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE blog_posts 
            SET title = ?, content = ?, meta_description = ?, image_url = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['title'],
            $data['content'],
            $data['meta_description'] ?? null,
            $data['image_url'] ?? null,
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM blog_posts WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
