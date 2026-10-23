# Handoff para db-design-developer

## Contexto

Plataforma humanitaria **Reencuentro** para reunificación de niños con familias tras terremotos en Venezuela. Sistema crítico que debe soportar alta disponibilidad, integridad de datos y auditoría básica.

**Stack confirmado:**
- **Base de datos:** MySQL 8 (WAMP64, puerto 3306)
- **Framework:** Laravel 11 (pero primero script SQL puro)
- **Ubicación proyecto:** `C:\wamp64\www\findchild`

---

## ⚠️ Flujo de trabajo obligatorio

**IMPORTANTE: El usuario ha definido este orden de trabajo:**

### Paso 1: Script SQL puro
- Crear archivo `docs/database-script.sql` con toda la estructura
- Incluir: tablas, claves, índices, foreign keys, constraints
- Incluir: datos de prueba (INSERT statements) para testing
- Incluir: comentarios explicando decisiones

### Paso 2: Revisión
- El usuario revisa el script SQL
- Se hacen ajustes si es necesario
- Se aprueba el diseño

### Paso 3: Migraciones Laravel
- **Solo después de aprobar el script SQL**, crear migraciones Laravel
- Cada tabla = una migración
- Orden correcto de migraciones (respetando foreign keys)

---

## Objetivo del modelo de datos

- Almacenar datos de niños, centros, usuarios y recogidas
- Mantener integridad referencial estricta
- Soportar consultas públicas (listado de niños) y privadas
- Permitir auditoría de quién registró qué
- Preparado para escalabilidad futura

---

## Entidades candidatas

| Entidad | Descripción |
|---------|-------------|
| **centros** | Centros de acogida (protección civil, hospital, casa de acogida, otro) |
| **users** | Usuarios del sistema (Admin, Gestor) |
| **ninos** | Niños registrados en la plataforma |
| **ninos_familiares** | Familiares conocidos del niño (antes de recogida) |
| **recogidas** | Registro de recogida por familiar |

---

## Relaciones candidatas

```
centros (1) ──────── (N) users
   │
   │
   └──────────────── (N) ninos
                         │
                         ├──────── (N) ninos_familiares
                         │
                         └──────── (0..1) recogidas

users (1) ──────── (N) ninos (creado_por)
users (1) ──────── (N) recogidas (registrado_por)
```

---

## Cardinalidades

| Relación | Tipo | Descripción |
|----------|------|-------------|
| Centro → Usuarios | 1:N | Un centro tiene muchos gestores |
| Centro → Niños | 1:N | Un centro acoge muchos niños |
| Usuario → Niños registrados | 1:N | Un gestor registra muchos niños |
| Niño → Familiares | 1:N | Un niño puede tener varios familiares registrados |
| Niño → Recogida | 1:0..1 | Un niño tiene máximo una recogida (opcional) |
| Usuario → Recogidas registradas | 1:N | Un gestor registra muchas recogidas |

---

## Schema propuesto (para script SQL)

### Tabla: centros

```sql
CREATE TABLE centros (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    tipo ENUM('proteccion_civil', 'hospital', 'casa_acogida', 'otro') NOT NULL,
    email VARCHAR(200) NOT NULL UNIQUE,
    telefono VARCHAR(20) NULL,
    direccion TEXT NULL,
    activo BOOLEAN DEFAULT FALSE COMMENT 'Admin debe validar el centro',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_centros_tipo (tipo),
    INDEX idx_centros_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Tabla: users (tabla estándar de Laravel)

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    centro_id BIGINT UNSIGNED NULL COMMENT 'NULL para admin, FK para gestores',
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(200) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'gestor') NOT NULL DEFAULT 'gestor',
    cargo VARCHAR(100) NULL COMMENT 'Cargo del usuario en el centro',
    activo BOOLEAN DEFAULT FALSE COMMENT 'Activo tras confirmar email',
    token_activacion VARCHAR(100) NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_users_centro FOREIGN KEY (centro_id) REFERENCES centros(id) ON DELETE RESTRICT,
    INDEX idx_users_centro (centro_id),
    INDEX idx_users_rol (rol),
    INDEX idx_users_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Tabla: ninos

```sql
CREATE TABLE ninos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    centro_id BIGINT UNSIGNED NOT NULL,
    foto_url VARCHAR(500) NULL COMMENT 'URL relativa en storage',
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    edad INT UNSIGNED NULL COMMENT 'Si se desconoce fecha exacta',
    fecha_nacimiento DATE NULL,
    sexo ENUM('M', 'F') NULL,
    direccion_previa TEXT NULL COMMENT 'Edificio/casa donde vivía',
    nombre_madre VARCHAR(200) NULL,
    nombre_padre VARCHAR(200) NULL,
    estado ENUM('encontrado', 'familia_localizada', 'recogido', 'otro_centro') DEFAULT 'encontrado',
    notas TEXT NULL COMMENT 'Observaciones adicionales',
    creado_por BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_ninos_centro FOREIGN KEY (centro_id) REFERENCES centros(id) ON DELETE RESTRICT,
    CONSTRAINT fk_ninos_creado_por FOREIGN KEY (creado_por) REFERENCES users(id) ON DELETE RESTRICT,
    
    INDEX idx_ninos_centro (centro_id),
    INDEX idx_ninos_estado (estado),
    INDEX idx_ninos_creado_por (creado_por),
    INDEX idx_ninos_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Tabla: ninos_familiares

```sql
CREATE TABLE ninos_familiares (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nino_id BIGINT UNSIGNED NOT NULL,
    parentesco VARCHAR(50) NOT NULL COMMENT 'Madre, Padre, Tío, Abuelo, etc.',
    nombre VARCHAR(200) NOT NULL,
    apellido VARCHAR(200) NULL,
    telefono VARCHAR(20) NULL,
    notas TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_ninos_familiares_nino FOREIGN KEY (nino_id) REFERENCES ninos(id) ON DELETE CASCADE,
    
    INDEX idx_ninos_familiares_nino (nino_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Tabla: recogidas

```sql
CREATE TABLE recogidas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nino_id BIGINT UNSIGNED NOT NULL UNIQUE COMMENT 'Un niño solo se recoge una vez',
    familiar_nombre VARCHAR(200) NOT NULL,
    familiar_apellido VARCHAR(200) NOT NULL,
    familiar_parentesco VARCHAR(50) NOT NULL,
    familiar_telefono VARCHAR(20) NOT NULL,
    familiar_email VARCHAR(200) NULL,
    familiar_documento VARCHAR(50) NULL COMMENT 'DNI/Cédula del familiar',
    foto_recogida_url VARCHAR(500) NOT NULL COMMENT 'Foto del niño con el familiar',
    notas TEXT NULL COMMENT 'Observaciones del proceso',
    fecha_recogida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    registrado_por BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_recogidas_nino FOREIGN KEY (nino_id) REFERENCES ninos(id) ON DELETE RESTRICT,
    CONSTRAINT fk_recogidas_registrado_por FOREIGN KEY (registrado_por) REFERENCES users(id) ON DELETE RESTRICT,
    
    INDEX idx_recogidas_nino (nino_id),
    INDEX idx_recogidas_fecha (fecha_recogida),
    INDEX idx_recogidas_registrado_por (registrado_por)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Tabla: personal_access_tokens (Sanctum)

```sql
-- Esta tabla la crea automáticamente Laravel Sanctum
-- pero la incluimos por completitud del script
CREATE TABLE personal_access_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_personal_access_tokens_tokenable (tokenable_type, tokenable_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Datos de prueba (Seeders)

```sql
-- Admin por defecto (password: admin123)
-- El hash corresponde a bcrypt('admin123')
INSERT INTO users (nombre, email, password, rol, activo, email_verified_at) VALUES
('Administrador', 'admin@findchild.org', '$2y$12$...hash...', 'admin', TRUE, NOW());

-- Centro de prueba
INSERT INTO centros (nombre, tipo, email, telefono, direccion, activo) VALUES
('Protección Civil Central', 'proteccion_civil', 'pc@findchild.org', '+58-212-5551234', 'Av. Principal, Caracas', TRUE);

-- Gestor de prueba (password: gestor123)
INSERT INTO users (centro_id, nombre, email, password, rol, cargo, activo, email_verified_at) VALUES
(1, 'María González', 'maria@proteccion.org', '$2y$12$...hash...', 'gestor', 'Coordinadora', TRUE, NOW());

-- Niños de prueba
INSERT INTO ninos (centro_id, nombre, apellido, edad, sexo, direccion_previa, nombre_madre, nombre_padre, estado, creado_por) VALUES
(1, 'Carlos', 'Rodríguez', 5, 'M', 'Edificio Los Jardines, Piso 3', 'Ana Rodríguez', 'Pedro Rodríguez', 'encontrado', 2),
(1, 'Sofía', 'Martínez', 3, 'F', 'Casa #45, Urbanización El Paraíso', 'Laura Martínez', NULL, 'encontrado', 2),
(1, 'Miguel', 'López', 8, 'M', NULL, 'Carmen López', 'Juan López', 'familia_localizada', 2);

-- Familiar de prueba
INSERT INTO ninos_familiares (nino_id, parentesco, nombre, apellido, telefono) VALUES
(1, 'Tía', 'Rosa', 'Hernández', '+58-414-5559876');
```

---

## Reglas de integridad

| Regla | Implementación |
|-------|----------------|
| Centro no se puede eliminar si tiene niños | `ON DELETE RESTRICT` |
| Niño no se puede eliminar si tiene recogida | `ON DELETE RESTRICT` |
| Familiares se eliminan si se elimina niño | `ON DELETE CASCADE` |
| Email único por usuario | `UNIQUE` constraint |
| Email único por centro | `UNIQUE` constraint |
| Un niño = máximo una recogida | `UNIQUE` en `recogidas.nino_id` |
| Edad entre 0 y 17 | Validación en backend (no en SQL para flexibilidad) |
| Estados válidos del niño | ENUM en SQL |

---

## Índices recomendados

| Tabla | Campo | Tipo | Razón |
|-------|-------|------|-------|
| centros | email | UNIQUE | Login y registro |
| centros | tipo | INDEX | Filtrar por tipo |
| centros | activo | INDEX | Listar centros activos |
| users | email | UNIQUE | Login |
| users | centro_id | INDEX | Consultas por centro |
| users | rol | INDEX | Filtrar por rol |
| ninos | centro_id | INDEX | Listar niños del centro |
| ninos | estado | INDEX | Filtrar por estado |
| ninos | created_at | INDEX | Ordenar por fecha |
| ninos_familiares | nino_id | INDEX | Familiares de un niño |
| recogidas | nino_id | UNIQUE | Unicidad de recogida |
| recogidas | fecha_recogida | INDEX | Ordenar recogidas |

---

## Auditoría

Todas las tablas incluyen:
- `created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP`
- `updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP`

Campos adicionales de auditoría:
- `ninos.creado_por` → usuario que registró al niño
- `recogidas.registrado_por` → usuario que registró la recogida
- `users.email_verified_at` → cuando se verificó el email

---

## Consideraciones de escalabilidad

| Escenario | Solución |
|-----------|----------|
| Tabla `ninos` crece a >100K registros | Particionar por `created_at` (mensual) |
| Muchas fotos | Usar storage externo (S3/Cloudinary) en producción |
| Consultas lentas en listado público | Cache Redis para listado de niños |
| Búsquedas por nombre/apellido | Índices FULLTEXT en campos de texto |

---

## Dudas abiertas

| Duda | Impacto | Recomendación |
|------|---------|---------------|
| ¿Soft delete o hard delete? | Recuperación de datos | Recomendaría soft delete (`deleted_at`) para niños y recogidas |
| ¿Historial de cambios de estado? | Auditoría completa | Dejar para v2, por ahora solo estado actual |
| ¿Campo de documento de identidad del familiar? | Verificación legal | Sí, incluir `familiar_documento` en recogidas |
| ¿Notas/observaciones en niño? | Información adicional | Sí, incluir campo `notas` TEXT |
| ¿Tracking de IPs en login? | Seguridad | Dejar para v2 |

---

## Archivo a generar

**El script SQL completo debe guardarse en:**
```
docs/database-script.sql
```

**Estructura del archivo:**
```sql
-- ============================================
-- Reencuentro - Script de Base de Datos
-- Plataforma de Reunificación Familiar
-- Fecha: 2026-06-29
-- Base de datos: MySQL 8
-- ============================================

-- 1. Crear base de datos (si no existe)
-- 2. Tablas (en orden correcto por foreign keys)
-- 3. Índices adicionales
-- 4. Datos de prueba
-- 5. Comentarios y documentación
```

---

## Proceso de trabajo

```
1. db-design-developer crea docs/database-script.sql
2. software-enginner revisa el script
3. Usuario aprueba el diseño
4. db-design-developer crea migraciones Laravel:
   - database/migrations/xxxx_create_centros_table.php
   - database/migrations/xxxx_create_users_table.php (actualizar existente)
   - database/migrations/xxxx_create_ninos_table.php
   - database/migrations/xxxx_create_ninos_familiares_table.php
   - database/migrations/xxxx_create_recogidas_table.php
5. Backend implementa modelos basados en las migraciones
```

---

**Documento creado por:** `software-enginner`  
**Fecha:** 2026-06-29  
**Versión:** 1.0  
**Para:** `db-design-developer`
