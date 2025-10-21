<?php
/**
 * Modelo de Evento
 * Maneja todas las operaciones relacionadas con eventos de la biblioteca
 */

class Event {
    private $conexion;
    
    public function __construct($db) {
        $this->conexion = $db;
    }
    
    /**
     * Obtener todos los eventos
     */
    public function all($estado = null) {
        if ($estado) {
            $sql = "SELECT e.*, u.username as admin_nombre,
                    (e.cupo_maximo - e.inscritos) as cupos_disponibles
                    FROM eventos e
                    INNER JOIN users u ON e.id_admin_creador = u.id
                    WHERE e.estado = ?
                    ORDER BY e.fecha_evento DESC, e.hora_inicio ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("s", $estado);
        } else {
            $sql = "SELECT e.*, u.username as admin_nombre,
                    (e.cupo_maximo - e.inscritos) as cupos_disponibles
                    FROM eventos e
                    INNER JOIN users u ON e.id_admin_creador = u.id
                    ORDER BY e.fecha_evento DESC, e.hora_inicio ASC";
            $stmt = $this->conexion->prepare($sql);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
        $stmt->close();
        return $events;
    }
    
    /**
     * Obtener eventos próximos (activos y futuros)
     */
    public function upcoming() {
        $sql = "SELECT e.*, u.username as admin_nombre,
                (e.cupo_maximo - e.inscritos) as cupos_disponibles
                FROM eventos e
                INNER JOIN users u ON e.id_admin_creador = u.id
                WHERE e.estado = 'activo' AND e.fecha_evento >= CURDATE()
                ORDER BY e.fecha_evento ASC, e.hora_inicio ASC";
        
        $result = $this->conexion->query($sql);
        
        $events = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $events[] = $row;
            }
        }
        return $events;
    }
    
    /**
     * Buscar evento por ID
     */
    public function findById($id) {
        $sql = "SELECT e.*, u.username as admin_nombre,
                (e.cupo_maximo - e.inscritos) as cupos_disponibles
                FROM eventos e
                INNER JOIN users u ON e.id_admin_creador = u.id
                WHERE e.id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $event = $result->fetch_assoc();
        $stmt->close();
        return $event;
    }
    
    /**
     * Crear nuevo evento
     */
    public function create($data) {
        $sql = "INSERT INTO eventos (titulo, descripcion, fecha_evento, hora_inicio, hora_fin, ubicacion, cupo_maximo, id_admin_creador) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param(
            "ssssssii",
            $data['titulo'],
            $data['descripcion'],
            $data['fecha_evento'],
            $data['hora_inicio'],
            $data['hora_fin'],
            $data['ubicacion'],
            $data['cupo_maximo'],
            $data['id_admin_creador']
        );
        
        if ($stmt->execute()) {
            $eventId = $this->conexion->insert_id;
            $stmt->close();
            return ['success' => true, 'event_id' => $eventId];
        }
        
        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Error al crear evento: ' . $error];
    }
    
    /**
     * Actualizar evento
     */
    public function update($id, $data) {
        $sql = "UPDATE eventos SET 
                titulo = ?, 
                descripcion = ?, 
                fecha_evento = ?, 
                hora_inicio = ?, 
                hora_fin = ?, 
                ubicacion = ?, 
                cupo_maximo = ?,
                estado = ?
                WHERE id = ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param(
            "ssssssssi",
            $data['titulo'],
            $data['descripcion'],
            $data['fecha_evento'],
            $data['hora_inicio'],
            $data['hora_fin'],
            $data['ubicacion'],
            $data['cupo_maximo'],
            $data['estado'],
            $id
        );
        
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true];
        }
        
        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Error al actualizar: ' . $error];
    }
    
    /**
     * Eliminar evento
     */
    public function delete($id) {
        $sql = "DELETE FROM eventos WHERE id = ?";
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
     * Inscribir usuario a evento
     */
    public function inscribir($eventoId, $userId) {
        // Verificar cupo disponible
        $evento = $this->findById($eventoId);
        
        if (!$evento) {
            return ['success' => false, 'message' => 'Evento no encontrado'];
        }
        
        if ($evento['estado'] !== 'activo') {
            return ['success' => false, 'message' => 'El evento no está activo'];
        }
        
        if ($evento['cupo_maximo'] && $evento['inscritos'] >= $evento['cupo_maximo']) {
            return ['success' => false, 'message' => 'No hay cupos disponibles'];
        }
        
        // Verificar si ya está inscrito
        $checkSql = "SELECT id FROM inscripciones_eventos WHERE id_evento = ? AND id_usuario = ?";
        $checkStmt = $this->conexion->prepare($checkSql);
        $checkStmt->bind_param("ii", $eventoId, $userId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            $checkStmt->close();
            return ['success' => false, 'message' => 'Ya estás inscrito en este evento'];
        }
        $checkStmt->close();
        
        // Iniciar transacción
        $this->conexion->begin_transaction();
        
        try {
            // Insertar inscripción
            $sql = "INSERT INTO inscripciones_eventos (id_evento, id_usuario) VALUES (?, ?)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("ii", $eventoId, $userId);
            $stmt->execute();
            $stmt->close();
            
            // Actualizar contador
            $updateSql = "UPDATE eventos SET inscritos = inscritos + 1 WHERE id = ?";
            $updateStmt = $this->conexion->prepare($updateSql);
            $updateStmt->bind_param("i", $eventoId);
            $updateStmt->execute();
            $updateStmt->close();
            
            $this->conexion->commit();
            return ['success' => true, 'message' => '¡Inscripción exitosa!'];
            
        } catch (Exception $e) {
            $this->conexion->rollback();
            return ['success' => false, 'message' => 'Error al inscribir: ' . $e->getMessage()];
        }
    }
    
    /**
     * Cancelar inscripción
     */
    public function cancelarInscripcion($eventoId, $userId) {
        $this->conexion->begin_transaction();
        
        try {
            // Eliminar inscripción
            $sql = "DELETE FROM inscripciones_eventos WHERE id_evento = ? AND id_usuario = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("ii", $eventoId, $userId);
            $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();
            
            if ($affected === 0) {
                $this->conexion->rollback();
                return ['success' => false, 'message' => 'No estás inscrito en este evento'];
            }
            
            // Actualizar contador
            $updateSql = "UPDATE eventos SET inscritos = inscritos - 1 WHERE id = ?";
            $updateStmt = $this->conexion->prepare($updateSql);
            $updateStmt->bind_param("i", $eventoId);
            $updateStmt->execute();
            $updateStmt->close();
            
            $this->conexion->commit();
            return ['success' => true, 'message' => 'Inscripción cancelada'];
            
        } catch (Exception $e) {
            $this->conexion->rollback();
            return ['success' => false, 'message' => 'Error al cancelar: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener inscripciones de un usuario
     */
    public function misInscripciones($userId) {
        $sql = "SELECT e.*, i.fecha_inscripcion, i.asistio,
                (e.cupo_maximo - e.inscritos) as cupos_disponibles
                FROM inscripciones_eventos i
                INNER JOIN eventos e ON i.id_evento = e.id
                WHERE i.id_usuario = ?
                ORDER BY e.fecha_evento DESC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $inscripciones = [];
        while ($row = $result->fetch_assoc()) {
            $inscripciones[] = $row;
        }
        $stmt->close();
        return $inscripciones;
    }
    
    /**
     * Verificar si un usuario está inscrito
     */
    public function estaInscrito($eventoId, $userId) {
        $sql = "SELECT id FROM inscripciones_eventos WHERE id_evento = ? AND id_usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $eventoId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $inscrito = $result->num_rows > 0;
        $stmt->close();
        return $inscrito;
    }
    
    /**
     * Contar eventos totales
     */
    public function count($estado = null) {
        if ($estado) {
            $sql = "SELECT COUNT(*) as total FROM eventos WHERE estado = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("s", $estado);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['total'];
        }
        
        $sql = "SELECT COUNT(*) as total FROM eventos";
        $result = $this->conexion->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}