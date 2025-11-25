# SkillLink Docker Setup Guide

This guide will help you run the SkillLink application using Docker containers.

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) installed on your system
- [Docker Compose](https://docs.docker.com/compose/install/) installed (usually comes with Docker Desktop)

## Quick Start

### 1. Clone or Navigate to the Project

```bash
cd c:/xampp/htdocs/Projects
```

### 2. Create Environment File (Optional)

Copy the example environment file and configure it:

```bash
cp .env.example .env
```

Edit `.env` to add your Google Books API key if you want to use the book import feature:

```env
GOOGLE_BOOKS_API_KEY=your_api_key_here
```

### 3. Build and Start the Containers

```bash
docker-compose up -d --build
```

This command will:
- Build the PHP application image
- Start MySQL database
- Start phpMyAdmin
- Initialize the database with the schema from `database.sql`

### 4. Access the Application

Once the containers are running, you can access:

- **SkillLink Application**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
  - Server: `db`
  - Username: `root`
  - Password: `rootpassword`

## Container Services

### Application (app)
- **Port**: 8080
- **Technology**: PHP 8.1 with Apache
- **Document Root**: `/var/www/html/public`

### Database (db)
- **Port**: 3306
- **Type**: MySQL 8.0
- **Database Name**: `my-app`
- **Root Password**: `rootpassword`
- **User**: `skilllink` / Password: `skilllink123`

### phpMyAdmin (phpmyadmin)
- **Port**: 8081
- **Purpose**: Database management interface

## Common Docker Commands

### Start the containers
```bash
docker-compose up -d
```

### Stop the containers
```bash
docker-compose down
```

### Stop and remove all data (including database)
```bash
docker-compose down -v
```

### View logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f db
```

### Restart a service
```bash
docker-compose restart app
```

### Access container shell
```bash
# PHP application container
docker exec -it skilllink-app bash

# MySQL container
docker exec -it skilllink-db bash
```

### Run Composer commands
```bash
docker exec -it skilllink-app composer install
docker exec -it skilllink-app composer update
```

### Run PHPUnit tests
```bash
docker exec -it skilllink-app vendor/bin/phpunit
```

## Database Management

### Import SQL file
```bash
docker exec -i skilllink-db mysql -uroot -prootpassword my-app < project/database.sql
```

### Export database
```bash
docker exec skilllink-db mysqldump -uroot -prootpassword my-app > backup.sql
```

### Access MySQL CLI
```bash
docker exec -it skilllink-db mysql -uroot -prootpassword my-app
```

## Troubleshooting

### Port already in use
If ports 8080, 8081, or 3306 are already in use, edit `docker-compose.yml` and change the port mappings:

```yaml
ports:
  - "9090:80"  # Change 8080 to 9090
```

### Database connection failed
1. Check if the database container is healthy:
   ```bash
   docker-compose ps
   ```

2. Wait for the database to fully initialize (first run may take 30-60 seconds)

3. Check database logs:
   ```bash
   docker-compose logs db
   ```

### Permission issues
If you encounter permission issues with cache or uploads:

```bash
docker exec -it skilllink-app chown -R www-data:www-data /var/www/html/cache
docker exec -it skilllink-app chmod -R 755 /var/www/html/cache
```

### Rebuild containers
If you make changes to Dockerfile or need a fresh start:

```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

## Development Workflow

### Making Code Changes
The `project/` directory is mounted as a volume, so any changes you make to the code will be immediately reflected in the container.

### Installing New PHP Dependencies
```bash
docker exec -it skilllink-app composer require package/name
```

### Viewing Application Logs
```bash
docker-compose logs -f app
```

## Production Considerations

For production deployment, consider:

1. **Change default passwords** in `docker-compose.yml`
2. **Use environment variables** for sensitive data
3. **Enable HTTPS** with a reverse proxy (nginx/traefik)
4. **Set up proper backups** for the database volume
5. **Use `.env` file** instead of hardcoded values
6. **Remove phpMyAdmin** or restrict access

## Stopping the Application

To stop all containers:
```bash
docker-compose down
```

To stop and remove all data (including database):
```bash
docker-compose down -v
```

## Support

For issues related to:
- Docker setup: Check Docker logs and this guide
- Application errors: Check application logs in the container
- Database issues: Use phpMyAdmin or MySQL CLI to investigate

