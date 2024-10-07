# RUSH Costing Web Application
Version : 1.0.0

Contributors: [Ranul Randombage](mailto:ranulrx@gmail.com)

This web application is designed to streamline the process of collecting and managing cost information for imported vehicle spare parts. It provides a centralized platform for gathering data related to the various costs associated with importing these parts, such as purchase price, shipping fees, import duties, and freight charges. 

## Tech Stack

**Framework:** [Laravel v10.48.22](https://laravel.com/docs/10.x/releases) 

**Server:** PHP v8.1.18

**Database**: MYSQL Server 5.7.42

**Software**: [Composer v2.5.5](https://getcomposer.org/download/2.5.5/composer.phar)

## Important Dev Notes

Make sure to have the above tech stacks setup on machine before next step.

# Getting Started
Execute the below steps in the given order, do not skip any step.
1.	Run `composer install` to install the dependecies
2.	Create a new database in the localhost, and add it along with the db user credentials to the .env file as show below,
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=rush_web_app
    DB_USERNAME=root
    DB_PASSWORD=root
    ```
3.	Run `php artisan migrate` to run database migration scripts
4.	Run `php artisan db:seed` to seed the test user to the user's table.
5.  Let's run the application by serving it on localhost port 8000 `php artisan serve`
6.  Use the below credentials to login,
    ```
    URL:localhost:8000
    Email:ranulrx@gmail.com
    Password:qwerty

