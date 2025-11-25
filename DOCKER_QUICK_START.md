# 🚀 SkillLink Docker Quick Start

## One-Command Start

### Windows
```bash
start-docker.bat
```

### Linux/Mac
```bash
chmod +x start-docker.sh
./start-docker.sh
```

### Manual Start
```bash
docker-compose up -d --build
```

## Access Points

| Service | URL | Credentials |
|---------|-----|-------------|
| **SkillLink App** | http://localhost:8080 | Use app registration |
| **phpMyAdmin** | http://localhost:8081 | root / rootpassword |
| **MySQL** | localhost:3306 | root / rootpassword |

## Essential Commands

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs -f

# Restart app
docker-compose restart app

# Access app shell
docker exec -it skilllink-app bash

# Run tests
docker exec -it skilllink-app vendor/bin/phpunit
```

## First Time Setup

1. **Start Docker Desktop** (Windows/Mac)

2. **Run the startup script**:
   ```bash
   start-docker.bat    # Windows
   ./start-docker.sh   # Linux/Mac
   ```

3. **Wait 30-60 seconds** for database initialization

4. **Open browser**: http://localhost:8080

5. **Register an account** or use admin credentials from `adminCredentials.txt`

## Troubleshooting

### "Port already in use"
Edit `docker-compose.yml` and change port numbers:
```yaml
ports:
  - "9090:80"  # Change from 8080
```

### "Database connection failed"
Wait longer - first startup takes time. Check status:
```bash
docker-compose ps
docker-compose logs db
```

### "Permission denied"
```bash
docker exec -it skilllink-app chown -R www-data:www-data /var/www/html
```

## Configuration

### Add Google Books API Key
1. Copy `.env.example` to `.env`
2. Add your API key:
   ```
   GOOGLE_BOOKS_API_KEY=your_key_here
   ```
3. Restart containers:
   ```bash
   docker-compose restart
   ```

## Complete Documentation

See [DOCKER_SETUP.md](DOCKER_SETUP.md) for detailed documentation.

