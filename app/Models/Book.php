<?php
/**
 * Modelo de Libro
 * Maneja todas las operaciones relacionadas con libros
 */

class Book {
    private $conexion;
    
    public function __construct($db) {
        $this->conexion = $db;
    }
    
    /**
     * Obtener todos los libros
     */
    public function all() {
        $sql = "SELECT l.*, c.nombre as categoria_nombre 
                FROM libros l 
                LEFT JOIN categorias c ON l.id_categoria = c.id 
                ORDER BY l.id DESC";
        $result = $this->conexion->query($sql);
        
        $books = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }
        }
        return $books;
    }
    
    /**
     * Buscar libro por ID
     */
    public function findById($id) {
        $sql = "SELECT l.*, c.nombre as categoria_nombre 
                FROM libros l 
                LEFT JOIN categorias c ON l.id_categoria = c.id 
                WHERE l.id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();
        $stmt->close();
        return $book;
    }
    
    /**
     * Buscar libros por título (búsqueda parcial)
     */
    public function searchByTitle($title) {
        $search = "%{$title}%";
        $sql = "SELECT l.*, c.nombre as categoria_nombre 
                FROM libros l 
                LEFT JOIN categorias c ON l.id_categoria = c.id 
                WHERE l.titulo LIKE ? 
                ORDER BY l.titulo ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $search);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        $stmt->close();
        return $books;
    }
    
    /**
     * Buscar libros por autor
     */
    public function searchByAuthor($author) {
        $search = "%{$author}%";
        $sql = "SELECT l.*, c.nombre as categoria_nombre 
                FROM libros l 
                LEFT JOIN categorias c ON l.id_categoria = c.id 
                WHERE l.autor LIKE ? 
                ORDER BY l.titulo ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $search);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        $stmt->close();
        return $books;
    }
    
    /**
     * Obtener libros por categoría
     */
    public function byCategory($categoryId) {
        $sql = "SELECT l.*, c.nombre as categoria_nombre 
                FROM libros l 
                LEFT JOIN categorias c ON l.id_categoria = c.id 
                WHERE l.id_categoria = ? 
                ORDER BY l.titulo ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        $stmt->close();
        return $books;
    }
    
    /**
     * Crear nuevo libro
     */
    public function create($data) {
        $sql = "INSERT INTO libros (titulo, autor, editorial, ano_publicacion, isbn, ejemplares, ubicacion, id_categoria) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param(
            "sssisisi",
            $data['titulo'],
            $data['autor'],
            $data['editorial'],
            $data['ano_publicacion'],
            $data['isbn'],
            $data['ejemplares'],
            $data['ubicacion'],
            $data['id_categoria']
        );
        
        if ($stmt->execute()) {
            $bookId = $this->conexion->insert_id;
            $stmt->close();
            return ['success' => true, 'book_id' => $bookId];
        }
        
        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Error al crear libro: ' . $error];
    }
    
    /**
     * Actualizar libro
     */
    public function update($id, $data) {
        $sql = "UPDATE libros SET 
                titulo = ?, 
                autor = ?, 
                editorial = ?, 
                ano_publicacion = ?, 
                isbn = ?, 
                ejemplares = ?, 
                ubicacion = ?, 
                id_categoria = ? 
                WHERE id = ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param(
            "sssisisii",
            $data['titulo'],
            $data['autor'],
            $data['editorial'],
            $data['ano_publicacion'],
            $data['isbn'],
            $data['ejemplares'],
            $data['ubicacion'],
            $data['id_categoria'],
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
     * Eliminar libro
     */
    public function delete($id) {
        // Verificar si el libro tiene préstamos activos
        $checkSql = "SELECT COUNT(*) as total FROM prestamos WHERE id_libro = ? AND estado = 'activo'";
        $checkStmt = $this->conexion->prepare($checkSql);
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $row = $result->fetch_assoc();
        $checkStmt->close();
        
        if ($row['total'] > 0) {
            return ['success' => false, 'message' => 'No se puede eliminar. El libro tiene préstamos activos.'];
        }
        
        $sql = "DELETE FROM libros WHERE id = ?";
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
     * Obtener libros disponibles (con ejemplares > 0)
     */
    public function available() {
        $sql = "SELECT l.*, c.nombre as categoria_nombre 
                FROM libros l 
                LEFT JOIN categorias c ON l.id_categoria = c.id 
                WHERE l.ejemplares > 0 
                ORDER BY l.titulo ASC";
        $result = $this->conexion->query($sql);
        
        $books = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }
        }
        return $books;
    }
    
    /**
     * Decrementar ejemplares disponibles
     */
    public function decrementCopies($id) {
        $sql = "UPDATE libros SET ejemplares = ejemplares - 1 WHERE id = ? AND ejemplares > 0";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected > 0;
    }
    
    /**
     * Incrementar ejemplares disponibles
     */
    public function incrementCopies($id) {
        $sql = "UPDATE libros SET ejemplares = ejemplares + 1 WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    /**
     * Contar total de libros
     */
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM libros";
        $result = $this->conexion->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    /**
     * Contar ejemplares totales
     */
    public function countCopies() {
        $sql = "SELECT SUM(ejemplares) as total FROM libros";
        $result = $this->conexion->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }
}