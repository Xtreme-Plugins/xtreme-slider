# Changelog

All notable changes to XtremeSlider are documented here.

## [1.2.1] - 2026-04-15

### Changed
- Renamed all plugin-specific PHP constants, classes, functions, and option keys to use the unique `xtrsl_` prefix for WordPress.org compliance
- Renamed database table prefixes from `xs_` to `xtrsl_` to avoid naming conflicts with other plugins
- Removed donate link returning a redirect response
- Added plugin author to contributors list in readme

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
