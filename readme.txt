=== XtremeSlider ===
Contributors: xtremeplugins, loanpartnership
Tags: slider, image slider, carousel, responsive slider, 3d slider
Tested up to: 6.9
Stable tag: 1.2.1
Requires at least: 6.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Beautiful image slider with 3 layout modes, infinite loop, and shortcode embedding. No jQuery on frontend, no WooCommerce required.

== Description ==

XtremeSlider is a free, lightweight WordPress slider plugin that lets you create stunning image sliders with three distinct layout modes and embed them anywhere using a simple shortcode.

Built for performance — the frontend uses pure vanilla JavaScript with zero external dependencies.

= Three Layout Modes =

**Default** — Editorial full-bleed slider with peek effect. Center slides are fully visible while adjacent slides peek from the edges. Features infinite seamless looping, vertical title text overlay, and caption/description sections below center slides.

**Cool** — Card-based slider with customizable gradient background. Rounded-corner slides with hover lift effects and pagination dots.

**3D** — Dramatic CSS perspective slider with depth transforms. Center slide is prominent while adjacent slides rotate and scale back in 3D space with glassmorphism-styled arrows.

= Key Features =

* **Infinite Loop** — Default layout uses clone-based seamless cycling that never ends
* **Up to 10 Slides** — Add up to 10 images per slider with drag-to-reorder
* **4 Content Fields Per Slide** — Title (vertical overlay), Caption (heading below), Description (paragraph below), Link URL
* **Relative & Absolute Links** — Link to `/roofing` or `https://example.com`
* **Image Ratios** — Choose 16:10 (landscape) or 1:1 (square) per slider
* **Autoplay** — Configurable speed (2-10 seconds), pauses on hover
* **Fullscreen Mode** — 100vw edge-to-edge display
* **Touch & Swipe** — Full mobile touch support
* **Responsive** — 4 breakpoints (desktop, tablet, mobile, small mobile)
* **Hover Effects** — Image zoom on hover, caption underline color change
* **Link Hover Color** — Configurable per slider via color picker
* **Gradient Background** — Two-color gradient picker for Cool layout
* **Shortcode Overrides** — Override layout, visible count, autoplay, fullscreen per instance
* **Conditional Loading** — CSS/JS only load on pages containing a slider

= No Dependencies =

Fully standalone. No WooCommerce, Elementor, or any other plugin required.

= Elementor & Gutenberg Compatible =

Works in Elementor Shortcode widget, Gutenberg Shortcode block, Classic Editor, or any page builder.

== Support ==

For documentation and more information visit:

[https://xtremeplugins.com/plugins/xtreme-slider](https://xtremeplugins.com/plugins/xtreme-slider)

Please submit bugs, patches, and feature requests to:

[https://github.com/Xtreme-Plugins/xtreme-slider](https://github.com/Xtreme-Plugins/xtreme-slider)

== Installation ==

1. Download `xtreme-slider.zip`
1. Unzip
1. Upload the `xtreme-slider` directory to `/wp-content/plugins`
1. Activate the plugin
1. Create your first slider from the **Xtreme Slider** menu
1. Copy the shortcode and paste it into any page

== Screenshots ==

1. Cool layout — card-based slider with gradient background and pagination dots
2. 3D layout — perspective depth slider with center slide prominent and adjacent slides rotated
3. Default layout — editorial full-bleed slider with vertical title overlay, peek effect, and caption below
4. 3D layout — wide view showing five visible slides with depth transforms and glassmorphism arrows

== Frequently Asked Questions ==

= Does this work without WooCommerce? =

Yes. Fully standalone with zero external dependencies.

= Does it work with Elementor? =

Yes. Paste `[xtreme_slider id="X"]` into Elementor's Shortcode widget.

= How many slides can I add? =

Up to 10 slides per slider. Configure 1-6 visible at once.

= Is the slider responsive? =

Yes. Adapts across 4 breakpoints with touch/swipe on mobile.

= Does it slow down my site? =

No. Vanilla JavaScript (~5KB), no jQuery. Assets only load on pages with a slider.

= Can I use relative URLs? =

Yes. Paths like `/roofing` work. Internal links stay in same tab, external links open in new tab.

= Can I override settings per shortcode? =

Yes. `[xtreme_slider id="5" layout="3d" visible="2" autoplay="true" fullscreen="true"]`

== Changelog ==

= 1.2.1 - 15th April 2026 =
* Renamed all plugin-specific PHP constants, classes, functions, and option keys to use the unique `xtrsl_` prefix for WordPress.org compliance
* Renamed database table prefixes from `xs_` to `xtrsl_` to avoid naming conflicts
* Removed donate link that was returning a redirect response
* Added plugin author to contributors list in readme

= 1.2.0 - 25th March 2026 =
* Added image ratio option: 16:10 (landscape) or 1:1 (square) per slider
* Added link hover color picker — configurable per slider
* Added shortcode attribute overrides: layout, visible, autoplay, fullscreen
* Added mouse drag support for Default layout
* Improved responsive breakpoints across 4 screen sizes
* Refined 3D layout depth and rotation values
* Fixed infinite loop flickering on fast consecutive clicks
* Fixed 3D layout not recalculating on resize
* Fixed autoplay timer not resetting after manual navigation

= 1.1.0 - 14th February 2026 =
* Added Cool layout — card-based slider with gradient background
* Added pagination dots for Cool and 3D layouts
* Added fullscreen mode (100vw edge-to-edge)
* Added gradient background picker with live preview
* Added touch/swipe support for all layouts
* Added autoplay with configurable speed and hover pause
* Admin design overhaul — neumorphic design system
* Drag-to-reorder slides with sortable cards
* Visual radio cards for layout selection
* Fixed shortcode rendering empty div with no slides
* Fixed color picker popup overlap on small screens
* Fixed status toggle save issue

= 1.0.0 - 8th January 2026 =
* Initial production release
* Default layout — editorial full-bleed slider with peek effect and infinite loop
* 3D layout — perspective slider with depth transforms and glassmorphism arrows
* Up to 10 slides per slider, 1-6 visible at once
* Per-slide fields: title, caption, description, link URL
* Relative and absolute link support
* Image hover zoom effect
* Conditional CSS/JS loading
* Admin panel with slider list, edit screen, and settings
* Copy-to-clipboard shortcode button
* Clean uninstall — drops tables and options
