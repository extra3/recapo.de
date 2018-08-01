# Install Recapo

## Note
If you want to use XAMPP for Windows, then follow this guide:
[howto-install-recapo-with-xampp.md](./howto-install-recapo-with-xampp.md)

## System requirements

* web server with php support
* PHP = 5.x (not PHP 7)
* MySQL server >= 5.5.0 for results in seconds
* MySQL server >= 5.6.4 for results in mikroseconds
                  recapo will use fractional seconds, which are available since version 5.6.4, see [mysql.com](http://dev.mysql.com/doc/refman/5.6/en/fractional-seconds.html)
* optional: "ImageMagick" and php extension "imagick" installed

## Installation
1) Install Apache or nginx, PHP 5 (not 7!) and MySQL on your webserver
2) Clone repository (with submodules!) or extract source to webserver directory, e. g. `/var/www/recapo.de/`
3) Log into mysql (`mysql -u [username] -p`) and create database for recapo (`create database recapo; quit;`)
4) Import sql dumps into database
   1) `mysql -u [username] -p recapo < /var/www/recapo.de/install/mysql-server-5.6-bachelorarbeit.sql`
   2) `mysql -u [username] -p recapo < /var/www/recapo.de/install/recapo-1.1-update.sql`
   3) `mysql -u [username] -p recapo < /var/www/recapo.de/install/recapo-extended`
5) Configure Apache (see [nginx.cnf](./nginx.cnf) for nginx)
   1) open `httpd.conf` and set `DocumentRoot` to the recapo web folder `/var/www/recapo.de/web/`
6) Configure PHP5 (might not be necessary)
   1) open `php.ini` and extend `include_path` with the recapo library (add `..\library`)
7) Configure Recapo
   1) open `web/index.php` and change the lines with `__CHANGE_ME__`
8) Optional: Install imagick for PHP and ImageMagick to enable modifications of uploaded images (ask your preferred search engine for help)
9) Restart apache/nginx and mysql
10) Open the URL for your webserver in your browser
11) Create a new Recapo user
