# Application Configuration Guide

## Configure Environment Variables

Duplicate the `.env.example` file and rename it to `.env`.

```bash
cp .env.example .env
```

Edit the `.env` file and set your database connection details, news source API keys and other configuration options.

```dotenv
NYT_API_KEY=
NEWSAPI_API_KEY=
GUARDIAN_API_KEY=
```

```bash
php artisan key:generate
```

This command generates a unique application key for your Laravel application.

## Run Migrations

```bash
php artisan migrate
```

## Set Up a Scheduler
For auto syncing of articles from news source to work properly. we need to run the scheduler.

```bash
  php artisan schedule:run
```
