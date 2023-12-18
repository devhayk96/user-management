## Installation

Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/5.4/installation#installation)

Alternative installation is possible without local dependencies relying on [Docker](#docker).

### Clone the repository

    git clone https://github.com/devhayk96/user-management.git

### Switch to the repo folder

    cd user-management

### Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env -> For Linux and Mac
    copy .env.example .env -> For Windows

### Install all the dependencies using composer

    composer install

### Generate a new application key

    php artisan key:generate

### Clear caches

    php artisan optimize:clear

### Run migrations
**Make sure you set the correct database connection information before running the migrations** [Environment variables](#environment-variables)

    php artisan migrate --seed

### Set MAIL provider credentials to send emails

    php artisan serve

### Start the local development server

    php artisan serve

You can now access the server at http://localhost:8000

