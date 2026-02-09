# Contributing to WP Client Onboarding

Thanks for your interest in contributing! This plugin provides an embedded user manual for WordPress admin.

## Getting Started

### Prerequisites

- PHP 8.2+
- WordPress 6.9+
- [DDEV](https://ddev.readthedocs.io/) (local dev)
- [Composer](https://getcomposer.org/)

### Local Setup

```bash
git clone git@github.com:Omerialis/wp-client-onboarding.git
cd wp-client-onboarding

# Start dev environment
cd dev
ddev start
ddev composer create roots/bedrock
ddev exec cp .env.example .env
# Configure .env: DB_NAME=db, DB_USER=db, DB_PASSWORD=db, DB_HOST=db, WP_HOME=${DDEV_PRIMARY_URL}
ddev exec wp core install --url='https://wcob-dev.ddev.site' --title='WCOB Dev' --admin_user=admin --admin_password=admin --admin_email=admin@example.com

# Mount plugin (add to dev/.ddev/docker-compose.plugin.yaml)
# services:
#   web:
#     volumes:
#       - ../../:/var/www/html/web/app/plugins/wp-client-onboarding:ro

ddev restart
ddev exec wp plugin activate wp-client-onboarding
```

## Branch Naming

- `feat/<description>` — new feature
- `fix/<description>` — bug fix
- `ci/<description>` — CI/CD changes
- `chore/<description>` — maintenance

## Commit Guidelines

We use [Conventional Commits](https://www.conventionalcommits.org/). This drives automatic versioning and releases.

| Prefix | Version bump | Usage |
|--------|-------------|-------|
| `feat:` | minor | New feature |
| `fix:` | patch | Bug fix |
| `ci:` | none | CI changes |
| `chore:` | none | Maintenance |
| `docs:` | none | Documentation |

Example:
```
feat: add search bar to manual sections list
fix: escape output in section title rendering
```

## Code Standards

- PHP: `declare(strict_types=1)`, PSR-12, snake_case methods
- JS: vanilla ES6+, no jQuery, `'use strict'`
- Security: sanitize all input, escape all output, nonce on forms, capability checks
- See `docs/rules/` for detailed coding rules

## Pull Request Process

1. Create a feature branch from `main`
2. Write code following the standards above
3. Test on a local DDEV environment
4. Open a PR targeting `main`
5. PR gets reviewed and merged
6. Release is auto-created if commits include `feat:` or `fix:`

## Releases

Fully automated via GitHub Actions:
- Merge to `main` triggers version calculation from conventional commits
- A zip is built, tagged, and published as a GitHub Release
- Download the zip from [Releases](https://github.com/Omerialis/wp-client-onboarding/releases) to install on any WordPress site

## License

GPLv2 or later. See [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html).
