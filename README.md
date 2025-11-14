# mock-test

## 環境構築
### Dockerビルド
    1. git clone git@github.com:soki-wada/mock1.git
    2. cd mock1
    3. docker-compose up -d --build

  ＊ MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせて docker-compose.yml ファイルを編集してください。

### Laravel環境構築
    1. docker-compose exec php bash
    2. composer install
    3. exit
    4. cd src/
    5. cp .env.example .env
    6. cd ..
    7. sudo chmod -R 777 src/*
    8. 環境変数を
        DB_CONNECTION=mysql
        DB_HOST=mysql
        DB_PORT=3306
        DB_DATABASE=laravel_db
        DB_USERNAME=laravel_user
        DB_PASSWORD=laravel_pass
        MAIL_FROM_ADDRESS=hello@example.com
        に書き換える

    9. https://dashboard.stripe.com/register からアカウントを作成
    10. 開発者からAPIキーを作成
    11. .env に下記のように設定
        STRIPE_KEY=pk_test_xxxxxxxxxxxxxxxxxxxx
        STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxxxxxx
        MAIL_FROM_ADDRESS=hello@example.com

    12. docker-compose exec php bash
    13. php artisan key:generate
    14. php artisan migrate
    15. php artisan db:seed
    16. exit
    17. docker-compose exec mysql bash
    18. mysql -u root -p  //パスワードはdocker-compose.yml参照のこと
    19. create database mock_test;
    20. exit
    21. exit
    22. cd src
    23. cp .env .env.testing
    24. APP_ENV=test
        APP_KEY=（空にする）
        DB_DATABASE=mock_test
        DB_USERNAME=root
        DB_PASSWORD=root 
        に書き換える
    25. cd ..
    26. docker-compose exec php bash
    27. php artisan key:generate --env=testing
    28. php artisan migrate --env=testing
    29. php artisan dusk:install
    30. chmod -R 777 ./.*
    31. cp .env .env.duck.local
    32. .env.duck.local で
        APP_URL=http://nginx:80
        に書き換える
        DUSK_DRIVER_URL=http://selenium:4444/wd/hub
        を追加する
    33. php artisan config:clear
        php artisan cache:clear
        php artisan view:clear
        php artisan route:clear

## 使用技術
    ・ php 7.4.9-fpm
    ・ Laravel 8.83.29
    ・ MySQL 8.0.26

## テーブル仕様
usersテーブル
| カラム名 | 型        | primary key  |   unique key   |  not null |   foreign key    |
|----|-------------|------------|--------------|--------|---------|
| id  | bigint    | 〇     |      | 〇 | | 
| name  | varchar(255)    | |   |〇 | |
| email  | varchar(255) |   | 〇  |〇 | |
| email_verified_at  | timestamp |        |      | | |
| password  | varchar(255) |        |      |〇 | |
| remember_token  | varchar(100) |        |      | | |
| created_at  | timestamp |        |      | | |
| updated_at  | timestamp |        |      | | |

conditionsテーブル
| カラム名 | 型        | primary key  |   unique key   |  not null |   foreign key    |
|----|-------------|------------|--------------|--------|---------|
|   id  |  bigint |〇||〇||
|   content |  varchar(255) |||〇||
|   created_at  |   timestamp   |||||
|   updated_at  |   timestamp   |||||

productsテーブル
| カラム名 | 型        | primary key  |   unique key   |  not null |   foreign key    |
|----|-------------|------------|--------------|--------|---------|
|id|bigint|〇||〇||
|user_id|bigint|||〇|users(id)|
|condition_id|bigint|||〇|conditions(id)|
|name|varchar(255)|||〇||
|price|int|||〇||
|description|varchar(255)|||〇||
|image|varchar(255)|||〇||
|brand|varchar(255)|||||
|is_purchased|boolean|||〇||
|created_at|timestamp|||||
|updated_at|timestamp|||||

commentsテーブル
| カラム名 | 型        | primary key  |   unique key   |  not null |   foreign key    |
|----|-------------|------------|--------------|--------|---------|
|id|bigint|〇||〇||
|user_id|bigint|||〇|users(id)|
|product_id|bigint|||〇|products(id)|
|content|varchar(255)|||〇||
|created_at|timestamp|||||
|updated_at|timestamp|||||

profilesテーブル
| カラム名 | 型        | primary key  |   unique key   |  not null |   foreign key    |
|----|-------------|------------|--------------|--------|---------|
|id|bigint|〇||〇||
|user_id|bigint|||〇|users(id)|
|username|varchar(255)|||〇||
|address|varchar(255)|||〇||
|building|varchar(255)|||||
|image|varchar(255)|||〇||
|postal_code|varchar(8)|||〇||
|created_at|timestamp|||||
|updated_at|timestamp|||||

categoriesテーブル
| カラム名 | 型        | primary key  |   unique key   |  not null |   foreign key    |
|----|-------------|------------|--------------|--------|---------|
|id|bigint|〇||〇||
|content|varchar(255)|||〇||
|created_at|timestamp|||||
|updated_at|timestamp|||||

category_productテーブル
| カラム名 | 型        | primary key  |   unique key   |  not null |   foreign key    |
|----|-------------|------------|--------------|--------|---------|
|id|bigint|〇||〇||
|product_id|bigint|||〇|products(id)|
|category_id|bigint|||〇|categories(id)|
|created_at|timestamp|||||
|updated_at|timestamp|||||

purchasesテーブル
| カラム名 | 型        | primary key  |   unique key   |  not null |   foreign key    |
|----|-------------|------------|--------------|--------|---------|
|id|bigint|〇||〇||
|user_id|bigint|||〇|profiles(id)|
|product_id|bigint||〇|〇|products(id)|
|payment|tinyint|||〇||
|address|varchar(255)|||〇||
|building|varchar(255)|||||
|postal_code|varchar(8)|||〇||
|created_at|timestamp|||||
|updated_at|timestamp|||||

favoritesテーブル
| カラム名 | 型        | primary key  |   unique key   |  not null |   foreign key    |
|----|-------------|------------|--------------|--------|---------|
|id|bigint|〇||〇||
|user_id|bigint|||〇|users(id)|
|product_id|bigint||〇|〇|products(id)|
|created_at|timestamp|||||
|updated_at|timestamp|||||

dealsテーブル
| カラム名 | 型        | primary key  |   unique key   |  not null |   foreign key    |
|----|-------------|------------|--------------|--------|---------|
|id|bigint|〇||〇||
|sellinig_user_id|bigint|||〇|users(id)|
|purchasing_user_id|bigint|||〇|users(id)|
|product_id|bigint|||〇|products(id)|
|is_deal|boolean|||〇||
|created_at|timestamp|||||
|updated_at|timestamp|||||

evaluationsテーブル
| カラム名 | 型        | primary key  |   unique key   |  not null |   foreign key    |
|----|-------------|------------|--------------|--------|---------|
|id|bigint|〇||〇||
|user_id|bigint|||〇|users(id)|
|target_user_id|bigint|||〇|users(id)|
|deal_id|bigint|||〇|deals(id)|
|rating|int|||〇||
|created_at|timestamp|||||
|updated_at|timestamp|||||

chatsテーブル
| カラム名 | 型        | primary key  |   unique key   |  not null |   foreign key    |
|----|-------------|------------|--------------|--------|---------|
|id|bigint|〇||〇||
|user_id|bigint|||〇|users(id)|
|deal_id|bigint|||〇|deals(id)|
|product_id|bigint|||〇|products(id)|
|message|varchar(255)|||〇||
|image|varchar(255)|||||
|is_read|boolean|||〇||
|created_at|timestamp|||||
|updated_at|timestamp|||||


## ER図
    以下はこのプロジェクトのER図です。

![ER図](https://github.com/soki-wada/mock1/blob/main/mock.png)

## テストユーザー
| id | name        | email          | password      |
|----|-------------|--------------------------|-----------------|
| 1  | 山田一郎    | yamada@gmail.com     | 12345678     |
| 2  | 佐藤次郎    | sato@gmail.com       | 87654321     |
| 3  | 鈴木三郎 | suzuki@gmail.com       | 11111111     |

## URL
    ・ 開発環境 : http://localhost/
    ・ phpMyAdmin : http://localhost:8080/
