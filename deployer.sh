git pull
php -c ~/etc/php.ini composer.phar install --no-dev --optimize-autoloader
php -c ~/etc/php.ini app/console cache:clear --env=prod --no-debug
php -c ~/etc/php.ini app/console assetic:dump --env=prod --no-debug
php -c ~/etc/php.ini app/console doctrine:schema:update --force --env=prod 
php -c ~/etc/php.ini app/console assets:install --env=prod 