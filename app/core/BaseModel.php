<?php
require_once __DIR__ . '/Database.php';

class BaseModel
{
    protected $db;
    protected $table = '';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findAll()
    {
        $stmt = $this->db->query("SELECT * FROM " . $this->table);
        return $stmt->fetchAll();
    }

    public function findById($id, $pk = null)
    {
        $pk = $pk ?? $this->getPrimaryKey();
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE `$pk` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    protected function getPrimaryKey()
    {
        return $this->table . '_id';
    }
}
