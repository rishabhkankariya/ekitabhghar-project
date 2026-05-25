#!/bin/bash

echo "🔍 E-Kitabghar Render Configuration Checker"
echo "=========================================="
echo ""

# Check if required files exist
echo "📁 Checking required files..."
files=("Dockerfile" "start-apache.sh" "health.php" "php/connection.php" "php/env_loader.php")
for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file exists"
    else
        echo "❌ $file is missing!"
    fi
done

echo ""
echo "🔐 Environment Variables Checklist:"
echo "Make sure these are set in Render Dashboard:"
echo ""
echo "Database:"
echo "  - DB_HOST"
echo "  - DB_PORT (usually 5432)"
echo "  - DB_USER"
echo "  - DB_PASS"
echo "  - DB_NAME"
echo ""
echo "SMTP (Email):"
echo "  - SMTP_HOST"
echo "  - SMTP_PORT"
echo "  - SMTP_USER"
echo "  - SMTP_PASS"
echo "  - SMTP_FROM_EMAIL"
echo "  - SMTP_FROM_NAME"
echo ""
echo "Security:"
echo "  - SPECIAL_ADMIN_PASSWORD"
echo "  - HEALTH_CHECK_TOKEN"
echo ""
echo "Optional:"
echo "  - RAZORPAY_KEY_ID"
echo "  - RAZORPAY_SECRET"
echo ""
echo "📝 Next Steps:"
echo "1. Commit and push these changes:"
echo "   git add ."
echo "   git commit -m 'Fix: Configure for Render deployment'"
echo "   git push origin main"
echo ""
echo "2. Go to Render Dashboard and set environment variables"
echo "3. Configure health check path: /health.php"
echo "4. Wait for automatic redeploy"
echo "5. Check logs in Render Dashboard"
echo ""
echo "🌐 Test your deployment:"
echo "   curl https://ekitabhghar-project.onrender.com/health.php"
echo ""
