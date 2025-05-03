<?php

namespace Core;

use PDO;

abstract class Model
{
    protected $conn;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    
    public function __construct($db = null)
    {
        $this->conn = $db ?: app('db')->getConnection();
    }
    
    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($data) {
            return $this->filterHidden($data);
        }
        
        return null;
    }
    
    public function all()
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map(function ($item) {
            return $this->filterHidden($item);
        }, $results);
    }
    
    public function create(array $data)
    {
        $filteredData = $this->filterFillable($data);
        
        if (empty($filteredData)) {
            return false;
        }
        
        $columns = implode(', ', array_keys($filteredData));
        $placeholders = ':' . implode(', :', array_keys($filteredData));
        
        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->conn->prepare($query);
        
        foreach ($filteredData as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        
        return $stmt->execute();
    }
    
    public function update($id, array $data)
    {
        $filteredData = $this->filterFillable($data);
        
        if (empty($filteredData)) {
            return false;
        }
        
        $fields = [];
        foreach (array_keys($filteredData) as $field) {
            $fields[] = "{$field} = :{$field}";
        }
        
        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE {$this->primaryKey} = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        foreach ($filteredData as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        
        return $stmt->execute();
    }
    
    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    public function paginate($page = 1, $perPage = 10, $search = null, $searchFields = [])
    {
        $page = max(1, (int)$page);
        $perPage = max(1, (int)$perPage);
        $offset = ($page - 1) * $perPage;
        
        // Build search conditions if needed
        $where = '';
        $params = [];
        
        if ($search && !empty($searchFields)) {
            $conditions = [];
            foreach ($searchFields as $field) {
                $conditions[] = "{$field} LIKE :search";
            }
            $where = 'WHERE ' . implode(' OR ', $conditions);
            $params[':search'] = "%{$search}%";
        }
        
        // Count total records
        $countQuery = "SELECT COUNT(*) as total FROM {$this->table} {$where}";
        $countStmt = $this->conn->prepare($countQuery);
        
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        
        $countStmt->execute();
        $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Get paginated data
        $query = "SELECT * FROM {$this->table} {$where} ORDER BY {$this->primaryKey} ASC LIMIT :offset, :limit";
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Filter hidden fields
        $data = array_map(function ($item) {
            return $this->filterHidden($item);
        }, $data);
        
        // Return paginated result
        return [
            'data' => $data,
            'total' => $totalCount,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($totalCount / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $totalCount),
        ];
    }
    
    protected function filterFillable(array $data)
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    protected function filterHidden(array $data)
    {
        if (empty($this->hidden)) {
            return $data;
        }
        
        return array_diff_key($data, array_flip($this->hidden));
    }
}