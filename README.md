123

- Зайдите в папку с проектом
- Скопируйте `app/config/parameters.yml.dist` в `app/config/parameters.yml` и настройте там всё получше.
- Выполните `composer update --no-scripts`.
- Выполните `vendor/bin/homestead make`.
- Откройте файл `Homestead.yml`.
- В секции `sites` добавьте `type: symfony` как тут http://symfony.com/doc/current/setup/homestead.html#setting-up-a-symfony-application а так-же измените `to` чтобы там был путь до папки `web`. Типо вот так вот `to: "/home/vagrant/stock-symfony/web"`.
- Измените название базы данных, на то что вы указали в `app/config/parameters.yml` .
- Выполните `vagrant up`.
- Выполните `vagrant ssh`.
- Выполните `php /home/vagrant/stock-symfony/bin/console stock:load-history` это загрузит историю акций с `Yahoo Finance`.
- Так-же чтобы каждый день загружать эту историю импортируйте себе в кронтаб файлик `cron` предварительно выставив время его запуска.
- Зайдите по адресу который.
