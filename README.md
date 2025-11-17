# アプリケーション名

Coachtechフリマ

## 環境構築

Dockerを利用した環境構築から、Laravelのマイグレーション、シーディングまでの手順を記述します。

### 1. Docker ビルド

1.  GitHubからリポジトリをクローンします。
    ```bash
    git clone <https://github.com/KaitoS828/coachtech_freemarket.git>
    ```
2.  Dockerコンテナをビルドし、起動します。
    ```bash
    docker-compose up -d --build
    ```

### 2. Laravel 環境構築

1.  コンテナに入り、Bashシェルを起動します。
    ```bash
    docker-compose exec php bash
    ```
2.  PHPの依存関係をインストールします。
    ```bash
    composer install
    ```
3.  環境設定ファイルを作成し、環境変数を変更します。
    ```bash
    cp .env.example .env
    ```
4.  Laravelのアプリケーションキーを生成します。
    ```bash
    php artisan key:generate
    ```
5.  データベースのマイグレーション（テーブル作成）を実行します。
    ```bash
    php artisan migrate
    ```
6.  ダミーデータ（テストユーザー、カテゴリ、商品）を投入します。
    ```bash
    php artisan db:seed
    ```

## 🖥️ 開発環境とURL

アプリケーションを以下のURLで実行できます。

| 項目 | URL |
| :--- | :--- |
| **開発環境** (お問い合わせ画面) | `http://localhost/` |
| **ユーザー登録** | `http://localhost/register` |
| **phpMyAdmin** | `http://localhost:8080/` |

## ⚙️ 使用技術 (実行環境)

本アプリケーションは以下の技術スタックで構築されています。

* **PHP:** 8.4.10
* **Webフレームワーク:** Laravel 8.83.29
* **データベース:** MySQL 8.0.26
* **Webサーバー:** nginx 1.21.1

## 🗺️ ER図

![ER図](ER.svg)

## ⚠️ 動作確認用情報

* **初期ログインメールアドレス:** `test1@example.com`
* **初期ログインパスワード:** `password`
