# Handoff para software-developer-backend

## Contexto

Plataforma humanitaria **Reencuentro** para reunificación de niños con familias tras terremotos en Venezuela. Sistema crítico que debe ser rápido, seguro y accesible desde móvil.

**Stack confirmado:**
- **Framework:** Laravel 11 (ya instalado en `C:\wamp64\www\findchild`)
- **PHP:** 8.2+ (WAMP64)
- **Base de datos:** MySQL 8 (WAMP64, puerto 3306)
- **Autenticación:** Laravel Sanctum (tokens API)
- **Almacenamiento:** `storage/app/public` con symlink

---

## Objetivo backend

- API REST para gestión de niños, centros, usuarios y recogidas
- Autenticación con Sanctum y roles (Admin, Gestor)
- Upload y almacenamiento de fotos
- Envío de emails de activación
- CORS configurado para frontend React

---

## Pautas de desarrollo Laravel

### 1. Estructura de carpetas

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php
│   │       ├── CentroController.php
│   │       ├── NinoController.php
│   │       └── RecogidaController.php
│   ├── Requests/
│   │   ├── RegisterCentroRequest.php
│   │   ├── StoreNinoRequest.php
│   │   ├── UpdateNinoRequest.php
│   │   └── StoreRecogidaRequest.php
│   ├── Resources/
│   │   ├── NinoResource.php
│   │   ├── NinoCollection.php
│   │   ├── CentroResource.php
│   │   └── RecogidaResource.php
│   └── Middleware/
│       ├── CheckRole.php
│       └── EnsureCentroActivo.php
├── Models/
│   ├── Centro.php
│   ├── User.php
│   ├── Nino.php
│   └── Recogida.php
├── Services/
│   ├── AuthService.php
│   ├── NinoService.php
│   └── RecogidaService.php
├── Observers/
│   └── NinoObserver.php
└── Enums/
    ├── CentroTipo.php
    ├── NinoEstado.php
    └── UserRole.php
```

### 2. Convenciones de código

- **Controllers:** Extender de `Controller` base, usar inyección de dependencias
- **Requests:** Toda validación en Form Requests, no en controllers
- **Resources:** Transformar TODAS las respuestas API con Resources
- **Services:** Lógica de negocio en Services, no en Controllers
- **Models:** Relaciones bien definidas, scopes para queries comunes
- **Enums:** Usar PHP 8.1+ enums para estados y roles

### 3. Respuestas API

**Éxito:**
```json
{
    "success": true,
    "data": { ... },
    "message": "Operación exitosa"
}
```

**Error:**
```json
{
    "success": false,
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "Los datos proporcionados no son válidos",
        "details": { ... }
    }
}
```

**HTTP Status Codes:**
- `200` — Éxito (GET, PUT)
- `201` — Creado (POST)
- `204` — Sin contenido (DELETE)
- `400` — Bad Request
- `401` — No autenticado
- `403` — Sin permisos
- `404` — No encontrado
- `409` — Conflicto
- `422` — Validación fallida
- `500` — Error interno

### 4. Autenticación

Usar **Laravel Sanctum** para autenticación API:

```php
// Registrar usuario y crear token
$user = User::create($data);
$token = $user->createToken('auth-token')->plainTextToken;

// Login
$user = User::where('email', $request->email)->firstOrFail();
if (!Hash::check($request->password, $user->password)) {
    return response()->json(['error' => 'Credenciales inválidas'], 401);
}
$token = $user->createToken('auth-token')->plainTextToken;

// Logout
$request->user()->currentAccessToken()->delete();
```

### 5. Middleware de roles

Crear middleware `CheckRole.php`:

```php
public function handle(Request $request, Closure $next, string ...$roles): Response
{
    if (!$request->user() || !in_array($request->user()->rol, $roles)) {
        return response()->json(['error' => 'No autorizado'], 403);
    }
    return $next($request);
}
```

Uso en rutas:
```php
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Rutas solo para admin
});

Route::middleware(['auth:sanctum', 'role:gestor,admin'])->group(function () {
    // Rutas para gestor o admin
});
```

### 6. Manejo de fotos

**Configuración:**
```bash
php artisan storage:link
```

**Upload en controller:**
```php
if ($request->hasFile('foto')) {
    $path = $request->file('foto')->store('ninos', 'public');
    $url = Storage::url($path);
}
```

**Validación en Request:**
```php
public function rules(): array
{
    return [
        'foto' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB
    ];
}
```

### 7. Emails de activación

Usar Laravel Mailables:

```php
// En AuthService
Mail::to($centro->email)->send(new ActivarCentroMail($centro, $token));
```

Crear mailable con:
```bash
php artisan make:mail ActivarCentroMail
```

### 8. Rate limiting

Configurar en `app/Providers/RouteServiceProvider.php`:

```php
RateLimiter::for('api', function (Request $request) {
    return Limit::perHour(100)->by($request->ip());
});

RateLimiter::for('auth', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});
```

---

## Módulos backend

### Módulo 1: Autenticación

**Archivos:**
- `app/Http/Controllers/Api/AuthController.php`
- `app/Http/Requests/RegisterCentroRequest.php`
- `app/Services/AuthService.php`
- `app/Mail/ActivarCentroMail.php`

**Endpoints:**
```
POST /api/auth/register        # Registro de centro
POST /api/auth/activate/{token} # Activar usuario
POST /api/auth/login           # Login
POST /api/auth/logout          # Logout (auth)
GET  /api/auth/me              # Usuario actual (auth)
```

### Módulo 2: Centros

**Archivos:**
- `app/Http/Controllers/Api/CentroController.php`
- `app/Models/Centro.php`
- `app/Http/Resources/CentroResource.php`

**Endpoints:**
```
GET  /api/centros              # Listar centros (admin)
PUT  /api/centros/{id}/validar # Validar centro (admin)
```

### Módulo 3: Niños

**Archivos:**
- `app/Http/Controllers/Api/NinoController.php`
- `app/Models/Nino.php`
- `app/Http/Requests/StoreNinoRequest.php`
- `app/Http/Requests/UpdateNinoRequest.php`
- `app/Http/Resources/NinoResource.php`
- `app/Services/NinoService.php`
- `app/Observers/NinoObserver.php`

**Endpoints:**
```
GET  /api/ninos                # Listado público
GET  /api/ninos/{id}           # Ficha niño (público)
POST /api/ninos                # Registrar niño (gestor)
PUT  /api/ninos/{id}           # Actualizar niño (gestor)
GET  /api/gestor/ninos         # Mis niños (gestor)
```

### Módulo 4: Recogidas

**Archivos:**
- `app/Http/Controllers/Api/RecogidaController.php`
- `app/Models/Recogida.php`
- `app/Http/Requests/StoreRecogidaRequest.php`
- `app/Http/Resources/RecogidaResource.php`
- `app/Services/RecogidaService.php`

**Endpoints:**
```
POST /api/ninos/{id}/recogida  # Registrar recogida (gestor)
GET  /api/recogidas            # Listar recogidas (admin)
```

---

## Entidades involucradas

Ver `docs/database-handoff.md` para schema completo.

**Models Laravel necesarios:**

```php
// app/Models/Centro.php
class Centro extends Model {
    protected $fillable = ['nombre', 'tipo', 'email', 'telefono', 'direccion', 'activo'];
    
    public function usuarios() { return $this->hasMany(User::class); }
    public function ninos() { return $this->hasMany(Nino::class); }
}

// app/Models/User.php
class User extends Authenticatable {
    use HasApiTokens, Notifiable;
    protected $fillable = ['centro_id', 'nombre', 'email', 'password', 'rol', 'activo', 'token_activacion'];
    
    public function centro() { return $this->belongsTo(Centro::class); }
    public function ninosRegistrados() { return $this->hasMany(Nino::class, 'creado_por'); }
}

// app/Models/Nino.php
class Nino extends Model {
    protected $fillable = [
        'centro_id', 'foto_url', 'nombre', 'apellido', 'edad', 'fecha_nacimiento',
        'sexo', 'direccion_previa', 'nombre_madre', 'nombre_padre', 'estado', 'creado_por'
    ];
    
    public function centro() { return $this->belongsTo(Centro::class); }
    public function creadoPor() { return $this->belongsTo(User::class, 'creado_por'); }
    public function familiares() { return $this->hasMany(NinoFamiliar::class); }
    public function recogida() { return $this->hasOne(Recogida::class); }
    
    // Scopes
    public function scopeEncontrados($query) { return $query->where('estado', 'encontrado'); }
    public function scopePublicos($query) { return $query->where('estado', 'encontrado')->where('activo', true); }
}

// app/Models/Recogida.php
class Recogida extends Model {
    protected $fillable = [
        'nino_id', 'familiar_nombre', 'familiar_apellido', 'familiar_parentesco',
        'familiar_telefono', 'familiar_email', 'foto_recogida_url', 'registrado_por'
    ];
    
    public function nino() { return $this->belongsTo(Nino::class); }
    public function registradoPor() { return $this->belongsTo(User::class, 'registrado_por'); }
}
```

---

## Reglas de negocio

1. **Un niño solo puede ser recogido una vez**
   - Verificar antes de crear recogida: `if ($nino->recogida) throw new ConflictException()`

2. **Datos sensibles solo visibles para autenticados**
   - En `NinoResource`: condicionar campos según `request()->user()`

3. **Email debe confirmarse antes de activar Gestor**
   - Usuario creado con `activo = false`
   - Generar token único y enviar email
   - Activar solo al confirmar token

4. **Solo Admin valida centros**
   - Middleware `role:admin` en rutas de validación

5. **Foto de recogida es obligatoria**
   - Validación: `'foto_recogida' => 'required|image'`

6. **Gestor solo ve sus propios niños**
   - En `NinoController@gestor`: `auth()->user()->ninosRegistrados()`

7. **Centro debe estar activo para operar**
   - Middleware `EnsureCentroActivo` en rutas de gestor

---

## Validaciones

### RegisterCentroRequest
```php
public function rules(): array
{
    return [
        'tipo_centro' => 'required|in:proteccion_civil,hospital,casa_acogida,otro',
        'nombre_centro' => 'required|string|max:200',
        'nombre' => 'required|string|max:100',
        'email' => 'required|email|unique:centros,email|unique:usuarios,email',
        'cargo' => 'required|string|max:100',
        'telefono' => 'nullable|string|max:20',
    ];
}
```

### StoreNinoRequest
```php
public function rules(): array
{
    return [
        'foto' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        'nombre' => 'required|string|max:100',
        'apellido' => 'required|string|max:100',
        'edad' => 'nullable|integer|min:0|max:17',
        'fecha_nacimiento' => 'nullable|date|before:today',
        'sexo' => 'nullable|in:M,F',
        'direccion_previa' => 'nullable|string|max:500',
        'nombre_madre' => 'nullable|string|max:200',
        'nombre_padre' => 'nullable|string|max:200',
        'familiares' => 'nullable|array',
        'familiares.*.parentesco' => 'required_with:familiares|string|max:50',
        'familiares.*.nombre' => 'required_with:familiares|string|max:200',
        'familiares.*.telefono' => 'nullable|string|max:20',
    ];
}
```

### StoreRecogidaRequest
```php
public function rules(): array
{
    return [
        'familiar_nombre' => 'required|string|max:200',
        'familiar_apellido' => 'required|string|max:200',
        'familiar_parentesco' => 'required|string|max:50',
        'familiar_telefono' => 'required|string|max:20',
        'familiar_email' => 'nullable|email|max:200',
        'foto_recogida' => 'required|image|mimes:jpeg,png,jpg|max:5120',
    ];
}
```

---

## Seguridad

### Configuración requerida

**.env:**
```env
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:5173
SESSION_DRIVER=sanctum
SESSION_DOMAIN=localhost
```

**config/sanctum.php:**
```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,localhost:5173')),
```

**config/cors.php:**
```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['http://localhost:5173'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => true,
```

### Middleware de autenticación

En `bootstrap/app.php` o `app/Http/Kernel.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\CheckRole::class,
        'centro.activo' => \App\Http\Middleware\EnsureCentroActivo::class,
    ]);
})
```

---

## Integraciones

### Email (desarrollo)

Usar Laravel Mailtrap o log:

```env
MAIL_MAILER=log
# O para testing real:
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_username
MAIL_PASSWORD=tu_password
```

### Almacenamiento

```env
FILESYSTEM_DISK=public
```

Fotos se guardarán en `storage/app/public/ninos/` y accesibles via `public/storage/ninos/`

---

## Manejo de errores

### API Exceptions

Crear `app/Exceptions/Handler.php`:

```php
public function render($request, Throwable $e): Response
{
    if ($e instanceof ModelNotFoundException) {
        return response()->json([
            'success' => false,
            'error' => ['code' => 'NOT_FOUND', 'message' => 'Recurso no encontrado']
        ], 404);
    }
    
    if ($e instanceof AuthenticationException) {
        return response()->json([
            'success' => false,
            'error' => ['code' => 'UNAUTHORIZED', 'message' => 'No autenticado']
        ], 401);
    }
    
    if ($e instanceof AuthorizationException) {
        return response()->json([
            'success' => false,
            'error' => ['code' => 'FORBIDDEN', 'message' => 'No autorizado']
        ], 403);
    }
    
    return parent::render($request, $e);
}
```

### Errores esperados

| Error | Código | Cuándo |
|-------|--------|--------|
| `VALIDATION_ERROR` | 422 | Datos inválidos |
| `UNAUTHORIZED` | 401 | Sin token o token inválido |
| `FORBIDDEN` | 403 | Sin permisos para el recurso |
| `NOT_FOUND` | 404 | Recurso no existe |
| `CONFLICT` | 409 | Niño ya recogido, email duplicado |
| `SERVER_ERROR` | 500 | Error interno no esperado |

---

## Testing recomendado

### PHPUnit tests

```bash
php artisan make:test AuthTest
php artisan make:test NinoTest
php artisan make:test RecogidaTest
```

### Tests mínimos requeridos

1. **AuthTest:**
   - Registro de centro crea usuario inactivo
   - Activación con token válido activa usuario
   - Login con credenciales correctas retorna token
   - Login con credenciales incorrectas falla

2. **NinoTest:**
   - Público puede ver listado de niños
   - Gestor puede crear niño con foto
   - Gestor no puede ver niños de otros centros
   - Niño no recogido puede ser recogido
   - Niño recogido no puede ser recogido de nuevo

3. **RecogidaTest:**
   - Recogida requiere foto
   - Recogida cambia estado del niño a "recogido"
   - Solo gestor del centro puede registrar recogida

### Ejecutar tests

```bash
php artisan test
php artisan test --filter NinoTest
```

---

## Prioridad de implementación

### 1. Configuración inicial (Día 1)
- [ ] Verificar `.env` configurado correctamente
- [ ] Configurar base de datos en `.env`
- [ ] Instalar Sanctum: `php artisan install:api`
- [ ] Configurar CORS
- [ ] Crear symlink de storage: `php artisan storage:link`

### 2. Base de datos (Día 1-2)
- [ ] Crear migraciones para todas las tablas
- [ ] Crear modelos con relaciones
- [ ] Crear seeders para datos de prueba
- [ ] Ejecutar migraciones: `php artisan migrate`

### 3. Autenticación (Día 2-3)
- [ ] AuthController (registro, login, logout, activate)
- [ ] AuthService
- [ ] CheckRole middleware
- [ ] Mailable de activación

### 4. Niños (Día 3-5)
- [ ] NinoController (CRUD completo)
- [ ] NinoRequest (validaciones)
- [ ] NinoResource (transformación)
- [ ] NinoService (lógica de negocio)
- [ ] Upload de fotos

### 5. Recogidas (Día 5-6)
- [ ] RecogidaController
- [ ] RecogidaRequest
- [ ] RecogidaResource
- [ ] RecogidaService
- [ ] Validación de niño no recogido

### 6. Administración (Día 6-7)
- [ ] CentroController (validación)
- [ ] Endpoints de estadísticas
- [ ] Dashboard data

### 7. Testing y ajustes (Día 7-8)
- [ ] Tests PHPUnit
- [ ] Pruebas manuales con Postman
- [ ] Ajustes y bug fixes

---

## Comandos útiles

```bash
# Crear controlador API
php artisan make:controller Api/NinoController --api

# Crear modelo con migración
php artisan make:model Nino -m

# Crear Request
php artisan make:request StoreNinoRequest

# Crear Resource
php artisan make:resource NinoResource

# Crear Middleware
php artisan make:middleware CheckRole

# Crear Mailable
php artisan make:mail ActivarCentroMail

# Crear Test
php artisan make:test NinoTest

# Ver rutas
php artisan route:list

# Limpiar caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## Skills sugeridas

- `php-developer` — Para implementación PHP/Laravel
- `sql-database-design` — Para consultas y optimización

---

## Notas adicionales

1. **No usar Blade** — Esto es API pura, el frontend es React separado
2. **Usar Postman/Insomnia** para probar endpoints durante desarrollo
3. **Documentar endpoints** con comentarios PHPDoc para futura generación de Swagger
4. **Mantener controllers delgados** — lógica en Services
5. **Usar transacciones DB** en operaciones críticas (registro de recogida)

---

**Documento creado por:** `software-enginner`  
**Fecha:** 2026-06-29  
**Versión:** 1.0  
**Para:** `software-developer-backend`
