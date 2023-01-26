echo "Entre servidor para configuración de despliegue [local, develop, production]"
read env

git pull
if [ $env = 'local' ]
then
    composer install
    php artisan launch:deploy
fi

if [ $env = 'develop' ]
then
    #/usr/bin/php8.1 composer_2_2_18.phar install --ignore-platform-reqs --optimize-autoloader --no-dev
    # /usr/bin/php8.1 /root/bin/composer install --ignore-platform-reqs --optimize-autoloader --no-dev
    # /usr/bin/php8.1 /root/bin/composer dump-autoload
    #/usr/bin/php8.1 artisan launch:deploy
fi

if [ $env = 'production' ]
then
    #/usr/bin/php8.1 composer_2_2_18.phar install --ignore-platform-reqs --optimize-autoloader --no-dev
    # /usr/bin/php8.1 /usr/bin/composer install --ignore-platform-reqs --optimize-autoloader --no-dev
    #/usr/bin/php8.1 artisan launch:deploy
fi
