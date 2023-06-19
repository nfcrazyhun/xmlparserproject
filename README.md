<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# xmlparserproject

## About this app
**The app is about parsing XML files and storing them into a database.**

_**Disclaimer: This application made for learning purposes only.**_


## Installation guide
1. Open a terminal
2. Clone this repository
```
git clone https://github.com/nfcrazyhun/xmlparserproject.git
```
3. `cd` into it
4. Install dependencies
```
composer install
```
5. Copy then rename .env.example to .env
```
copy .env.example .env
```
6. Generate application key
```
php artisan key:generate
```
7. Create a database
    1. Name: `xmlparser`
    2. (collation: utf8mb4_unicode_ci)
   > Update the database credentials in the .env file.
8. Set up VirtualHost for the project (recommended: http://xmlparserproject.test )
    > Be sure to update the **APP_URL** line in the .env file according to your VirtualHost.

## Note
The project was created using the following software versions:
- PHP 8.2.7
- Laravel Framework 10.13.0
- Tailwind CSS 3.3.2
- Alpine.js 3.12.0
