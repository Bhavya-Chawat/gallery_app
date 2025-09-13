#!/bin/bash

echo "�� COMPREHENSIVE APP VERIFICATION"
echo "=================================="

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Check 1: Laravel App Status
echo -e "\n${YELLOW}1. Checking Laravel App...${NC}"
if ./vendor/bin/sail artisan --version > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Laravel is running${NC}"
else
    echo -e "${RED}❌ Laravel not responding${NC}"
fi

# Check 2: Database Connection
echo -e "\n${YELLOW}2. Checking Database Connection...${NC}"
if ./vendor/bin/sail artisan migrate:status > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Database connected${NC}"
else
    echo -e "${RED}❌ Database connection failed${NC}"
fi

# Check 3: Storage Configuration
echo -e "\n${YELLOW}3. Checking Storage Configuration...${NC}"
STORAGE_DISK=$(./vendor/bin/sail artisan tinker --execute="echo config('filesystems.default');" 2>/dev/null | grep -v "Psy Shell")
echo "Current storage disk: $STORAGE_DISK"

if [ "$STORAGE_DISK" = "s3" ]; then
    echo -e "${GREEN}✅ Using MinIO (S3) storage${NC}"
elif [ "$STORAGE_DISK" = "public" ]; then
    echo -e "${GREEN}✅ Using public storage${NC}"
else
    echo -e "${RED}❌ Unknown storage configuration${NC}"
fi

# Check 4: Required Files Exist
echo -e "\n${YELLOW}4. Checking Required Files...${NC}"

FILES_TO_CHECK=(
    "app/Services/ImageService.php"
    "resources/js/utils/imageHelpers.js"
    "resources/js/Components/UserAvatar.vue"
    "nixpacks.toml"
    ".env.production.example"
)

for file in "${FILES_TO_CHECK[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✅ $file${NC}"
    else
        echo -e "${RED}❌ $file missing${NC}"
    fi
done

# Check 5: Routes
echo -e "\n${YELLOW}5. Checking Routes...${NC}"
if ./vendor/bin/sail artisan route:list | grep -q "profile/avatar"; then
    echo -e "${GREEN}✅ Avatar upload route exists${NC}"
else
    echo -e "${RED}❌ Avatar upload route missing${NC}"
fi

# Check 6: JavaScript Syntax
echo -e "\n${YELLOW}6. Checking JavaScript Files...${NC}"
if [ -f "resources/js/utils/imageHelpers.js" ]; then
    if node -c resources/js/utils/imageHelpers.js 2>/dev/null; then
        echo -e "${GREEN}✅ imageHelpers.js syntax OK${NC}"
    else
        echo -e "${RED}❌ imageHelpers.js has syntax errors${NC}"
    fi
fi

# Check 7: PHP Syntax
echo -e "\n${YELLOW}7. Checking PHP Files...${NC}"
if php -l app/Services/ImageService.php > /dev/null 2>&1; then
    echo -e "${GREEN}✅ ImageService.php syntax OK${NC}"
else
    echo -e "${RED}❌ ImageService.php has syntax errors${NC}"
fi

# Check 8: Composer Dependencies
echo -e "\n${YELLOW}8. Checking Dependencies...${NC}"
if ./vendor/bin/sail composer validate --no-check-all > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Composer dependencies OK${NC}"
else
    echo -e "${RED}❌ Composer dependency issues${NC}"
fi

# Check 9: NPM Dependencies
echo -e "\n${YELLOW}9. Checking NPM...${NC}"
if [ -f "package.json" ]; then
    if ./vendor/bin/sail npm list --depth=0 > /dev/null 2>&1; then
        echo -e "${GREEN}✅ NPM dependencies OK${NC}"
    else
        echo -e "${YELLOW}⚠️  NPM dependencies may need installation${NC}"
    fi
fi

echo -e "\n${YELLOW}=================================="
echo -e "VERIFICATION COMPLETE"
echo -e "==================================${NC}"
