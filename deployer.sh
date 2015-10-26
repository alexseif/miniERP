
composer install --no-dev --optimize-autoloader
app/console cache:clear --env=prod --no-debug
app/console assetic:dump --env=prod --no-debug
app/console doctrine:schema:update --force --env=prod 
app/console assets:install --env=prod 