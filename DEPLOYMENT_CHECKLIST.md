# 📋 Render Deployment Checklist

## ✅ Completed (Already Done)

- [x] Fixed Dockerfile to use Render's PORT
- [x] Created start-apache.sh for dynamic port configuration
- [x] Enhanced health.php with database check
- [x] Committed changes to git
- [x] Pushed changes to GitHub

## ⚠️ YOUR ACTION REQUIRED

### 1. Set Environment Variables in Render Dashboard

**Go to:** https://dashboard.render.com → Your Service → Environment

#### Database Variables (CRITICAL - App won't work without these):
- [ ] `DB_HOST` = _________________
- [ ] `DB_PORT` = `5432`
- [ ] `DB_USER` = _________________
- [ ] `DB_PASS` = _________________
- [ ] `DB_NAME` = _________________

#### SMTP Variables (Required for emails):
- [ ] `SMTP_HOST` = `smtp-relay.brevo.com`
- [ ] `SMTP_PORT` = `587`
- [ ] `SMTP_USER` = _________________
- [ ] `SMTP_PASS` = _________________
- [ ] `SMTP_FROM_EMAIL` = _________________
- [ ] `SMTP_FROM_NAME` = `E-KITABGHAR Portal`

#### Security Variables:
- [ ] `SPECIAL_ADMIN_PASSWORD` = _________________
- [ ] `HEALTH_CHECK_TOKEN` = _________________

#### Optional (if using payments):
- [ ] `RAZORPAY_KEY_ID` = _________________
- [ ] `RAZORPAY_SECRET` = _________________

### 2. Configure Health Check

**Go to:** Render Dashboard → Your Service → Settings → Health & Alerts

- [ ] Set **Health Check Path** to: `/health.php`
- [ ] Click **Save Changes**

### 3. Wait for Deployment

- [ ] Check **Events** tab - should show "Deploying"
- [ ] Wait for status to change to "Live" (usually 2-5 minutes)
- [ ] Monitor **Logs** tab for any errors

### 4. Test Your Deployment

Run these tests after deployment completes:

#### Test 1: Health Check
```bash
curl https://ekitabhghar-project.onrender.com/health.php
```
- [ ] Returns HTTP 200
- [ ] Shows `"status": "healthy"`
- [ ] Shows `"database": "connected"`

#### Test 2: Main Page
```bash
curl -I https://ekitabhghar-project.onrender.com/
```
- [ ] Returns HTTP 200 (not 502!)
- [ ] Page loads in browser

#### Test 3: Admin Login
- [ ] Go to: https://ekitabhghar-project.onrender.com/admin/admin_login.php
- [ ] Page loads without errors
- [ ] Can attempt login (tests database connection)

#### Test 4: Database Connection
- [ ] Check Render logs for "PostgreSQL Connection failed" errors
- [ ] If no errors, database is connected ✅

## 🐛 Troubleshooting

### If you still see 502 error:

#### Check 1: Environment Variables
```bash
# In Render Dashboard → Environment tab
# Verify all DB_* variables are set correctly
```
- [ ] All required variables are present
- [ ] No typos in variable names
- [ ] Values are correct (especially DB_HOST)

#### Check 2: PostgreSQL Service
```bash
# In Render Dashboard
# Check if PostgreSQL service is running
```
- [ ] PostgreSQL service status is "Available"
- [ ] Connection string matches your DB_HOST

#### Check 3: Render Logs
```bash
# In Render Dashboard → Logs tab
# Look for these messages:
```
- [ ] "Apache started successfully" or similar
- [ ] No "Connection refused" errors
- [ ] No "Port already in use" errors

#### Check 4: Build Logs
```bash
# In Render Dashboard → Events → View Build Logs
```
- [ ] Docker build completed successfully
- [ ] No errors during image creation
- [ ] All dependencies installed

### Common Error Messages & Solutions:

| Error | Solution |
|-------|----------|
| "PostgreSQL Connection failed" | Check DB_HOST, DB_USER, DB_PASS, DB_NAME |
| "Port 80 already in use" | Should be fixed by new Dockerfile |
| "Cannot bind to port" | Render auto-redeploy should fix this |
| "Permission denied" | Should be fixed by Dockerfile permissions |
| "Health check failed" | Check /health.php endpoint directly |

## 📊 Success Criteria

Your deployment is successful when:

- [x] Code pushed to GitHub
- [ ] Render shows "Live" status (green)
- [ ] Health check returns 200 OK
- [ ] Main page loads (no 502)
- [ ] Database queries work
- [ ] Admin panel accessible
- [ ] No errors in logs

## 🎉 Post-Deployment

Once everything is working:

- [ ] Test all major features (login, registration, etc.)
- [ ] Verify email sending works
- [ ] Check file uploads (if applicable)
- [ ] Test on mobile devices
- [ ] Set up monitoring/alerts in Render
- [ ] Document any custom configurations

## 📞 Getting Help

If stuck after following all steps:

1. **Check Render Logs** (most important!)
   - Go to Logs tab
   - Copy last 50 lines
   - Look for error messages

2. **Verify Database Connection**
   - Test PostgreSQL service separately
   - Check connection string format
   - Verify credentials

3. **Manual Redeploy**
   - Render Dashboard → Manual Deploy
   - Deploy latest commit
   - Watch logs during deployment

4. **Render Support**
   - Check Render status page
   - Review Render documentation
   - Contact Render support if needed

---

## 🔗 Quick Links

- **Your Site:** https://ekitabhghar-project.onrender.com
- **Health Check:** https://ekitabhghar-project.onrender.com/health.php
- **Render Dashboard:** https://dashboard.render.com
- **GitHub Repo:** https://github.com/rishabhkankariya/ekitabhghar-project
- **Render Docs:** https://render.com/docs

---

**Last Updated:** May 25, 2026  
**Status:** Awaiting environment variable configuration
