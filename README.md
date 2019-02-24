# demo_bejee
Тестовое задание: написать MVC приложение на чистом PHP без использования фреймворков.

Необходимо создать приложение-задачник.

Задачи состоят из: 
- имени пользователя; 
- е-mail; 
- текста задачи;

Стартовая страница - список задач с возможностью сортировки по имени пользователя, email и статусу. Вывод задач нужно сделать страницами по 3 штуки (с пагинацией). Видеть список задач и создавать новые может любой посетитель без регистрации.

Сделать вход для администратора, который имеет возможность редактировать текст задачи и поставить галочку о выполнении. Выполненные задачи в общем списке выводятся с соответствующей отметкой.

## Как запустить демку

```
composer install --no-dev
docker-compose up -d
```

## Как запустить тесты

```
composer install
composer test
```

# Changelog

## [0.2.0](https://github.com/asedov/demo_bejee/tree/v0.2.0)
### Added
- [PSR-7: HTTP message interfaces](https://www.php-fig.org/psr/psr-7/);


## [0.1.0](https://github.com/asedov/demo_bejee/tree/v0.1.0)
### Added
- Добавлен и покрыт модульными тестами роутер;
- Добавлен [Composer](https://getcomposer.org);
- Добавлены линтеры [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer), [PHPMD](https://phpmd.org), [Phan](https://github.com/phan/phan), [Psalm](https://github.com/vimeo/psalm), [PHPStan](https://github.com/phpstan/phpstan);
- Изменились пути: `POST /?action=addNewTask` теперь `POST /tasks`, а `GET /?action=editTask&taskId=abc` теперь `GET /tasks/abc`.  

## [0.0.1](https://github.com/asedov/demo_bejee/tree/v0.0.1)
### Added
- Минимально жизнеспособный продукт.
