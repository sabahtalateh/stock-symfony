123

- Скопируйте ```app/config/parameters.yml.dist``` в ```app/config/parameters.yml``` 
- Выполните ```composer update --no-scripts``
- Выполните ```vendor/bin/homestead make``
- Настройте ```Homestead.yml```
- Выполните ```vagrant up```
- Выполните ```vagrant ssh```
- Выполните ```php /home/vagrant/app/bin/console stock:load-history```
- Зайдите по адресу который
