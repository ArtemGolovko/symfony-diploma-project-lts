# О проекте

Дипломный проект курса по Symfony. Написанный на LTS версии фреймворка Symfony.

# Зависимости проекта

 - PHP версии 7.4
 - MySQL Server версии 5.7
 - Composer версии 2.3 или выше
 - SMTP Server (Во время разработки использовался [Mailtrap](https://mailtrap.io/))
 - Symfony CLI (необязательно) - позволяет запускать HTTPS сервер 

# Развертывание проекта

### Шаг первый: клонируйте git репозиторий

```bash
git clone https://github.com/ArtemGolovko/symfony-diploma-project-lts.git
```

### Шаг второй: установка зависимостей проекта

Перейдите в директорию проекта и выполните команду
> Чтобы установить только зависимости продакшена, добавте флаг `--no-dev`
```bash
composer install
```

### Шаг третий: Настройка переменных окружения

В корне проекта создайте файл `.env.local`⁣ и заполните его седейшим содержимым

```ini
DATABASE_URL="ваш URL для подключения к базе данних"
MAILER_DSN="ваш URL для подключения к SMTP серверу"
```

### Шаг четвертый: Создайте базу данных

> Если вы уже создали базу данных, пропустите этот шаг.

Для того чтобы создать базу данных средствами Symfony и Doctrine Orm выполните следующую команду

```bash
php bin/console doctrine:database:create	
```

### Шаг пятый: выполните миграции
```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

### Шаг шестой (необязательно): Заполните базу данных ненастоящими данными

> Заполнение бази данних ненастояшими данними использовалось во время разработки. Не выполняйте этот шаг при зарвертывании проекта на продакшин.

```bash
php bin/console doctrine:fixtures:load --no-interaction
```

# Запуск проекта

Для запуска проекта на 127.0.0.1:8000 в корне проекта выполните

```bash
APP_ENV=prod symfony serve 
```

Если Symfony CLI не установлен:

```bash
APP_ENV=prod php -S 127.0.0.1:8000 -t public/
```

# Лицензия

MIT © [ArtemGolovko](https://github.com/ArtemGolovko)

