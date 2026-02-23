# Vercel Deployment Guide

## Prerequisites
- Vercel account
- GitHub repository
- Backend API deployed separately

## Frontend Deployment Steps

### 1. Push to GitHub
```bash
git add .
git commit -m "Ready for Vercel deployment"
git push origin main
```

### 2. Deploy to Vercel
1. Go to [vercel.com](https://vercel.com)
2. Click "New Project"
3. Import your GitHub repository
4. Select the `frontend` folder as root directory
5. Vercel will auto-detect Vue.js + Vite
6. Click "Deploy"

### 3. Environment Variables
In Vercel dashboard, add environment variable:
- `VITE_API_BASE_URL`: Your backend API URL (e.g., `https://your-backend.com/api`)

### 4. Backend Deployment Options

#### Option A: Laravel Forge (Recommended)
- Use Laravel Forge for easy Laravel deployment
- Provides managed servers, SSL, and deployment automation

#### Option B: DigitalOcean/AWS
- Deploy Laravel to a cloud server
- Configure Nginx + PHP-FPM
- Set up SSL certificate

#### Option C: Ploi.io
- Laravel-specific hosting platform
- Easy deployment and management

## Post-Deployment Checklist
- [ ] Update CORS in Laravel backend to allow Vercel domain
- [ ] Test API endpoints
- [ ] Verify all frontend features work
- [ ] Check responsive design
- [ ] Test form submissions

## CORS Configuration (Laravel)
Add this to your `config/cors.php`:
```php
'paths' => ['api/*'],
'allowed_origins' => [
    'https://your-vercel-app.vercel.app',
    'http://localhost:3000',
],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```
