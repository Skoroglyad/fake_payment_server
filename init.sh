#!/bin/bash

docker-compose up -d
echo 'docker-compose up'

docker-compose exec php composer install
echo 'composer installed'

docker-compose exec php php bin/console doctrine:migrations:diff
echo 'migrations:diff'

docker-compose exec php php bin/console --no-interaction doctrine:migrations:migrate
echo 'migrations:migrate'

docker-compose exec php php bin/console server:start
echo 'Go to Chrome. Server listening on http://127.0.0.1:8000 '

docker-compose exec php php bin/console rabbitmq:consumer send_payment

