# Auth Service

Laravel 12 JWT authentication service for the Danendz platform. Issues tokens that are shared across all services via a common `JWT_SECRET`. Deployed on Railway.com at `https://api.danendz.com/api/auth/`.

## Tech Stack

| | |
|---|---|
| **Language** | PHP ^8.4 |
| **Framework** | Laravel 12 |
| **Database** | PostgreSQL |
| **Auth** | JWT (`php-open-source-saver/jwt-auth`) |
| **Testing** | Pest |

## API Endpoints

| Method | Endpoint | Auth required | Description |
|---|---|---|---|
| `POST` | `/auth/register` | No | Register a new user |
| `POST` | `/auth/login` | No | Login, returns access + refresh tokens |
| `GET` | `/auth/me` | Yes | Get current user info |
| `POST` | `/auth/logout` | Yes | Invalidate current token |
| `POST` | `/auth/refresh` | No | Refresh access token |

All responses are JSON. Protected endpoints require `Authorization: Bearer <token>`.

## Getting Started

```bash
# Install dependencies, run migrations, build assets
composer setup

# Start dev server
composer dev
```

Or with Sail (Docker):

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
```

## Testing

```bash
composer test

# Or with Sail
./vendor/bin/sail test
```

## Environment Variables

```env
APP_PORT=8082

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=auth
DB_USERNAME=sail
DB_PASSWORD=password

JWT_SECRET=          # shared with all other services
JWT_TTL=60           # access token TTL in minutes
JWT_REFRESH_TTL=     # refresh token TTL in minutes
```

## Deployment

Deployed on Railway.com. Base URL: `https://api.danendz.com/api/auth/`

The `JWT_SECRET` must be identical across all services (Auth, Marquer Backend, etc.) for cross-service token validation to work.
