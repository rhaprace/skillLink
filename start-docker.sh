#!/bin/bash

# SkillLink Docker Startup Script

echo "🚀 Starting SkillLink with Docker..."
echo ""

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Error: Docker is not running. Please start Docker and try again."
    exit 1
fi

# Check if .env file exists, if not create from example
if [ ! -f .env ]; then
    echo "📝 Creating .env file from .env.example..."
    cp .env.example .env
    echo "✅ .env file created. You can edit it to add your Google Books API key."
    echo ""
fi

# Build and start containers
echo "🔨 Building and starting Docker containers..."
docker-compose up -d --build

# Wait for services to be ready
echo ""
echo "⏳ Waiting for services to be ready..."
sleep 10

# Check if containers are running
if docker-compose ps | grep -q "Up"; then
    echo ""
    echo "✅ SkillLink is now running!"
    echo ""
    echo "📱 Access the application at:"
    echo "   - Application: http://localhost:8080"
    echo "   - phpMyAdmin:  http://localhost:8081"
    echo ""
    echo "🗄️  Database credentials:"
    echo "   - Host: localhost:3306"
    echo "   - Database: my-app"
    echo "   - Username: root"
    echo "   - Password: rootpassword"
    echo ""
    echo "📋 Useful commands:"
    echo "   - View logs:        docker-compose logs -f"
    echo "   - Stop containers:  docker-compose down"
    echo "   - Restart:          docker-compose restart"
    echo ""
else
    echo ""
    echo "❌ Error: Containers failed to start. Check logs with:"
    echo "   docker-compose logs"
    exit 1
fi

