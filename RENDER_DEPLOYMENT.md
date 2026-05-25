# Render Deployment Guide for E-Kitabghar

## 🔧 Fixing HTTP 502 Error

### Step 1: Update Your Repository
```bash
git add Dockerfile start-apache.sh health.php RENDER_DEPLOYMENT.md
git commit -m "Fix: Configure Apache for Render's dynamic PORT"
git push origin main
```

### Step 2: Configure Environment Variables in Render Dashboard

Go to your Render service dashboard and add these environment variables:

#### Required Database Variables:
- `DB_HOST` - Your PostgreSQL host (e.g., from Render PostgreSQL service)
- `DB_PORT` - Usually `5432`
- `DB_USER` - Your database username
- `DB_PASS` - Your database password
- `DB_NAME` - Your database name

#### Required SMTP Variables (for email):
- `SMTP_HOST` - `smtp-relay.brevo.com`
- `SMTP_PORT` - `587`
- `SMTP_USER` - Your Brevo SMTP username
- `SMTP_PASS` - Your Brevo SMTP password
- `SMTP_FROM_EMAIL` - Your sender email
- `SMTP_FROM_NAME` - `E-KITABGHAR Portal`

#### Security Variables:
- `SPECIAL_ADMIN_PASSWORD` - Your admin password
- `HEALTH_CHECK_TOKEN` - A random secure token

#### Optional Variables:
- `RAZORPAY_KEY_ID` - Your Razorpay key (if using payments)
- `RAZORPAY_SECRET` - Your Razorpay secret

### Step 3: Configure Health Check in Render

In your Render service settings:
1. Go to **Settings** → **Health Check**
2. Set **Health Check Path** to: `/health.php`
3. Save changes

### Step 4: Check Render Logs

After pushing the changes:
1. Go to your Render dashboard
2. Click on your service
3. Go to **Logs** tab
4. Look for:
   - ✅ "Apache started successfully"
   - ✅ "Listening on port 10000" (or whatever PORT Render assigns)
   - ❌ Any database connection errors
   - ❌ Any PHP errors

## 🐛 Common Issues & Solutions

### Issue 1: Database Connection Failed
**Symptom:** Logs show "PostgreSQL Connection failed"

**Solution:**
1. Verify your PostgreSQL service is running in Render
2. Check that `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME` are correctly set
3. Ensure your PostgreSQL service allows connections from your web service
4. Check if database exists and has proper permissions

### Issue 2: Port Binding Error
**Symptom:** "Address already in use" or "Cannot bind to port"

**Solution:**
- The new Dockerfile configuration should fix this
- Render automatically sets the `PORT` environment variable
- Our startup script now reads this PORT and configures Apache accordingly

### Issue 3: File Permission Errors
**Symptom:** "Permission denied" errors in logs

**Solution:**
```dockerfile
# Already included in updated Dockerfile
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
```

### Issue 4: Missing PHP Extensions
**Symptom:** "Call to undefined function" errors

**Solution:**
The Dockerfile already includes:
- `pdo`
- `pdo_pgsql`
- `zip`

If you need more extensions, add them to the Dockerfile:
```dockerfile
RUN docker-php-ext-install mysqli gd mbstring
```

## 📊 Verify Deployment

### Test Health Endpoint:
```bash
curl https://ekitabhghar-project.onrender.com/health.php
```

Expected response:
```json
{
    "status": "healthy",
    "timestamp": "2026-05-25 12:00:00",
    "service": "E-Kitabghar Portal",
    "database": "connected"
}
```

### Test Main Page:
```bash
curl -I https://ekitabhghar-project.onrender.com/
```

Expected: `HTTP/2 200`

## 🔍 Debugging Steps

1. **Check Render Logs:**
   ```
   Render Dashboard → Your Service → Logs
   ```

2. **Check Build Logs:**
   ```
   Render Dashboard → Your Service → Events → View Build Logs
   ```

3. **Test Database Connection:**
   - Create a test file `test-db.php`:
   ```php
   <?php
   require_once 'php/connection.php';
   echo "Database connected successfully!";
   ?>
   ```

4. **Check Apache Status:**
   - In Render shell (if available):
   ```bash
   service apache2 status
   netstat -tulpn | grep apache
   ```

## 🚀 After Successful Deployment

1. Test all major features:
   - [ ] Homepage loads
   - [ ] Login works
   - [ ] Database queries work
   - [ ] File uploads work (if applicable)
   - [ ] Email sending works

2. Monitor logs for any errors

3. Set up automatic deployments:
   - Render can auto-deploy on git push
   - Configure in: Settings → Build & Deploy

## 📞 Still Having Issues?

If you're still seeing 502 errors after following these steps:

1. Share the Render logs (last 50 lines)
2. Verify all environment variables are set
3. Check if the PostgreSQL service is running
4. Try manual redeploy: Render Dashboard → Manual Deploy → Deploy latest commit

## 🔐 Security Checklist

- [ ] All sensitive data is in environment variables (not in code)
- [ ] `.env` file is in `.gitignore`
- [ ] Database credentials are secure
- [ ] SMTP credentials are secure
- [ ] Admin passwords are strong
- [ ] Health check endpoint doesn't expose sensitive info
