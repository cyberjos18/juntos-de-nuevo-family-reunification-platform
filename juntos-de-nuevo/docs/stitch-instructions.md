# 🎨 Instrucciones de Diseño para Google Stitch — Reencuentro

## Plataforma de Reunificación Familiar (Venezuela)

---

## 🎯 Contexto del proyecto

**Reencuentro** es una plataforma web humanitaria creada para facilitar la reunificación de niños separados de sus familias tras los terremotos en Venezuela.

**Tono emocional:** Esperanza, confianza, profesionalismo, calma en medio de la crisis.
**Audiencia principal:** Familias buscando niños, personal de centros de acogida.
**Dispositivo principal:** Móvil (70%+ del uso).

---

## 🎨 Sistema de Diseño

### Estilo general
- **Material Design 3** (Material You)
- **Mobile-first** (diseñar primero para móvil, luego tablet y desktop)
- **Bordes redondeados** generosos (12-16px)
- **Sombras suaves** (elevation levels de Material 3)
- **Espaciado generoso** (padding mínimo 16px, gap 16-24px)
- **Iconografía:** Material Symbols (outlined)

### Paleta de colores

```
PRIMARY (Azul)
├── Primary:         #1565C0  (azul medio — botones principales, headers)
├── On Primary:      #FFFFFF  (texto sobre primary)
├── Primary Container:  #D1E4FF  (fondos suaves, badges)
└── On Primary Container: #001D35  (texto sobre container)

SECONDARY (Azul grisáceo)
├── Secondary:       #546E7A  (acciones secundarias, iconos)
├── On Secondary:    #FFFFFF
├── Secondary Container: #E0E7EE  (cards, superficies)
└── On Secondary Container: #112B3C

TERTIARY (Verde esperanza — para éxito)
├── Tertiary:        #2E7D32  (confirmaciones, éxito, estado "recogido")
├── On Tertiary:     #FFFFFF
└── Tertiary Container: #C8E6C9

ERROR (Rojo suave)
├── Error:           #D32F2F  (errores, estado urgente)
├── On Error:        #FFFFFF
└── Error Container: #FFCDD2

SURFACE (Blancos y grises)
├── Background:      #F5F7FA  (fondo general de la app)
├── Surface:         #FFFFFF  (cards, modales)
├── Surface Variant: #E8EDF2  (dividers, inputs)
├── Outline:         #B0BEC5  (bordes de inputs)
└── Outline Variant: #DDE3E9  (bordes sutiles)

ESTADOS (Semánticos)
├── Encontrado:      #1565C0  (azul — buscando familia)
├── Familia Localizada: #F57C00  (naranja — en proceso)
├── Recogido:        #2E7D32  (verde — caso cerrado)
└── Otro Centro:     #78909C  (gris — transferido)

TEXTO
├── On Background:   #1A1C1E  (texto principal)
├── On Surface:      #1A1C1E
├── On Surface Variant: #43474E  (texto secundario)
└── Disabled:        #9E9E9E  (texto deshabilitado)
```

### Tipografía

```
Familia: Roboto (Google Fonts)

Display Large:   57px / 64px  (solo en landing hero si aplica)
Headline Large:  32px / 40px  (títulos de sección)
Headline Medium: 28px / 36px  (títulos de página)
Title Large:     22px / 28px  (cards, modales)
Title Medium:    16px / 24px  (subtítulos)
Body Large:      16px / 24px  (texto principal)
Body Medium:     14px / 20px  (texto secundario)
Label Large:     14px / 20px  (botones, tabs)
Label Medium:    12px / 16px  (badges, chips)
```

### Componentes Material 3 a usar

| Componente M3 | Uso en Reencuentro |
|--------------|-----------------|
| **Top App Bar** | Navbar superior con logo y menú |
| **Navigation Bar** | Navegación inferior en móvil (gestor/admin) |
| **Cards (Elevated)** | Tarjetas de niños en el listado |
| **FAB (Extended)** | Botón "Registrar Niño" (gestor) |
| **Text Fields (Outlined)** | Todos los inputs de formularios |
| **Buttons (Filled)** | Acciones principales |
| **Buttons (Outlined)** | Acciones secundarias |
| **Buttons (Text)** | Acciones terciarias, links |
| **Chips (Assist)** | Filtros de estado en listado |
| **Chips (Suggestion)** | Tags en ficha de niño |
| **Badge** | Contadores en navegación |
| **Dialog** | Confirmaciones (recogida, eliminar) |
| **Snackbar** | Notificaciones temporales |
| **Progress Indicator** | Estados de carga |
| **Divider** | Separadores |
| **Icon Buttons** | Acciones en cards y toolbar |

---

## 📱 Pantallas — Instrucciones detalladas

---

### PANTALLA 1: Home (Listado público de niños)

**Ruta:** `/`
**Acceso:** Público (cualquier visitante)
**Propósito:** Mostrar todos los niños encontrados para que familias puedan buscarlos

#### Layout (Móvil)

```
┌─────────────────────────────────┐
│  TOP APP BAR                    │
│  [Logo Reencuentro]    [🔍] [≡]  │
├─────────────────────────────────┤
│                                 │
│  BANNER HERO                    │
│  "Ayudamos a reunir familias"   │
│  Subtítulo + botón "Registrar   │
│  centro"                        │
│                                 │
├─────────────────────────────────┤
│                                 │
│  CHIPS DE FILTRO                │
│  [Todos] [Encontrados]          │
│  [Familia localizada]           │
│                                 │
├─────────────────────────────────┤
│                                 │
│  GRID DE CARDS (2 columnas)     │
│  ┌──────────┐  ┌──────────┐    │
│  │   FOTO   │  │   FOTO   │    │
│  │  niño    │  │  niño    │    │
│  ├──────────┤  ├──────────┤    │
│  │ Nombre   │  │ Nombre   │    │
│  │ Edad     │  │ Edad     │    │
│  │ Centro   │  │ Centro   │    │
│  │ [Badge]  │  │ [Badge]  │    │
│  └──────────┘  └──────────┘    │
│                                 │
│  ┌──────────┐  ┌──────────┐    │
│  │   ...    │  │   ...    │    │
│  └──────────┘  └──────────┘    │
│                                 │
└─────────────────────────────────┘
```

#### Componentes específicos

**Top App Bar:**
- Logo "Reencuentro" a la izquierda (texto en Primary color)
- Icono de búsqueda (🔍) a la derecha
- Icono de menú hamburguesa (≡) para login/registro
- Fondo blanco, elevation 1
- Altura: 56px

**Banner Hero:**
- Fondo con gradiente suave de Primary Container (#D1E4FF → #FFFFFF)
- Título: "Ayudamos a reunir familias" (Headline Medium)
- Subtítulo: "Plataforma de búsqueda de niños encontrados tras los terremotos en Venezuela" (Body Large)
- Botón "Registrar centro" (Outlined button)
- Padding: 24px
- Altura aproximada: 180px

**Chips de filtro:**
- Horizontal scroll en móvil
- Chips tipo "Assist" de Material 3
- Opciones: Todos | Encontrados | Familia localizada | Recogidos
- Chip seleccionado con Primary color de fondo

**Cards de niño:**
- Grid de 2 columnas en móvil, 3 en tablet, 4 en desktop
- Card tipo "Elevated" (Material 3)
- **Foto:** Aspect ratio 1:1 (cuadrada), bordes redondeados superiores (12px)
  - Si no hay foto: placeholder con icono de niño en Primary Container
- **Nombre:** Title Medium, negrita, truncado a 1 línea
- **Edad:** Body Medium, color On Surface Variant
- **Centro:** Label Medium, color Secondary, truncado
- **Badge de estado:** Chip pequeño con color del estado
  - Encontrado: azul
  - Familia localizada: naranja
  - Recogido: verde
- Sombra sutil (elevation 1)
- Al hacer tap: navegar a ficha del niño
- Gap entre cards: 12px

#### Layout (Desktop)

```
┌────────────────────────────────────────────────────────────┐
│  [Logo Reencuentro]        [Buscar...]  [Login] [Registro]  │
├────────────────────────────────────────────────────────────┤
│                                                            │
│   HERO BANNER (más ancho, horizontal)                      │
│   "Ayudamos a reunir familias" + ilustración               │
│                                                            │
├────────────────────────────────────────────────────────────┤
│                                                            │
│   [Todos] [Encontrados] [Familia localizada] [Recogidos]  │
│                                                            │
│   GRID 4 columnas                                          │
│   ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐                        │
│   │card │ │card │ │card │ │card │                         │
│   └─────┘ └─────┘ └─────┘ └─────┘                        │
│   ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐                        │
│   │card │ │card │ │card │ │card │                         │
│   └─────┘ └─────┘ └─────┘ └─────┘                        │
│                                                            │
│   [Paginación: 1 2 3 ... >]                               │
│                                                            │
├────────────────────────────────────────────────────────────┤
│  Footer: "Reencuentro © 2026 — Plataforma humanitaria"      │
└────────────────────────────────────────────────────────────┘
```

---

### PANTALLA 2: Ficha del niño

**Ruta:** `/nino/:id`
**Acceso:** Público (datos sensibles ocultos sin login)
**Propósito:** Mostrar todos los datos del niño para que familias lo identifiquen

#### Layout (Móvil)

```
┌─────────────────────────────────┐
│  TOP APP BAR                    │
│  [← Back]  Ficha del niño      │
├─────────────────────────────────┤
│                                 │
│  FOTO GRANDE                    │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  │     Foto del niño       │   │
│  │     (full width)        │   │
│  │                         │   │
│  └─────────────────────────┘   │
│                                 │
│  NOMBRE + BADGE ESTADO          │
│  Carlos Rodríguez               │
│  [🔵 Encontrado]               │
│                                 │
├─────────────────────────────────┤
│                                 │
│  INFO BÁSICA (Card)             │
│  ┌─────────────────────────┐   │
│  │ 👤 Edad: 5 años         │   │
│  │ 📅 Nacimiento: 15/03/21 │   │
│  │ 📍 Centro: Prot. Civil  │   │
│  │ 📆 Registro: 29/06/26   │   │
│  └─────────────────────────┘   │
│                                 │
│  DATOS FAMILIARES (Card) *      │
│  ┌─────────────────────────┐   │
│  │ 👩 Madre: Ana Rodríguez │   │
│  │ 👨 Padre: Pedro Rodríg. │   │
│  │ 🏠 Zona: Edif. Jardines │   │
│  └─────────────────────────┘   │
│                                 │
│  FAMILIARES CONOCIDOS (Card) *  │
│  ┌─────────────────────────┐   │
│  │ Tía - Rosa Hernández    │   │
│  │   📞 +58-414-555-9876   │   │
│  │                         │   │
│  │ Abuela - Carmen Rodríg. │   │
│  │   📞 +58-424-555-1122   │   │
│  └─────────────────────────┘   │
│                                 │
│  CONTACTO DEL CENTRO (Card) *   │
│  ┌─────────────────────────┐   │
│  │ Protección Civil Central│   │
│  │ 📞 +58-212-555-1234     │   │
│  │ ✉️  pc@findchild.org     │   │
│  │ [📞 Llamar]  [✉️ Email] │   │
│  └─────────────────────────┘   │
│                                 │
│  * Solo visible con login      │
│  Sin login: mensaje "Inicia    │
│  sesión para ver más datos"    │
│                                 │
└─────────────────────────────────┘
```

#### Componentes específicos

**Foto:**
- Full width, aspect ratio 4:3
- Bordes inferiores redondeados (16px)
- Si no hay foto: placeholder grande con icono

**Datos básicos:**
- Card tipo "Filled" (Surface Variant)
- Iconos Material Symbols a la izquierda de cada dato
- Datos en formato lista vertical

**Datos familiares (solo autenticado):**
- Card tipo "Elevated"
- Título de sección: "Información familiar"
- Si no hay datos: "Sin información disponible"

**Familiares conocidos (solo autenticado):**
- Lista de chips/cards pequeños por familiar
- Cada uno muestra: parentesco, nombre, teléfono (con ícono clickeable para llamar)

**Contacto del centro (solo autenticado):**
- Card destacada
- Botones de acción: "Llamar" (abre teléfono) y "Email" (abre cliente email)
- Botones tipo "Filled Tonal"

**Mensaje para no autenticados:**
- Card con fondo Primary Container
- Icono de candado
- Texto: "Inicia sesión para ver información de contacto y datos familiares"
- Botón "Iniciar sesión"

---

### PANTALLA 3: Login

**Ruta:** `/login`
**Acceso:** Público
**Propósito:** Acceso para Gestores y Admin

#### Layout (Móvil)

```
┌─────────────────────────────────┐
│                                 │
│         [Logo Reencuentro]        │
│      (centrado, grande)         │
│                                 │
│     Iniciar sesión              │
│     (Headline Medium)           │
│                                 │
│  ┌─────────────────────────┐   │
│  │ Email                    │   │
│  │ ┌─────────────────────┐ │   │
│  │ │ 📧 tu@email.com     │ │   │
│  │ └─────────────────────┘ │   │
│  │                          │   │
│  │ Contraseña               │   │
│  │ ┌─────────────────────┐ │   │
│  │ │ 🔒 ••••••••    👁️  │ │   │
│  │ └─────────────────────┘ │   │
│  │                          │   │
│  │ [  INICIAR SESIÓN  ]    │   │
│  │  (Filled button full)   │   │
│  │                          │   │
│  └─────────────────────────┘   │
│                                 │
│  ¿No tienes cuenta?            │
│  [Registra tu centro]          │
│                                 │
└─────────────────────────────────┘
```

#### Componentes específicos

- Card central elevada (max-width: 400px, centrada)
- Logo Reencuentro arriba, tamaño grande (48px height)
- Text fields Outlined de Material 3
- Botón "Iniciar sesión" tipo Filled, full width
- Link a registro debajo
- Padding generoso: 32px horizontal
- Fondo de la pantalla: Background (#F5F7FA)

---

### PANTALLA 4: Registro de Centro

**Ruta:** `/registro-centro`
**Acceso:** Público
**Propósito:** Formulario para que nuevos centros se registren

#### Layout (Móvil)

```
┌─────────────────────────────────┐
│  TOP APP BAR                    │
│  [← Back]  Registrar centro    │
├─────────────────────────────────┤
│                                 │
│  CARD INTRO                     │
│  ┌─────────────────────────┐   │
│  │ 🏠                      │   │
│  │ ¿Eres un centro de      │   │
│  │ acogida?                │   │
│  │                         │   │
│  │ Regístrate para poder   │   │
│  │ registrar niños         │   │
│  │ encontrados y ayudar a  │   │
│  │ reunir familias.        │   │
│  └─────────────────────────┘   │
│                                 │
│  FORMULARIO                     │
│                                 │
│  Tipo de centro *               │
│  ┌─────────────────────────┐   │
│  │ ▼ Seleccionar...        │   │
│  └─────────────────────────┘   │
│  Opciones: Protección Civil,   │
│  Hospital, Casa de Acogida,    │
│  Otro                          │
│                                 │
│  Nombre del centro *            │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  └─────────────────────────┘   │
│                                 │
│  Tu nombre *                    │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  └─────────────────────────┘   │
│                                 │
│  Email *                        │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  └─────────────────────────┘   │
│                                 │
│  Cargo en el centro *           │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  └─────────────────────────┘   │
│                                 │
│  Contraseña *                   │
│  ┌─────────────────────────┐   │
│  │ 🔒 ••••••••        👁️  │   │
│  └─────────────────────────┘   │
│                                 │
│  Teléfono (opcional)            │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  └─────────────────────────┘   │
│                                 │
│  [  REGISTRAR CENTRO  ]        │
│  (Filled button, full width)   │
│                                 │
│  * Campos obligatorios         │
│                                 │
└─────────────────────────────────┘
```

---

### PANTALLA 5: Activación de cuenta

**Ruta:** `/activar/:token`
**Acceso:** Público (link desde email)
**Propósito:** Confirmar email y activar cuenta

#### Layout (Móvil)

```
┌─────────────────────────────────┐
│                                 │
│         [Logo Reencuentro]        │
│                                 │
│  ✅ (icono grande verde)        │
│                                 │
│  ¡Cuenta activada!              │
│  (Headline Medium, centrado)    │
│                                 │
│  Tu cuenta ha sido activada     │
│  exitosamente. Ya puedes        │
│  iniciar sesión en la           │
│  plataforma.                    │
│                                 │
│  Nota: Tu centro debe ser       │
│  validado por un administrador  │
│  antes de poder registrar       │
│  niños.                         │
│                                 │
│  [  INICIAR SESIÓN  ]          │
│  (Filled button, centrado)     │
│                                 │
└─────────────────────────────────┘
```

**Estado de error (token inválido):**
- Icono ❌ rojo
- Título: "Token inválido"
- Mensaje: "El enlace de activación no es válido o ha expirado"
- Botón: "Volver al inicio"

---

### PANTALLA 6: Dashboard Gestor

**Ruta:** `/gestor/dashboard`
**Acceso:** Gestor autenticado
**Propósito:** Panel de control del gestor

#### Layout (Móvil)

```
┌─────────────────────────────────┐
│  TOP APP BAR                    │
│  [Logo]    [🔔] [Avatar ▾]    │
├─────────────────────────────────┤
│                                 │
│  Bienvenido, María              │
│  Protección Civil Central       │
│  (Headline Medium + Subtitle)   │
│                                 │
├─────────────────────────────────┤
│                                 │
│  STATS CARDS (2 columnas)       │
│  ┌──────────┐  ┌──────────┐    │
│  │   🔵     │  │   🟢     │    │
│  │    12    │  │     3    │    │
│  │ Niños    │  │Recogidos │    │
│  │ activos  │  │          │    │
│  └──────────┘  └──────────┘    │
│  ┌──────────┐  ┌──────────┐    │
│  │   🟡     │  │   📊     │    │
│  │    2     │  │   17     │    │
│  │Familia   │  │  Total   │    │
│  │localiz.  │  │ registros│    │
│  └──────────┘  └──────────┘    │
│                                 │
├─────────────────────────────────┤
│                                 │
│  MIS NIÑOS (últimos 5)         │
│  ┌─────────────────────────┐   │
│  │ [📷] Carlos R.  5 años  │   │
│  │        🔵 Encontrado    │   │
│  ├─────────────────────────┤   │
│  │ [📷] Sofía M.   3 años  │   │
│  │        🔵 Encontrado    │   │
│  ├─────────────────────────┤   │
│  │ [📷] Miguel L.  8 años  │   │
│  │        🟡 Fam. Localiz. │   │
│  └─────────────────────────┘   │
│                                 │
│  [Ver todos →]                 │
│                                 │
├─────────────────────────────────┤
│  NAVIGATION BAR (bottom)        │
│  [🏠 Home] [👶 Niños] [⚙️]   │
└─────────────────────────────────┘
│                                 │
│  FAB (Extended)                 │
│  [+ Registrar Niño]             │
│  (Flotante, esquina inf. der.) │
└─────────────────────────────────┘
```

#### Componentes específicos

**Stats cards:**
- 4 cards pequeñas en grid 2x2
- Cada card: icono de color, número grande, label
- Card tipo "Filled" con Secondary Container de fondo
- Bordes redondeados 16px

**Lista de niños recientes:**
- Lista vertical con cards tipo "compact"
- Cada item: thumbnail 48x48, nombre + edad, badge de estado
- Separador tipo Divider entre items
- Link "Ver todos" al final

**FAB (Extended):**
- Botón flotante Material 3
- Primary color, texto "Registrar Niño" con icono +
- Posición: bottom-right, 16px del borde
- Siempre visible en dashboard del gestor

**Navigation Bar (bottom):**
- Material 3 Navigation Bar
- Items: Home, Niños, Configuración
- Solo visible en móvil
- En desktop: sidebar a la izquierda

---

### PANTALLA 7: Registrar Niño

**Ruta:** `/gestor/ninos/nuevo`
**Acceso:** Gestor autenticado
**Propósito:** Formulario para registrar un niño encontrado

#### Layout (Móvil)

```
┌─────────────────────────────────┐
│  TOP APP BAR                    │
│  [← Back]  Registrar niño      │
├─────────────────────────────────┤
│                                 │
│  FOTO *                         │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  │    📷 Tomar foto        │   │
│  │    o subir imagen       │   │
│  │                         │   │
│  │    [📷 Cámara]           │   │
│  │    [🖼️ Galería]          │   │
│  │                         │   │
│  └─────────────────────────┘   │
│  (Aspect ratio 4:3, centrado)  │
│                                 │
│  DATOS DEL NIÑO                 │
│                                 │
│  Nombre *                       │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  └─────────────────────────┘   │
│                                 │
│  Apellido *                     │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  └─────────────────────────┘   │
│                                 │
│  Edad                           │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  └─────────────────────────┘   │
│                                 │
│  Sexo                           │
│  (  Masculino  ) (  Femenino  )│
│  [Radio buttons]                │
│                                 │
│  Fecha de nacimiento            │
│  ┌─────────────────────────┐   │
│  │ 📅 Seleccionar fecha    │   │
│  └─────────────────────────┘   │
│                                 │
│  Edificio/Casa donde vivía      │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  └─────────────────────────┘   │
│                                 │
│  Nombre de la madre             │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  └─────────────────────────┘   │
│                                 │
│  Nombre del padre               │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  └─────────────────────────┘   │
│                                 │
│  Observaciones                  │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  │                         │   │
│  └─────────────────────────┘   │
│  (Textarea, 3 líneas)           │
│                                 │
├─────────────────────────────────┤
│                                 │
│  FAMILIARES CONOCIDOS           │
│  (Sección colapsable)           │
│                                 │
│  [+ Agregar familiar]          │
│  (Outlined button)             │
│                                 │
│  Si se agrega:                  │
│  ┌─────────────────────────┐   │
│  │ Parentesco *             │   │
│  │ ┌─────────────────────┐ │   │
│  │ │ ▼ Seleccionar...    │ │   │
│  │ └─────────────────────┘ │   │
│  │ Nombre *                │   │
│  │ ┌─────────────────────┐ │   │
│  │ │                     │ │   │
│  │ └─────────────────────┘ │   │
│  │ Teléfono                │   │
│  │ ┌─────────────────────┐ │   │
│  │ │                     │ │   │
│  │ └─────────────────────┘ │   │
│  │              [✕ Quitar]│   │
│  └─────────────────────────┘   │
│                                 │
├─────────────────────────────────┤
│                                 │
│  [  REGISTRAR NIÑO  ]          │
│  (Filled button, full width)   │
│                                 │
└─────────────────────────────────┘
```

#### Componentes específicos

**Zona de foto:**
- Área grande clickeable (aspect ratio 4:3)
- Estado vacío: icono de cámara + texto
- Dos botones: "Tomar foto" (abre cámara) y "Subir imagen" (abre galería)
- Estado con foto: preview de la imagen con botón de cambiar
- Borde dashed cuando está vacío

**Formulario:**
- Todos los inputs tipo "Outlined Text Field" de Material 3
- Campos obligatorios marcados con *
- Sexo: Radio buttons horizontales
- Fecha: Date picker de Material 3

**Sección de familiares:**
- Card colapsable (accordion)
- Botón "+ Agregar familiar" para añadir más
- Cada familiar es una sub-card con sus campos
- Botón "✕ Quitar" para eliminar

---

### PANTALLA 8: Gestionar Recogida

**Ruta:** `/gestor/ninos/:id/recogida`
**Acceso:** Gestor autenticado
**Propósito:** Registrar que un familiar recoge al niño

#### Layout (Móvil)

```
┌─────────────────────────────────┐
│  TOP APP BAR                    │
│  [← Back]  Registrar recogida  │
├─────────────────────────────────┤
│                                 │
│  NIÑO A RECOGER (Card resumen)  │
│  ┌─────────────────────────┐   │
│  │ [📷] Carlos Rodríguez    │   │
│  │       5 años · 🔵       │   │
│  │       Prot. Civil       │   │
│  └─────────────────────────┘   │
│                                 │
│  DATOS DEL FAMILIAR             │
│                                 │
│  Nombre *                       │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  └─────────────────────────┘   │
│                                 │
│  Apellido *                     │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  └─────────────────────────┘   │
│                                 │
│  Parentesco *                   │
│  ┌─────────────────────────┐   │
│  │ ▼ Seleccionar...        │   │
│  └─────────────────────────┘   │
│  (Padre, Madre, Tío/a,         │
│   Abuelo/a, Hermano/a, Otro)   │
│                                 │
│  Teléfono *                     │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  └─────────────────────────┘   │
│                                 │
│  Email                          │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  └─────────────────────────┘   │
│                                 │
│  Documento de identidad         │
│  ┌─────────────────────────┐   │
│  │ Cédula / DNI            │   │
│  └─────────────────────────┘   │
│                                 │
├─────────────────────────────────┤
│                                 │
│  FOTO DE RECOGIDA *             │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  │  📷 Foto del niño con   │   │
│  │     el familiar         │   │
│  │                         │   │
│  │  [📷 Tomar foto]        │   │
│  └─────────────────────────┘   │
│                                 │
│  Observaciones                  │
│  ┌─────────────────────────┐   │
│  │                         │   │
│  └─────────────────────────┘   │
│                                 │
├─────────────────────────────────┤
│                                 │
│  ⚠️ ADVERTENCIA                 │
│  ┌─────────────────────────┐   │
│  │ Esta acción es          │   │
│  │ irreversible. El niño   │   │
│  │ será marcado como       │   │
│  │ "Recogido".             │   │
│  └─────────────────────────┘   │
│                                 │
│  [  CONFIRMAR RECOGIDA  ]      │
│  (Filled button, color verde)  │
│                                 │
└─────────────────────────────────┘
```

#### Componentes específicos

**Card resumen del niño:**
- Horizontal: thumbnail + nombre + edad + estado
- Fondo Primary Container
- No editable, solo informativo

**Foto de recogida:**
- Zona grande para tomar foto del niño CON el familiar
- Botón principal: "📷 Tomar foto" (abre cámara directamente)
- Obligatoria (no se puede confirmar sin foto)

**Advertencia:**
- Card con fondo Error Container (#FFCDD2)
- Icono de advertencia ⚠️
- Texto explicando que es irreversible
- Bordes redondeados

**Botón confirmar:**
- Color verde (Tertiary) en vez de Primary
- Texto: "Confirmar Recogida"
- Full width
- Al hacer tap: muestra Dialog de confirmación

**Dialog de confirmación:**
- Material 3 Dialog
- Título: "¿Confirmar recogida?"
- Texto: "Carlos Rodríguez será marcado como recogido por [nombre familiar]. Esta acción no se puede deshacer."
- Botones: "Cancelar" (Text) + "Confirmar" (Filled, verde)

---

### PANTALLA 9: Dashboard Admin

**Ruta:** `/admin/dashboard`
**Acceso:** Admin autenticado
**Propósito:** Panel de control general

#### Layout (Móvil)

```
┌─────────────────────────────────┐
│  TOP APP BAR                    │
│  [Logo] Admin  [Avatar ▾]      │
├─────────────────────────────────┤
│                                 │
│  Panel de Administración        │
│  (Headline Medium)              │
│                                 │
│  STATS (2 columnas)             │
│  ┌──────────┐  ┌──────────┐    │
│  │  🏠 12   │  │  👶 45   │    │
│  │ Centros  │  │  Niños   │    │
│  └──────────┘  └──────────┘    │
│  ┌──────────┐  ┌──────────┐    │
│  │  🟢 8    │  │  🔴 4    │    │
│  │ Activos  │  │Pendientes│    │
│  └──────────┘  └──────────┘    │
│  ┌──────────┐  ┌──────────┐    │
│  │  ✅ 15   │  │  👥 22   │    │
│  │Recogidos │  │ Usuarios │    │
│  └──────────┘  └──────────┘    │
│                                 │
├─────────────────────────────────┤
│                                 │
│  ACCIONES RÁPIDAS               │
│  ┌─────────────────────────┐   │
│  │ 🏠 Validar centros  [→] │   │
│  ├─────────────────────────┤   │
│  │ 📋 Ver recogidas    [→] │   │
│  ├─────────────────────────┤   │
│  │ ⚙️  Config. Mailjet  [→] │   │
│  ├─────────────────────────┤   │
│  │ 👥 Ver todos niños   [→] │   │
│  └─────────────────────────┘   │
│                                 │
├─────────────────────────────────┤
│  NAVIGATION BAR (bottom)        │
│  [📊 Stats] [🏠 Centros] [⚙️] │
└─────────────────────────────────┘
```

#### Componentes específicos

**Stats cards:**
- 6 cards en grid 2x3
- Cada card con icono, número grande y label
- Color del icono según métrica

**Acciones rápidas:**
- Lista de items tipo "List" de Material 3
- Cada item: icono + texto + flecha →
- Al hacer tap: navegar a la sección

---

### PANTALLA 10: Validar Centros (Admin)

**Ruta:** `/admin/centros`
**Acceso:** Admin autenticado
**Propósito:** Listar y validar centros pendientes

#### Layout (Móvil)

```
┌─────────────────────────────────┐
│  TOP APP BAR                    │
│  [← Back]  Centros             │
├─────────────────────────────────┤
│                                 │
│  TABS                           │
│  [Pendientes (4)] [Activos (8)]│
│  ─────────────────              │
│                                 │
│  LISTA DE CENTROS               │
│                                 │
│  ┌─────────────────────────┐   │
│  │ 🏥 Hospital San José    │   │
│  │ Tipo: Hospital           │   │
│  │ Email: hospital@...     │   │
│  │ 👶 0 niños · 👥 1 user  │   │
│  │                         │   │
│  │ [✅ Validar]  [✕ Rech.] │   │
│  └─────────────────────────┘   │
│                                 │
│  ┌─────────────────────────┐   │
│  │ 🏠 Casa Hogar María     │   │
│  │ Tipo: Casa de acogida    │   │
│  │ Email: casa@...         │   │
│  │ 👶 0 niños · 👥 1 user  │   │
│  │                         │   │
│  │ [✅ Validar]  [✕ Rech.] │   │
│  └─────────────────────────┘   │
│                                 │
├─────────────────────────────────┤
│  NAVIGATION BAR (bottom)        │
│  [📊 Stats] [🏠 Centros] [⚙️] │
└─────────────────────────────────┘
```

#### Componentes específicos

**Tabs:**
- Material 3 Tabs con badge de conteo
- "Pendientes" con badge rojo si hay pendientes
- "Activos" con badge azul

**Card de centro:**
- Card tipo "Elevated"
- Icono de tipo de centro (🏥 🏠 🏢)
- Nombre, tipo, email
- Conteos de niños y usuarios
- Botones de acción:
  - "Validar" (Filled Tonal, verde)
  - "Rechazar" (Outlined, rojo)
- Al validar: Dialog de confirmación

---

### PANTALLA 11: Configuración Mailjet (Admin)

**Ruta:** `/admin/configuracion`
**Acceso:** Admin autenticado
**Propósito:** Configurar servicio de email

#### Layout (Móvil)

```
┌─────────────────────────────────┐
│  TOP APP BAR                    │
│  [← Back]  Configuración       │
├─────────────────────────────────┤
│                                 │
│  ⚙️ CONFIGURACIÓN DE EMAIL      │
│                                 │
│  ESTADO ACTUAL                  │
│  ┌─────────────────────────┐   │
│  │ ⚠️ Mailjet NO configurado│   │
│  │ (o ✅ Configurado)       │   │
│  │ Driver actual: log       │   │
│  └─────────────────────────┘   │
│                                 │
│  CREDENCIALES MAILJET           │
│                                 │
│  API Key *                      │
│  ┌─────────────────────────┐   │
│  │ ••••••••••••••••   👁️  │   │
│  └─────────────────────────┘   │
│                                 │
│  Secret Key *                   │
│  ┌─────────────────────────┐   │
│  │ ••••••••••••••••   👁️  │   │
│  └─────────────────────────┘   │
│                                 │
│  CONFIGURACIÓN SMTP             │
│                                 │
│  Servidor SMTP                  │
│  ┌─────────────────────────┐   │
│  │ in-v3.mailjet.com       │   │
│  └─────────────────────────┘   │
│                                 │
│  Puerto                         │
│  ┌─────────────────────────┐   │
│  │ 587                     │   │
│  └─────────────────────────┘   │
│                                 │
│  REMITENTE                      │
│                                 │
│  Email remitente                │
│  ┌─────────────────────────┐   │
│  │ noreply@findchild.org   │   │
│  └─────────────────────────┘   │
│                                 │
│  Nombre remitente               │
│  ┌─────────────────────────┐   │
│  │ Reencuentro               │   │
│  └─────────────────────────┘   │
│                                 │
│  [  GUARDAR CONFIGURACIÓN  ]   │
│  (Filled button, full width)   │
│                                 │
│  [  📧 Enviar email de prueba ] │
│  (Outlined button, full width) │
│                                 │
└─────────────────────────────────┘
```

#### Componentes específicos

**Estado actual:**
- Badge de estado grande
- No configurado: fondo Error Container + icono ⚠️
- Configurado: fondo Tertiary Container + icono ✅

**Inputs de API keys:**
- Tipo password (ocultos por defecto)
- Botón de "ojo" (👁️) para mostrar/ocultar
- Monospace font para las keys

**Botón enviar prueba:**
- Outlined button
- Al hacer tap: muestra loading y luego snackbar con resultado

---

## 📐 Breakpoints responsivos

| Breakpoint | Ancho | Layout |
|-----------|-------|--------|
| **Móvil** | < 600px | 1 columna, Navigation Bar inferior, FAB |
| **Tablet** | 600px - 1024px | 2 columnas, Navigation Rail lateral |
| **Desktop** | > 1024px | 3-4 columnas, Navigation Drawer lateral |

---

## 🎭 Estados de UI (para todas las pantallas)

### Loading
- Skeleton cards (shimmer animation) en listados
- Circular Progress Indicator en acciones
- Linear Progress Indicator en uploads

### Empty
- Ilustración simple (Material style)
- Texto descriptivo
- CTA si aplica ("Registra el primer niño")

### Error
- Snackbar rojo para errores temporales
- Pantalla completa con icono para errores graves
- Botón "Reintentar"

### Success
- Snackbar verde para confirmaciones
- Duración: 3 segundos

### Unauthorized
- Pantalla completa con icono de candado
- Texto: "No tienes permisos para acceder"
- Botón: "Volver al inicio"

---

## 📝 Notas adicionales para Stitch

1. **Accesibilidad:** Contraste mínimo WCAG AA (4.5:1 para texto, 3:1 para elementos grandes)
2. **Animaciones:** Transiciones suaves (200-300ms), usar Motion de Material 3
3. **Iconografía:** Material Symbols Outlined, tamaño 24px por defecto
4. **Densidad:** Comfortable (no compacta) — la audiencia puede ser personas mayores buscando familiares
5. **Idioma:** Todo en español (Venezuela)
6. **Fotos de placeholder:** Usar avatares genéricos de niños (NO fotos reales) en los mockups
7. **Logo:** Texto "Reencuentro" con un icono de casa 🏠 o corazón como marca

---

*Documento creado por `software-enginner` para Google Stitch*
*Fecha: 2026-06-29*
*Proyecto: Reencuentro — Plataforma de Reunificación Familiar*
