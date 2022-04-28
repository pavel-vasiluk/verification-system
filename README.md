# Pseudo-Verification system

Event driven pseudo-verification system with public and private facing REST(-ish) API.

## Preamble

The goal of the project is to show tactical and strategic Domain-Driven Design skills in close to real word (small) project.

## Intro

Technologies used in the project:

`PHP 8.1`
`Symfony ^6.0.*`
`MySQL 8.0`
`Nginx`
`RabbitMQ`

As well as:
`Mailhog`- for emails verifications
`Gotify`- for sms verifications

## Setup

**Prerequisites**: you will need Docker & Docker-compose in order to set up the project.

1. Checkout code from git - `git clone https://github.com/pavel-vasiluk/verification-system.git`
2. Spin-up docker containers by running `docker-compose up`
3. Then proceed to `php` container in order to continue project setup - `docker exec -it --user root php /bin/bash`
4. Run `composer install` in order to install project dependencies
5. Afterwards run `bin/console doctrine:migrations:migrate`to execute all project db migrations (for test-database use additional `-e test`option)
6. Make sure everything is up - project should start being available at http://localhost:8000
7. In order to test SMS notifications additional steps need to be completed: 
   1. Proceed to http://localhost:8080 (Gotify UI), login if needed 
   2. Press **APPS** -> **Create application**-> provide any name / description you want, and press **Create**
   3. Copy your created application **token** value and proceed to project `.env` file (or create local override `.env.local`) 
   4. Add Gotify token parameter, e.g. `GOTIFY_APP_TOKEN=AEZ.CAEd79GiR40`
8. In order to start consuming notification messages, following console command should be running on the background - `bin/console messenger:consume`

## Public system behavior

Create new verification via public REST endpoint request. Examples:

`POST http://localhost:8000/verifications` - creating email verification

    {
	   "subject": {
           "identity": "john.doe@abc.xyz",
	       "type": "email_confirmation"
	   }
	}

`POST http://localhost:8000/verifications` - creating mobile verification

    {
	   "subject": {
           "identity": "+37120000001",
	       "type": "mobile_confirmation"
	   }
	}

Expected response example:

    {
	   "id": "4e1daba5-0470-41fe-966a-9372aa428304"
    }

Afterwards, notification should be sent to your email or sms client (Mailhog / Gotify).
Copy `id` from response, as well as `code` from your email or sms.

Prepare verification confirmation request:

`PUT http://localhost:8000/verifications/4e1daba5-0470-41fe-966a-9372aa428304/confirm`

    {
	   "code": "10539088"
	}
Expected response: `204 (no content)`

## Specifications

System services specifications:

[Template service](https://github.com/pavel-vasiluk/verification-system/blob/master/specs/template.md)
[Verification service](https://github.com/pavel-vasiluk/verification-system/blob/master/specs/verification.md)
[Notification service](https://github.com/pavel-vasiluk/verification-system/blob/master/specs/notification.md)

## Development
Project has its code style rules defined by `php-cs-fixer` dev-dependency.
In order to test your code suggestion against it, run `vendor/bin/php-cs-fixer fix`

## Testing

Project has both unit and functional tests (phpunit).
Tests can be executed by `bin/phpunit`*

* Note! test environment also requires valid Gotify app token as .env variable.
* You can add it to the local-test .env override file `.env.test`, e.g. `GOTIFY_APP_TOKEN=AEZ.CAEd79GiR40`