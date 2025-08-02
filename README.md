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
    4. https://dashboard.stripe.com/register からアカウントを作成
    5. 開発者からAPIキーを作成
    6. .env に下記のように設定
        STRIPE_KEY=pk_test_xxxxxxxxxxxxxxxxxxxx
        STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxxxxxx
        MAIL_FROM_ADDRESS=hello@example.com

    4. php artisan key:generate
    5. php artisan migrate
    6. php artisan db:seed
    8. docker-compose exec mysql bash
    9. mysql -u root -p
    10. create database mock_test;
    7. cp .env .env.testing
    11. APP_ENV=test
        APP_KEY=     
        に書き換える（KEYは空にする）
    12. DB_DATABASE=mock_test
        DB_USERNAME=root
        DB_PASSWORD=root 
        に書き換える
    13. docker-compose exec php bash
    13. php artisan key:generate --env=testing
    14. php artisan migrate --env=testing
    15. php artisan dusk:install
    16. cp .env .env.duck.local
    17. APP_URL=http://nginx
        DUSK_DRIVER_URL=http://selenium:4444/wd/hub
        に書き換える


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
