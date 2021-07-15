# <p align="center">Simple Money Transfer API 💲</p>

<p align="center">
    <img src="https://img.shields.io/badge/Code-Lumen-informational?style=flat-square&logo=lumen&color=F4645F" alt="Lumen" />
    <img src="https://img.shields.io/badge/Code-PHP-informational?style=flat-square&logo=php&color=777bb4&logoColor=8892BF" alt="PHP" />
    <img src="https://img.shields.io/badge/Tools-MySQL-informational?style=flat-square&logo=mysql&color=4479A1&logoColor=2496ED" alt="MySQL" />
    <img src="https://img.shields.io/badge/Tools-Docker-informational?style=flat-square&logo=docker&color=2496ED" alt="Docker" />
</p>

## 💬 About

This is a simple API to transfer money between users.

## Architecture

This system uses an Event Driven Architecture with jobs & queues.

![Architecture](architecture.png)

## :computer: Technologies

-   [Lumen 8](https://lumen.laravel.com/)
-   [PHP 8](https://www.php.net/)
-   [MySQL 8](https://www.mysql.com/)
-   [Nginx](https://www.nginx.com/)
-   [Docker](https://www.docker.com/)

## :scroll: Requirements

-   [Docker](https://www.docker.com/)
-   [Docker Compose](https://docs.docker.com/compose/)

## :cd: Installation

```sh
git clone git@github.com:filipe1309/t-simple-money-transfer-api.git
```

```sh
cd t-simple-money-transfer-api
```

## :runner: Running

```sh
docker-compose up -d
docker-compose exec php php artisan queue:listen --queue=transactionJobQueue,notificationEventQueue --timeout=60 --sleep=3 --tries=3
```

> Access http://localhost:5001

### Trigger transaction

`POST http://localhost:5001/v1/transactions`

```json
{
    "payer": "91e92c5f-d9d0-437a-9435-58839fdbb6c5",
    "payee": "9442fd46-44cf-4571-9bfd-59670b765719",
    "value": 444
}
```

> `payer` & `payee` are the wallets id of the users

## :white_check_mark: Tests

After up the container:

```sh
docker-compose exec -t php ./vendor/bin/phpunit
```

## :pushpin: Roadmap

-   [ ] Use a Supervisor to monitor the queues and keep works active
-   [ ] Improve code organization with Clean Architectures
-   [ ] Improve authentication with Laravel Passport
-   [ ] Refactor Transaction & Notification systems into isolated microservices
-   [ ] Migrato to Cloud, like AWS, to be able to scale and use things like SQS, Lambdas & SNS.

## License

[MIT](https://choosealicense.com/licenses/mit/)

## About Me

<p align="center">
    <a style="font-weight: bold" href="https://www.linkedin.com/in/filipe1309/">
    <img style="border-radius:50%" width="100px; "src="https://avatars.githubusercontent.com/u/2081014?s=60&v=4"/>
    </a>
</p>

---

<p align="center">
    Done with ♥ by <a style="font-weight: bold" href="https://www.linkedin.com/in/filipe1309/">Filipe Leuch Bonfim</a> 🖖
</p>

---

> @ Generated with [skeleton-courses](https://github.com/filipe1309/skeleton-courses)
