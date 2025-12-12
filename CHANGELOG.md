# Changelog

All notable changes to `filament-pinpoint` will be documented in this file.

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
