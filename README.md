# mock2-test

## 環境構築
### Dockerビルド
    1. git clone git@github.com:soki-wada/mock2.git
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

    5. php artisan key:generate
    6. php artisan migrate
    7. php artisan db:seed
    8. docker-compose exec mysql bash
    9. mysql -u root -p
    10. create database mock_test;
    11. cp .env .env.testing
    12. APP_ENV=test
        APP_KEY=（空にする）
        DB_DATABASE=mock_test
        DB_USERNAME=root
        DB_PASSWORD=root 
        に書き換える
    13. docker-compose exec php bash
    14. php artisan key:generate --env=testing
    15. php artisan migrate --env=testing
    16. php artisan config:clear
        php artisan cache:clear
        php artisan view:clear
        php artisan route:clear
    17. sudo chmod -R 777 src/*

## 登録済みユーザー（管理者ユーザー・一般ユーザー）
    /src/database/seeders/UsersTableSeeder.phpを参照

## 使用技術
    ・ php 7.4.9-fpm
    ・ Laravel 8.83.29
    ・ MySQL 8.0.26

## ER図
    以下はこのプロジェクトのER図です。

![ER図](https://github.com/soki-wada/mock2/blob/main/mock2.png)

## URL
    ・ 開発環境 : http://localhost/
    ・ phpMyAdmin : http://localhost:8080/
