# V4 Brand & Theme

Brand identity, colors, fonts, and visual assets for the V4 rebuild.

---

## Brand Identity

- **Brand Name:** HOMI (Hotel Management Information System)
- **Logo Files:** See `assets/` folder
  - `homiLogo.png` — primary logo (used in login, dashboard headers)
  - `homiLogo2.png` — alternative/secondary logo
- **Background:** `hotel-bg.jpg` — used on login/welcome page

---

## Primary Brand Color

| Name | Hex | Usage |
|------|-----|-------|
| **Brand Blue** | `#009EF5` | Primary buttons, headers, links, gradients |

### Color Palette

| Token | Hex/Value | Usage |
|-------|-----------|-------|
| `brand` | `#009EF5` | Primary actions, logo color |
| `brand-dark` | `#0080CC` | Hover states, active states |
| `brand-light` | `#E6F4FF` | Backgrounds, highlights |
| `danger` | Tailwind `rose` | Errors, destructive actions |
| `success` | Tailwind `green` | Confirmations, available rooms |
| `warning` | Tailwind `yellow/amber` | Alerts, approaching deadlines |
| `info` | Tailwind `blue` | Informational, deposits |

### Room Status Colors (from V3)

| Status | Color | Hex Suggestion |
|--------|-------|----------------|
| Available | Green | `#22C55E` (green-500) |
| Occupied | Red | `#EF4444` (red-500) |
| Reserved | Blue | `#3B82F6` (blue-500) |
| Uncleaned | Orange | `#F97316` (orange-500) |
| Cleaning | Yellow | `#EAB308` (yellow-500) |
| Cleaned | Teal | `#14B8A6` (teal-500) |
| Maintenance | Gray | `#6B7280` (gray-500) |

---

## Typography

| Purpose | Font | Weight |
|---------|------|--------|
| **Primary (body, UI)** | DM Sans | 400, 500, 600, 700 |
| **Decorative (branding)** | Alkatra | 400 |

Both loaded via Google Fonts.

---

## Tailwind Configuration (V3 Reference)

```javascript
// tailwind.config.js — to be adapted for V4
module.exports = {
  presets: [require('./vendor/wireui/wireui/tailwind.config.js')],
  theme: {
    fontFamily: {
      sans: ['DM Sans', 'sans-serif'],
      alkatra: ['Alkatra', 'cursive'],
    },
    extend: {
      colors: {
        brand: '#009EF5',
        // Filament will use this as primary
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
```

---

## Filament Table Styling (V3 Custom CSS)

```css
/* Custom Filament table header — brand blue */
.fi-ta-header-cell {
  background-color: #009EF5 !important;
  color: white !important;
}

.fi-ta-ctn {
  background-color: white !important;
}
```

---

## Login/Welcome Page

```
V3 Design:
  - Full-screen gradient: from-[#009EF5] to-gray-300 (opacity 30%)
  - Centered logo: homiLogo.png
  - Login form card (white, rounded, shadow)
  - Background image: hotel-bg.jpg

V4: Keep same visual identity, modernize components.
```

---

## Assets Inventory

All assets copied to `docs/v4-project/assets/`:

| File | Purpose | Keep for V4? |
|------|---------|-------------|
| `homiLogo.png` | Primary brand logo | Yes |
| `homiLogo2.png` | Secondary logo | Yes |
| `hotel-bg.jpg` | Login background | Yes |
| `default.jpeg` | Default placeholder | Yes |
| `category.jpg` | Category placeholder | Review |
| `categories.png` | Categories image | Review |
| `menu.jpg` | Menu background | Review |
| `inventories.jpg` | Inventory reference | Review |
