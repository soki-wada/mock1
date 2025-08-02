# mock-test

## 環境構築
### Dockerビルド
    1. git clone git@github.com:soki-wada/pigly-check-test.git
    2. docker-compose up -d --build

  ＊ MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせて docker-compose.yml ファイルを編集してください。

### Laravel環境構築
    1. docker-compose exec php bash
    2. composer install
    3. cp .env.example .env
    4. 環境変数を
        DB_CONNECTION=mysql
        DB_HOST=mysql
        DB_PORT=3306
        DB_DATABASE=laravel_db
        DB_USERNAME=laravel_user
        DB_PASSWORD=laravel_pass
        に書き換える

    5. https://dashboard.stripe.com/register からアカウントを作成
    6. 開発者からAPIキーを作成
    7. .env に下記のように設定
        STRIPE_KEY=pk_test_xxxxxxxxxxxxxxxxxxxx
        STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxxxxxx
        MAIL_FROM_ADDRESS=hello@example.com

    8. php artisan key:generate
    9. php artisan migrate
    10. php artisan db:seed
    11. docker-compose exec mysql bash
    12. mysql -u root -p
    13. create database mock_test;
    14. cp .env .env.testing
    15. APP_ENV=test
        APP_KEY=（空にする）
        DB_DATABASE=mock_test
        DB_USERNAME=root
        DB_PASSWORD=root 
        に書き換える
    16. docker-compose exec php bash
    17. php artisan key:generate --env=testing
    18. php artisan migrate --env=testing
    19. php artisan dusk:install
    20. chmod -R 777 ./.*
    21. cp .env .env.duck.local
    22. .env.duck.local で
        APP_URL=http://nginx:80
        に書き換える
        DUSK_DRIVER_URL=http://selenium:4444/wd/hub
        を追加する
    23. php artisan config:clear
        php artisan cache:clear
        php artisan view:clear
        php artisan route:clear



## 使用技術
    ・ php 7.4.9-fpm
    ・ Laravel 8.83.29
    ・ MySQL 8.0.26

## ER図
    以下はこのプロジェクトのER図です。

![ER図](https://github.com/soki-wada/mock1/blob/main/mock.png)

## URL
    ・ 開発環境 : http://localhost/
    ・ phpMyAdmin : http://localhost:8080/
