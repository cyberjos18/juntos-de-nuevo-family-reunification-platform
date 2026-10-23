-- ============================================================================
-- Reset de datos — Reencuentro (findchild)
-- ============================================================================
-- Borra TODOS los datos de negocio (centros, usuarios gestores, niños,
-- familiares, recogidas, sesiones y tokens de API) y deja intactas:
--   - La tabla `configuracion` (settings de mail/Brevo/etc.)
--   - El usuario administrador: joseangarita8@gmail.com
--
-- Uso local (WAMP, MariaDB puerto 3307):
--   "/c/wamp64/bin/mariadb/mariadb11.5.2/bin/mysql.exe" -h 127.0.0.1 -P 3307 -u root findchild < reset-datos-mantener-admin.sql
--
-- Uso en Hostinger (vía SSH, dentro de juntos-de-nuevo/):
--   mysql -u <usuario> -p <basededatos> < docs/reset-datos-mantener-admin.sql
--
-- ⚠️ IRREVERSIBLE. Haz un backup antes si hay datos que te importa conservar:
--   mysqldump -h 127.0.0.1 -P 3307 -u root findchild > backup_antes_de_reset.sql
-- ============================================================================

START TRANSACTION;

-- 1. Recogidas (depende de ninos y users)
DELETE FROM recogidas;

-- 2. Familiares de niños (depende de ninos; también cae en cascada, pero se
--    borra explícito para que el script sea autoexplicativo)
DELETE FROM ninos_familiares;

-- 3. Niños (depende de centros y users)
DELETE FROM ninos;

-- 4. Todos los usuarios excepto el administrador
DELETE FROM users WHERE email <> 'joseangarita8@gmail.com';

-- 5. Centros (ya sin usuarios ni niños que los referencien)
DELETE FROM centros;

-- 6. Sesiones activas (invalida logins de usuarios ya borrados) y tokens API
DELETE FROM sessions;
DELETE FROM personal_access_tokens;

-- 7. Tablas operativas de Laravel (cache/colas) — limpieza de cortesía
DELETE FROM cache;
DELETE FROM cache_locks;
DELETE FROM jobs;
DELETE FROM failed_jobs;
DELETE FROM job_batches;

COMMIT;

-- Reinicia los autoincrementales de las tablas vaciadas por completo, para
-- que el próximo centro/niño registrado empiece desde id=1. No se toca
-- `users` ni `configuracion` porque conservan filas.
ALTER TABLE centros AUTO_INCREMENT = 1;
ALTER TABLE ninos AUTO_INCREMENT = 1;
ALTER TABLE ninos_familiares AUTO_INCREMENT = 1;
ALTER TABLE recogidas AUTO_INCREMENT = 1;

-- ============================================================================
-- Verificación
-- ============================================================================
SELECT 'users'          AS tabla, COUNT(*) AS filas FROM users
UNION ALL SELECT 'configuracion',      COUNT(*) FROM configuracion
UNION ALL SELECT 'centros',            COUNT(*) FROM centros
UNION ALL SELECT 'ninos',              COUNT(*) FROM ninos
UNION ALL SELECT 'ninos_familiares',   COUNT(*) FROM ninos_familiares
UNION ALL SELECT 'recogidas',          COUNT(*) FROM recogidas
UNION ALL SELECT 'sessions',           COUNT(*) FROM sessions
UNION ALL SELECT 'personal_access_tokens', COUNT(*) FROM personal_access_tokens;

SELECT id, nombre, email, rol, activo FROM users;
