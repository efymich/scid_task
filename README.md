# Инструкция по установке и эксплуатации JSON API

## Установка:

* Склонировать проект из репозитория <https://github.com/efymich/scid_task>
* Выполнить команду ```composer install``` для установки актуальных версий используемых библиотек
* В файле **config/db_conf.php** и в **migration_module/migration_dbconf.php**  
  записываем свои параметры подключения к базе данных
* Применяем команду ```php migration.php pull``` для создания необходимых для работы таблиц в БД

## Эксплуатация:

Описание работы API приведено в Swagger формате в файле **openapi.yaml**
