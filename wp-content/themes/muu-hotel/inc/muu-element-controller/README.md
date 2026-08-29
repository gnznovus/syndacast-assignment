# MUU Theme Controller

Theme-owned reusable components for the MUU Hotel Assessment theme.

The controller complements Elementor Free by keeping shared component behavior, settings, responsive presentation, and shortcode rendering inside the theme rather than duplicating them across Elementor pages.

## Architecture

```text
muu-element-controller/
├── assets/
│   ├── css/
│   ├── images/
│   └── js/
├── includes/
│   ├── class-muu-admin-controller.php
│   ├── class-muu-element-controller.php
│   └── class-muu-footer-controller.php
└── bootstrap.php
```

Responsibilities are intentionally separated:

- **MUU_Admin_Controller** — WordPress admin page, AJAX panels, shortcode documentation, settings forms, and Media Library integration.
- **MUU_Element_Controller** — navigation/left-tab, orange-shape and contact-form shortcodes, frontend assets, fonts, and component settings.
- **MUU_Footer_Controller** — footer settings and shortcode rendering.

## WordPress Admin

After activating **MUU Hotel Assessment**, open:

**WordPress Admin → MUU Controller**

The controller provides:

- Overview
- Shortcodes
- Navigation settings
- Footer settings
- shortcode examples and supported arguments
- copy controls for generated shortcode examples
- Media Library integration for footer artwork

The in-dashboard **Shortcodes** panel is the canonical reference for configurable shortcode arguments.

## Shortcodes

### Navigation + Left Tab

```text
[muu_nav_lefttab]
```

The navigation can render with or without the expandable left-tab content.

Example without the left tab:

```text
[muu_nav_lefttab left-tab="false"]
```

Example with desktop/mobile color overrides:

```text
[muu_nav_lefttab left-tab="false" logo-color="#000" nav-color="#000" mobile-logo-color="#fff" mobile-nav-color="#fff"]
```

When used over an Elementor hero, add `muu-hero-host` to the target container's **Advanced → CSS Classes** field.

### Orange Shape

```text
[muu_orange_shape]
```

Renders the configurable decorative orange artwork. Position, size, rotation, flip, color, class, and target behavior can be configured through shortcode arguments.

### Contact Form

```text
[muu_contact_form id="254"]
```

Renders the MUU-styled Contact Form 7 presentation. Contact Form 7 handles validation/submission processing and Flamingo stores submissions.

Use the actual Contact Form 7 form ID for the target installation.

### Footer

```text
[muu_footer]
```

Renders the reusable responsive footer using values configured in **MUU Controller → Shortcodes → Footer**.

## Notes

- Component settings are stored through the WordPress Settings API and sanitized before persistence.
- Admin AJAX requests are protected by capability checks and a nonce.
- Frontend output is escaped according to context.
- Responsive component behavior is maintained in the controller's frontend styles rather than duplicated in Elementor page content.
- Detailed argument descriptions and generated usage examples are maintained in the WordPress admin UI.
