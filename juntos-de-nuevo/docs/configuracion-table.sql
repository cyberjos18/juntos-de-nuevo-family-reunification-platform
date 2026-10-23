-- ============================================================
-- Reencuentro - Tabla `configuracion`
-- Configuración dinámica del sistema (editable solo por Admin)
-- ============================================================
-- Esta tabla no estaba capturada en database-script.sql ni en
-- laravel-aux-tables.sql; se documenta aparte porque no es una
-- tabla auxiliar de Laravel, sino de dominio de la aplicación.
--
-- IMPORTANTE: los valores de abajo son valores por defecto SEGUROS
-- (claves de API vacías). Nunca commitear aquí credenciales reales
-- de Mailjet/Brevo; esas se cargan en producción desde el panel
-- /admin/configuracion/mailjet o directamente en la base de datos.
-- ============================================================

-- Nota: este script NO fija ni crea una base de datos (sin USE).
-- Se ejecuta contra la base ya seleccionada en la conexión.

CREATE TABLE IF NOT EXISTS `configuracion` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `clave` VARCHAR(100) NOT NULL COMMENT 'Clave única de configuración',
    `valor` TEXT NULL COMMENT 'Valor de la configuración',
    `grupo` VARCHAR(50) NOT NULL DEFAULT 'general' COMMENT 'Grupo de configuración: mailjet, general, etc.',
    `descripcion` VARCHAR(500) NULL COMMENT 'Descripción de para qué sirve esta config',
    `es_secreto` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Si es dato sensible (API keys)',
    `creado_por` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `clave` (`clave`),
    KEY `idx_configuracion_grupo` (`grupo`),
    KEY `idx_configuracion_clave` (`clave`),
    CONSTRAINT `fk_configuracion_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Configuración dinámica del sistema (solo admin)';

-- Valores por defecto (sin secretos)
INSERT INTO `configuracion` (`clave`, `valor`, `grupo`, `descripcion`, `es_secreto`) VALUES
('MAIL_MAILER', 'log', 'mailjet', 'Driver de correo: smtp, mailjet, brevo, log', 0),
('MAILJET_API_KEY', '', 'mailjet', 'API Key pública de Mailjet', 1),
('MAILJET_SECRET_KEY', '', 'mailjet', 'API Key secreta de Mailjet', 1),
('MAIL_FROM_ADDRESS', 'noreply@juntosdenuevo.org', 'mailjet', 'Email remitente de los correos', 0),
('MAIL_FROM_NAME', 'JuntosDeNuevo', 'mailjet', 'Nombre del remitente', 0),
('MAIL_HOST', 'in-v3.mailjet.com', 'mailjet', 'Servidor SMTP de Mailjet', 0),
('MAIL_PORT', '587', 'mailjet', 'Puerto SMTP de Mailjet', 0),
('BREVO_API_KEY', '', 'brevo', 'API Key v3 de Brevo para envío de correos', 1),
('HABILITAR_REGISTRO_CENTRO', '1', 'general', 'Habilitar registro de nuevos centros', 0)
ON DUPLICATE KEY UPDATE `clave` = `clave`;
