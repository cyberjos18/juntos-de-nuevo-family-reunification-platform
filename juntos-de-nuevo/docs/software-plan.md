# Software Engineering Plan — Reencuentros Venezuela (Reencuentro)

## 1. Resumen del producto

**Nombre:** `Reencuentro` (Plataforma de Reunificación Familiar)

Plataforma web humanitaria para facilitar la reunificación de niños separados de sus familias tras los terremotos en Venezuela. Permite registrar niños encontrados, visualizarlos públicamente y gestionar el proceso de recogida por familiares verificados.

---

## 2. Problema que resuelve

- Niños aparecen sin familia tras los terremotos
- Familias buscan desesperadamente a sus hijos
- No hay un sistema centralizado para cruzar información
- Los centros de acogida necesitan una herramienta rápida y accesible desde móvil

---

## 3. Objetivo del sistema

- Centralizar el registro de niños encontrados
- Facilitar la búsqueda visual por parte de familias
- Gestionar el proceso de recogida con verificación y expediente
- Permitir que centros de acogida se registren y gestionen sus casos

---

## 4. Usuarios y roles

| Rol | Descripción | Acceso |
|-----|-------------|--------|
| **Público** | Cualquier persona buscando familia | Solo lectura: ver listado y fichas de niños |
| **Gestor** | Personal de centros de acogida | Registro de niños, gestión de recogidas, ver sus casos |
| **Administrador** | Superusuario único | Gestión de usuarios, validación de centros, estadísticas, todo el sistema |

---

## 5. Alcance del MVP

### Incluido en MVP
- ✅ Registro de niños con foto y datos básicos
- ✅ Home público con listado visual de niños
- ✅ Ficha detallada de cada niño
- ✅ Registro de centros (formulario + activación por email)
- ✅ Login para Gestor y Administrador
- ✅ Proceso de recogida (datos + foto con familiar)
- ✅ Diseño responsive (móvil primero)
- ✅ Acceso a cámara desde el navegador

### No incluido en MVP
- ❌ Notificaciones push/SMS a familias
- ❌ Búsqueda avanzada con filtros complejos
- ❌ Reportes estadísticos avanzados
- ❌ API pública para terceros
- ❌ Multi-idioma

---

## 6. Funcionalidades principales

| Módulo | Funcionalidad |
|--------|---------------|
| **Niños** | Registrar, listar, ver ficha, actualizar estado |
| **Centros** | Registro, activación por email, gestión de usuarios Gestor |
| **Recogidas** | Registrar recogida, datos del familiar, foto de evidencia |
| **Autenticación** | Login, registro de Gestor, activación por email |
| **Administración** | Validar centros, gestionar usuarios, ver estadísticas |

---

## 7. Funcionalidades futuras

- Búsqueda por edad, ubicación, fecha
- Filtros por centro de acogida
- Exportación de datos para autoridades
- Historial de movimientos del niño
- Comunicación segura entre centros y familias

---

## 8. Módulos del sistema

```
┌─────────────────────────────────────────────────────────┐
│                    PLATAFORMA WEB                        │
├─────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────┐  │
│  │   Módulo    │  │   Módulo    │  │   Módulo        │  │
│  │   Niños     │  │  Centros    │  │   Recogidas     │  │
│  └─────────────┘  └─────────────┘  └─────────────────┘  │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────┐  │
│  │   Módulo    │  │   Módulo    │  │   Módulo        │  │
│  │   Auth      │  │  Admin      │  │   Notificaciones│  │
│  └─────────────┘  └─────────────┘  └─────────────────┘  │
├─────────────────────────────────────────────────────────┤
│                    LARAVEL BACKEND                       │
│                    (API REST + JWT)                      │
├─────────────────────────────────────────────────────────┤
│                    REACT FRONTEND                        │
│                    (SPA + Tailwind)                      │
├─────────────────────────────────────────────────────────┤
│                    BASE DE DATOS                         │
│                    (MySQL via WAMP)                      │
│                    ALMACENAMIENTO FOTOS                  │
│                    (storage/app/public)                  │
└─────────────────────────────────────────────────────────┘
```

---

## 9. Reglas de negocio

1. **Cualquiera puede ver** el listado de niños (página pública)
2. **Solo Gestores** pueden registrar niños y gestionar recogidas
3. **Solo Administrador** puede validar centros y crear otros Administradores
4. **Un niño solo puede ser recogido una vez** (cambio de estado irreversible)
5. **La foto de recogida es obligatoria** para completar el proceso
6. **El email debe confirmarse** antes de activar un usuario Gestor
7. **Los datos sensibles** (teléfonos, emails) solo visibles para usuarios autenticados

---

## 10. Flujos principales

### Flujo 1: Registro de niño encontrado
```
Gestor logueado → Nuevo niño → Subir foto → Rellenar datos → Guardar → Niño visible en Home
```

### Flujo 2: Búsqueda de familia (Público)
```
Home → Ver listado → Click en niño → Ver ficha detallada → Contactar centro (si está habilitado)
```

### Flujo 3: Recogida de niño
```
Gestor → Buscar niño → Iniciar proceso de recogida → Rellenar datos familiar → Tomar foto → Confirmar → Niño marcado como "Recogido"
```

### Flujo 4: Registro de centro
```
Formulario público → Rellenar datos centro → Confirmar email → Administrador valida → Usuario Gestor activado
```

---

## 11. Modelo conceptual de datos

```
┌──────────────┐       ┌──────────────┐       ┌──────────────┐
│    CENTRO    │       │     NIÑO     │       │   FAMILIAR   │
├──────────────┤       ├──────────────┤       ├──────────────┤
│ id           │       │ id           │       │ id           │
│ nombre       │       │ centro_id    │◄──────│ niño_id      │
│ tipo         │       │ foto         │       │ nombre       │
│ email        │       │ nombre       │       │ apellido     │
│ telefono     │       │ apellido     │       │ parentesco   │
│ direccion    │       │ edad         │       │ telefono     │
│ activo       │       │ fecha_nac    │       │ email        │
│ creado_en    │       │ sexo         │       │ foto_recogida│
└──────────────┘       │ direccion    │       │ fecha_recogida│
         ▲             │ estado       │       └──────────────┘
         │             │ creado_por   │
         │             │ fecha_registro│
         │             └──────────────┘
         │
         │             ┌──────────────┐
         └────────────►│   USUARIO    │
                       ├──────────────┤
                       │ id           │
                       │ centro_id    │
                       │ nombre       │
                       │ email        │
                       │ password     │
                       │ rol          │
                       │ activo       │
                       └──────────────┘
```

---

## 12. Entidades y relaciones

| Entidad | Relaciones | Cardinalidad |
|---------|------------|--------------|
| **Centro** | Tiene muchos Niños | 1:N |
| **Centro** | Tiene muchos Usuarios Gestor | 1:N |
| **Niño** | Pertenece a un Centro | N:1 |
| **Niño** | Tiene una Recogida (opcional) | 1:0..1 |
| **Usuario** | Pertenece a un Centro | N:1 |
| **Familiar** | Recoge un Niño | 1:1 |

---

## 13. Estados y eventos importantes

### Estados del Niño
- `ENCONTRADO` — Registrado, buscando familia
- `FAMILIA_LOCALIZADA` — Familia contactada, en proceso
- `RECOGIDO` — Recogido por familiar (cierre del caso)
- `OTRO_CENTRO` — Transferido a otro centro

### Eventos del dominio
- `NiñoRegistrado` — Cuando se crea el registro
- `NiñoVisible` — Cuando se activa para búsqueda pública
- `FamiliaContactada` — Cuando hay un familiar interesado
- `NiñoRecogido` — Cuando se completa la recogida

---

## 14. Arquitectura recomendada

```
┌─────────────────────────────────────────────────────────────┐
│                         FRONTEND                             │
│                    (React + Vite + Tailwind)                 │
│              Responsive - Mobile First - PWA                 │
│              Puerto: 5173 (dev) / Build estático            │
├─────────────────────────────────────────────────────────────┤
│                         BACKEND                              │
│                    (Laravel 11 + PHP 8.2+)                   │
│              API REST - JWT Auth - Sanctum                   │
│              Puerto: 8000 (php artisan serve)                │
├─────────────────────────────────────────────────────────────┤
│                       BASE DE DATOS                          │
│                    (MySQL 8 via WAMP64)                      │
│              Puerto: 3306                                    │
├─────────────────────────────────────────────────────────────┤
│                    ALMACENAMIENTO                            │
│              (storage/app/public → symlink public/storage)   │
│                   Fotos de niños y recogidas                 │
└─────────────────────────────────────────────────────────────┘
```

**Stack técnico confirmado:**
- **Backend:** Laravel 11 (ya instalado en `C:\wamp64\www\findchild`)
- **Frontend:** React + Vite + Tailwind CSS (a crear)
- **Base de datos:** MySQL 8 (WAMP64)
- **Autenticación:** Laravel Sanctum (JWT-like tokens)
- **Fotos:** `storage/app/public` con symlink a `public/storage`

---

## 15. Backend plan

**Arquitectura Laravel:**

| Capa | Ubicación | Responsabilidad |
|------|-----------|-----------------|
| **Controllers** | `app/Http/Controllers/Api` | Endpoints API, validación, respuestas JSON |
| **Requests** | `app/Http/Requests` | Validación de entrada, reglas, mensajes |
| **Services** | `app/Services` | Lógica de negocio, reglas, orquestación |
| **Repositories** | `app/Repositories` | Acceso a base de datos, queries |
| **Models** | `app/Models` | Eloquent models, relaciones, scopes |
| **Resources** | `app/Http/Resources` | Transformación de datos a JSON |
| **Middleware** | `app/Http/Middleware` | Auth, permisos por rol, logging |
| **Events/Observers** | `app/Events`, `app/Observers` | Eventos del dominio, audit trail |

**Estructura de carpetas recomendada:**
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
│   │   ├── StoreCentroRequest.php
│   │   ├── StoreNinoRequest.php
│   │   └── StoreRecogidaRequest.php
│   ├── Resources/
│   │   ├── NinoResource.php
│   │   └── CentroResource.php
│   └── Middleware/
│       └── CheckRole.php
├── Models/
│   ├── Centro.php
│   ├── User.php
│   ├── Nino.php
│   └── Recogida.php
├── Services/
│   ├── AuthService.php
│   ├── NinoService.php
│   └── RecogidaService.php
└── Observers/
    └── NinoObserver.php
```

---

## 16. Frontend plan

### Pantallas necesarias

| Pantalla | Ruta | Acceso | Descripción |
|----------|------|--------|-------------|
| Home | `/` | Público | Listado visual de niños |
| Ficha Niño | `/nino/:id` | Público | Detalles completos |
| Login | `/login` | Público | Acceso usuarios |
| Registro Centro | `/registro-centro` | Público | Formulario nuevo centro |
| Activación | `/activar/:token` | Público | Confirmación email |
| Dashboard Gestor | `/gestor/dashboard` | Gestor | Resumen y acciones |
| Registrar Niño | `/gestor/ninos/nuevo` | Gestor | Formulario registro |
| Gestionar Recogida | `/gestor/ninos/:id/recogida` | Gestor | Proceso de recogida |
| Dashboard Admin | `/admin/dashboard` | Admin | Gestión del sistema |
| Validar Centros | `/admin/centros` | Admin | Lista de centros pendientes |

### Componentes principales
- `NiñoCard` — Tarjeta visual para listado
- `NiñoDetail` — Ficha completa
- `CameraCapture` — Componente para tomar fotos
- `LoginForm` — Autenticación
- `CentroRegisterForm` — Registro de centros
- `RecogidaForm` — Formulario de recogida
- `ProtectedRoute` — Wrapper de rutas protegidas
- `Navbar` — Navegación condicional por rol
- `LoadingSpinner` — Estados de carga
- `ErrorBoundary` — Manejo de errores

### Estados de UI requeridos
- Loading (carga de datos)
- Empty (sin resultados)
- Error (fallos de API)
- Success (operaciones completadas)
- Unauthorized (sin permisos)
- Validation error (formularios)

---

## 17. Database design plan

Ver `docs/database-handoff.md` para detalles completos del diseño de base de datos.

---

## 18. APIs necesarias

| Método | Endpoint | Auth | Rol | Descripción |
|--------|----------|------|-----|-------------|
| POST | `/api/auth/register` | No | — | Registro de centro |
| POST | `/api/auth/activate/:token` | No | — | Activar usuario |
| POST | `/api/auth/login` | No | — | Login |
| POST | `/api/auth/logout` | Sí | Todos | Logout |
| GET | `/api/ninos` | No | — | Listado público |
| GET | `/api/ninos/:id` | No | — | Ficha niño |
| POST | `/api/ninos` | Sí | Gestor | Registrar niño |
| PUT | `/api/ninos/:id` | Sí | Gestor | Actualizar niño |
| POST | `/api/ninos/:id/recogida` | Sí | Gestor | Registrar recogida |
| GET | `/api/gestor/ninos` | Sí | Gestor | Mis niños registrados |
| GET | `/api/admin/centros` | Sí | Admin | Centros pendientes |
| PUT | `/api/admin/centros/:id/validar` | Sí | Admin | Validar centro |
| GET | `/api/admin/estadisticas` | Sí | Admin | Dashboard stats |

---

## 19. Seguridad y permisos

| Recurso | Público | Gestor | Admin |
|---------|---------|--------|-------|
| Ver listado niños | ✅ | ✅ | ✅ |
| Ver ficha niño | ✅ | ✅ | ✅ |
| Ver datos sensibles (teléfono) | ❌ | ✅ | ✅ |
| Registrar niño | ❌ | ✅ | ✅ |
| Registrar recogida | ❌ | ✅ | ✅ |
| Validar centros | ❌ | ❌ | ✅ |
| Crear admin | ❌ | ❌ | ✅ |
| Ver todos los centros | ❌ | ❌ | ✅ |

**Medidas de seguridad:**
- Laravel Sanctum para autenticación API
- Password hash con bcrypt
- HTTPS obligatorio en producción
- Rate limiting con Laravel RateLimiter
- Validación de archivos (fotos)
- Sanitización de inputs
- CORS configurado para el frontend React

---

## 20. Riesgos técnicos

| Riesgo | Impacto | Mitigación |
|--------|---------|------------|
| Acceso a cámara en móviles | Alto | Usar API `navigator.mediaDevices`, fallback a upload manual |
| Fotos pesadas | Medio | Compresión en cliente, límites de tamaño, lazy loading |
| Conexión lenta en zona de desastre | Alto | PWA con cache, imágenes optimizadas, mínimo JS |
| Datos sensibles expuestos | Alto | Separar datos públicos/privados, autenticación estricta |
| Spam de registros falsos | Medio | Validación manual por Admin, email requerido |
| Escalabilidad súbita | Medio | CDN para imágenes, base de datos con índices, caching |

---

## 21. Roadmap de implementación

### Fase 1 — Base del sistema (Semana 1)
- [ ] Configurar Laravel (ya hecho)
- [ ] Diseñar base de datos y migraciones
- [ ] Implementar autenticación (registro, login, activación)
- [ ] Sistema de roles y permisos
- [ ] Configurar almacenamiento de fotos

### Fase 2 — Módulo principal: Niños (Semana 2)
- [ ] CRUD de niños (backend Laravel)
- [ ] Home público con listado (frontend React)
- [ ] Ficha detallada de niño (frontend React)
- [ ] Componente de cámara para fotos
- [ ] Integración frontend-backend

### Fase 3 — Módulo: Recogidas (Semana 3)
- [ ] Endpoint de registro de recogida
- [ ] Formulario de recogida con cámara
- [ ] Cambio de estado del niño
- [ ] Historial de recogidas

### Fase 4 — Administración (Semana 4)
- [ ] Dashboard de Admin
- [ ] Validación de centros
- [ ] Estadísticas básicas
- [ ] Gestión de usuarios

### Fase 5 — Calidad y despliegue (Semana 5)
- [ ] Testing de flujos críticos
- [ ] Optimización móvil
- [ ] Variables de entorno
- [ ] Deploy a producción
- [ ] Documentación de uso

---

## 22. Handoff para software-developer-backend

Ver documento separado: `docs/backend-handoff.md`

---

## 23. Handoff para software-developer-frontend

Ver documento separado: `docs/frontend-handoff.md`

---

## 24. Handoff para db-design-developer

Ver documento separado: `docs/database-handoff.md`

---

## 25. Preguntas abiertas

| Pregunta | Impacto | Recomendación |
|----------|---------|---------------|
| ¿Dónde se alojará la aplicación en producción? | Infraestructura | Recomendaría Vercel (front) + Railway/Render (back) o un VPS |
| ¿Hay presupuesto para servicios cloud? | Almacenamiento | Si no, usar almacenamiento local; si sí, AWS S3 o Cloudinary |
| ¿Se necesita multi-idioma (español/inglés)? | Frontend | Dejar preparado desde el inicio con i18n |
| ¿Quién gestionará el Administrador inicial? | Seguridad | Crear seeder o comando CLI para primer admin |
| ¿Se requiere backup automático de datos? | Infraestructura | Sí, crítico para datos humanitarios |

---

## 26. Diseño visual (Stitch)

**Flujo de trabajo para diseños:**

1. **Usuario crea diseños en Stitch de Google**
   - Home público (listado de niños)
   - Ficha de niño
   - Login
   - Dashboard Gestor
   - Dashboard Admin
   - Formularios (registro, niño, recogida)

2. **Usuario entrega diseños a `software-enginner`**

3. **`software-enginner` documenta en `docs/frontend-handoff.md`:**
   - Referencias a diseños de Stitch
   - Componentes identificados
   - Estilos y comportamientos observados

4. **`software-developer-frontend` implementa siguiendo:**
   - Diseños de Stitch como referencia visual
   - `docs/frontend-handoff.md` como especificación técnica

---

## 27. Comandos útiles del proyecto

```bash
# Backend (Laravel)
cd /c/wamp64/www/findchild
php artisan serve                    # Iniciar servidor dev (puerto 8000)
php artisan make:model -mcr          # Crear modelo con migración y controlador
php artisan make:request             # Crear request de validación
php artisan make:resource            # Crear recurso API
php artisan make:middleware          # Crear middleware
php artisan migrate                  # Ejecutar migraciones
php artisan storage:link             # Crear symlink para fotos

# Frontend (React - a crear)
cd /c/wamp64/www/findchild/frontend
npm install                          # Instalar dependencias
npm run dev                          # Servidor dev (puerto 5173)
npm run build                        # Build de producción
```

---

**Documento creado por:** `software-enginner`  
**Fecha:** 2026-06-29  
**Versión:** 1.0  
**Estado:** Aprobado para implementación
