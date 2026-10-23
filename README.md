# Juntos de Nuevo (Reencuentro)

Plataforma web humanitaria para facilitar la reunificación de niños, niñas y adolescentes separados de sus familias tras emergencias (terremotos en Venezuela). Permite que centros de acogida registren a los menores bajo su cuidado y que familiares los localicen públicamente para iniciar el proceso de reencuentro.

**Sitio en producción:** https://juntosdenuevo.org

> Proyecto humanitario sin fines de lucro. Maneja datos sensibles de menores — cualquier cambio debe priorizar seguridad, privacidad e integridad de los datos.

---

## Roles y accesos

| Rol | Descripción | Acceso |
|-----|-------------|--------|
| **Público** | Cualquier persona buscando a un familiar | Solo lectura: listado y ficha pública de niños |
| **Gestor** | Personal de un centro de acogida | Registrar niños, gestionar recogidas de su propio centro |
| **Administrador** | Superusuario | Validar centros, gestionar usuarios, configuración, visión global |

## Funcionalidades principales

- Registro público de niños encontrados (foto + datos básicos), visibles en la home
- Ficha detallada por niño (`/ninos/{nino}`)
- Registro y activación de centros de acogida por email
- Gestión de usuarios Gestor por parte del Administrador
- Proceso de **recogida**: registro del familiar que retira al menor, con evidencia fotográfica (cambio de estado irreversible)
- Configuración del proveedor de email transaccional (Brevo)

Reglas de negocio clave en [`docs/software-plan.md`](juntos-de-nuevo/docs/software-plan.md).

## Stack técnico

- **Backend:** Laravel 13 / PHP 8.3
- **Frontend:** Blade + Alpine.js + Tailwind CSS 4 (Vite)
- **Base de datos:** MySQL 8
- **Auth API:** Laravel Sanctum
- **Email:** Brevo (`app/Services/BrevoMailService.php`)
- **Tests:** Pest 4 / PHPUnit

## Arquitectura del código

El proyecto separa responsabilidades más allá del MVC estándar de Laravel:

```
app/
├── Http/Controllers/
│   ├── Web/            # Controladores de vistas Blade (Auth, Gestor, Admin)
│   └── Api/             # Controladores de la API (Sanctum)
├── Http/Middleware/      # CheckRole, EnsureCentroActivo
├── Models/               # Eloquent: Centro, Nino, NinoFamiliar, Recogida, User, Configuracion
├── Persistencia/         # Capa de repositorios sobre los modelos
├── Validaciones/         # Form requests / reglas de validación por caso de uso
├── Services/             # Integraciones externas (Brevo, config de mail)
└── Mail/                 # Mailables (activación de centro, notificaciones)
```

## Puesta en marcha local

Requisitos: PHP 8.3, Composer, Node 20+, MySQL 8 (en este entorno se usa WAMP64).

```bash
composer install
npm install

cp .env.example .env   # completar credenciales de BD y mail
php artisan key:generate
```

**Importante — base de datos:** las tablas principales (`centros`, `ninos`, `ninos_familiares`, `recogidas`, `configuracion`) no se crean con `php artisan migrate`; se definieron primero como scripts SQL puros. Estos scripts no fijan ni crean una base de datos por sí mismos — se ejecutan contra la que ya hayas seleccionado en la conexión, así que créala y apúntala tú antes de importar:

```bash
mysql -u <usuario> -p -e "CREATE DATABASE <base_de_datos> DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -u <usuario> -p <base_de_datos> < docs/database-script.sql
mysql -u <usuario> -p <base_de_datos> < docs/laravel-aux-tables.sql
mysql -u <usuario> -p <base_de_datos> < docs/configuracion-table.sql
php artisan migrate   # solo tablas propias de Laravel (personal_access_tokens ya viene del script; esto aplica migraciones nuevas futuras)
```

Levantar el entorno de desarrollo:

```bash
composer run dev   # servidor, queue listener, logs (pail) y vite en paralelo
```

## Tests

Los tests están escritos en [Pest](https://pestphp.com) (sobre PHPUnit), organizados en dos suites:

```
tests/
├── Feature/   # HTTP end-to-end: rutas Web (Blade) y API (Sanctum), por rol
└── Unit/      # Lógica de modelos en aislamiento (scopes, helpers de estado)
```

**Importante — base de datos de test:** los tests usan una base MySQL real y separada (`findchild_test`), no SQLite en memoria, porque dependen de las mismas restricciones de integridad referencial (`FOREIGN KEY`, `NOT NULL`) que producción. Cada test corre dentro de una transacción que se revierte al finalizar (`DatabaseTransactions`), así que los datos fijos de prueba no se pierden entre tests.

Configuración esperada (ver `phpunit.xml`): MySQL en `127.0.0.1:3307`, base `findchild_test`. Antes de correr los tests por primera vez:

```bash
mysql -u root -P 3307 -e "CREATE DATABASE findchild_test DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -u root -P 3307 findchild_test < docs/database-script.sql
mysql -u root -P 3307 findchild_test < docs/laravel-aux-tables.sql
mysql -u root -P 3307 findchild_test < docs/configuracion-table.sql
```

Los datos de prueba (`docs/database-script.sql`) incluyen usuarios y niños con IDs fijos que los tests referencian directamente (ej. `admin@findchild.org` / `admin123`, niño `#1` del centro "Protección Civil", niño `#4` ya recogido). Ver credenciales exactas en las cabeceras del propio script SQL y en `tests/Helpers.php` (funciones `adminUser()`, `gestorUser()`, `gestorHospitalUser()`).

Ejecutar los tests:

```bash
php artisan test
# o directamente
./vendor/bin/pest
```

### Qué cubren

- **Autenticación** (web y API): login, logout, registro de centro, activación por token, cuentas inactivas
- **Roles y permisos**: un Gestor nunca opera sobre datos de otro centro; rutas de Admin bloqueadas para Gestor y viceversa
- **`EnsureCentroActivo`**: un Gestor de un centro aún no validado por el Admin no puede crear niños, listar los suyos ni registrar recogidas (ni por web ni por API)
- **Niños**: registro, ficha pública vs. autenticada (oculta datos sensibles a visitantes anónimos), validaciones de edad/sexo/estado
- **Recogidas**: solo una vez por niño (estado irreversible), foto de evidencia obligatoria
- **Emails transaccionales**: se verifica con `Mail::fake()` que se despachan `ActivarCentroMail`, `NuevoCentroPendienteMail` y `CentroActivadoMail` en los flujos de registro y validación de centro
- **Modelos** (`Unit`): scopes de `Nino` (`encontrados`, `recogidos`, `familiaLocalizada`, `porCentro`) y helpers de rol/estado de `User` y `Nino`

## Documentación adicional

La carpeta [`docs/`](docs/) contiene los documentos de diseño y handoff del proyecto:

- `software-plan.md` — objetivo, alcance del MVP, reglas de negocio
- `database-handoff.md` / `database-script.sql` / `laravel-aux-tables.sql` / `configuracion-table.sql` — modelo de datos
- `backend-handoff.md`, `frontend-handoff.md`, `blade-handoff.md` — decisiones de implementación
- `blade-security-rules.md` — reglas de seguridad para las vistas
- `reset-datos-mantener-admin.sql` — script para limpiar datos de prueba conservando el usuario admin

## Seguridad

Este sistema almacena información de menores en situación vulnerable (ubicación, estado, datos de quien los retira). Antes de desplegar cambios, verificar:

- Reglas de acceso por rol (`CheckRole`, `EnsureCentroActivo`)
- Que un Gestor solo pueda operar sobre los niños de su propio centro
- Que los datos de contacto (teléfono, email) no se expongan en endpoints públicos
- Validación de las clases en `app/Validaciones/` antes de cualquier escritura

Si detectas una vulnerabilidad, repórtala de forma responsable antes de discutirla públicamente.
