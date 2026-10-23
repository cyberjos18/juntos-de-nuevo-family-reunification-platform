-- ============================================================
-- Reencuentro - Script de Base de Datos
-- Plataforma de Reunificación Familiar (terremotos Venezuela)
-- ============================================================
-- Fecha: 2026-06-30 | Versión: 1.0
-- Motor: MySQL/MariaDB (InnoDB)
-- Charset: utf8mb4_unicode_ci
--
-- USUARIOS DE PRUEBA:
--   Admin:  admin@findchild.org / admin123
--   Gestor: maria.gonzalez@proteccion.org / gestor123
--   Gestor: carlos.ramirez@hospital.org / gestor123
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Nota: este script NO fija ni crea una base de datos (sin CREATE DATABASE/USE).
-- Se ejecuta contra la base ya seleccionada en la conexión, ej.:
--   mysql -u root -p nombre_de_tu_bd < docs/database-script.sql

-- Tablas
DROP TABLE IF EXISTS `recogidas`;
DROP TABLE IF EXISTS `ninos_familiares`;
DROP TABLE IF EXISTS `ninos`;
DROP TABLE IF EXISTS `personal_access_tokens`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `centros`;

CREATE TABLE `centros` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(200) NOT NULL,
    `tipo` ENUM('proteccion_civil','hospital','casa_acogida','otro') NOT NULL,
    `email` VARCHAR(200) NOT NULL UNIQUE,
    `telefono` VARCHAR(20) NULL,
    `direccion` TEXT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `centro_id` BIGINT UNSIGNED NULL,
    `nombre` VARCHAR(100) NOT NULL,
    `email` VARCHAR(200) NOT NULL UNIQUE,
    `email_verified_at` TIMESTAMP NULL,
    `password` VARCHAR(255) NOT NULL,
    `rol` ENUM('admin','gestor') NOT NULL DEFAULT 'gestor',
    `cargo` VARCHAR(100) NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 0,
    `token_activacion` VARCHAR(100) NULL,
    `foto_identificacion` VARCHAR(500) NULL,
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_users_centro` FOREIGN KEY (`centro_id`) REFERENCES `centros` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `personal_access_tokens` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tokenable_type` VARCHAR(255) NOT NULL,
    `tokenable_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `token` VARCHAR(64) NOT NULL UNIQUE,
    `abilities` TEXT NULL,
    `last_used_at` TIMESTAMP NULL,
    `expires_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_pat_tokenable` (`tokenable_type`, `tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ninos` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `centro_id` BIGINT UNSIGNED NOT NULL,
    `foto_url` VARCHAR(500) NULL,
    `nombre` VARCHAR(100) NOT NULL,
    `apellido` VARCHAR(100) NOT NULL,
    `edad` INT UNSIGNED NULL,
    `fecha_nacimiento` DATE NULL,
    `sexo` ENUM('M','F') NULL,
    `direccion_previa` TEXT NULL,
    `nombre_madre` VARCHAR(200) NULL,
    `nombre_padre` VARCHAR(200) NULL,
    `estado` ENUM('encontrado','familia_localizada','recogido','otro_centro') NOT NULL DEFAULT 'encontrado',
    `notas` TEXT NULL,
    `creado_por` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_ninos_centro` FOREIGN KEY (`centro_id`) REFERENCES `centros` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_ninos_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ninos_familiares` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nino_id` BIGINT UNSIGNED NOT NULL,
    `parentesco` VARCHAR(50) NOT NULL,
    `nombre` VARCHAR(200) NOT NULL,
    `apellido` VARCHAR(200) NULL,
    `telefono` VARCHAR(20) NULL,
    `notas` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_nf_nino` FOREIGN KEY (`nino_id`) REFERENCES `ninos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `recogidas` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nino_id` BIGINT UNSIGNED NOT NULL UNIQUE,
    `familiar_nombre` VARCHAR(200) NOT NULL,
    `familiar_apellido` VARCHAR(200) NOT NULL,
    `familiar_parentesco` VARCHAR(50) NOT NULL,
    `familiar_telefono` VARCHAR(20) NOT NULL,
    `familiar_email` VARCHAR(200) NULL,
    `familiar_documento` VARCHAR(50) NULL,
    `foto_recogida_url` VARCHAR(500) NOT NULL,
    `notas` TEXT NULL,
    `fecha_recogida` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `registrado_por` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_rec_nino` FOREIGN KEY (`nino_id`) REFERENCES `ninos` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_rec_user` FOREIGN KEY (`registrado_por`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- DATOS DE PRUEBA
INSERT INTO `centros` (`id`, `nombre`, `tipo`, `email`, `telefono`, `direccion`, `activo`) VALUES
(1, 'Protección Civil Central', 'proteccion_civil', 'pc.central@findchild.org', '+58-212-5551234', 'Av. Principal de Chacao, Caracas', 1),
(2, 'Hospital Dr. Domingo Luciani', 'hospital', 'hospital.luciani@findchild.org', '+58-212-5555678', 'Av. Francisco de Miranda, Caracas', 1);

INSERT INTO `users` (`id`, `centro_id`, `nombre`, `email`, `email_verified_at`, `password`, `rol`, `cargo`, `activo`) VALUES
(1, NULL, 'Administrador Sistema', 'admin@findchild.org', NOW(), '$2y$12$8iF1s3OTx78pbBx/mAHyFu9v9MdQIvi2VhC1y.7RaQoUNLnQ4hHvy', 'admin', 'Administrador General', 1),
(2, 1, 'María González', 'maria.gonzalez@proteccion.org', NOW(), '$2y$12$56FjEM4GQ.VSvyawqkMUceqihQ1IRE2exhVB8xNcwOPTsYHBizo6W', 'gestor', 'Coordinadora de Acogida', 1),
(3, 2, 'Dr. Carlos Ramírez', 'carlos.ramirez@hospital.org', NOW(), '$2y$12$56FjEM4GQ.VSvyawqkMUceqihQ1IRE2exhVB8xNcwOPTsYHBizo6W', 'gestor', 'Médico de Guardia', 1);

INSERT INTO `ninos` (`id`, `centro_id`, `foto_url`, `nombre`, `apellido`, `edad`, `sexo`, `direccion_previa`, `nombre_madre`, `nombre_padre`, `estado`, `creado_por`) VALUES
(1, 1, NULL, 'Carlos', 'Rodríguez', 5, 'M', 'Edificio Los Jardines', 'Ana María Rodríguez', 'Pedro José Rodríguez', 'encontrado', 2),
(2, 1, NULL, 'Sofía', 'Martínez', 3, 'F', 'Casa #45, El Paraíso', 'Laura Martínez', NULL, 'encontrado', 2),
(3, 1, NULL, 'Miguel Ángel', 'López', 8, 'M', NULL, 'Carmen López', 'Juan López', 'familia_localizada', 2),
(4, 2, NULL, 'Valentina', 'Pérez', 2, 'F', 'Torre Parque Central', 'Gabriela Pérez', 'Roberto Díaz', 'recogido', 3),
(5, 2, NULL, 'José', 'Hernández', 10, 'M', 'Barrio Petare', 'María Hernández', NULL, 'encontrado', 3),
(6, 1, NULL, 'Desconocido', 'Desconocido', 4, 'M', NULL, NULL, NULL, 'encontrado', 2);

INSERT INTO `ninos_familiares` (`id`, `nino_id`, `parentesco`, `nombre`, `telefono`) VALUES
(1, 1, 'Tía', 'Rosa', '+58-414-5559876'),
(2, 1, 'Abuela', 'Carmen', '+58-424-5551122'),
(3, 3, 'Madre', 'Carmen', '+58-412-5553344');

INSERT INTO `recogidas` (`id`, `nino_id`, `familiar_nombre`, `familiar_apellido`, `familiar_parentesco`, `familiar_telefono`, `familiar_email`, `familiar_documento`, `foto_recogida_url`, `registrado_por`) VALUES
(1, 4, 'Roberto', 'Díaz', 'Padre', '+58-416-5557788', 'roberto.diaz@email.com', 'V-15.234.567', 'recogidas/valentina_perez_recogida.jpg', 3);
