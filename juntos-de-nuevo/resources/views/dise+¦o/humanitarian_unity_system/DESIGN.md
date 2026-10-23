---
name: Humanitarian Unity System
colors:
  surface: '#f9f9ff'
  surface-dim: '#d8dae2'
  surface-bright: '#f9f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f3fb'
  surface-container: '#ecedf6'
  surface-container-high: '#e7e8f0'
  surface-container-highest: '#e1e2ea'
  on-surface: '#191c21'
  on-surface-variant: '#424752'
  inverse-surface: '#2e3037'
  inverse-on-surface: '#eff0f9'
  outline: '#727783'
  outline-variant: '#c2c6d4'
  surface-tint: '#005db7'
  primary: '#004d99'
  on-primary: '#ffffff'
  primary-container: '#1565c0'
  on-primary-container: '#dae5ff'
  inverse-primary: '#a9c7ff'
  secondary: '#48626e'
  on-secondary: '#ffffff'
  secondary-container: '#cbe7f5'
  on-secondary-container: '#4e6874'
  tertiary: '#005c15'
  on-tertiary: '#ffffff'
  tertiary-container: '#25752b'
  on-tertiary-container: '#a5f99e'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d6e3ff'
  primary-fixed-dim: '#a9c7ff'
  on-primary-fixed: '#001b3d'
  on-primary-fixed-variant: '#00468c'
  secondary-fixed: '#cbe7f5'
  secondary-fixed-dim: '#afcbd8'
  on-secondary-fixed: '#021f29'
  on-secondary-fixed-variant: '#304a55'
  tertiary-fixed: '#a3f69c'
  tertiary-fixed-dim: '#88d982'
  on-tertiary-fixed: '#002204'
  on-tertiary-fixed-variant: '#005312'
  background: '#f9f9ff'
  on-background: '#191c21'
  surface-variant: '#e1e2ea'
typography:
  display-lg:
    fontFamily: Roboto Flex
    fontSize: 57px
    fontWeight: '400'
    lineHeight: 64px
    letterSpacing: -0.25px
  headline-lg:
    fontFamily: Roboto Flex
    fontSize: 32px
    fontWeight: '400'
    lineHeight: 40px
  headline-lg-mobile:
    fontFamily: Roboto Flex
    fontSize: 28px
    fontWeight: '400'
    lineHeight: 36px
  title-lg:
    fontFamily: Roboto Flex
    fontSize: 22px
    fontWeight: '500'
    lineHeight: 28px
  body-lg:
    fontFamily: Roboto Flex
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
    letterSpacing: 0.5px
  body-md:
    fontFamily: Roboto Flex
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
    letterSpacing: 0.25px
  label-lg:
    fontFamily: Roboto Flex
    fontSize: 14px
    fontWeight: '500'
    lineHeight: 20px
    letterSpacing: 0.1px
  label-md:
    fontFamily: Roboto Flex
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.5px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 8px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 32px
  gutter: 16px
  margin-mobile: 16px
  margin-desktop: 64px
---

## Brand & Style

The design system is centered on the humanitarian mission of family reunification. The personality is **professional, hopeful, and calm**, designed to provide a sense of security and clarity during stressful situations. 

The aesthetic follows **Material Design 3 (Material You)** principles, emphasizing accessibility, clear hierarchy, and a soft, approachable interface. It utilizes a sophisticated balance of "Corporate Modern" and "Humanist" styles—eschewing aggressive sharp corners for softer, organic shapes that feel welcoming yet authoritative. 

The visual language communicates reliability through stable grids and hope through a purposeful use of nature-inspired greens and calming blues. Every interaction is designed to reduce cognitive load for users who may be in a state of distress.

## Colors

The palette is anchored by **Primary Blue (#1565C0)**, representing trust and the institutional strength of the platform. **Tertiary Green (#2E7D32)** is used as a "Hope" accent, signaling success and positive progress in the reunification process.

### Status Indicators
To provide immediate visual context for child status updates in the Venezuelan context, the system uses a dedicated semantic palette:
- **Encontrado (Found):** Primary Blue, signifying the start of the official process.
- **Familia Localizada (Family Located):** Vibrant Orange, signaling an active transition and vital news.
- **Recogido (Collected/Reunited):** Success Green, marking the completion of the journey.
- **Otro Centro (Other Center):** Neutral Blue-Grey, indicating a lateral move or administrative update.

Surfaces use a very light grey-blue background to reduce screen glare and eye strain.

## Typography

The typography uses **Roboto Flex**, a highly legible neo-grotesque sans-serif that ensures clarity across all digital touchpoints. 

- **Headlines:** Set in regular weights with generous line height to maintain a "calm" reading pace.
- **Body Text:** Optimized for readability in Spanish, ensuring that diacritics and long words (common in Spanish) have enough breathing room.
- **Hierarchy:** Use `title-lg` for card titles and section headers to maintain a professional, structured appearance.
- **Labels:** Use `label-lg` for buttons and navigation items, utilizing a medium weight to provide clear affordance.

## Layout & Spacing

This design system employs a **fluid grid system** based on an 8px square baseline, favoring "Generous" density to avoid overwhelming users.

- **Margins:** A minimum of 16px on mobile devices, scaling to 64px on large desktops to center content and reduce eye-travel.
- **Gutter & Gaps:** A consistent 16px gutter is used between grid columns. Vertical spacing between logical sections should ideally be 24px (lg) or 32px (xl) to create a sense of breathing room.
- **Container Strategy:** Content is grouped in large, clearly defined containers to help users focus on one task at a time.

## Elevation & Depth

In accordance with Material 3, depth is communicated through both **Tonal Layers** and **Ambient Shadows**.

- **Level 0 (Flat):** Used for the main background surface.
- **Level 1 (Tonal):** Surfaces that are slightly elevated use a subtle primary-tinted overlay (surface-container-low).
- **Level 2 (Shadow):** Standard cards and navigation components use a soft, diffused shadow (4px-8px blur, low opacity) to indicate interactability.
- **Level 3 (High):** Modals, dialogs, and floating action buttons (FABs) use more pronounced shadows to pull focus during critical user actions like "Reportar Desaparición".

## Shapes

The shape language is **Rounded**, utilizing a 12px to 16px corner radius for most containers. This choice softens the "institutional" feel of a humanitarian platform, making it feel more human-centric.

- **Small Components:** Buttons and chips use a 12px radius.
- **Medium Components:** Input fields and small cards use a 12px radius.
- **Large Components:** Main content cards and dialogs use a 16px (rounded-lg) or 24px (rounded-xl) radius to frame information gently.
- **Icons:** Icons should feature rounded caps and corners to match the UI's softness.

## Components

### Buttons & Navigation
- **Primary FAB:** A large, 16px-radius button for the primary action (e.g., "Crear Reporte").
- **Buttons:** All buttons use `label-lg` typography. Primary buttons are filled with Primary Blue; secondary buttons use a tonal-tint or outlined style.

### Cards & Profiles
- **Profile Cards:** Used for "Niños Encontrados". They must feature a clear photo (12px radius), name, age, and a prominent **Status Chip** in the top right corner.
- **Status Chips:** Small, 8px-radius containers using the semantic status colors with white or high-contrast text.

### Input Fields
- **Search & Forms:** Text fields should be "Filled" with a bottom indicator line or fully "Outlined" with a 12px radius. In Spanish-language forms, ensure labels are clear and helper text is always visible to assist users with varying levels of digital literacy.

### Identity
- **Logo:** The 'FindChild' wordmark should be paired with a heart or house icon. The icon is always placed to the left or above the text, rendered in Primary Blue or Tertiary Green to evoke home and love.