-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-10-2025 a las 05:16:38
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bookary`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Ficción', 'Novelas y cuentos de ficción'),
(2, 'No Ficción', 'Libros informativos y educativos'),
(3, 'Ciencia', 'Libros científicos y técnicos'),
(4, 'Historia', 'Libros de historia y biografías'),
(5, 'Literatura Clásica', 'Clásicos de la literatura mundial'),
(6, 'Autoayuda', 'Desarrollo personal y motivación'),
(7, 'Tecnología', 'Informática y nuevas tecnologías'),
(8, 'Arte', 'Libros sobre arte y cultura');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_evento` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time DEFAULT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `cupo_maximo` int(11) DEFAULT NULL,
  `inscritos` int(11) DEFAULT 0,
  `imagen_url` varchar(255) DEFAULT NULL,
  `estado` enum('activo','cancelado','finalizado') DEFAULT 'activo',
  `id_admin_creador` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `titulo`, `descripcion`, `fecha_evento`, `hora_inicio`, `hora_fin`, `ubicacion`, `cupo_maximo`, `inscritos`, `imagen_url`, `estado`, `id_admin_creador`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(16, 'Simposio investigacion CINEM', 'espacio ponencia para la institucion', '2025-10-30', '07:00:00', '17:00:00', 'Auditorio biblioteca', NULL, 0, NULL, 'activo', 2, '2025-10-21 00:19:10', '2025-10-21 00:19:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones_eventos`
--

CREATE TABLE `inscripciones_eventos` (
  `id` int(11) NOT NULL,
  `id_evento` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_inscripcion` timestamp NOT NULL DEFAULT current_timestamp(),
  `asistio` tinyint(1) DEFAULT 0,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

CREATE TABLE `libros` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `autor` varchar(255) NOT NULL,
  `editorial` varchar(255) DEFAULT NULL,
  `ano_publicacion` int(11) DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `ejemplares` int(11) DEFAULT 1,
  `ubicacion` varchar(100) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`id`, `titulo`, `autor`, `editorial`, `ano_publicacion`, `isbn`, `ejemplares`, `ubicacion`, `id_categoria`, `created_at`, `updated_at`) VALUES
(1, 'Cien Años de Soledad', 'Gabriel García Márquez', 'Sudamericana', 1967, '978-0307474728', 1, 'Estante A-1', 5, '2025-10-06 23:44:30', '2025-10-19 04:51:16'),
(2, 'El Principito', 'Antoine de Saint-Exupéry', 'Reynal & Hitchcock', 1943, '978-0156012195', 5, 'Estante A-2', 1, '2025-10-06 23:44:30', '2025-10-06 23:44:30'),
(3, 'Rayuela', 'Julio Cortázar', 'Sudamericana', 1963, '978-8437604572', 2, 'Estante A-3', 5, '2025-10-06 23:44:30', '2025-10-06 23:44:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

CREATE TABLE `prestamos` (
  `id` int(11) NOT NULL,
  `id_libro` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_prestamo` date NOT NULL,
  `fecha_devolucion` date NOT NULL,
  `fecha_devuelto` date DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `prestamos`
--

INSERT INTO `prestamos` (`id`, `id_libro`, `id_usuario`, `fecha_prestamo`, `fecha_devolucion`, `fecha_devuelto`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-10-18', '2025-11-02', NULL, 'activo', '2025-10-19 00:47:28', '2025-10-19 00:47:28'),
(2, 1, 1, '2025-10-18', '2025-11-02', NULL, 'activo', '2025-10-19 04:51:16', '2025-10-19 04:51:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_libro` int(11) NOT NULL,
  `fecha_reserva` date NOT NULL,
  `estado` varchar(20) DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_prestamo`
--

CREATE TABLE `solicitudes_prestamo` (
  `id` int(11) NOT NULL,
  `id_libro` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_solicitud` datetime DEFAULT current_timestamp(),
  `estado` enum('pendiente','aprobada','rechazada') DEFAULT 'pendiente',
  `notas_usuario` text DEFAULT NULL,
  `notas_admin` text DEFAULT NULL,
  `fecha_respuesta` datetime DEFAULT NULL,
  `id_admin_respuesta` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `solicitudes_prestamo`
--

INSERT INTO `solicitudes_prestamo` (`id`, `id_libro`, `id_usuario`, `fecha_solicitud`, `estado`, `notas_usuario`, `notas_admin`, `fecha_respuesta`, `id_admin_respuesta`) VALUES
(1, 1, 1, '2025-10-18 23:05:03', 'aprobada', NULL, 'fea', '2025-10-18 23:51:16', 2),
(2, 2, 1, '2025-10-18 23:14:01', 'rechazada', 'hola amiquo', 'por enana', '2025-10-18 23:58:44', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'estudiante',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'sara', '$2y$10$WC6SINld9LjwSPLV/h/yZ.pcbXUAITtAW7S4LWcushd6Gl7tuim5u', 'estudiante', '2025-10-06 23:44:30', '2025-10-07 00:33:56'),
(2, 'Angel', '$2y$10$YNj96JXgGhsvG3yuxuMYze58FI1d8Lpf1kmJjS7PGe4oajqW.8LWK', 'administrador', '2025-10-06 23:44:30', '2025-10-07 00:33:56');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_admin_creador` (`id_admin_creador`),
  ADD KEY `idx_fecha_evento` (`fecha_evento`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `inscripciones_eventos`
--
ALTER TABLE `inscripciones_eventos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_inscripcion` (`id_evento`,`id_usuario`),
  ADD KEY `idx_usuario` (`id_usuario`),
  ADD KEY `idx_evento` (`id_evento`);

--
-- Indices de la tabla `libros`
--
ALTER TABLE `libros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `idx_libros_id_categoria` (`id_categoria`),
  ADD KEY `idx_libros_titulo` (`titulo`),
  ADD KEY `idx_libros_autor` (`autor`);

--
-- Indices de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_prestamos_id_libro` (`id_libro`),
  ADD KEY `idx_prestamos_id_usuario` (`id_usuario`),
  ADD KEY `idx_prestamos_estado` (`estado`),
  ADD KEY `idx_prestamos_fecha_devolucion` (`fecha_devolucion`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reservas_id_libro` (`id_libro`),
  ADD KEY `idx_reservas_id_usuario` (`id_usuario`),
  ADD KEY `idx_reservas_estado` (`estado`);

--
-- Indices de la tabla `solicitudes_prestamo`
--
ALTER TABLE `solicitudes_prestamo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_libro` (`id_libro`),
  ADD KEY `id_admin_respuesta` (`id_admin_respuesta`),
  ADD KEY `idx_usuario` (`id_usuario`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha` (`fecha_solicitud`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_users_username` (`username`),
  ADD KEY `idx_users_role` (`role`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `inscripciones_eventos`
--
ALTER TABLE `inscripciones_eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `libros`
--
ALTER TABLE `libros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `solicitudes_prestamo`
--
ALTER TABLE `solicitudes_prestamo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`id_admin_creador`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `inscripciones_eventos`
--
ALTER TABLE `inscripciones_eventos`
  ADD CONSTRAINT `inscripciones_eventos_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscripciones_eventos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `libros`
--
ALTER TABLE `libros`
  ADD CONSTRAINT `fk_libros_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD CONSTRAINT `fk_prestamos_libro` FOREIGN KEY (`id_libro`) REFERENCES `libros` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_prestamos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `fk_reservas_libro` FOREIGN KEY (`id_libro`) REFERENCES `libros` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reservas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitudes_prestamo`
--
ALTER TABLE `solicitudes_prestamo`
  ADD CONSTRAINT `solicitudes_prestamo_ibfk_1` FOREIGN KEY (`id_libro`) REFERENCES `libros` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `solicitudes_prestamo_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `solicitudes_prestamo_ibfk_3` FOREIGN KEY (`id_admin_respuesta`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
