<?php
/**
 * Modelo de Solicitud de Préstamo
 * Maneja todas las operaciones relacionadas con solicitudes de préstamos
 */

class LoanRequest {
    private $conexion;
    
    public function __construct($db) {
        $this->conexion = $db;
    }
    
    /**
     * Crear nueva solicitud de préstamo
     */
    public function create($bookId, $userId, $notes = null) {
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
        
        // Verificar si ya existe una solicitud pendiente para este libro
        $existingSql = "SELECT id FROM solicitudes_prestamo 
                       WHERE id_libro = ? AND id_usuario = ? AND estado = 'pendiente'";
        $existingStmt = $this->conexion->prepare($existingSql);
        $existingStmt->bind_param("ii", $bookId, $userId);
        $existingStmt->execute();
        $existingResult = $existingStmt->get_result();
        $existingStmt->close();
        
        if ($existingResult->num_rows > 0) {
            return ['success' => false, 'message' => 'Ya tienes una solicitud pendiente para este libro'];
        }
        
        // Crear solicitud
        $sql = "INSERT INTO solicitudes_prestamo (id_libro, id_usuario, notas_usuario) 
                VALUES (?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iis", $bookId, $userId, $notes);
        
        if ($stmt->execute()) {
            $requestId = $this->conexion->insert_id;
            $stmt->close();
            return ['success' => true, 'request_id' => $requestId];
        }
        
        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Error al crear solicitud: ' . $error];
    }
    
    /**
     * Obtener todas las solicitudes pendientes
     */
    public function getPending() {
        $sql = "SELECT 
                    s.id,
                    s.fecha_solicitud,
                    s.notas_usuario,
                    s.estado,
                    l.titulo as libro_titulo,
                    l.autor as libro_autor,
                    l.ejemplares as libro_ejemplares,
                    u.username as usuario_nombre,
                    u.id as usuario_id
                FROM solicitudes_prestamo s
                INNER JOIN libros l ON s.id_libro = l.id
                INNER JOIN users u ON s.id_usuario = u.id
                WHERE s.estado = 'pendiente'
                ORDER BY s.fecha_solicitud ASC";
        
        $result = $this->conexion->query($sql);
        
        $requests = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $requests[] = $row;
            }
        }
        return $requests;
    }
    
    /**
     * Obtener solicitudes por usuario
     */
    public function byUser($userId, $status = null) {
        if ($status) {
            $sql = "SELECT 
                        s.*,
                        l.titulo as libro_titulo,
                        l.autor as libro_autor,
                        l.id as libro_id
                    FROM solicitudes_prestamo s
                    INNER JOIN libros l ON s.id_libro = l.id
                    WHERE s.id_usuario = ? AND s.estado = ?
                    ORDER BY s.fecha_solicitud DESC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("is", $userId, $status);
        } else {
            $sql = "SELECT 
                        s.*,
                        l.titulo as libro_titulo,
                        l.autor as libro_autor,
                        l.id as libro_id
                    FROM solicitudes_prestamo s
                    INNER JOIN libros l ON s.id_libro = l.id
                    WHERE s.id_usuario = ?
                    ORDER BY s.fecha_solicitud DESC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $userId);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
        $stmt->close();
        return $requests;
    }
    
    /**
     * Aprobar solicitud y crear préstamo
     */
    public function approve($requestId, $adminId, $adminNotes = null) {
        // Obtener información de la solicitud
        $sql = "SELECT s.*, l.ejemplares 
                FROM solicitudes_prestamo s
                INNER JOIN libros l ON s.id_libro = l.id
                WHERE s.id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $requestId);
        $stmt->execute();
        $result = $stmt->get_result();
        $request = $result->fetch_assoc();
        $stmt->close();
        
        if (!$request) {
            return ['success' => false, 'message' => 'Solicitud no encontrada'];
        }
        
        if ($request['estado'] !== 'pendiente') {
            return ['success' => false, 'message' => 'Esta solicitud ya fue procesada'];
        }
        
        if ($request['ejemplares'] <= 0) {
            return ['success' => false, 'message' => 'El libro ya no está disponible'];
        }
        
        // Iniciar transacción
        $this->conexion->begin_transaction();
        
        try {
            // Actualizar solicitud
            $updateSql = "UPDATE solicitudes_prestamo 
                         SET estado = 'aprobada', 
                             fecha_respuesta = NOW(), 
                             id_admin_respuesta = ?,
                             notas_admin = ?
                         WHERE id = ?";
            $updateStmt = $this->conexion->prepare($updateSql);
            $updateStmt->bind_param("isi", $adminId, $adminNotes, $requestId);
            $updateStmt->execute();
            $updateStmt->close();
            
            // Crear préstamo
            require_once __DIR__ . '/Loan.php';
            $loanModel = new Loan($this->conexion);
            $returnDate = date('Y-m-d', strtotime('+' . (DEFAULT_LOAN_DAYS ?? 15) . ' days'));
            $loanResult = $loanModel->create($request['id_libro'], $request['id_usuario'], $returnDate);
            
            if (!$loanResult['success']) {
                throw new Exception($loanResult['message']);
            }
            
            $this->conexion->commit();
            return ['success' => true, 'message' => 'Solicitud aprobada y préstamo creado'];
            
        } catch (Exception $e) {
            $this->conexion->rollback();
            return ['success' => false, 'message' => 'Error al aprobar: ' . $e->getMessage()];
        }
    }
    
    /**
     * Rechazar solicitud
     */
    public function reject($requestId, $adminId, $adminNotes = null) {
        $sql = "UPDATE solicitudes_prestamo 
                SET estado = 'rechazada', 
                    fecha_respuesta = NOW(), 
                    id_admin_respuesta = ?,
                    notas_admin = ?
                WHERE id = ? AND estado = 'pendiente'";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("isi", $adminId, $adminNotes, $requestId);
        
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $stmt->close();
            return ['success' => true, 'message' => 'Solicitud rechazada'];
        }
        
        $stmt->close();
        return ['success' => false, 'message' => 'No se pudo rechazar la solicitud'];
    }
    
    /**
     * Contar solicitudes pendientes
     */
    public function countPending() {
        $sql = "SELECT COUNT(*) as total FROM solicitudes_prestamo WHERE estado = 'pendiente'";
        $result = $this->conexion->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    /**
     * Cancelar solicitud (por el usuario)
     */
    public function cancel($requestId, $userId) {
        $sql = "DELETE FROM solicitudes_prestamo 
                WHERE id = ? AND id_usuario = ? AND estado = 'pendiente'";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $requestId, $userId);
        
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $stmt->close();
            return ['success' => true, 'message' => 'Solicitud cancelada'];
        }
        
        $stmt->close();
        return ['success' => false, 'message' => 'No se pudo cancelar la solicitud'];
    }
}