git pull
composer install
app/console cache:clear
app/console assetic:dump
app/console doctrine:schema:update --force 
app/console assets:install 