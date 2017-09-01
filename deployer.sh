git pull
export SYMFONY_ENV=prod
php -d extension=phar.so composer.phar install --no-dev --optimize-autoloader
app/console cache:clear --env=prod --no-debug
app/console assetic:dump --env=prod --no-debug
app/console doctrine:migrations:migrate --no-debug --no-interaction --env=prod 
app/console assets:install --env=prod 