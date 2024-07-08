<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Laravel Box Packaging

# This project is build with the following:

* Apache 2.4.56
* MariaDB 10.4.28
* PHP 8.1.17 (VS16 X86 64bit thread safe) + PEAR
* XAMPP Control Panel Version 3.3.0
* Laravel Framework 10.29.0
  * [Laravel starter kit (Breeze and Inertia)](https://laravel.com/docs/10.x/starter-kits#breeze-and-inertia)
* Vue3
  * Packages
      * [Vue3 Table Lite](https://www.npmjs.com/package/vue3-table-lite)
      * [Font awesome](https://www.npmjs.com/package/@fortawesome/fontawesome-svg-core)
      * [Vue3 Icon](https://www.npmjs.com/package/vue3-icon)
      * [Vue3 Toastify](https://www.npmjs.com/package/vue3-toastify)

# What to expect in this project

* Migration and seeder are included  

# Commands used in building this project

> upon installation, execute when necessary
```
composer install
```

> required for fresh DB
```
php artisan storage:link
php artisan migrate
php artisan db:seed
```
