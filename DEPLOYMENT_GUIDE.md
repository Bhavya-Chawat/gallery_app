# 🚀 Deployment Guide

## For Railway Deployment

1. **Prepare for deployment:**
./deploy-railway.sh

text

2. **Push to GitHub and deploy via Railway**

3. **Set these environment variables in Railway:**
- Copy from `.env.production.example`
- Add PostgreSQL service (variables auto-provided)

4. **After deployment, run:**
php artisan migrate --force
php artisan storage:link

text

## Storage Configuration

- **Local Development**: Uses MinIO (S3-compatible)
- **Production**: Uses Laravel public storage
- **Automatic switching**: Based on environment

## Image URL Generation

All image URLs are now generated automatically:
- `$user->avatar_url` - Avatar URL
- `$image->image_url` - Image URL  
- `$image->thumbnail_url` - Thumbnail URL

## JavaScript Helpers

Import and use:
import { getImageUrl, getAvatarUrl } from '@/utils/imageHelpers'

text
