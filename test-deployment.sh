#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

SITE_URL="https://ekitabhghar-project.onrender.com"

echo "🧪 Testing E-Kitabghar Deployment on Render"
echo "==========================================="
echo ""

# Test 1: Health Check
echo "Test 1: Health Check Endpoint"
echo "Testing: $SITE_URL/health.php"
HEALTH_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$SITE_URL/health.php")

if [ "$HEALTH_RESPONSE" = "200" ]; then
    echo -e "${GREEN}✅ Health check passed (HTTP $HEALTH_RESPONSE)${NC}"
    echo "Response:"
    curl -s "$SITE_URL/health.php" | jq '.' 2>/dev/null || curl -s "$SITE_URL/health.php"
else
    echo -e "${RED}❌ Health check failed (HTTP $HEALTH_RESPONSE)${NC}"
fi

echo ""
echo "---"
echo ""

# Test 2: Main Page
echo "Test 2: Main Page"
echo "Testing: $SITE_URL/"
MAIN_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$SITE_URL/")

if [ "$MAIN_RESPONSE" = "200" ]; then
    echo -e "${GREEN}✅ Main page loaded successfully (HTTP $MAIN_RESPONSE)${NC}"
else
    echo -e "${RED}❌ Main page failed (HTTP $MAIN_RESPONSE)${NC}"
    if [ "$MAIN_RESPONSE" = "502" ]; then
        echo -e "${YELLOW}⚠️  Still getting 502 error. Check:${NC}"
        echo "   1. Environment variables are set in Render"
        echo "   2. PostgreSQL service is running"
        echo "   3. Render logs for specific errors"
    fi
fi

echo ""
echo "---"
echo ""

# Test 3: Response Time
echo "Test 3: Response Time"
RESPONSE_TIME=$(curl -s -o /dev/null -w "%{time_total}" "$SITE_URL/health.php")
echo "Response time: ${RESPONSE_TIME}s"

if (( $(echo "$RESPONSE_TIME < 2.0" | bc -l) )); then
    echo -e "${GREEN}✅ Response time is good${NC}"
else
    echo -e "${YELLOW}⚠️  Response time is slow (might be cold start)${NC}"
fi

echo ""
echo "---"
echo ""

# Test 4: SSL Certificate
echo "Test 4: SSL Certificate"
SSL_CHECK=$(curl -s -o /dev/null -w "%{ssl_verify_result}" "$SITE_URL/")

if [ "$SSL_CHECK" = "0" ]; then
    echo -e "${GREEN}✅ SSL certificate is valid${NC}"
else
    echo -e "${YELLOW}⚠️  SSL certificate issue${NC}"
fi

echo ""
echo "==========================================="
echo "🏁 Testing Complete"
echo ""

if [ "$HEALTH_RESPONSE" = "200" ] && [ "$MAIN_RESPONSE" = "200" ]; then
    echo -e "${GREEN}🎉 All tests passed! Your site is live and working!${NC}"
    echo ""
    echo "Visit your site: $SITE_URL"
else
    echo -e "${RED}⚠️  Some tests failed. Check Render logs for details.${NC}"
    echo ""
    echo "Troubleshooting steps:"
    echo "1. Go to Render Dashboard: https://dashboard.render.com"
    echo "2. Click on your service"
    echo "3. Check the Logs tab for errors"
    echo "4. Verify environment variables are set"
    echo "5. Ensure PostgreSQL service is running"
fi

echo ""
