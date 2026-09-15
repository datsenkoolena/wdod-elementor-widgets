# Changelog

All notable changes to this project are documented in this file.
The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project adheres to [Semantic Versioning](https://semver.org/).

## [1.0.0] - 2026-09-14

### Added
- Pricing Table widget with features repeater, custom icons, featured badge, button URL control and full Style tab (typography, colours, border, shadow, radius, padding, hover states).
- Team Member widget with manual and post-driven modes (`wdod_ew_team_post_types` filter), image size group control, social links repeater and Style tab.
- Testimonial Slider widget on Elementor's bundled Swiper with responsive slides-per-view, autoplay, loop, arrows and dots; scroll-snap fallback when Swiper is unavailable.
- Shortcodes `[wdod_pricing_table]`, `[wdod_team_member]` and `[wdod_testimonials]` that render the same templates without Elementor.
- Theme template overrides via `<theme>/wdod-elementor-widgets/<template>.php` and the `wdod_ew_template_path` filter.
- ACF-aware meta helper (`get_field()` with `get_post_meta()` fallback).
- Dismissible admin notice with install / activate / update links when Elementor is missing or outdated.
- Dynamic tags on every text, URL and image control.
- WordPress Coding Standards + PHPCompatibility configuration, POT file, uninstall routine.
