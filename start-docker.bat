@echo off
REM SkillLink Docker Startup Script for Windows

echo.
echo Starting SkillLink with Docker...
echo.

REM Check if Docker is running
docker info >nul 2>&1
if errorlevel 1 (
    echo Error: Docker is not running. Please start Docker Desktop and try again.
    pause
    exit /b 1
)

REM Check if .env file exists, if not create from example
if not exist .env (
    echo Creating .env file from .env.example...
    copy .env.example .env >nul
    echo .env file created. You can edit it to add your Google Books API key.
    echo.
)

REM Build and start containers
echo Building and starting Docker containers...
docker-compose up -d --build

REM Wait for services to be ready
echo.
echo Waiting for services to be ready...
timeout /t 10 /nobreak >nul

REM Check if containers are running
docker-compose ps | find "Up" >nul
if errorlevel 1 (
    echo.
    echo Error: Containers failed to start. Check logs with:
    echo    docker-compose logs
    pause
    exit /b 1
)

echo.
echo SkillLink is now running!
echo.
echo Access the application at:
echo    - Application: http://localhost:8080
echo    - phpMyAdmin:  http://localhost:8081
echo.
echo Database credentials:
echo    - Host: localhost:3306
echo    - Database: my-app
echo    - Username: root
echo    - Password: rootpassword
echo.
echo Useful commands:
echo    - View logs:        docker-compose logs -f
echo    - Stop containers:  docker-compose down
echo    - Restart:          docker-compose restart
echo.
pause

