# Syndacast WordPress Developer Skills Assessment

Two-page responsive hotel website built for the Syndacast WordPress Developer Skills Assessment.

## Scope

- Home
- Contact Us
- Desktop implementation closely follows the supplied Figma design
- Tablet and mobile layouts are adapted responsively
- Main content is editable through WordPress Admin
- Contact Us includes a working contact form

## Local Development

### Requirements

- Docker Desktop / Docker Engine with Docker Compose

### Setup

```bash
cp .env.example .env
docker compose up -d
```

Open `http://localhost:8080` (or the port configured in `.env`) and complete the WordPress installer.

The custom theme is mounted from:

```text
wp-content/themes/muu-hotel
```

Activate **MUU Hotel Assessment** from WordPress Admin.

## Development Approach

The project uses a lightweight custom WordPress theme for the global visual system, shared layout, responsive behavior, and WordPress integration. Elementor Free is used for editable main page content where appropriate, avoiding Elementor Pro-only functionality.

This split keeps the implementation maintainable and design-focused while ensuring the assessment's main content can be managed through WordPress Admin.

## SEO, Performance & Accessibility

SEO readiness is treated as part of the theme architecture rather than being dependent on a specific SEO plugin.

The implementation aims to provide:

- semantic HTML5 landmarks and a logical heading hierarchy
- native WordPress document title support
- crawlable page content and clean internal navigation
- meaningful image alternative text managed through the WordPress Media Library
- WordPress responsive image support (`srcset` and `sizes`) where applicable
- lightweight theme assets with minimal JavaScript and third-party dependencies
- responsive layouts designed with Core Web Vitals in mind
- keyboard-friendly navigation and accessible form markup
- compatibility with common WordPress SEO plugins without duplicating or conflicting with their metadata output

Structured data will only be added where the required real business information is available; placeholder hotel data will not be exposed as production schema.

## Plugins

- Elementor (Free)
- Contact form plugin: to be finalized during Contact Us implementation

Plugins are kept intentionally minimal. SEO fundamentals are implemented at theme/content level so the site remains compatible with a dedicated SEO plugin if one is introduced later.

## Design Reference

The supplied Figma/FigJam desktop layouts are treated as the primary visual reference. Responsive tablet/mobile layouts are adapted using the same visual language and standard responsive UX practices.

Typography follows the font assets supplied with the assessment where licensing permits their use in the submitted source.

## Implementation Priorities

1. Desktop Figma accuracy
2. Responsive tablet/mobile UX
3. WordPress-editable content
4. Working contact form
5. SEO, performance and accessibility fundamentals
6. Clean, maintainable implementation
7. Visual polish and QA
8. Documentation

## Status

Foundation setup in progress.
