# WP Client Onboarding

WordPress plugin that provides an embedded user manual in the admin dashboard for client onboarding.

## Features

- Admin-only documentation hub accessible from the WP admin menu
- Rich content sections: text, images, embedded videos (via URL)
- Custom Post Type for managing manual sections
- Role-based access control: editors manage content, admins read only
- Section ordering (drag & drop or manual order)
- JSON import for pre-populated manuals on site delivery
- Minimalist UI that matches WordPress admin design

## Requirements

- WordPress 6.9+
- PHP 8.2+

## Installation

1. Download or clone this repository into `wp-content/plugins/`
2. Activate the plugin from the WordPress admin
3. Navigate to **Manuel** in the admin sidebar

## Security

- All inputs sanitized, all outputs escaped
- Nonce verification on every form and AJAX request
- Capability checks on every action
- Direct file access blocked

## License

GPLv2 or later
