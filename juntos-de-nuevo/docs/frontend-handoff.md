# Handoff para software-developer-frontend

## Contexto

Plataforma humanitaria **Reencuentro** para reunificación de niños con familias tras terremotos en Venezuela. Debe funcionar perfectamente en móviles, con acceso a cámara y carga rápida en conexiones lentas.

**Stack confirmado:**
- **Framework:** React + Vite
- **Estilos:** Tailwind CSS
- **HTTP:** Axios
- **Router:** React Router v6
- **Estado:** Context API o Zustand (simple)
- **Cámara:** MediaDevices API + fallback a input file

**Backend API:** Laravel 11 en `http://localhost:8000/api`

---

## ⚠️ Diseño visual pendiente

**IMPORTANTE:** El usuario está creando diseños en **Google Stitch**.

El flujo es:
1. Usuario crea diseños en Stitch
2. Usuario entrega diseños a `software-enginner`
3. `software-enginner` actualiza este documento con referencias a Stitch
4. Frontend implementa siguiendo los diseños

**Por ahora:** Implementar con Tailwind CSS, mobile-first, diseño funcional pero sin pixel-perfect. Se refinará cuando lleguen los diseños de Stitch.

---

## Objetivo frontend

- Interfaz responsive mobile-first
- Listado visual de niños (público)
- Ficha detallada con datos sensibles protegidos
- Formularios para registro, login, niños y recogidas
- Integración con cámara del dispositivo
- PWA básica para uso offline parcial

---

## Estructura del proyecto

```
frontend/
├── public/
│   └── favicon.ico
├── src/
│   ├── components/
│   │   ├── common/
│   │   │   ├── Navbar.jsx
│   │   │   ├── LoadingSpinner.jsx
│   │   │   ├── ErrorBoundary.jsx
│   │   │   ├── ProtectedRoute.jsx
│   │   │   └── Toast.jsx
│   │   ├── ninos/
│   │   │   ├── NinoCard.jsx
│   │   │   └── NinoDetail.jsx
│   │   ├── camera/
│   │   │   └── CameraCapture.jsx
│   │   └── forms/
│   │       ├── LoginForm.jsx
│   │       ├── CentroRegisterForm.jsx
│   │       ├── NinoForm.jsx
│   │       └── RecogidaForm.jsx
│   ├── pages/
│   │   ├── Home.jsx
│   │   ├── NinoPage.jsx
│   │   ├── Login.jsx
│   │   ├── RegistroCentro.jsx
│   │   ├── Activacion.jsx
│   │   ├── gestor/
│   │   │   ├── Dashboard.jsx
│   │   │   ├── NuevoNino.jsx
│   │   │   └── GestionarRecogida.jsx
│   │   └── admin/
│   │       ├── Dashboard.jsx
│   │       └── Centros.jsx
│   ├── hooks/
│   │   ├── useAuth.jsx
│   │   ├── useCamera.jsx
│   │   └── useApi.jsx
│   ├── context/
│   │   └── AuthContext.jsx
│   ├── services/
│   │   └── api.js (Axios instance)
│   ├── utils/
│   │   └── helpers.js
│   ├── App.jsx
│   ├── main.jsx
│   └── index.css (Tailwind)
├── .env
├── package.json
├── vite.config.js
└── tailwind.config.js
```

---

## Pantallas necesarias

### Públicas

| Pantalla | Ruta | Componente | Descripción |
|----------|------|------------|-------------|
| Home | `/` | `Home.jsx` | Listado visual de niños |
| Ficha Niño | `/nino/:id` | `NinoPage.jsx` | Detalles completos |
| Login | `/login` | `Login.jsx` | Acceso usuarios |
| Registro Centro | `/registro-centro` | `RegistroCentro.jsx` | Formulario nuevo centro |
| Activación | `/activar/:token` | `Activacion.jsx` | Confirmación email |

### Gestor (protegidas)

| Pantalla | Ruta | Componente | Descripción |
|----------|------|------------|-------------|
| Dashboard | `/gestor/dashboard` | `Dashboard.jsx` | Resumen y acciones |
| Registrar Niño | `/gestor/ninos/nuevo` | `NuevoNino.jsx` | Formulario registro |
| Gestionar Recogida | `/gestor/ninos/:id/recogida` | `GestionarRecogida.jsx` | Proceso de recogida |

### Admin (protegidas)

| Pantalla | Ruta | Componente | Descripción |
|----------|------|------------|-------------|
| Dashboard | `/admin/dashboard` | `admin/Dashboard.jsx` | Gestión del sistema |
| Validar Centros | `/admin/centros` | `admin/Centros.jsx` | Lista de centros pendientes |

---

## Rutas en React Router

```jsx
const routes = [
    // Públicas
    { path: '/', element: <Home /> },
    { path: '/nino/:id', element: <NinoPage /> },
    { path: '/login', element: <Login /> },
    { path: '/registro-centro', element: <RegistroCentro /> },
    { path: '/activar/:token', element: <Activacion /> },
    
    // Gestor (protegidas)
    { path: '/gestor/dashboard', element: <ProtectedRoute role="gestor"><GestorDashboard /></ProtectedRoute> },
    { path: '/gestor/ninos/nuevo', element: <ProtectedRoute role="gestor"><NuevoNino /></ProtectedRoute> },
    { path: '/gestor/ninos/:id/recogida', element: <ProtectedRoute role="gestor"><GestionarRecogida /></ProtectedRoute> },
    
    // Admin (protegidas)
    { path: '/admin/dashboard', element: <ProtectedRoute role="admin"><AdminDashboard /></ProtectedRoute> },
    { path: '/admin/centros', element: <ProtectedRoute role="admin"><Centros /></ProtectedRoute> },
];
```

---

## Componentes principales

### 1. CameraCapture (CRÍTICO)

**Descripción:** Componente para tomar fotos con la cámara del dispositivo.

**Requisitos:**
- Usar `navigator.mediaDevices.getUserMedia()` para acceso a cámara
- Fallback a `<input type="file" accept="image/*" capture="environment">` si no hay permiso
- Preview de la foto antes de enviar
- Opción de retomar foto
- Comprimir imagen antes de enviar (max 2MB)

**Props:**
```jsx
<CameraCapture 
    onCapture={(imageFile) => {}}  // Callback con la imagen
    onCancel={() => {}}             // Callback al cancelar
    facingMode="environment"        // 'user' o 'environment' (cámara trasera)
/>
```

**Lógica:**
```jsx
// Intentar acceso a cámara
try {
    const stream = await navigator.mediaDevices.getUserMedia({ 
        video: { facingMode: 'environment' } 
    });
    // Mostrar video stream
    // Botón para capturar
    // Convertir canvas a blob y comprimir
} catch (error) {
    // Fallback a input file
    <input type="file" accept="image/*" capture="environment" />
}
```

### 2. NinoCard

**Descripción:** Tarjeta visual para el listado de niños.

**Props:**
```jsx
<NinoCard 
    id={1}
    foto="url"
    nombre="Carlos"
    apellido="Rodríguez"
    centro="Protección Civil Central"
    estado="encontrado"
/>
```

**Diseño:**
- Grid responsive: 1 col móvil, 2 col tablet, 3 col desktop
- Foto cuadrada (aspect-ratio 1:1)
- Nombre y centro visibles
- Badge de estado con color
- Click → navegar a ficha

### 3. NinoDetail

**Descripción:** Ficha completa del niño.

**Props:**
```jsx
<NinoDetail nino={...} />
```

**Lógica de visibilidad:**
- **Público:** foto, nombre, edad, centro, estado
- **Autenticado:** + teléfono de centro, familiares conocidos
- **Gestor/Admin:** + todos los datos sensibles

### 4. ProtectedRoute

**Descripción:** Wrapper para rutas protegidas.

```jsx
<ProtectedRoute role="gestor">
    <Componente />
</ProtectedRoute>
```

**Lógica:**
- Si no hay token → redirect a `/login`
- Si token pero rol no coincide → mostrar "No autorizado"
- Si token y rol OK → renderizar children

### 5. Navbar

**Descripción:** Navegación condicional por rol.

**Estados:**
- **No autenticado:** Home, Login, Registro Centro
- **Gestor:** Home, Dashboard, Registrar Niño, Logout
- **Admin:** Home, Dashboard Admin, Validar Centros, Logout

---

## Flujos de usuario

### Flujo 1: Público buscando niño

```
Home (listado) 
  → Click en NinoCard 
  → Ver ficha NinoDetail 
  → Si encuentra coincidencia: contactar centro (teléfono/email)
```

### Flujo 2: Gestor registrando niño

```
Login → Dashboard → Botón "Registrar Niño"
  → Formulario con datos
  → Tomar foto con CameraCapture
  → Submit → Ver niño en Home
```

### Flujo 3: Gestor gestionando recogida

```
Login → Dashboard → Buscar niño en listado
  → Click "Registrar Recogida"
  → Formulario con datos del familiar
  → Tomar foto del niño con familiar (CameraCapture)
  → Submit → Niño marcado como "recogido"
```

### Flujo 4: Centro registrándose

```
Registro Centro (formulario público)
  → Submit → Backend crea centro + usuario inactivo
  → Email de activación enviado
  → Click en link email → Activación exitosa
  → Login con credenciales
```

---

## Datos necesarios por pantalla

### Home
```json
{
    "ninos": [
        {
            "id": 1,
            "foto_url": "storage/ninos/foto.jpg",
            "nombre": "Carlos",
            "apellido": "Rodríguez",
            "centro": { "nombre": "Protección Civil Central" },
            "estado": "encontrado"
        }
    ]
}
```

### Ficha Niño
```json
{
    "id": 1,
    "foto_url": "storage/ninos/foto.jpg",
    "nombre": "Carlos",
    "apellido": "Rodríguez",
    "edad": 5,
    "fecha_nacimiento": "2021-03-15",
    "sexo": "M",
    "direccion_previa": "Edificio Los Jardines",
    "nombre_madre": "Ana Rodríguez",
    "nombre_padre": "Pedro Rodríguez",
    "estado": "encontrado",
    "centro": { "nombre": "PC Central", "telefono": "+58...", "email": "..." },
    "familiares": [
        { "parentesco": "Tía", "nombre": "Rosa Hernández", "telefono": "+58..." }
    ]
}
```

---

## Estados de UI

### Loading
- **Skeleton cards** en listado (Home)
- **Spinner** en formularios durante submit
- **Pulse animation** en detalles

```jsx
// Skeleton Card
<div className="animate-pulse bg-gray-200 rounded-lg h-64"></div>
```

### Empty
```jsx
// Home sin niños
<div className="text-center py-12">
    <h2 className="text-xl text-gray-600">No hay niños registrados</h2>
    <p className="text-gray-500">Los centros están trabajando para registrar casos</p>
</div>
```

### Error
```jsx
// Banner de error
<div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
    <p>Error al cargar datos. <button>Reintentar</button></p>
</div>
```

### Success
```jsx
// Toast de éxito
<div className="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded shadow-lg">
    ✓ Niño registrado exitosamente
</div>
```

### Unauthorized
```jsx
// Página de no autorizado
<div className="text-center py-12">
    <h2 className="text-2xl font-bold text-red-600">No autorizado</h2>
    <p className="text-gray-600">No tienes permisos para acceder a esta página</p>
</div>
```

### Validation error
```jsx
// Error inline en campo
<div>
    <input className="border-red-500" />
    <p className="text-red-500 text-sm mt-1">El nombre es obligatorio</p>
</div>
```

---

## Validaciones frontend

| Campo | Regla | Mensaje |
|-------|-------|---------|
| Email | `^[^@]+@[^@]+\.[^@]+$` | "Email inválido" |
| Teléfono | `^\+?[\d\s-]{7,20}$` | "Teléfono inválido" |
| Edad | `0-17` | "Edad debe ser entre 0 y 17 años" |
| Foto | `image/jpeg, image/png`, max 5MB | "Imagen JPG/PNG, máximo 5MB" |
| Nombre | `required, max 100` | "Nombre obligatorio, máximo 100 caracteres" |
| Campos requeridos | `required` | "[Campo] es obligatorio" |

---

## Integración con backend

### Configuración Axios

```javascript
// src/services/api.js
import axios from 'axios';

const api = axios.create({
    baseURL: 'http://localhost:8000/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

// Interceptor para agregar token
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Interceptor para manejar errores 401
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export default api;
```

### Llamadas API principales

```javascript
// Auth
await api.post('/auth/register', { tipo_centro, nombre_centro, nombre, email, cargo });
await api.post('/auth/activate/' + token);
await api.post('/auth/login', { email, password });
await api.post('/auth/logout');

// Niños
const { data } = await api.get('/ninos');  // Listado público
const { data } = await api.get(`/ninos/${id}`);  // Ficha
await api.post('/ninos', formData, { headers: { 'Content-Type': 'multipart/form-data' } });

// Recogidas
await api.post(`/ninos/${id}/recogida`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });

// Admin
const { data } = await api.get('/admin/centros');
await api.put(`/admin/centros/${id}/validar`);
```

---

## Configuración inicial

### 1. Crear proyecto React

```bash
cd /c/wamp64/www/findchild
npm create vite@latest frontend -- --template react
cd frontend
npm install
npm install react-router-dom axios tailwindcss postcss autoprefixer
npx tailwindcss init -p
```

### 2. Configurar Tailwind

**tailwind.config.js:**
```javascript
export default {
    content: ['./index.html', './src/**/*.{js,jsx}'],
    theme: { extend: {} },
    plugins: [],
}
```

**src/index.css:**
```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

### 3. Configurar Vite

**vite.config.js:**
```javascript
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [react()],
    server: {
        port: 5173,
        proxy: {
            '/api': {
                target: 'http://localhost:8000',
                changeOrigin: true,
            },
        },
    },
});
```

### 4. Variables de entorno

**.env:**
```env
VITE_API_URL=http://localhost:8000/api
```

---

## Testing recomendado

### 1. Testing manual

- **Responsive:** Probar en móvil (375px), tablet (768px), desktop (1920px)
- **Cámara:** Probar en Chrome móvil, Safari iOS, Firefox
- **Flujos críticos:** Registro, login, niño, recogida
- **Errores:** Sin internet, API down, token expirado

### 2. Testing automatizado (futuro)

```bash
npm install --save-dev @testing-library/react @testing-library/jest-dom vitest
npm install --save-dev cypress  # E2E testing
```

---

## Diseño visual (Stitch) - PENDIENTE

**Estado:** Esperando diseños del usuario.

**Cuando lleguen los diseños, actualizar esta sección con:**
- Referencias a archivos de Stitch
- Capturas de pantalla de cada pantalla
- Componentes identificados
- Paleta de colores
- Tipografía
- Espaciados y breakpoints

**Por ahora:** Implementar con Tailwind por defecto, mobile-first, accesible.

---

## Prioridad de implementación

### 1. Setup inicial (Día 1)
- [ ] Crear proyecto React + Vite
- [ ] Instalar dependencias
- [ ] Configurar Tailwind
- [ ] Configurar React Router
- [ ] Crear estructura de carpetas

### 2. Componentes base (Día 2)
- [ ] Navbar
- [ ] ProtectedRoute
- [ ] LoadingSpinner
- [ ] Toast/Notifications
- [ ] CameraCapture (CRÍTICO)

### 3. Autenticación (Día 3)
- [ ] LoginForm
- [ ] AuthContext
- [ ] useAuth hook
- [ ] Página Login
- [ ] Página Registro Centro
- [ ] Página Activación

### 4. Módulo Niños (Día 4-5)
- [ ] NinoCard
- [ ] NinoDetail
- [ ] Home (listado)
- [ ] Ficha de niño
- [ ] NinoForm (registro)
- [ ] Página Nuevo Niño

### 5. Módulo Recogidas (Día 6)
- [ ] RecogidaForm
- [ ] Página Gestionar Recogida
- [ ] Integración con cámara

### 6. Administración (Día 7)
- [ ] Dashboard Admin
- [ ] Lista de centros pendientes
- [ ] Validación de centros

### 7. Pulido y responsive (Día 8)
- [ ] Testing responsive
- [ ] Optimización de imágenes
- [ ] Accesibilidad básica
- [ ] Performance (lazy loading)

---

**Documento creado por:** `software-enginner`  
**Fecha:** 2026-06-29  
**Versión:** 1.0  
**Para:** `software-developer-frontend`
