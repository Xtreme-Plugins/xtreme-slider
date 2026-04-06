# Changelog

All notable changes to XtremeSlider are documented here.

## [1.3.1] - 2026-04-05

### Fixed
- Gradient color fields now display the actual saved value after reload — removed `?: default` fallback from the input `value=` attribute that was masking the real DB value with the default color even when a different color (including white) was saved
- Arrows no longer shown when slide count equals visible count (nothing to scroll)

## [1.3.0] - 2026-04-05

### Added
- Fixed Height image ratio option — user sets a pixel height; width scales automatically to preserve natural image proportions
- DB migration: `fixed_height` column added to `wp_xs_sliders` with live ALTER TABLE support for existing installs

### Changed
- Sidebar sections compacted (reduced padding/gaps) so Save button is visible without scrolling
- Fixed Height input now appears inline next to the Image Ratio select instead of occupying a separate row
- Color pickers now initialized per-field with correct `defaultColor` so Clear button restores the field's own default (red for link hover, pink/purple for gradient) instead of resetting to white

### Fixed
- Colors saving as `#ffffff` when Clear was clicked — Iris wp-color-picker was defaulting to white with no `defaultColor` set
- Hex color sanitizer now correctly accepts 3-digit shorthand (`#fff`) and expands to 6-digit
- Color field defaults (`link_hover_color`, `gradient_start`, `gradient_end`) applied after sanitization instead of before, preventing wrong fallback on invalid input

## [1.2.0] - 2026-03-25

### Added
- Image ratio option: choose between 16:10 (landscape) and 1:1 (square) per slider
- Link hover color picker — configurable per slider
- Shortcode attribute overrides: `layout`, `visible`, `autoplay`, `fullscreen`
- Mouse drag support for Default layout (click-and-drag to navigate)
- Click-through prevention after drag gesture to avoid accidental link navigation

### Changed
- Improved responsive breakpoints — smoother transitions between 4 screen sizes
- Default layout arrows repositioned closer to peek slides for better UX
- Refined 3D layout depth: adjusted rotation angles and scale factors for adjacent slides

### Fixed
- Clone-based infinite loop occasionally flickering on fast consecutive clicks
- 3D layout slides not recalculating positions on window resize
- Autoplay timer not resetting after manual navigation

## [1.1.0] - 2026-02-14

### Added
- Cool layout — card-based slider with customizable two-color gradient background
- Pagination dots for Cool and 3D layouts
- Fullscreen mode (100vw edge-to-edge display)
- Gradient background picker with live preview in admin
- Touch/swipe support for all layouts on mobile devices
- Autoplay with configurable speed (2–10 seconds) and hover pause

### Changed
- Admin design overhaul — migrated to neumorphic design system
- Slide cards now use drag-to-reorder with jQuery UI Sortable
- Replaced text-based layout selector with visual radio cards

### Fixed
- Shortcode rendering empty div when slider had no slides
- Admin color picker popup overlapping sidebar on smaller screens
- Status toggle not saving correctly when switching from active to draft

## [1.0.0] - 2026-01-08

### Added
- Initial release
- Default layout — editorial full-bleed slider with peek effect and infinite seamless looping
- 3D layout — CSS perspective slider with depth transforms and glassmorphism arrows
- Up to 10 slides per slider with 1–6 visible at once
- Per-slide fields: title (vertical overlay), caption, description, link URL
- Relative and absolute link support (internal links same tab, external new tab)
- Image hover zoom effect
- Conditional CSS/JS loading — assets only enqueued on pages containing a slider
- Admin panel with slider list, edit screen, and global settings page
- Copy-to-clipboard shortcode button
- Save button with loading spinner feedback
- Clean uninstall — drops custom tables and removes options

## [0.9.0] - 2025-12-18

### Added
- Beta release for internal testing
- Default and 3D layouts
- Basic admin UI with slide upload and ordering
- Shortcode registration and frontend rendering

### Known Issues
- No touch/swipe support yet
- Infinite loop has occasional visual glitch on fast clicks
- Admin not fully responsive below 960px
