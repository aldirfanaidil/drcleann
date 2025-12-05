# TiDB Connection Information for Render Deployment

## Environment Variables to Set in Render Dashboard

Copy these values to your Render service environment variables:

```
DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com
DB_PORT=4000
DB_NAME=DRCLEAN
DB_USER=4MQbmjj75JU22dn.root
DB_PASS=y5EfIFa7FJcZPq6C
APP_ENV=production
```

## Steps to Deploy to Render:

1. **Push to GitHub:**
   ```bash
   git add .
   git commit -m "Add TiDB configuration for deployment"
   git push origin main
   ```

2. **Create Web Service on Render:**
   - Go to dashboard.render.com
   - Click "New +" → "Web Service"
   - Connect your GitHub repository
   - Select "Docker" runtime
   - Set environment variables above

3. **Configure Environment Variables:**
   - Go to your service → Settings → Environment
   - Add all variables from above

4. **Deploy!**

## Testing Connection:

Access `https://your-app.onrender.com/test_tidb_connection.php` to verify database connection.

## Database Schema:

After deployment, import your database schema to TiDB:
1. Use MySQL client or TiDB Cloud console
2. Run the SQL files from `/database/` folder