# Restaurant Analytics Demo

A small event analytics application built with a Laravel API backend, a separate Next.js frontend, and PostgreSQL for persistence. The project demonstrates browser event capture, server-side ingestion, and a lightweight dashboard for restaurant-style analytics.

## Overview

This project is intentionally simple and API-first:

- Backend: Laravel 13 + PHP 8.4+
- Frontend: Next.js 16 + React + TypeScript
- Database: PostgreSQL 16
- Containerization: Docker + Docker Compose

The frontend sends analytics events to the Laravel backend, which stores them in the `events` table and exposes summary data for the dashboard.

## Architecture

```text
frontend (Next.js)
    └── sends page_view and order_created events
            ↓
backend (Laravel API)
    └── validates and stores events in PostgreSQL
            ↓
analytics dashboard
    └── aggregates totals, pages, and trend data
```

## Project Structure

```text
project-root/
├── app/                  # Laravel API backend
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── routes/
│   ├── tests/
│   ├── .env
│   ├── composer.json
│   ├── Dockerfile
│   └── artisan
├── frontend/             # Next.js frontend
│   ├── src/
│   ├── package.json
│   ├── Dockerfile
│   └── next.config.ts
├── docker-compose.yml
├── .gitignore
└── README.md
```

## Features

- Event ingestion endpoint for browser and server events
- Generic `events` model with JSON metadata support
- Page view and order event tracking demo
- Dashboard summary cards
- Popular page analysis
- Event trend visualization
- Separate frontend/backend deployment model
- Ready-to-run Docker setup

## Prerequisites

For local development without Docker:

- PHP 8.4.1 or newer
- Composer 2+
- Node.js 20+
- npm 10+
- PostgreSQL 16

For Docker-based setup:

- Docker
- Docker Compose

## Quick Start with Docker

From the project root:

```bash
cd /path/to/project
docker compose up --build
```

Then open:

- Frontend: http://localhost:3000
- Backend API: http://localhost:8000
- PostgreSQL: localhost:5432

Docker Compose starts:

- `analytics-db` PostgreSQL container
- `analytics-backend` Laravel API container
- `analytics-frontend` Next.js frontend container

## Local Development Setup

### 1. Backend (Laravel)

```bash
cd /path/to/project/app
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve --host=0.0.0.0 --port=8000
```

Make sure the app environment points to PostgreSQL:

```env
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

### 2. Frontend (Next.js)

```bash
cd /path/to/project/frontend
npm install
npm run dev -- --hostname 127.0.0.1 --port=3000
```

The frontend expects the backend API URL to be available through the app environment or fallback configuration. In the demo it is set to the Laravel API on port 8000.

## Environment Variables

### Backend (`app/.env`)

```env
APP_NAME="Restaurant Analytics"
APP_ENV=local
APP_DEBUG=true
APP_KEY=base64:...
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

### Frontend (`frontend/.env.local` or equivalent)

```env
NEXT_PUBLIC_API_URL=http://localhost:8000
```

## API Endpoints

### Hello endpoint

```http
GET /api/hello
```

Returns a simple JSON response confirming the API is live.

### Event ingestion

```http
POST /api/events
Content-Type: application/json
```

Example payload:

```json
{
  "event_name": "page_view",
  "page": "/",
  "source": "browser",
  "session_id": "sess_123",
  "user_id": "user_456",
  "user_agent": "Mozilla/5.0",
  "ip_address": "127.0.0.1",
  "metadata": {
    "restaurant": "Sunset Grill",
    "section": "home"
  }
}
```

### Dashboard summary

```http
GET /api/analytics/dashboard?start=2026-10-01&end=2026-10-31
```

Returns:

- total events
- page view count
- unique visitors
- popular pages
- event trend by date

## Database Schema

The analytics model stores events in a generic `events` table.

Relevant fields:

- `event_name`
- `page`
- `source`
- `session_id`
- `user_id`
- `user_agent`
- `ip_address`
- `metadata` (JSON)
- `created_at`
- `updated_at`

The model is defined in:

```text
app/app/Models/Event.php
```

## Frontend Behavior

The frontend renders a restaurant analytics page with:

- summary cards for metrics
- a bar chart for event totals
- simulated page visits and orders
- calls to the Laravel API for event tracking

The UI is intentionally minimal and meant as a demo of how analytics data can be captured and displayed.

## Tests

The Laravel backend includes feature tests for the analytics flow.

Run tests with:

```bash
cd /path/to/project/app
php artisan test
```

## Troubleshooting

### Docker build fails due to PHP version mismatch

This project requires PHP 8.4.1+ because of the locked Laravel 13 dependencies. If you see Composer errors about Symfony or Laravel requiring a newer PHP version, ensure the container uses `php:8.4-cli` and that your local PHP version is also 8.4+.

### Database connection refused

If the backend cannot connect to PostgreSQL:

```bash
docker compose up db
```

Then verify the container has started and that the env vars match the service configuration.

### Frontend not loading data

Ensure the frontend API base URL points to the backend:

```env
NEXT_PUBLIC_API_URL=http://localhost:8000
```

### Laravel migrations not running

Run:

```bash
cd /path/to/project/app
php artisan migrate --force
```

## Assumptions and Limitations

- This is a demo MVP, not a production analytics platform.
- There is no user authentication or role-based access control.
- Event validation is intentionally simple.
- The frontend uses a lightweight browser event tracker rather than a packaged SDK.
- The dashboard aggregates basic metrics only.

## Future Improvements

Possible next steps:

- Add authentication and protected analytics endpoints
- Create a reusable browser tracking SDK
- Add more detailed date filtering and charts
- Support server-side event tracking helpers
- Add pagination or export tools for event data
- Move the API URL to a proper config system

## License

This project is intended for learning and demonstration purposes in the current workspace.
