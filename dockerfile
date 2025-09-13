# -------- Base PHP image --------
FROM laravelsail/php82-composer AS backend

WORKDIR /var/www/html

# Copy code
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install frontend dependencies + build assets
FROM node:18 AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# -------- Final stage --------
FROM laravelsail/php82-composer

WORKDIR /var/www/html
COPY --from=backend /var/www/html /var/www/html
COPY --from=frontend /app/public/build /var/www/html/public/build

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080
CMD php artisan serve --host=0.0.0.0 --port=8080
