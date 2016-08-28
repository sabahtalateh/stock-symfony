#!/usr/bin/env bash

cd /home/vagrant/stock-symfony
composer update

php /home/vagrant/stock-symfony/bin/console doctrine:migrations:migrate -n
