# WDOD Elementor Widgets

Three production-ready Elementor widgets — **Pricing Table**, **Team Member** and **Testimonial Slider** — packaged the way a client project needs them: full Style tabs, dynamic tags, repeaters, theme-overridable templates, and **shortcode fallbacks that keep the content rendering even when Elementor is deactivated**.

![PHP 7.4+](https://img.shields.io/badge/PHP-7.4%2B-777bb4?logo=php&logoColor=white)
![WordPress 6.4+](https://img.shields.io/badge/WordPress-6.4%2B-21759b?logo=wordpress&logoColor=white)
![Elementor 3.5+](https://img.shields.io/badge/Elementor-3.5%2B%20(optional)-92003b?logo=elementor&logoColor=white)
![WPCS](https://img.shields.io/badge/code%20style-WordPress-0073aa)
![License GPL-2.0-or-later](https://img.shields.io/badge/license-GPL--2.0--or--later-blue)

## What it does

- Registers a **"WDOD Widgets"** category in the Elementor panel with three widgets.
- Every widget renders through a plain PHP template in `templates/`. The **same templates power the shortcodes**, so a site never loses content if Elementor is removed, and a theme can override any of them.
- Widget classes are loaded **only inside `elementor/widgets/register`**, so nothing references Elementor classes at file-load time and the plugin can be active without Elementor.
- Requirement checks (Elementor ≥ 3.5, PHP ≥ 7.4) produce a **dismissible admin notice** with one-click *Install* / *Activate* / *Update Elementor* links (capability-checked and nonce-protected).
- Meta lookups go through an **ACF-aware helper**: `get_field()` when ACF is active, `get_post_meta()` otherwise.
- The Testimonial Slider uses **Elementor's bundled Swiper** (`elementorFrontend.utils.swiper`) and falls back to a **CSS scroll-snap carousel** when Swiper is unavailable — no extra library shipped.
- Colours default to Elementor's **global colour variables** (`--e-global-color-primary` …) with plain fallbacks.

## Widgets

### Pricing Table (`wdod-pricing-table`)

| Tab | Section | Controls |
| --- | --- | --- |
| Content | Header | Title (dynamic), Featured plan switcher, Badge text (dynamic, shown when featured) |
| Content | Price | Currency symbol, Price, Period (all dynamic) |
| Content | Features | Repeater: Feature text (dynamic), Included switcher, Custom icon (ICONS) |
| Content | Button | Text (dynamic), Link (URL control with new-tab / nofollow, dynamic) |
| Style | Box | Background, Featured accent colour, Border, Border radius, Box shadow, Padding, Alignment |
| Style | Title & Price | Title colour + typography, Price colour + typography, Period colour + typography, Badge colours |
| Style | Features | Text colour, Typography, Included / Excluded icon colours, Excluded opacity, Row gap |
| Style | Button | Typography, Normal / Hover colours and backgrounds, Border, Radius, Padding, Full width |

### Team Member (`wdod-team-member`)

| Tab | Section | Controls |
| --- | --- | --- |
| Content | Member | Source (Manual / Post), Post select (50 most recent posts from `wdod_ew_team_post_types`), Name, Role, Photo (MEDIA + Image Size group control), Bio, Alignment |
| Content | Social links | Repeater: Icon (ICONS), Label, Link (URL, dynamic) |
| Style | Box | Background, Border, Radius, Shadow, Padding |
| Style | Photo | Width, Shape (circle / rounded / square), Radius, Border, Spacing |
| Style | Text | Name / Role / Bio colours and typography |
| Style | Social icons | Icon size, Gap, Normal / Hover icon colour and background |

In **Post** mode the widget reads the post title and featured image plus the fields `role`, `bio`, `photo` and `socials` (ACF field names or plain meta keys). `socials` may be an ACF repeater (`network`/`label` + `url`) or a JSON string.

### Testimonial Slider (`wdod-testimonial-slider`)

| Tab | Section | Controls |
| --- | --- | --- |
| Content | Testimonials | Repeater: Quote, Author, Role / Company, Avatar (MEDIA), Rating 0–5; Show rating switcher |
| Content | Slider | Slides per view (responsive 1–4), Space between (responsive), Autoplay, Autoplay speed, Pause on hover, Infinite loop, Transition speed, Arrows, Dots |
| Style | Card | Background, Border, Radius, Shadow, Padding |
| Style | Text | Quote / Author / Role colours and typography, Star colours, Avatar size |
| Style | Navigation | Arrow colour, background and size, Dot / active dot colours |

The widget outputs Swiper-compatible markup (`.swiper > .swiper-wrapper > .swiper-slide`) with a `data-settings` JSON attribute consumed by `assets/js/frontend.js`.

## Shortcode fallback

All three shortcodes work with **or without Elementor** and enqueue the front-end assets on demand.

```text
[wdod_pricing_table title="Professional" price="29" currency="$" period="/ month"
    features="10 projects|Unlimited storage|Priority support|-White label"
    button_text="Get started" button_url="https://example.com/checkout"
    featured="yes" badge="Most popular"]
```
Prefix a feature with `-` to mark it as *not included*.

```text
[wdod_team_member post_id="42" size="medium"]

[wdod_team_member name="Jane Doe" role="Lead Developer" photo="128"
    bio="Builds fast, accessible WordPress sites."
    socials="linkedin:https://linkedin.com/in/jane|x:https://x.com/jane|github:https://github.com/jane"]
```
`photo` accepts an attachment ID or a URL. Supported social icons: `facebook`, `x` (or `twitter`), `linkedin`, `instagram`, `github`, `youtube`, `email`, anything else renders a link icon.

```text
[wdod_testimonials post_type="testimonial" count="6" orderby="date" order="DESC"
    autoplay="yes" autoplay_speed="5000" loop="yes"
    slides="2" slides_tablet="2" slides_mobile="1" space="24"
    arrows="yes" dots="yes"]
```
Pulls published posts of the given type. The quote is the post content; `author` (falls back to the post title), `role`, `avatar` (falls back to the featured image) and `rating` are read via the meta helper. Use `ids="3,8,12"` to pick specific posts.

## Screenshots

_Screenshots will be added here (widgets panel, each widget on the front end, shortcode fallback)._

## Requirements

| | Minimum |
| --- | --- |
| WordPress | 6.4 |
| PHP | 7.4 |
| Elementor | 3.5 — **optional**; needed for the widgets, not for the shortcodes |
| ACF | optional; used automatically when present |

## Installation

1. Copy the `wdod-elementor-widgets` folder to `wp-content/plugins/` (or upload the zip in *Plugins → Add New*).
2. Activate **WDOD Elementor Widgets**.
3. With Elementor active you will find the widgets under the **WDOD Widgets** category. Without Elementor, use the shortcodes above.

## Hooks & Filters

| Hook | Type | Description |
| --- | --- | --- |
| `wdod_ew_loaded` | action | Fires after the plugin booted. Receives the `Plugin` instance. |
| `wdod_ew_team_post_types` | filter | Post types offered in the Team Member "Post" select. Default `['team', 'post']`. |
| `wdod_ew_template_path` | filter | Absolute path of a template file. Receives `$file, $name, $args`. |
| `wdod_ew_template_args` | filter | Arguments passed to a template. Receives `$args, $name`. |
| `wdod_ew_meta_value` | filter | Value returned by `Meta::get()`. Receives `$value, $post_id, $key`. |
| `wdod_ew_testimonials_query_args` | filter | `WP_Query` arguments used by `[wdod_testimonials]`. |
| `wdod_ew_widget_classes` | filter | Widget class names registered with Elementor (add your own `Widget_Base` subclasses). |

### Template overrides

Copy any file from `templates/` to `<your-theme>/wdod-elementor-widgets/<name>.php`. `Template::render()` checks `locate_template()` first, then the plugin default, and finally applies `wdod_ew_template_path`.

```php
add_filter( 'wdod_ew_template_path', function ( $file, $name ) {
	if ( 'pricing-table' === $name ) {
		return get_stylesheet_directory() . '/parts/pricing.php';
	}
	return $file;
}, 10, 2 );
```

## Development

```bash
composer install       # dev tooling only – the runtime never needs vendor/
composer lint          # php -l on every file
composer phpcs         # WordPress Coding Standards + PHPCompatibility (7.4-)
composer phpcbf        # auto-fix
```

Structure:

```text
wdod-elementor-widgets.php      bootstrap, constants, plugins_loaded hook
uninstall.php                   removes options / user meta
includes/
  class-autoloader.php          namespace → class-*.php mapping
  class-plugin.php              singleton, requirement checks
  class-admin-notices.php       dismissible notice with install/activate links
  class-assets.php              register/enqueue front-end + editor assets
  class-widgets-manager.php     category + widget registration
  class-shortcodes.php          shortcode fallbacks
  support/class-meta.php        ACF / post meta helper
  support/class-template.php    template loader with theme overrides
  support/class-icons.php       inline SVG icons for templates
  widgets/class-widget-base.php abstract base (category, style deps, helpers)
  widgets/class-pricing-table.php
  widgets/class-team-member.php
  widgets/class-testimonial-slider.php
templates/                      shared PHP views (widgets + shortcodes)
assets/css/frontend.css         BEM styles, Elementor colour variables, scroll-snap fallback
assets/css/editor.css           panel tweaks
assets/js/frontend.js           Swiper init via elementorFrontend.utils.swiper + vanilla fallback
languages/                      POT file
```

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

GPL-2.0-or-later.
