# Покупатор акций

## Как это развернуть

- Зайдите в папку с проектом
- Скопируйте `app/config/parameters.yml.dist` в `app/config/parameters.yml` и настройте там всё получше.
- Выполните `composer update --no-scripts`.
- Выполните `vendor/bin/homestead make`.
- Откройте файл `Homestead.yml`.
- В секции `sites` добавьте `type: symfony` как тут http://symfony.com/doc/current/setup/homestead.html#setting-up-a-symfony-application а так-же измените `to` чтобы там был путь до папки `web`. Типо вот так вот `to: "/home/vagrant/stock-symfony/web"`.
- Измените название базы данных, на то что вы указали в `app/config/parameters.yml` .
- Выполните `vagrant up`.
- Выполните `vagrant ssh`.
- Выполните `php /home/vagrant/stock-symfony/bin/console stock:load-history` это загрузит историю акций с `Yahoo Finance`.  Подождите немного, данные загружаются за 2 года.
- Так-же чтобы каждый день загружать эту историю импортируйте себе в кронтаб файлик `cron` предварительно выставив время его запуска.
- Зайдите по ip-адресу который написали в `Homestead.yml`.

## А так-же
Чтобы смотреть красивый график была сделана команда, которая наполнит случайными акциями портфель пользователя

- Сначала зарегестрируйтесь
- Потом создайте портфель
- `vagrant ssh`
- `php /home/vagrant/stock-symfony/bin/console stock:random-quotes Имя пользователя`

## Функции
После регистрации и создания портфеля вы можете
- Купить акции `quotes`
- Псмотреть портфель `portfolio`
- Посмотреть график измененния стоимости портфеля `portfolio -> list -> view cost graph`
- Посмотреть какие акции сейчас в вашем портфеле `portfolio -> list -> view quotes`
- Так же можно создать ещё портфель и сделать его активным, и тогда акции будут покупать в него


