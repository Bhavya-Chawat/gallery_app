#!/bin/bash

echo "🔍 COMPLETE APP VERIFICATION FOR RAILWAY DEPLOYMENT"
echo "=================================================="

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

ISSUES_FOUND=0

# Check 1: Laravel App Status
echo -e "\n${BLUE}1. LARAVEL APP STATUS${NC}"
echo "--------------------"
if ./vendor/bin/sail artisan --version > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Laravel is running${NC}"
    LARAVEL_VERSION=$(./vendor/bin/sail artisan --version)
    echo "   Version: $LARAVEL_VERSION"
else
    echo -e "${RED}❌ Laravel not responding${NC}"
    ((ISSUES_FOUND++))
fi

# Check 2: Database Connection
echo -e "\n${BLUE}2. DATABASE CONNECTION${NC}"
echo "----------------------"
if ./vendor/bin/sail artisan migrate:status > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Database connected${NC}"
    MIGRATION_COUNT=$(./vendor/bin/sail artisan migrate:status | grep -c "Yes")
    echo "   Migrations run: $MIGRATION_COUNT"
else
    echo -e "${RED}❌ Database connection failed${NC}"
    ((ISSUES_FOUND++))
fi

# Check 3: Storage Configuration
echo -e "\n${BLUE}3. STORAGE CONFIGURATION${NC}"
echo "------------------------"
STORAGE_DISK=$(./vendor/bin/sail artisan tinker --execute="echo config('filesystems.default');" 2>/dev/null | grep -v "Psy Shell" | tail -1)
echo "Current storage disk: $STORAGE_DISK"

if [ "$STORAGE_DISK" = "s3" ]; then
    echo -e "${GREEN}✅ Using MinIO (S3) storage - Good for local dev${NC}"
    # Check MinIO connection
    MINIO_ENDPOINT=$(./vendor/bin/sail artisan tinker --execute="echo config('filesystems.disks.s3.endpoint');" 2>/dev/null | grep -v "Psy Shell" | tail -1)
    echo "   MinIO endpoint: $MINIO_ENDPOINT"
elif [ "$STORAGE_DISK" = "public" ]; then
    echo -e "${GREEN}✅ Using public storage - Good for production${NC}"
else
    echo -e "${YELLOW}⚠️  Storage disk: $STORAGE_DISK${NC}"
fi

# Check 4: Required Files
echo -e "\n${BLUE}4. REQUIRED FILES CHECK${NC}"
echo "-----------------------"

FILES_TO_CHECK=(
    "app/Services/ImageService.php"
    "resources/js/utils/imageHelpers.js" 
    "resources/js/Components/UserAvatar.vue"
    "nixpacks.toml"
    ".env.production.example"
    "deploy-railway.sh"
    "DEPLOYMENT_GUIDE.md"
)

for file in "${FILES_TO_CHECK[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✅ $file${NC}"
        # Show file size
        SIZE=$(ls -lh "$file" | awk '{print $5}')
        echo "   Size: $SIZE"
    else
        echo -e "${RED}❌ $file missing${NC}"
        ((ISSUES_FOUND++))
    fi
done

# Check 5: Routes
echo -e "\n${BLUE}5. ROUTES CHECK${NC}"
echo "---------------"
AVATAR_ROUTE=$(./vendor/bin/sail artisan route:list | grep "profile/avatar" | head -1)
if [ -n "$AVATAR_ROUTE" ]; then
    echo -e "${GREEN}✅ Avatar upload route exists${NC}"
    echo "   Route: $AVATAR_ROUTE"
else
    echo -e "${RED}❌ Avatar upload route missing${NC}"
    ((ISSUES_FOUND++))
fi

PRIVACY_ROUTE=$(./vendor/bin/sail artisan route:list | grep "profile/privacy" | head -1)
if [ -n "$PRIVACY_ROUTE" ]; then
    echo -e "${GREEN}✅ Privacy update route exists${NC}"
else
    echo -e "${YELLOW}⚠️  Privacy update route missing${NC}"
fi

# Check 6: PHP Syntax
echo -e "\n${BLUE}6. PHP SYNTAX CHECK${NC}"
echo "-------------------"
PHP_FILES=(
    "app/Services/ImageService.php"
    "app/Models/User.php"
    "app/Http/Controllers/ProfileController.php"
    "app/Http/Middleware/HandleInertiaRequests.php"
    "app/Providers/AppServiceProvider.php"
)

for file in "${PHP_FILES[@]}"; do
    if [ -f "$file" ]; then
        if php -l "$file" > /dev/null 2>&1; then
            echo -e "${GREEN}✅ $file syntax OK${NC}"
        else
            echo -e "${RED}❌ $file has syntax errors${NC}"
            php -l "$file"
            ((ISSUES_FOUND++))
        fi
    else
        echo -e "${YELLOW}⚠️  $file not found${NC}"
    fi
done

# Check 7: JavaScript Syntax  
echo -e "\n${BLUE}7. JAVASCRIPT SYNTAX CHECK${NC}"
echo "---------------------------"
if [ -f "resources/js/utils/imageHelpers.js" ]; then
    if node -c "resources/js/utils/imageHelpers.js" 2>/dev/null; then
        echo -e "${GREEN}✅ imageHelpers.js syntax OK${NC}"
    else
        echo -e "${RED}❌ imageHelpers.js has syntax errors${NC}"
        node -c "resources/js/utils/imageHelpers.js"
        ((ISSUES_FOUND++))
    fi
fi

# Check 8: Dependencies
echo -e "\n${BLUE}8. DEPENDENCIES CHECK${NC}"
echo "---------------------"
if ./vendor/bin/sail composer validate --no-check-all > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Composer dependencies OK${NC}"
else
    echo -e "${RED}❌ Composer dependency issues${NC}"
    ((ISSUES_FOUND++))
fi

if [ -f "package.json" ]; then
    if [ -d "node_modules" ]; then
        echo -e "${GREEN}✅ NPM dependencies installed${NC}"
    else
        echo -e "${YELLOW}⚠️  NPM dependencies not installed${NC}"
        echo "   Run: ./vendor/bin/sail npm install"
    fi
fi

# Check 9: Build Assets
echo -e "\n${BLUE}9. ASSETS BUILD CHECK${NC}"
echo "---------------------"
if [ -f "public/build/manifest.json" ]; then
    echo -e "${GREEN}✅ Assets built (Vite manifest exists)${NC}"
    BUILD_TIME=$(stat -c %y "public/build/manifest.json" 2>/dev/null || stat -f %Sm "public/build/manifest.json" 2>/dev/null)
    echo "   Last built: $BUILD_TIME"
else
    echo -e "${YELLOW}⚠️  Assets not built${NC}"
    echo "   Run: ./vendor/bin/sail npm run build"
fi

# Check 10: Environment Files
echo -e "\n${BLUE}10. ENVIRONMENT FILES${NC}"
echo "---------------------"
if [ -f ".env" ]; then
    echo -e "${GREEN}✅ .env exists${NC}"
    APP_KEY=$(grep "APP_KEY=" .env | head -1)
    if [ -n "$APP_KEY" ] && [ "$APP_KEY" != "APP_KEY=" ]; then
        echo -e "${GREEN}✅ APP_KEY is set${NC}"
    else
        echo -e "${RED}❌ APP_KEY not set${NC}"
        ((ISSUES_FOUND++))
    fi
    
    DB_CONNECTION=$(grep "DB_CONNECTION=" .env | head -1)
    echo "   $DB_CONNECTION"
    
    FILESYSTEM=$(grep "FILESYSTEM_DISK=" .env | head -1)
    echo "   $FILESYSTEM"
else
    echo -e "${RED}❌ .env file missing${NC}"
    ((ISSUES_FOUND++))
fi

# Check 11: Git Status
echo -e "\n${BLUE}11. GIT STATUS${NC}"
echo "-------------"
if git status > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Git repository initialized${NC}"
    
    UNSTAGED=$(git status --porcelain | wc -l)
    if [ "$UNSTAGED" -eq 0 ]; then
        echo -e "${GREEN}✅ All changes committed${NC}"
    else
        echo -e "${YELLOW}⚠️  $UNSTAGED uncommitted changes${NC}"
        echo "   Uncommitted files:"
        git status --porcelain | head -5
    fi
    
    REMOTE=$(git remote -v | head -1)
    if [ -n "$REMOTE" ]; then
        echo -e "${GREEN}✅ Git remote configured${NC}"
        echo "   $REMOTE"
    else
        echo -e "${YELLOW}⚠️  No git remote configured${NC}"
        echo "   You'll need a GitHub repo for Railway deployment"
    fi
else
    echo -e "${RED}❌ Not a git repository${NC}"
    ((ISSUES_FOUND++))
fi

# Check 12: Test App Accessibility
echo -e "\n${BLUE}12. APP ACCESSIBILITY TEST${NC}"
echo "--------------------------"
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost 2>/dev/null)
if [ "$HTTP_STATUS" = "200" ]; then
    echo -e "${GREEN}✅ App accessible at http://localhost${NC}"
elif [ "$HTTP_STATUS" = "000" ]; then
    echo -e "${YELLOW}⚠️  App not accessible (containers might be down)${NC}"
    echo "   Run: ./vendor/bin/sail up -d"
else
    echo -e "${RED}❌ App returns HTTP $HTTP_STATUS${NC}"
    ((ISSUES_FOUND++))
fi

# Check 13: Model Tests
echo -e "\n${BLUE}13. MODEL FUNCTIONALITY TEST${NC}"
echo "----------------------------"
USER_TEST=$(./vendor/bin/sail artisan tinker --execute="
try {
    \$user = App\Models\User::first();
    if (\$user) {
        echo 'User found: ' . \$user->name;
        echo 'Avatar URL: ' . (\$user->avatar_url ?: 'No avatar');
    } else {
        echo 'No users found';
    }
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage();
}
" 2>/dev/null | grep -v "Psy Shell")

if echo "$USER_TEST" | grep -q "Error"; then
    echo -e "${RED}❌ Model test failed${NC}"
    echo "   $USER_TEST"
    ((ISSUES_FOUND++))
else
    echo -e "${GREEN}✅ Models working${NC}"
    echo "   $USER_TEST"
fi

# Check 14: ImageService Test
echo -e "\n${BLUE}14. IMAGE SERVICE TEST${NC}"
echo "----------------------"
SERVICE_TEST=$(./vendor/bin/sail artisan tinker --execute="
try {
    \$url = App\Services\ImageService::getImageUrl('test-image.jpg');
    echo 'Generated URL: ' . \$url;
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage();
}
" 2>/dev/null | grep -v "Psy Shell")

if echo "$SERVICE_TEST" | grep -q "Error"; then
    echo -e "${RED}❌ ImageService test failed${NC}"
    echo "   $SERVICE_TEST"
    ((ISSUES_FOUND++))
else
    echo -e "${GREEN}✅ ImageService working${NC}"
    echo "   $SERVICE_TEST"
fi

# Summary
echo -e "\n${BLUE}=================================================="
echo -e "                 SUMMARY REPORT"
echo -e "==================================================${NC}"

if [ "$ISSUES_FOUND" -eq 0 ]; then
    echo -e "\n${GREEN}🎉 EXCELLENT! Your app is ready for deployment!${NC}"
    echo -e "${GREEN}All checks passed successfully.${NC}"
    echo ""
    echo -e "${YELLOW}�� NEXT STEPS FOR RAILWAY DEPLOYMENT:${NC}"
    echo "1. Ensure changes are committed: git add . && git commit -m 'Ready for deployment'"
    echo "2. Push to GitHub: git push origin main"  
    echo "3. Create nixpacks.toml (already done ✅)"
    echo "4. Deploy via Railway dashboard"
    echo "5. Add PostgreSQL service in Railway"
    echo "6. Configure environment variables"
    echo "7. Generate domain and test!"
    
elif [ "$ISSUES_FOUND" -le 3 ]; then
    echo -e "\n${YELLOW}⚠️  Minor issues found ($ISSUES_FOUND). Should be deployable but fix if possible.${NC}"
    echo "Most of these are warnings and won't prevent deployment."
    
else
    echo -e "\n${RED}❌ Several issues found ($ISSUES_FOUND). Recommend fixing before deployment.${NC}"
    echo "Please address the red ❌ items above."
fi

echo -e "\n${BLUE}Current Configuration:${NC}"
echo "- Storage: $STORAGE_DISK"
echo "- Environment: Local Development"
echo "- Total Issues: $ISSUES_FOUND"

echo -e "\n${YELLOW}💡 Remember: The app will automatically switch to public storage on Railway!${NC}"
