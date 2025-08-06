-- Script para crear la base de datos de la API de usuarios

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Usuario de prueba (contraseña ya hasheada)
INSERT INTO `users` (`name`, `email`, `password_hash`, `role`, `created_at`) VALUES
('Jesus', 'jesus@example.com', '$2y$10$tvPPhajl8pbtstc/LkQjz.D00zM/5U6QICVq4nw7zk78hmSetejEO', 'admin', '2025-08-06 16:07:30');
