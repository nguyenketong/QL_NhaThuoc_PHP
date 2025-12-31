<?php
/**
 * Base Model
 */
class Model
{
    protected $db;
    protected $table;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function all($orderBy = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) $sql .= " ORDER BY $orderBy";
        return $this->db->query($sql)->fetchAll();
    }

    public function find($id, $primaryKey = 'id')
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE $primaryKey = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function where($conditions, $params = [], $orderBy = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE $conditions";
        if ($orderBy) $sql .= " ORDER BY $orderBy";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count($conditions = '1=1', $params = [])
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE $conditions");
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function insert($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }

    public function update($id, $data, $primaryKey = 'id')
    {
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $stmt = $this->db->prepare("UPDATE {$this->table} SET $set WHERE $primaryKey = ?");
        $values = array_values($data);
        $values[] = $id;
        return $stmt->execute($values);
    }

    public function delete($id, $primaryKey = 'id')
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE $primaryKey = ?");
        return $stmt->execute([$id]);
    }
}
