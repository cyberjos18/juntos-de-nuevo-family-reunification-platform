# Reglas de Seguridad Obligatorias para Vistas Blade

**Fecha:** 2026-06-30  
**Para:** `software-developer-maquetador`  
**Estado:** OBLIGATORIO — Sin excepciones

---

## 1. CSRF TOKENS — Obligatorio en todo formulario

### Formulario POST estándar
```blade
<form method="POST" action="{{ route('ninos.store') }}">
    @csrf
    <!-- campos -->
</form>
```

### Formulario con PUT/PATCH/DELETE
```blade
<form method="POST" action="{{ route('ninos.update', $nino) }}">
    @csrf
    @method('PUT')
    <!-- campos -->
</form>

<form method="POST" action="{{ route('ninos.destroy', $nino) }}">
    @csrf
    @method('DELETE')
    <!-- campos -->
</form>
```

### Para peticiones AJAX/Fetch
En el layout base (`layouts/app.blade.php`):
```blade
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- resto del head -->
</head>
```

En el JS:
```javascript
fetch(url, {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
});
```

**Regla:** NUNCA omitir `@csrf` en ningún formulario POST. Sin excepciones.

---

## 2. PROTECCIÓN XSS — Obligatorio en toda salida de datos

### SIEMPRE usar doble llave `{{ }}`
```blade
<p>{{ $nino->nombre }}</p>
<p>{{ $nino->notas }}</p>
<input type="text" value="{{ old('nombre', $nino->nombre) }}">
<a href="{{ route('ninos.show', $nino->id) }}">{{ $nino->nombre }}</a>
```

### NUNCA usar llaves con exclamación `{!! !!}`
```blade
<!-- ❌ PROHIBIDO -->
<p>{!! $nino->nombre !!}</p>
<p>{!! $nino->notas !!}</p>
```

**Excepción única:** Contenido HTML 100% controlado por el desarrollador (no proveniente del usuario ni de la BD), sanitizado con `htmlspecialchars()` o HTMLPurifier.

### Para contenido richtext (si aplica en el futuro)
Usar paquete `mews/purifier`:
```bash
composer require mews/purifier
```

```blade
{!! clean($contenido_richtext_sanitizado) !!}
```

---

## 3. VALIDACIÓN — Mostrar errores y preservar datos

### Todo input debe tener:
```blade
<div>
    <label for="nombre">Nombre *</label>
    <input type="text" 
           id="nombre" 
           name="nombre" 
           value="{{ old('nombre') }}" 
           required>
    @error('nombre')
        <span class="text-red-500 text-sm">{{ $message }}</span>
    @enderror
</div>
```

### Para selects:
```blade
<select name="tipo_centro" required>
    <option value="">Seleccionar...</option>
    <option value="proteccion_civil" {{ old('tipo_centro') == 'proteccion_civil' ? 'selected' : '' }}>
        Protección Civil
    </option>
    <option value="hospital" {{ old('tipo_centro') == 'hospital' ? 'selected' : '' }}>
        Hospital
    </option>
</select>
```

### Para checkboxes/radios:
```blade
<label>
    <input type="checkbox" name="activo" {{ old('activo') ? 'checked' : '' }}>
    Activo
</label>
```

---

## 4. PROTECCIÓN DE RUTAS

En `routes/web.php`:

```php
// Rutas públicas (sin autenticación)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/ninos/{nino}', [NinoController::class, 'show'])->name('ninos.show');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/registro', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/registro', [RegisterController::class, 'register']);
});

Route::get('/activar/{token}', [ActivationController::class, 'activate'])->name('activate');

// Logout (requiere auth)
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Gestor (protegidas)
Route::middleware(['auth', 'role:gestor', 'centro.activo'])
    ->prefix('gestor')
    ->name('gestor.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/ninos', [NinoController::class, 'index'])->name('ninos.index');
        Route::get('/ninos/crear', [NinoController::class, 'create'])->name('ninos.create');
        Route::post('/ninos', [NinoController::class, 'store'])->name('ninos.store');
        Route::get('/ninos/{nino}', [NinoController::class, 'show'])->name('ninos.show');
        Route::get('/ninos/{nino}/recogida', [RecogidaController::class, 'create'])->name('recogidas.create');
        Route::post('/ninos/{nino}/recogida', [RecogidaController::class, 'store'])->name('recogidas.store');
    });

// Admin (protegidas)
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/centros', [CentroController::class, 'index'])->name('centros.index');
        Route::put('/centros/{centro}/validar', [CentroController::class, 'validar'])->name('centros.validar');
        Route::get('/configuracion/mailjet', [ConfiguracionController::class, 'mailjet'])->name('configuracion.mailjet');
        Route::put('/configuracion/mailjet', [ConfiguracionController::class, 'updateMailjet'])->name('configuracion.mailjet.update');
        Route::post('/configuracion/mailjet/test', [ConfiguracionController::class, 'testMailjet'])->name('configuracion.mailjet.test');
    });
```

---

## 5. CHECKLIST DE SEGURIDAD POR VISTA

Antes de dar por terminada cada vista Blade, verificar:

- [ ] ¿Tiene `@csrf` todo formulario POST?
- [ ] ¿Tiene `@method('PUT')` o `@method('DELETE')` donde corresponda?
- [ ] ¿Usa `{{ }}` (doble llave) para TODAS las salidas de datos?
- [ ] ¿NO usa `{!! !!}` en ningún lugar?
- [ ] ¿Muestra errores con `@error('campo')` debajo de cada input?
- [ ] ¿Preserva datos con `{{ old('campo') }}`?
- [ ] ¿La ruta tiene middleware de autenticación si es protegida?
- [ ] ¿El meta tag CSRF está en el layout base para AJAX?

**Regla:** Si una vista no pasa el checklist, NO se considera terminada.

---

## 6. STACK TECNOLÓGICO APROBADO

### ✅ Permitido
- **Laravel 11** (backend)
- **Blade** (motor de plantillas)
- **Tailwind CSS v4** (estilos)
- **Alpine.js** (interactividad UI: dropdowns, modales, tabs, confirmaciones)
- **Vanilla JS moderno** (casos específicos: validaciones complejas, fetch a API)
- **Material Design 3** (guía visual)

### ❌ Prohibido
- **jQuery** (obsoleto, 87KB innecesarios)
- **React/Vue/Angular** (descartados por urgencia)
- **`{!! !!}`** (XSS vulnerability)
- **JavaScript inline en Blade** (excepto Alpine.js)
- **CSS inline en Blade** (excepto clases Tailwind)

---

## 7. ALPINE.JS — Guía de uso

### Instalación
```bash
npm install alpinejs
```

En `resources/js/app.js`:
```javascript
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
```

### Casos de uso comunes

#### Dropdown
```blade
<div x-data="{ open: false }" @click.outside="open = false">
    <button @click="open = !open">Usuario ▾</button>
    <div x-show="open" x-transition>
        <a href="{{ route('logout') }}">Cerrar sesión</a>
    </div>
</div>
```

#### Modal de confirmación
```blade
<div x-data="{ open: false }">
    <button @click="open = true">Eliminar</button>
    <div x-show="open" class="modal">
        <p>¿Confirmar eliminación?</p>
        <form method="POST" action="{{ route('ninos.destroy', $nino->id) }}">
            @csrf @method('DELETE')
            <button type="submit">Sí, eliminar</button>
            <button type="button" @click="open = false">Cancelar</button>
        </form>
    </div>
</div>
```

#### Formulario dinámico (agregar familiares)
```blade
<div x-data="{ 
    familiares: [], 
    agregar() { this.familiares.push({ parentesco: '', nombre: '', telefono: '' }) },
    quitar(index) { this.familiares.splice(index, 1) }
}">
    <template x-for="(fam, index) in familiares" :key="index">
        <div>
            <input :name="`familiares[${index}][parentesco]`" x-model="fam.parentesco" required>
            <input :name="`familiares[${index}][nombre]`" x-model="fam.nombre" required>
            <input :name="`familiares[${index}][telefono]`" x-model="fam.telefono">
            <button type="button" @click="quitar(index)">Quitar</button>
        </div>
    </template>
    <button type="button" @click="agregar()">+ Agregar familiar</button>
</div>
```

#### Tabs
```blade
<div x-data="{ tab: 'pendientes' }">
    <button :class="{ 'active': tab === 'pendientes' }" @click="tab = 'pendientes'">Pendientes</button>
    <button :class="{ 'active': tab === 'activos' }" @click="tab = 'activos'">Activos</button>
    
    <div x-show="tab === 'pendientes'">Contenido pendientes</div>
    <div x-show="tab === 'activos'">Contenido activos</div>
</div>
```

---

## 8. ESTRUCTURA DE CARPETAS OBLIGATORIA

```
resources/views/
├── layouts/
│   └── app.blade.php              ← Layout único con sidebar condicional por rol
├── components/
│   ├── navbar.blade.php
│   ├── footer.blade.php
│   ├── sidebar.blade.php
│   ├── nino-card.blade.php
│   ├── alert.blade.php            ← Componente <x-alert> con auto-dismiss
│   ├── badge-estado.blade.php
│   └── stat-card.blade.php
├── partials/
│   └── flash-messages.blade.php
├── home/
│   └── index.blade.php
├── auth/
│   ├── login.blade.php
│   ├── register.blade.php
│   └── activate.blade.php
├── ninos/
│   └── show.blade.php
├── gestor/
│   ├── dashboard/
│   │   └── index.blade.php
│   ├── ninos/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── show.blade.php
│   └── recogidas/
│       └── create.blade.php
└── admin/
    ├── dashboard/
    │   └── index.blade.php
    ├── centros/
    │   └── index.blade.php
    └── configuracion/
        └── mailjet.blade.php
```

**Regla:** Cada vista DEBE estar en su propia carpeta. No se aceptan archivos Blade sueltos sin organización.

---

## 9. SEPARACIÓN DE CÓDIGO

### JavaScript
- **Dónde:** `resources/js/*.js` (archivos modulares)
- **Incluir con:** `@vite(['resources/css/app.css', 'resources/js/app.js'])` en el layout
- **Permitido en Blade:** Solo Alpine.js (atributos `x-*`)
- **Prohibido en Blade:** `<script>...</script>`, `onclick="..."`, `onsubmit="..."`

### CSS
- **Dónde:** `resources/css/*.css` (archivos modulares)
- **Incluir con:** `@vite(['resources/css/app.css', 'resources/js/app.js'])` en el layout
- **Permitido en Blade:** Solo clases Tailwind
- **Prohibido en Blade:** `<style>...</style>`, `style="..."`

### Lógica PHP
- **Dónde:** Controladores web
- **Prohibido en Blade:** Consultas a BD, lógica de negocio compleja, cálculos pesados

---

**Documento aprobado por:** `software-enginner`  
**Fecha:** 2026-06-30  
**Estado:** OBLIGATORIO
