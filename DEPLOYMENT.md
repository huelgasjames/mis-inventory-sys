# Deployment Guide

## Backend Deployment to Vercel

### Prerequisites
- Vercel account
- Git repository connected to Vercel

### Steps to Deploy Backend

1. **Generate Laravel App Key**
   ```bash
   php artisan key:generate --show
   ```
   Copy the generated key and update it in `backend/vercel.json`

2. **Deploy to Vercel**
   ```bash
   # From the backend directory
   vercel --prod
   ```

3. **Environment Variables in Vercel Dashboard**
   After deployment, set these environment variables in your Vercel project settings:
   - `APP_KEY`: Your Laravel app key
   - `APP_URL`: Your Vercel domain URL
   - Any other required environment variables

### Frontend Configuration

Update the frontend API base URL in `frontend/src/utils/api.js`:
```javascript
const API_BASE_URL = 'https://your-backend-vercel-domain.vercel.app/api';
```

### Full Stack Deployment

For deploying both frontend and backend:
1. Deploy backend first to get the URL
2. Update frontend API configuration
3. Deploy frontend

### Notes
- SQLite database will be created in `/tmp/database.sqlite` (ephemeral)
- For production, consider using external database services
- Sessions and cache use array driver for serverless compatibility
