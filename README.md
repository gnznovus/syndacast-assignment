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

## Plugins

- Elementor (Free)
- Contact form plugin: to be finalized during Contact Us implementation

## Design Reference

The supplied Figma/FigJam desktop layouts are treated as the primary visual reference. Responsive tablet/mobile layouts are adapted using the same visual language and standard responsive UX practices.

## Status

Foundation setup in progress.
