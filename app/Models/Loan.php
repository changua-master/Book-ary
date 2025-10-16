<?php
/**
 * Modelo de Préstamo
 * Maneja todas las operaciones relacionadas con préstamos de libros
 */

class Loan {
    private $conexion;
    
    public function __construct($db) {
        $this->conexion = $db;
    }
    
    /**
     * Obtener todos los préstamos
     */
    public function all() {
        $sql = "SELECT 
                    p.id, 
                    p.fecha_prestamo, 
                    p.fecha_devolucion, 
                    p.fecha_devuelto,
                    p.estado,
                    l.titulo as libro_titulo,
                    l.autor as libro_autor,
                    u.username as usuario_nombre,
                    u.id as usuario_id
                FROM prestamos p
                INNER JOIN libros l ON p.id_libro = l.id
                INNER JOIN users u ON p.id_usuario = u.id
                ORDER BY p.fecha_prestamo DESC";
        
        $result = $this->conexion->query($sql);
        
        $loans = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $loans[] = $row;
            }
        }
        return $loans;
    }
    
    /**
     * Buscar préstamo por ID
     */
    public function findById($id) {
        $sql = "SELECT 
                    p.*,
                    l.titulo as libro_titulo,
                    l.autor as libro_autor,
                    u.username as usuario_nombre
                FROM prestamos p
                INNER JOIN libros l ON p.id_libro = l.id
                INNER JOIN users u ON p.id_usuario = u.id
                WHERE p.id = ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $loan = $result->fetch_assoc();
        $stmt->close();
        return $loan;
    }
    
    /**
     * Obtener préstamos por usuario
     */
    public function byUser($userId, $status = null) {
        if ($status) {
            $sql = "SELECT 
                        p.*,
                        l.titulo as libro_titulo,
                        l.autor as libro_autor,
                        l.id as libro_id
                    FROM prestamos p
                    INNER JOIN libros l ON p.id_libro = l.id
                    WHERE p.id_usuario = ? AND p.estado = ?
                    ORDER BY p.fecha_prestamo DESC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("is", $userId, $status);
        } else {
            $sql = "SELECT 
                        p.*,
                        l.titulo as libro_titulo,
                        l.autor as libro_autor,
                        l.id as libro_id
                    FROM prestamos p
                    INNER JOIN libros l ON p.id_libro = l.id
                    WHERE p.id_usuario = ?
                    ORDER BY p.fecha_prestamo DESC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $userId);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $loans = [];
        while ($row = $result->fetch_assoc()) {
            $loans[] = $row;
        }
        $stmt->close();
        return $loans;
    }
    
    /**
     * Obtener préstamos activos
     */
    public function active() {
        $sql = "SELECT 
                    p.*,
                    l.titulo as libro_titulo,
                    l.autor as libro_autor,
                    u.username as usuario_nombre
                FROM prestamos p
                INNER JOIN libros l ON p.id_libro = l.id
                INNER JOIN users u ON p.id_usuario = u.id
                WHERE p.estado = 'activo'
                ORDER BY p.fecha_devolucion ASC";
        
        $result = $this->conexion->query($sql);
        
        $loans = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $loans[] = $row;
            }
        }
        return $loans;
    }
    
    /**
     * Obtener préstamos vencidos
     */
    public function overdue() {
        $sql = "SELECT 
                    p.*,
                    l.titulo as libro_titulo,
                    l.autor as libro_autor,
                    u.username as usuario_nombre
                FROM prestamos p
                INNER JOIN libros l ON p.id_libro = l.id
                INNER JOIN users u ON p.id_usuario = u.id
                WHERE p.estado = 'activo' 
                AND p.fecha_devolucion < CURDATE()
                ORDER BY p.fecha_devolucion ASC";
        
        $result = $this->conexion->query($sql);
        
        $loans = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $loans[] = $row;
            }
        }
        return $loans;
    }
    
    /**
     * Crear nuevo préstamo
     */
    public function create($bookId, $userId, $returnDate) {
        // Verificar que el libro esté disponible
        $checkSql = "SELECT ejemplares FROM libros WHERE id = ?";
        $checkStmt = $this->conexion->prepare($checkSql);
        $checkStmt->bind_param("i", $bookId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $book = $result->fetch_assoc();
        $checkStmt->close();
        
        if (!$book || $book['ejemplares'] <= 0) {
            return ['success' => false, 'message' => 'El libro no está disponible'];
        }
        
        // Verificar préstamos activos del usuario
        $activeLoansSql = "SELECT COUNT(*) as total FROM prestamos WHERE id_usuario = ? AND estado = 'activo'";
        $activeStmt = $this->conexion->prepare($activeLoansSql);
        $activeStmt->bind_param("i", $userId);
        $activeStmt->execute();
        $activeResult = $activeStmt->get_result();
        $activeRow = $activeResult->fetch_assoc();
        $activeStmt->close();
        
        $maxLoans = defined('MAX_ACTIVE_LOANS') ? MAX_ACTIVE_LOANS : 3;
        if ($activeRow['total'] >= $maxLoans) {
            return ['success' => false, 'message' => 'Has alcanzado el límite de préstamos activos'];
        }
        
        // Iniciar transacción
        $this->conexion->begin_transaction();
        
        try {
            // Insertar préstamo
            $loanDate = date('Y-m-d');
            $sql = "INSERT INTO prestamos (id_libro, id_usuario, fecha_prestamo, fecha_devolucion, estado) 
                    VALUES (?, ?, ?, ?, 'activo')";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("iiss", $bookId, $userId, $loanDate, $returnDate);
            $stmt->execute();
            $loanId = $this->conexion->insert_id;
            $stmt->close();
            
            // Decrementar ejemplares
            $updateSql = "UPDATE libros SET ejemplares = ejemplares - 1 WHERE id = ?";
            $updateStmt = $this->conexion->prepare($updateSql);
            $updateStmt->bind_param("i", $bookId);
            $updateStmt->execute();
            $updateStmt->close();
            
            $this->conexion->commit();
            
            return ['success' => true, 'loan_id' => $loanId];
            
        } catch (Exception $e) {
            $this->conexion->rollback();
            return ['success' => false, 'message' => 'Error al crear préstamo: ' . $e->getMessage()];
        }
    }
    
    /**
     * Registrar devolución
     */
    public function returnBook($loanId) {
        // Obtener información del préstamo
        $loan = $this->findById($loanId);
        
        if (!$loan) {
            return ['success' => false, 'message' => 'Préstamo no encontrado'];
        }
        
        if ($loan['estado'] !== 'activo') {
            return ['success' => false, 'message' => 'Este préstamo ya fue devuelto'];
        }
        
        // Iniciar transacción
        $this->conexion->begin_transaction();
        
        try {
            // Actualizar préstamo
            $returnDate = date('Y-m-d');
            $sql = "UPDATE prestamos SET estado = 'devuelto', fecha_devuelto = ? WHERE id = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("si", $returnDate, $loanId);
            $stmt->execute();
            $stmt->close();
            
            // Incrementar ejemplares
            $updateSql = "UPDATE libros SET ejemplares = ejemplares + 1 WHERE id = ?";
            $updateStmt = $this->conexion->prepare($updateSql);
            $updateStmt->bind_param("i", $loan['id_libro']);
            $updateStmt->execute();
            $updateStmt->close();
            
            $this->conexion->commit();
            
            return ['success' => true, 'message' => 'Devolución registrada exitosamente'];
            
        } catch (Exception $e) {
            $this->conexion->rollback();
            return ['success' => false, 'message' => 'Error al registrar devolución: ' . $e->getMessage()];
        }
    }
    
    /**
     * Renovar préstamo
     */
    public function renew($loanId, $newReturnDate) {
        $loan = $this->findById($loanId);
        
        if (!$loan) {
            return ['success' => false, 'message' => 'Préstamo no encontrado'];
        }
        
        if ($loan['estado'] !== 'activo') {
            return ['success' => false, 'message' => 'Solo se pueden renovar préstamos activos'];
        }
        
        $sql = "UPDATE prestamos SET fecha_devolucion = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("si", $newReturnDate, $loanId);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Préstamo renovado exitosamente'];
        }
        
        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Error al renovar: ' . $error];
    }
    
    /**
     * Contar préstamos activos
     */
    public function countActive() {
        $sql = "SELECT COUNT(*) as total FROM prestamos WHERE estado = 'activo'";
        $result = $this->conexion->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    /**
     * Contar préstamos vencidos
     */
    public function countOverdue() {
        $sql = "SELECT COUNT(*) as total FROM prestamos 
                WHERE estado = 'activo' AND fecha_devolucion < CURDATE()";
        $result = $this->conexion->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    /**
     * Historial de préstamos de un usuario
     */
    public function history($userId, $limit = null) {
        $sql = "SELECT 
                    p.*,
                    l.titulo as libro_titulo,
                    l.autor as libro_autor
                FROM prestamos p
                INNER JOIN libros l ON p.id_libro = l.id
                WHERE p.id_usuario = ?
                ORDER BY p.fecha_prestamo DESC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("ii", $userId, $limit);
        } else {
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $userId);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $loans = [];
        while ($row = $result->fetch_assoc()) {
            $loans[] = $row;
        }
        $stmt->close();
        return $loans;
    }
}