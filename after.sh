#!/usr/bin/env bash

cd /home/vagrant/app
composer update

php /home/vagrant/app/bin/console doctrine:migrations:migrate -n
