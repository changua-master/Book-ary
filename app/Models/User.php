<?php
/**
 * Modelo de Usuario
 * Maneja todas las operaciones relacionadas con usuarios
 */

class User {
    private $conexion;
    
    public function __construct($db) {
        $this->conexion = $db;
    }
    
    /**
     * Buscar usuario por nombre de usuario
     */
    public function findByUsername($username) {
        $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }
    
    /**
     * Buscar usuario por ID
     */
    public function findById($id) {
        $sql = "SELECT id, username, role FROM users WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }
    
    /**
     * Crear nuevo usuario
     */
    public function create($username, $password, $role = 'estudiante') {
        // Verificar si el usuario ya existe
        if ($this->findByUsername($username)) {
            return ['success' => false, 'message' => 'El usuario ya existe'];
        }
        
        // Encriptar contraseña
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("sss", $username, $hashedPassword, $role);
        
        if ($stmt->execute()) {
            $userId = $this->conexion->insert_id;
            $stmt->close();
            return ['success' => true, 'user_id' => $userId];
        }
        
        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Error al crear usuario: ' . $error];
    }
    
    /**
     * Actualizar usuario
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        $types = '';
        
        if (isset($data['username'])) {
            $fields[] = "username = ?";
            $values[] = $data['username'];
            $types .= 's';
        }
        
        if (isset($data['password'])) {
            $fields[] = "password = ?";
            $values[] = password_hash($data['password'], PASSWORD_DEFAULT);
            $types .= 's';
        }
        
        if (isset($data['role'])) {
            $fields[] = "role = ?";
            $values[] = $data['role'];
            $types .= 's';
        }
        
        if (empty($fields)) {
            return ['success' => false, 'message' => 'No hay datos para actualizar'];
        }
        
        $values[] = $id;
        $types .= 'i';
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param($types, ...$values);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true];
        }
        
        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Error al actualizar: ' . $error];
    }
    
    /**
     * Eliminar usuario
     */
    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $affected = $stmt->affected_rows;
            $stmt->close();
            return ['success' => true, 'affected_rows' => $affected];
        }
        
        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Error al eliminar: ' . $error];
    }
    
    /**
     * Obtener todos los usuarios
     */
    public function all($role = null) {
        if ($role) {
            $sql = "SELECT id, username, role FROM users WHERE role = ? ORDER BY username ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("s", $role);
        } else {
            $sql = "SELECT id, username, role FROM users ORDER BY username ASC";
            $stmt = $this->conexion->prepare($sql);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $users = [];
        
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        $stmt->close();
        return $users;
    }
    
    /**
     * Verificar contraseña
     */
    public function verifyPassword($username, $password) {
        $user = $this->findByUsername($username);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Contar usuarios por rol
     */
    public function countByRole($role) {
        $sql = "SELECT COUNT(*) as total FROM users WHERE role = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['total'];
    }
}