# Changelog

All notable changes to `filament-pinpoint` will be documented in this file.

## v1.1.4 - 2026-01-26

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
