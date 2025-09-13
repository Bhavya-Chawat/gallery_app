#!/bin/bash

echo "🚀 Preparing for Railway deployment..."

# Copy production environment template
cp .env.production.example .env

echo "✅ Environment configured for Railway"
echo "📝 Don't forget to:"
echo "   1. Set environment variables in Railway dashboard"
echo "   2. Add PostgreSQL service"
echo "   3. Generate domain"
echo "   4. Run migrations after deployment"

echo "🎯 Ready for Railway deployment!"
