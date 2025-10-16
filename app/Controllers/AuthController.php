<?php
/**
 * Controlador de Autenticación
 * Maneja login, registro y logout
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';

class AuthController {
    private $userModel;
    
    public function __construct($db) {
        $this->userModel = new User($db);
        AuthMiddleware::startSession();
    }
    
    /**
     * Procesar login
     */
    public function login($username, $password) {
        // Validar inputs
        if (empty($username) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Usuario y contraseña son requeridos'
            ];
        }
        
        // Buscar usuario
        $user = $this->userModel->findByUsername($username);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ];
        }
        
        // Verificar contraseña
        if (!password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ];
        }
        
        // Establecer sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = strtolower($user['role']);
        $_SESSION['login_time'] = time();
        
        // Determinar redirect según rol
        $redirectUrl = $this->getRedirectByRole($user['role']);
        
        return [
            'success' => true,
            'message' => 'Login exitoso',
            'redirect' => $redirectUrl,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ]
        ];
    }
    
    /**
     * Procesar registro
     */
    public function register($username, $password, $passwordConfirm) {
        // Validaciones
        if (empty($username) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Todos los campos son requeridos'
            ];
        }
        
        if (strlen($username) < 3) {
            return [
                'success' => false,
                'message' => 'El usuario debe tener al menos 3 caracteres'
            ];
        }
        
        if (strlen($password) < 6) {
            return [
                'success' => false,
                'message' => 'La contraseña debe tener al menos 6 caracteres'
            ];
        }
        
        if ($password !== $passwordConfirm) {
            return [
                'success' => false,
                'message' => 'Las contraseñas no coinciden'
            ];
        }
        
        // Verificar si el usuario ya existe
        if ($this->userModel->findByUsername($username)) {
            return [
                'success' => false,
                'message' => 'El usuario ya existe'
            ];
        }
        
        // Crear usuario (por defecto es estudiante)
        $result = $this->userModel->create($username, $password, 'estudiante');
        
        if ($result['success']) {
            return [
                'success' => true,
                'message' => '¡Registro exitoso! Ya puedes iniciar sesión',
                'redirect' => '../public/login.php'
            ];
        }
        
        return $result;
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        AuthMiddleware::logout();
        return [
            'success' => true,
            'message' => 'Sesión cerrada exitosamente',
            'redirect' => '../public/index.php'
        ];
    }
    
    /**
     * Verificar estado de sesión
     */
    public function checkSession() {
        return [
            'authenticated' => AuthMiddleware::check(),
            'user' => [
                'id' => AuthMiddleware::id(),
                'username' => AuthMiddleware::username(),
                'role' => AuthMiddleware::role()
            ]
        ];
    }
    
    /**
     * Obtener URL de redirección según el rol
     */
    private function getRedirectByRole($role) {
        $role = strtolower($role);
        
        // Verificar si hay una URL prevista
        if (isset($_SESSION['intended_url'])) {
            $intended = $_SESSION['intended_url'];
            unset($_SESSION['intended_url']);
            return $intended;
        }
        
        // Redirección por defecto según rol
        switch ($role) {
            case 'administrador':
                return '../admin/dashboard.php';
            case 'estudiante':
                return '../student/dashboard.php';
            default:
                return '../public/index.php';
        }
    }
    
    /**
     * Cambiar contraseña
     */
    public function changePassword($userId, $oldPassword, $newPassword, $newPasswordConfirm) {
        // Validaciones
        if (empty($oldPassword) || empty($newPassword)) {
            return [
                'success' => false,
                'message' => 'Todos los campos son requeridos'
            ];
        }
        
        if (strlen($newPassword) < 6) {
            return [
                'success' => false,
                'message' => 'La nueva contraseña debe tener al menos 6 caracteres'
            ];
        }
        
        if ($newPassword !== $newPasswordConfirm) {
            return [
                'success' => false,
                'message' => 'Las contraseñas no coinciden'
            ];
        }
        
        // Obtener usuario
        $user = $this->userModel->findById($userId);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado'
            ];
        }
        
        // Verificar contraseña actual
        $fullUser = $this->userModel->findByUsername($user['username']);
        if (!password_verify($oldPassword, $fullUser['password'])) {
            return [
                'success' => false,
                'message' => 'La contraseña actual es incorrecta'
            ];
        }
        
        // Actualizar contraseña
        return $this->userModel->update($userId, ['password' => $newPassword]);
    }
}