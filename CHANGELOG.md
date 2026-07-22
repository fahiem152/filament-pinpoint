# Changelog

All notable changes to `filament-pinpoint` will be documented in this file.

## v1.1.8 - 2026-07-22

### Added

- 🔒 **Read-only mode support** - Implement `CanBeReadOnly` on Pinpoint form component (#18 by [@gpibarra](https://github.com/gpibarra))
  - New `readOnly()` method to make the map display-only (no drag, no click, no search)
  - New `readOnlyOn()` method to restrict editing on specific operations (e.g., `readOnlyOn('view')`)
  - Supports closures for conditional read-only: `readOnly(fn () => !auth()->user()->can('edit'))`
  - Works with both Google Maps and Leaflet providers
  - Map remains pannable and zoomable for viewing
  - Radius circle is displayed but not editable when read-only
  - Search box, "Use My Location" button, and helper text are hidden when read-only
- 🌐 **Spanish (es) translation** — Added Spanish localization for all UI strings (PR #17 by [@daljo25](https://github.com/daljo25))

### Improved

- 🎨 **Theme-compatible CSS** - Replaced all hardcoded CSS colors with Filament CSS variables (#20 by [@RibesAlexandre](https://github.com/RibesAlexandre))
  - Search input, dropdown, button, and map border now use `var(--gray-*)` and `var(--primary-*)` instead of hardcoded hex values
  - Plugin automatically adapts to custom Filament themes and color palettes (e.g., Slate, Zinc, custom primary colors)
  - Radius circle color now follows the configured primary theme color
  - Replaced non-compiling Tailwind utility classes with theme-aware CSS classes
  - Removed all `!important` overrides from dark mode styles

## v1.1.7 - 2026-05-04

### Fixed

- 🌙 **Dark mode dropdown unreadable on Leaflet provider** - Fixed search autocomplete dropdown showing white background with invisible text in dark mode (#16 by [@daljo25](https://github.com/daljo25))
  - Replaced Tailwind utility classes with plain CSS using `.dark` selector, since plugin views aren't scanned by the app's Tailwind JIT compiler
  - Scoped all search component styles (`.pinpoint-search-input`, `.pinpoint-search-dropdown`, `.pinpoint-search-item`) under `.fi-fo-pinpoint` for proper specificity
  - Search input, dropdown container, dropdown items, and result text now fully adapt to dark mode
  - Applied same fix to Google Maps search input for consistency

## v1.1.6 - 2026-03-31

### Added

- 🗺️ **Leaflet.js provider** - Free alternative to Google Maps using OpenStreetMap + Nominatim. No API key required. Set `PINPOINT_PROVIDER=leaflet` in your `.env` to enable.
  - Full search via Nominatim (location search & reverse geocoding)
  - Draggable marker, resizable radius circle, current location button
  - All auto-fill fields supported: address, province, city, district, village, postal code, country, street, street number
  - Multi-marker support for `PinpointEntry` with colored SVG pins, popups, and `fitBounds`
- 🌓 **Dark tile URL for Leaflet** - Set `LEAFLET_TILE_URL_DARK` to use a different tile layer in dark mode (e.g. CartoDB Dark)
- ⚙️ **`provider()` method** - Override the map provider per field instance: `Pinpoint::make('location')->provider('leaflet')`
- 📦 **New config keys**: `provider`, `leaflet.tile_url`, `leaflet.tile_url_dark`, `leaflet.tile_attribution`, `leaflet.nominatim_url`

### Notes

- **Fully backward compatible** — existing users default to `provider = 'google'`, no changes required
- Leaflet.js (1.9.4) and its CSS are loaded on-demand from unpkg CDN only when the leaflet provider is active

## v1.1.5 - 2026-02-16

### Added

- 🚀 **Filament v5 official support** - The package now fully supports Filament v5 alongside v4. Composer constraint updated to `^4.0|^5.0` (PR #11 by [@jyrkidn](https://github.com/jyrkidn), PR #12 by [@manusiakemos](https://github.com/manusiakemos))
- 🏠 **Street & street number auto-fill** - New `streetField()` and `streetNumberField()` methods for auto-filling street name and street number from reverse geocoding (PR #11 by [@jyrkidn](https://github.com/jyrkidn))

### Fixed

- 🌙 **Dark theme search box** - Fixed search input background and border styling in dark mode (PR #14 by [@ismailalterweb](https://github.com/ismailalterweb))
  - Changed dark background from `gray-900` to `gray-800` for better contrast
  - Removed unnecessary `!important` overrides on border and focus styles

## v1.1.4 - 2026-01-26

### Added

- 🚀 **Filament v5 support** - The package now officially supports Filament v5.
- 📦 Updated composer constraints to allow `filament/filament: ^5.0`.

### Improved

- 🎨 **Enhanced radius circle UX and visual hierarchy**
  - Changed circle color from red to blue (#4285F4) for better visual appearance
  - Added `zIndex` layering: marker (200) appears above circle (100)
  - Added `clickable: true` to circle for better interactivity
  - Improved drag-to-resize experience with proper z-index stacking
- 🔧 Code quality improvements (quote style consistency)

### Technical Details

- Circle is now fully editable with `editable: true` property
- Users can resize radius by dragging the circle's edge handles
- Radius value automatically syncs to form field in real-time
- Better visual feedback during drag operations

## v1.1.3 - 2026-01-06

### Added

- ✨ **Multi-pin support for PinpointEntry** - Display multiple markers on a single map in infolist view
  - New `pins()` method to set array of multiple markers with coordinates
  - Each pin supports optional `label` for marker title
  - Each pin supports optional `color` (8 predefined colors: red, blue, green, yellow, purple, pink, orange, ltblue)
  - Each pin supports optional `icon` for custom marker icon URL
  - Each pin supports optional `info` for custom HTML info window content
  - Info windows appear on marker click
- 🎯 **Auto-fit bounds** - Map automatically zooms to show all markers
  - New `fitBounds()` method to enable/disable auto-fit behavior
  - When enabled, map viewport adjusts to display all pins
  - Single pin displays at default zoom level
- 📍 **"Use my location" button** - New GPS location button for Pinpoint form field
  - Uses browser's Geolocation API to get current device location
  - Auto-fills coordinates and reverse geocodes address
  - Clean grayscale design that works with any theme
  - Smooth hover and active states
  - Full dark mode support

### Fixed

- 🐛 Fixed "Use my location" button positioning issues in form field
  - Button no longer appears outside form container
  - Proper RTL (Right-to-Left) layout support
  - Consistent styling across different themes

### Changed

- 🎨 Improved map container overflow handling with `overflow: clip` for stricter clipping
- 🔄 Refactored PinpointEntry to support both single marker and multiple markers
- 💅 Enhanced button styling with better accessibility (focus states, active states)

## v1.1.2 - 2025-12-19

### Fixed

- 🔧 **Environment variables now properly override default map settings** (#5)
  - Default properties (`defaultLat`, `defaultLng`, `defaultZoom`, `height`) changed from hardcoded values to `null`
  - Config values from `.env` are now used when `defaultLocation()` is not explicitly called
  - Added `GOOGLE_MAPS_DEFAULT_HEIGHT` environment variable support

- 🔄 **Repeater field compatibility** (#6)
  - Added `getFieldPath()` function to calculate correct field paths for Repeater items
  - Fixed field updates not working inside Repeater (was using `data.latitude` instead of `data.items.0.latitude`)
  - Added `loadExistingCoordinates()` to properly load saved lat/lng values when editing Repeater items
  - Map now correctly displays saved location when editing (instead of default location)

### Changed

- Updated all `$wire.set()` calls to use dynamic path calculation
- Improved state hydration for nested form structures

### Contributors

- Thanks to [@dmitrijsmihailovs](https://github.com/dmitrijsmihailovs) for reporting #5
- Thanks to [@nicollassilva](https://github.com/nicollassilva) for reporting #6

## v1.1.1 - 2025-12-15

### Added

- 🌐 **Multi-language support** with translation files:
  - English (en)
  - Arabic (ar) - العربية
  - Dutch (nl) - Nederlands
  - Indonesian (id)
- New address component fields:
  - `shortAddressField()` - Auto-fill short address (premise + route + street number)
  - `countryField()` - Auto-fill country name
- `PinpointEntry` component for Infolists (read-only map display)

### Fixed

- Translation strings now use proper Laravel package namespace (`filament-pinpoint::pinpoint.*`)
- Restored missing translation files that were accidentally removed

### Contributors

- Thanks to [@ismailalterweb](https://github.com/ismailalterweb) for:
  - Short address & country fields (PR #2)
  - PinpointEntry infolist component (PR #3)
  - Translation support

## v1.1.0 - 2025-12-12

### Added

- New address component fields for reverse geocoding:
  - `provinceField()` - Auto-fill province/state (administrative_area_level_1)
  - `cityField()` - Auto-fill city/county (administrative_area_level_2)
  - `districtField()` - Auto-fill district (administrative_area_level_3)
  - `postalCodeField()` - Auto-fill postal/zip code
- Improved search input styling with visible border

### Fixed

- Address component fields now always update when location changes
- Fields are set to `null` when Google Maps API returns no data, preventing stale data from persisting
- Search input border now displays correctly in both light and dark modes

### Changed

- Relocated current location button position for better UX
- Updated button styling (border-radius changed to 20px)

### Contributors

- Thanks to [@SalmanAlmajali](https://github.com/SalmanAlmajali) for the address components feature (PR #1)
- Thanks to [@ismailalterweb](https://github.com/ismailalterweb) for the null handling feedback

## v1.0.3 - 2025-12-04

### Fixed

- 🐛 Map blank on edit - map now displays correctly when editing existing records
- 🐛 Address not showing in search box - address from database now displays in search input during edit mode
- 🐛 Lat/Lng not saving properly - coordinates from database (string) now correctly converted to float

### Changed

- Add `addressField` to state hydration for loading address from database on edit
- Add `parseFloat()` for lat/lng string to float conversion
- Add `x-model="address"` binding on search input for two-way data binding
- Update `reverseGeocode()` to sync address state with search box

## v1.0.2 - 2025-12-01

### Added

- ⭐ Post-install star reminder message
- 🌙 Dark mode support for all UI elements

### Changed

- Search input now adapts to light/dark theme
- Location button now adapts to light/dark theme
- Helper text now adapts to light/dark theme
- Fix homepage URL in composer.json

### Removed

- Coordinates display below map (lat/lng still saved to form fields)

## v1.0.1 - 2025-12-01

### Fixed

- Minor fixes and improvements

## v1.0.0 - 2025-12-01

### Initial Release

- 📍 Google Maps location picker for Filament 4
- 🔍 Search location using Google Places Autocomplete
- 📍 Click on map to set marker
- ✋ Draggable marker support
- 📱 Get current device location
- 🏠 Reverse geocoding to auto-fill address fields
- ⚙️ Configurable default location, zoom, and height
