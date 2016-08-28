123

- Зайдите в папку с проектом
- Скопируйте `app/config/parameters.yml.dist` в `app/config/parameters.yml` 
- Выполните `composer update --no-scripts`
- Выполните `vendor/bin/homestead make`
- Откройте файл `Homestead.yml`
- В секции `sites` добавьте `type: symfony` как тут http://symfony.com/doc/current/setup/homestead.html#setting-up-a-symfony-application а так-же измените `to` чтобы там был путь до папки `web`. Типо вот так вот `to: "/home/vagrant/stock-symfony/web"`
- 
- Настройте `Homestead.yml`
- Выполните `vagrant up`
- Выполните `vagrant ssh`
- Выполните `php /home/vagrant/app/bin/console stock:load-history`
- Зайдите по адресу который
