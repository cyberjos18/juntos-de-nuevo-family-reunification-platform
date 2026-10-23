# Handoff para software-developer-maquetador

## Contexto del cambio de arquitectura

**Fecha:** 2026-06-30  
**Decisión:** Cambiar de React a Laravel Blade por urgencia del proyecto  
**Razón:** Necesidad de desplegar rápido en servidor Apache con WAMP  

El proyecto Reencuentro es una plataforma humanitaria para reunificación de niños tras los terremotos en Venezuela. Debido a la urgencia, se ha decidido usar Laravel Blade en lugar de React para poder desplegar inmediatamente en el servidor Apache existente.

---

## Stack técnico

- **Backend:** Laravel 11 (ya implementado)
- **Base de datos:** MariaDB 11.4.9 (puerto 3307)
- **Vistas:** Laravel Blade + Tailwind CSS + Material Design
- **Servidor:** Apache (WAMP)
- **Autenticación:** Laravel Sanctum (para API) + Laravel Session (para web)

---

## Diseños de Stitch disponibles

Los diseños HTML generados por Google Stitch están en:

```
resources/views/diseño/
├── home_reencuentro/
├── ficha_del_ni_o_reencuentro/
├── iniciar_sesi_n_reencuentro/
├── registro_de_centro_reencuentro/
├── activaci_n_de_cuenta_reencuentro/
├── dashboard_gestor_reencuentro/
├── registrar_ni_o_reencuentro/
├── gestionar_recogida_reencuentro/
├── dashboard_admin_reencuentro/
├── validar_centros_reencuentro/
├── configuraci_n_mailjet_reencuentro/
└── humanitarian_unity_system/
```

Cada carpeta contiene:
- `code.html` — HTML generado por Stitch (usar como referencia visual)
- `screen.png` — Captura de pantalla del diseño

**IMPORTANTE:** Los archivos `code.html` son solo referencia visual. Debes convertirlos a Blade usando las convenciones de Laravel.

---

## Mapeo de vistas a endpoints

### 1. Home (Listado público de niños)

**Vista Blade:** `resources/views/home.blade.php`  
**Ruta web:** `GET /`  
**Diseño Stitch:** `diseño/home_reencuentro/code.html`

**Datos necesarios desde backend:**
- Lista de niños con estado "encontrado" o "familia_localizada"
- Nombre del centro de cada niño
- Foto del niño (si existe)
- Nombre, apellido, edad
- Estado actual

**Endpoint API (para referencia):**
```
GET /api/ninos
```

**Controlador web a crear:**
```php
// app/Http/Controllers/Web/HomeController.php
public function index()
{
    $ninos = Nino::with('centro')
        ->whereIn('estado', ['encontrado', 'familia_localizada'])
        ->orderBy('created_at', 'desc')
        ->paginate(20);
    
    return view('home', compact('ninos'));
}
```

**Variables disponibles en Blade:**
```blade
{{ $ninos }} // Collection paginada
@foreach($ninos as $nino)
    {{ $nino->nombre }}
    {{ $nino->apellido }}
    {{ $nino->edad }}
    {{ $nino->centro->nombre }}
    {{ $nino->estado }}
    {{ $nino->foto_url ? asset('storage/' . $nino->foto_url) : '' }}
@endforeach
{{ $ninos->links() }} // Paginación
```

---

### 2. Ficha del niño

**Vista Blade:** `resources/views/ninos/show.blade.php`  
**Ruta web:** `GET /ninos/{nino}`  
**Diseño Stitch:** `diseño/ficha_del_ni_o_reencuentro/code.html`

**Datos necesarios desde backend:**
- Todos los datos del niño
- Centro al que pertenece
- Familiares registrados (si existen)
- Estado actual

**Endpoint API (para referencia):**
```
GET /api/ninos/{nino}
```

**Controlador web a crear:**
```php
// app/Http/Controllers/Web/NinoController.php
public function show(Nino $nino)
{
    $nino->load(['centro', 'familiares']);
    return view('ninos.show', compact('nino'));
}
```

**Variables disponibles en Blade:**
```blade
{{ $nino->nombre }}
{{ $nino->apellido }}
{{ $nino->edad }}
{{ $nino->fecha_nacimiento?->format('d/m/Y') }}
{{ $nino->sexo }}
{{ $nino->direccion_previa }}
{{ $nino->nombre_madre }}
{{ $nino->nombre_padre }}
{{ $nino->estado }}
{{ $nino->centro->nombre }}
{{ $nino->centro->direccion }}

@if($nino->familiares->count())
    @foreach($nino->familiares as $familiar)
        {{ $familiar->parentesco }}
        {{ $familiar->nombre }}
        {{ $familiar->telefono }}
    @endforeach
@endif
```

---

### 3. Login

**Vista Blade:** `resources/views/auth/login.blade.php`  
**Ruta web:** `GET /login` y `POST /login`  
**Diseño Stitch:** `diseño/iniciar_sesi_n_reencuentro/code.html`

**Datos necesarios:**
- Ninguno (formulario vacío)

**Endpoint API (para referencia):**
```
POST /api/auth/login
```

**Controlador web a crear:**
```php
// app/Http/Controllers/Web/Auth/LoginController.php
public function showLoginForm()
{
    return view('auth.login');
}

public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        
        $user = Auth::user();
        if ($user->rol === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('gestor.dashboard');
    }

    return back()->withErrors([
        'email' => 'Las credenciales no coinciden.',
    ])->onlyInput('email');
}
```

**Formulario Blade:**
```blade
<form method="POST" action="{{ route('login') }}">
    @csrf
    <input type="email" name="email" value="{{ old('email') }}" required>
    @error('email') <span>{{ $message }}</span> @enderror
    
    <input type="password" name="password" required>
    
    <label>
        <input type="checkbox" name="remember">
        Recordarme
    </label>
    
    <button type="submit">Iniciar sesión</button>
</form>
```

---

### 4. Registro de centro

**Vista Blade:** `resources/views/auth/register.blade.php`  
**Ruta web:** `GET /registro` y `POST /registro`  
**Diseño Stitch:** `diseño/registro_de_centro_reencuentro/code.html`

**Datos necesarios:**
- Ninguno (formulario vacío)

**Endpoint API (para referencia):**
```
POST /api/auth/register
```

**Controlador web a crear:**
```php
// app/Http/Controllers/Web/Auth/RegisterController.php
public function showRegistrationForm()
{
    return view('auth.register');
}

public function register(Request $request)
{
    $validated = $request->validate([
        'nombre_centro' => 'required|string|max:255',
        'tipo_centro' => 'required|in:proteccion_civil,hospital,casa_acogida,otro',
        'direccion_centro' => 'required|string',
        'telefono_centro' => 'required|string',
        'email_centro' => 'required|email|unique:centros,email',
        'nombre_gestor' => 'required|string',
        'apellido_gestor' => 'required|string',
        'email_gestor' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
    ]);

    // Lógica de registro (ver AuthController@register en API)
    
    return redirect()->route('login')
        ->with('success', 'Registro exitoso. Revisa tu email para activar la cuenta.');
}
```

**Formulario Blade:**
```blade
<form method="POST" action="{{ route('register') }}">
    @csrf
    
    <!-- Datos del centro -->
    <input type="text" name="nombre_centro" value="{{ old('nombre_centro') }}" required>
    @error('nombre_centro') <span>{{ $message }}</span> @enderror
    
    <select name="tipo_centro" required>
        <option value="">Seleccionar...</option>
        <option value="proteccion_civil" {{ old('tipo_centro') == 'proteccion_civil' ? 'selected' : '' }}>
            Protección Civil
        </option>
        <option value="hospital" {{ old('tipo_centro') == 'hospital' ? 'selected' : '' }}>
            Hospital
        </option>
        <option value="casa_acogida" {{ old('tipo_centro') == 'casa_acogida' ? 'selected' : '' }}>
            Casa de Acogida
        </option>
        <option value="otro" {{ old('tipo_centro') == 'otro' ? 'selected' : '' }}>
            Otro
        </option>
    </select>
    
    <!-- ... más campos ... -->
    
    <button type="submit">Registrarse</button>
</form>
```

---

### 5. Activación de cuenta

**Vista Blade:** `resources/views/auth/activate.blade.php`  
**Ruta web:** `GET /activar/{token}`  
**Diseño Stitch:** `diseño/activaci_n_de_cuenta_reencuentro/code.html`

**Datos necesarios:**
- Token de activación (desde URL)

**Endpoint API (para referencia):**
```
POST /api/auth/activate/{token}
```

**Controlador web a crear:**
```php
// app/Http/Controllers/Web/Auth/ActivationController.php
public function activate(string $token)
{
    $user = User::where('token_activacion', $token)->first();
    
    if (!$user) {
        return view('auth.activate', ['success' => false]);
    }
    
    $user->update([
        'activo' => true,
        'email_verified_at' => now(),
        'token_activacion' => null,
    ]);
    
    return view('auth.activate', ['success' => true]);
}
```

**Variables en Blade:**
```blade
@if($success)
    <h1>¡Cuenta activada!</h1>
    <p>Tu cuenta ha sido activada exitosamente.</p>
    <a href="{{ route('login') }}">Iniciar sesión</a>
@else
    <h1>Error de activación</h1>
    <p>El token de activación es inválido o ha expirado.</p>
@endif
```

---

### 6. Dashboard Gestor

**Vista Blade:** `resources/views/gestor/dashboard.blade.php`  
**Ruta web:** `GET /gestor/dashboard`  
**Diseño Stitch:** `diseño/dashboard_gestor_reencuentro/code.html`  
**Middleware:** `auth`, `role:gestor`, `centro.activo`

**Datos necesarios desde backend:**
- Estadísticas del centro del gestor
- Lista de niños del centro
- Número de niños por estado

**Controlador web a crear:**
```php
// app/Http/Controllers/Web/Gestor/DashboardController.php
public function index()
{
    $centro = auth()->user()->centro;
    
    $stats = [
        'total_ninos' => $centro->ninos()->count(),
        'encontrados' => $centro->ninos()->where('estado', 'encontrado')->count(),
        'familia_localizada' => $centro->ninos()->where('estado', 'familia_localizada')->count(),
        'recogidos' => $centro->ninos()->where('estado', 'recogido')->count(),
    ];
    
    $ninos = $centro->ninos()
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
    
    return view('gestor.dashboard', compact('stats', 'ninos', 'centro'));
}
```

**Variables en Blade:**
```blade
<h1>Dashboard - {{ $centro->nombre }}</h1>

<div class="stats">
    <div>Total niños: {{ $stats['total_ninos'] }}</div>
    <div>Encontrados: {{ $stats['encontrados'] }}</div>
    <div>Familia localizada: {{ $stats['familia_localizada'] }}</div>
    <div>Recogidos: {{ $stats['recogidos'] }}</div>
</div>

<h2>Últimos niños registrados</h2>
@foreach($ninos as $nino)
    <div>
        {{ $nino->nombre }} {{ $nino->apellido }}
        <span class="badge-{{ $nino->estado }}">{{ $nino->estado }}</span>
    </div>
@endforeach
```

---

### 7. Registrar niño

**Vista Blade:** `resources/views/gestor/ninos/create.blade.php`  
**Ruta web:** `GET /gestor/ninos/crear` y `POST /gestor/ninos`  
**Diseño Stitch:** `diseño/registrar_ni_o_reencuentro/code.html`  
**Middleware:** `auth`, `role:gestor`, `centro.activo`

**Datos necesarios:**
- Ninguno (formulario vacío)

**Endpoint API (para referencia):**
```
POST /api/ninos
```

**Controlador web a crear:**
```php
// app/Http/Controllers/Web/Gestor/NinoController.php
public function create()
{
    return view('gestor.ninos.create');
}

public function store(Request $request)
{
    $validated = $request->validate([
        'nombre' => 'required|string|max:100',
        'apellido' => 'required|string|max:100',
        'edad' => 'nullable|integer|min:0|max:17',
        'sexo' => 'nullable|in:M,F',
        'fecha_nacimiento' => 'nullable|date|before:today',
        'direccion_previa' => 'nullable|string',
        'nombre_madre' => 'nullable|string',
        'nombre_padre' => 'nullable|string',
        'notas' => 'nullable|string',
        'foto' => 'required|image|max:5120', // 5MB
        'familiares' => 'nullable|array',
        'familiares.*.parentesco' => 'required_with:familiares|string',
        'familiares.*.nombre' => 'required_with:familiares|string',
        'familiares.*.telefono' => 'nullable|string',
    ]);
    
    // Lógica de creación (ver NinoController@store en API)
    
    return redirect()->route('gestor.ninos.index')
        ->with('success', 'Niño registrado exitosamente');
}
```

**Formulario Blade:**
```blade
<form method="POST" action="{{ route('gestor.ninos.store') }}" enctype="multipart/form-data">
    @csrf
    
    <input type="text" name="nombre" value="{{ old('nombre') }}" required>
    @error('nombre') <span>{{ $message }}</span> @enderror
    
    <input type="file" name="foto" accept="image/*" required>
    @error('foto') <span>{{ $message }}</span> @enderror
    
    <!-- ... más campos ... -->
    
    <!-- Sección de familiares (dinámica con JavaScript) -->
    <div id="familiares-container">
        <!-- Se agregan dinámicamente -->
    </div>
    <button type="button" onclick="agregarFamiliar()">+ Agregar familiar</button>
    
    <button type="submit">Registrar niño</button>
</form>
```

---

### 8. Gestionar recogida

**Vista Blade:** `resources/views/gestor/recogidas/create.blade.php`  
**Ruta web:** `GET /gestor/ninos/{nino}/recogida` y `POST /gestor/ninos/{nino}/recogida`  
**Diseño Stitch:** `diseño/gestionar_recogida_reencuentro/code.html`  
**Middleware:** `auth`, `role:gestor`, `centro.activo`

**Datos necesarios:**
- Datos del niño a recoger

**Endpoint API (para referencia):**
```
POST /api/ninos/{nino}/recogida
```

**Controlador web a crear:**
```php
// app/Http/Controllers/Web/Gestor/RecogidaController.php
public function create(Nino $nino)
{
    // Verificar que el niño pertenece al centro del usuario
    if ($nino->centro_id !== auth()->user()->centro_id) {
        abort(403);
    }
    
    if (!$nino->puedeSerRecogido()) {
        return redirect()->route('gestor.ninos.show', $nino)
            ->with('error', 'Este niño ya fue recogido');
    }
    
    return view('gestor.recogidas.create', compact('nino'));
}

public function store(Request $request, Nino $nino)
{
    $validated = $request->validate([
        'familiar_nombre' => 'required|string',
        'familiar_apellido' => 'required|string',
        'familiar_parentesco' => 'required|string',
        'familiar_telefono' => 'required|string',
        'familiar_email' => 'nullable|email',
        'familiar_documento' => 'nullable|string',
        'foto_recogida' => 'required|image|max:5120',
        'notas' => 'nullable|string',
    ]);
    
    // Lógica de recogida (ver RecogidaController@store en API)
    
    return redirect()->route('gestor.ninos.index')
        ->with('success', 'Recogida registrada exitosamente');
}
```

**Variables en Blade:**
```blade
<h1>Registrar recogida</h1>

<div class="nino-info">
    <img src="{{ $nino->foto_url ? asset('storage/' . $nino->foto_url) : '' }}" alt="">
    <p>{{ $nino->nombre }} {{ $nino->apellido }}</p>
    <p>{{ $nino->edad }} años</p>
</div>

<form method="POST" action="{{ route('gestor.recogidas.store', $nino) }}" enctype="multipart/form-data">
    @csrf
    
    <input type="text" name="familiar_nombre" value="{{ old('familiar_nombre') }}" required>
    <input type="text" name="familiar_apellido" value="{{ old('familiar_apellido') }}" required>
    
    <select name="familiar_parentesco" required>
        <option value="">Seleccionar...</option>
        <option value="Padre">Padre</option>
        <option value="Madre">Madre</option>
        <option value="Tío/a">Tío/a</option>
        <option value="Abuelo/a">Abuelo/a</option>
        <option value="Hermano/a">Hermano/a</option>
        <option value="Otro">Otro</option>
    </select>
    
    <input type="file" name="foto_recogida" accept="image/*" required>
    
    <!-- ... más campos ... -->
    
    <button type="submit">Confirmar recogida</button>
</form>
```

---

### 9. Dashboard Admin

**Vista Blade:** `resources/views/admin/dashboard.blade.php`  
**Ruta web:** `GET /admin/dashboard`  
**Diseño Stitch:** `diseño/dashboard_admin_reencuentro/code.html`  
**Middleware:** `auth`, `role:admin`

**Datos necesarios:**
- Estadísticas globales del sistema
- Número de centros activos/pendientes
- Número de niños por estado
- Número de usuarios por rol

**Endpoint API (para referencia):**
```
GET /api/admin/estadisticas
```

**Controlador web a crear:**
```php
// app/Http/Controllers/Web/Admin/DashboardController.php
public function index()
{
    $stats = [
        'total_centros' => Centro::count(),
        'centros_activos' => Centro::where('activo', true)->count(),
        'centros_pendientes' => Centro::where('activo', false)->count(),
        'total_ninos' => Nino::count(),
        'ninos_encontrados' => Nino::where('estado', 'encontrado')->count(),
        'ninos_recogidos' => Nino::where('estado', 'recogido')->count(),
        'ninos_familia_localizada' => Nino::where('estado', 'familia_localizada')->count(),
        'total_recogidas' => Recogida::count(),
        'total_usuarios' => User::count(),
        'usuarios_gestores' => User::where('rol', 'gestor')->count(),
    ];
    
    return view('admin.dashboard', compact('stats'));
}
```

**Variables en Blade:**
```blade
<h1>Dashboard Administrador</h1>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Centros</h3>
        <p>Total: {{ $stats['total_centros'] }}</p>
        <p>Activos: {{ $stats['centros_activos'] }}</p>
        <p>Pendientes: {{ $stats['centros_pendientes'] }}</p>
    </div>
    
    <div class="stat-card">
        <h3>Niños</h3>
        <p>Total: {{ $stats['total_ninos'] }}</p>
        <p>Encontrados: {{ $stats['ninos_encontrados'] }}</p>
        <p>Recogidos: {{ $stats['ninos_recogidos'] }}</p>
    </div>
    
    <!-- ... más stats ... -->
</div>
```

---

### 10. Validar centros

**Vista Blade:** `resources/views/admin/centros/index.blade.php`  
**Ruta web:** `GET /admin/centros` y `PUT /admin/centros/{centro}/validar`  
**Diseño Stitch:** `diseño/validar_centros_reencuentro/code.html`  
**Middleware:** `auth`, `role:admin`

**Datos necesarios:**
- Lista de centros con conteos de niños y usuarios
- Filtro por estado (activos/pendientes)

**Endpoint API (para referencia):**
```
GET /api/admin/centros
PUT /api/admin/centros/{centro}/validar
```

**Controlador web a crear:**
```php
// app/Http/Controllers/Web/Admin/CentroController.php
public function index(Request $request)
{
    $query = Centro::withCount(['ninos', 'usuarios']);
    
    if ($request->has('estado')) {
        $query->where('activo', $request->estado === 'activos');
    }
    
    $centros = $query->orderBy('created_at', 'desc')->paginate(20);
    
    return view('admin.centros.index', compact('centros'));
}

public function validar(Centro $centro)
{
    if ($centro->activo) {
        return back()->with('error', 'Este centro ya está activo');
    }
    
    $centro->update(['activo' => true]);
    $centro->usuarios()->update([
        'activo' => true,
        'email_verified_at' => now(),
    ]);
    
    return back()->with('success', 'Centro validado exitosamente');
}
```

**Variables en Blade:**
```blade
<h1>Validar centros</h1>

<div class="tabs">
    <a href="{{ route('admin.centros.index', ['estado' => 'pendientes']) }}" 
       class="{{ request('estado') === 'pendientes' ? 'active' : '' }}">
        Pendientes
    </a>
    <a href="{{ route('admin.centros.index', ['estado' => 'activos']) }}"
       class="{{ request('estado') === 'activos' ? 'active' : '' }}">
        Activos
    </a>
</div>

@foreach($centros as $centro)
    <div class="centro-card">
        <h3>{{ $centro->nombre }}</h3>
        <p>Tipo: {{ $centro->tipo }}</p>
        <p>Email: {{ $centro->email }}</p>
        <p>Niños: {{ $centro->ninos_count }}</p>
        <p>Usuarios: {{ $centro->usuarios_count }}</p>
        
        @if(!$centro->activo)
            <form method="POST" action="{{ route('admin.centros.validar', $centro) }}">
                @csrf
                @method('PUT')
                <button type="submit">Validar centro</button>
            </form>
        @endif
    </div>
@endforeach

{{ $centros->links() }}
```

---

### 11. Configuración Mailjet

**Vista Blade:** `resources/views/admin/configuracion/mailjet.blade.php`  
**Ruta web:** `GET /admin/configuracion/mailjet` y `PUT /admin/configuracion/mailjet`  
**Diseño Stitch:** `diseño/configuraci_n_mailjet_reencuentro/code.html`  
**Middleware:** `auth`, `role:admin`

**Datos necesarios:**
- Configuración actual de Mailjet desde BD

**Endpoint API (para referencia):**
```
GET /api/admin/configuracion/mailjet
PUT /api/admin/configuracion/mailjet
POST /api/admin/configuracion/mailjet/test
```

**Controlador web a crear:**
```php
// app/Http/Controllers/Web/Admin/ConfiguracionController.php
public function mailjet()
{
    $config = Configuracion::where('grupo', 'mailjet')->get()->keyBy('clave');
    
    return view('admin.configuracion.mailjet', compact('config'));
}

public function updateMailjet(Request $request)
{
    $validated = $request->validate([
        'MAIL_MAILER' => 'required|in:smtp,mailjet,log',
        'MAILJET_API_KEY' => 'nullable|string',
        'MAILJET_SECRET_KEY' => 'nullable|string',
        'MAIL_FROM_ADDRESS' => 'nullable|email',
        'MAIL_FROM_NAME' => 'nullable|string|max:100',
        'MAIL_HOST' => 'nullable|string',
        'MAIL_PORT' => 'nullable|integer|min:1|max:65535',
    ]);
    
    foreach ($validated as $clave => $valor) {
        Configuracion::updateOrCreate(
            ['clave' => $clave],
            ['valor' => $valor, 'grupo' => 'mailjet']
        );
    }
    
    return back()->with('success', 'Configuración actualizada');
}

public function testMailjet()
{
    try {
        Mail::raw('Email de prueba desde Reencuentro', function ($message) {
            $message->to(auth()->user()->email)
                ->subject('[Reencuentro] Email de prueba');
        });
        
        return back()->with('success', 'Email de prueba enviado a ' . auth()->user()->email);
    } catch (\Exception $e) {
        return back()->with('error', 'Error al enviar email: ' . $e->getMessage());
    }
}
```

**Variables en Blade:**
```blade
<h1>Configuración de Mailjet</h1>

<form method="POST" action="{{ route('admin.configuracion.mailjet.update') }}">
    @csrf
    @method('PUT')
    
    <label>Driver de correo</label>
    <select name="MAIL_MAILER" required>
        <option value="log" {{ $config['MAIL_MAILER']->valor ?? '' === 'log' ? 'selected' : '' }}>Log (desarrollo)</option>
        <option value="smtp" {{ $config['MAIL_MAILER']->valor ?? '' === 'smtp' ? 'selected' : '' }}>SMTP</option>
        <option value="mailjet" {{ $config['MAIL_MAILER']->valor ?? '' === 'mailjet' ? 'selected' : '' }}>Mailjet</option>
    </select>
    
    <label>API Key</label>
    <input type="password" name="MAILJET_API_KEY" value="{{ $config['MAILJET_API_KEY']->valor ?? '' }}">
    
    <label>Secret Key</label>
    <input type="password" name="MAILJET_SECRET_KEY" value="{{ $config['MAILJET_SECRET_KEY']->valor ?? '' }}">
    
    <!-- ... más campos ... -->
    
    <button type="submit">Guardar configuración</button>
</form>

<form method="POST" action="{{ route('admin.configuracion.mailjet.test') }}">
    @csrf
    <button type="submit">Enviar email de prueba</button>
</form>
```

---

### 12. Humanitarian Unity System

**Diseño Stitch:** `diseño/humanitarian_unity_system/code.html`

Este es el sistema de diseño (design system) que define:
- Paleta de colores
- Tipografía
- Componentes reutilizables
- Estilos de botones, cards, badges, etc.

**Usar como referencia para:**
- Crear componentes Blade reutilizables en `resources/views/components/`
- Definir clases CSS comunes
- Mantener consistencia visual

---

## Estructura de carpetas a crear

```
resources/views/
├── layouts/
│   └── app.blade.php (layout principal)
├── components/
│   ├── navbar.blade.php
│   ├── footer.blade.php
│   ├── nino-card.blade.php
│   ├── alert.blade.php
│   └── badge-estado.blade.php
├── home.blade.php
├── ninos/
│   └── show.blade.php
├── auth/
│   ├── login.blade.php
│   ├── register.blade.php
│   └── activate.blade.php
├── gestor/
│   ├── dashboard.blade.php
│   ├── ninos/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── show.blade.php
│   └── recogidas/
│       └── create.blade.php
└── admin/
    ├── dashboard.blade.php
    ├── centros/
    │   └── index.blade.php
    └── configuracion/
        └── mailjet.blade.php
```

---

## Rutas web a crear

Crear en `routes/web.php`:

```php
// Rutas públicas
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/ninos/{nino}', [NinoController::class, 'show'])->name('ninos.show');

// Auth
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/registro', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/registro', [RegisterController::class, 'register']);

Route::get('/activar/{token}', [ActivationController::class, 'activate'])->name('activate');

// Gestor (protegidas)
Route::middleware(['auth', 'role:gestor', 'centro.activo'])->prefix('gestor')->name('gestor.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/ninos', [NinoController::class, 'index'])->name('ninos.index');
    Route::get('/ninos/crear', [NinoController::class, 'create'])->name('ninos.create');
    Route::post('/ninos', [NinoController::class, 'store'])->name('ninos.store');
    Route::get('/ninos/{nino}', [NinoController::class, 'show'])->name('ninos.show');
    Route::get('/ninos/{nino}/recogida', [RecogidaController::class, 'create'])->name('recogidas.create');
    Route::post('/ninos/{nino}/recogida', [RecogidaController::class, 'store'])->name('recogidas.store');
});

// Admin (protegidas)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/centros', [CentroController::class, 'index'])->name('centros.index');
    Route::put('/centros/{centro}/validar', [CentroController::class, 'validar'])->name('centros.validar');
    Route::get('/configuracion/mailjet', [ConfiguracionController::class, 'mailjet'])->name('configuracion.mailjet');
    Route::put('/configuracion/mailjet', [ConfiguracionController::class, 'updateMailjet'])->name('configuracion.mailjet.update');
    Route::post('/configuracion/mailjet/test', [ConfiguracionController::class, 'testMailjet'])->name('configuracion.mailjet.test');
});
```

---

## Prioridades de implementación

### Fase 1 — Vistas públicas (CRÍTICO)
1. Home (listado de niños)
2. Ficha del niño
3. Login
4. Registro de centro
5. Activación de cuenta

### Fase 2 — Vistas de gestor
6. Dashboard gestor
7. Registrar niño
8. Gestionar recogida

### Fase 3 — Vistas de admin
9. Dashboard admin
10. Validar centros
11. Configuración Mailjet

---

## Notas importantes

1. **Usar los diseños de Stitch como referencia visual**, no copiar el HTML directamente
2. **Seguir las convenciones de Laravel** para formularios, validación y autenticación
3. **Mantener la lógica de negocio en los controladores**, no en las vistas Blade
4. **Usar componentes Blade** para elementos reutilizables
5. **Implementar responsive design** usando Tailwind CSS
6. **Mostrar mensajes flash** de éxito/error después de acciones
7. **Validar en el frontend** con HTML5 y JavaScript básico
8. **Proteger rutas** con middleware de autenticación y roles

---

**Documento creado por:** `software-enginner`  
**Fecha:** 2026-06-30  
**Para:** `software-developer-maquetador`  
**Versión:** 1.0
