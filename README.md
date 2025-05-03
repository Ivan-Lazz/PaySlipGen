# Payroll System API

This is the production API for the BM Payroll System.

## Installation

1. Clone the repository
2. Install dependencies: `composer install --no-dev --optimize-autoloader`
3. Copy `.env.example` to `.env` and configure
4. Run migrations: `php database/migrator.php up`
5. Set up web server to point to `/public` directory

## Requirements

- PHP 7.4+
- MySQL 5.7+
- Composer

## API Documentation

See `/docs/api.md` for API endpoints and usage.

## Security

Make sure to:
- Set proper file permissions
- Keep `.env` file secure
- Regularly update dependencies
- Use HTTPS in production

## Support

For support, contact: admin@example.com
