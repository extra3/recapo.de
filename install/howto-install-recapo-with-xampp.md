# Install Recapo with XAMPP for Windows
Learn how to install Recapo inside XAMPP for local testing on Windows.

## 1) Install XAMPP
* Download [XAMPP 5.x.x](https://www.apachefriends.org/download.html) (not version 7.x.x) from   [www.apachefriends.org](https://www.apachefriends.org/download.html)
  * Make sure to get a version with PHP 5, not PHP 7
* Install (or extract) XAMPP directly on the root of your partition, e.g. to `C:\xampp\`
  * If you don't want to extract XAMPP to the top level folder of a partition, then you need to execute `xampp\setup_xampp.bat` afterwards. Make sure that there are no spaces or special characters inside the path to the XAMPP directory.
* Go to `C:\xampp\htdocs\`
  * Optional: Delete everything in this folder
  * Extract Recapo into this folder or clone it (with submodules) from the git repository (e.g. `git clone --recurse-submodule https://github.com/lherich/recapo.de.git`)
  * You will now have Recapo inside `C:\xampp\htdocs\recapo.de\`

For reference: Submodules from `recapo.de\library\`
* `https://github.com/lherich/Slim.git`
* `https://github.com/lherich/Twig.git`
* `https://github.com/lherich/Upload.git`
* `https://github.com/lherich/Valitron.git`

## 2) Configure Database
* Open the XAMPP Control Panel `C:\xampp\xampp-control.exe`
* Click on the `start button` in the second line to start MySQL
* Click on `Shell` in the last column (and confirm the dialog for creating the xampp_shell.bat)
* `mysql -u root`
* `create database recapo;`
* `quit;`

Import the dumps into the database (still in the shell):
* `mysql.exe -u root recapo < C:\xampp\htdocs\recapo.de\install\recapo-1.0-mysql-server-5.6.sql`
* `mysql.exe -u root recapo < C:\xampp\htdocs\recapo.de\install\recapo-1.1-update.sql`
* `mysql.exe -u root recapo < C:\xampp\htdocs\recapo.de\install\recapo-extended.sql`
* You can now close the shell

## 3) Configure Apache & PHP
* Switch back to the XAMPP Control Panel `C:\xampp\xampp-control.exe`
* You might want to change the default editor, because notepad.exe cannot handle the unix line endings in the following config files
  * Click on `Config` in the last column of the Control Panel (above "Shell")
  * Set your desired editor, e.g. [Notepad++](https://www.notepad-plus-plus.org)
* In the first line of the Control Panel (beginning with `Apache`) click the third button `Config` and then on `Apache (httpd.conf)`
  * Change `DocumentRoot` to the recapo web folder:
    * `DocumentRoot "/xampp/htdocs/recapo.de/web/"`
  * Save file
* In the first line of the Control Panel (beginning with `Apache`) click the third button `Config` and then on `PHP (php.ini)`
  * Extend `include_path` with the recapo library:
    * `include_path = ".;\xampp\php\PEAR;..\library"`
  * Save file

## 4) Configure Recapo
* Open `C:\xampp\htdocs\recapo.de\web\index.php` in your preferred editor
  * Set `domain` to `localhost`
  * Set `absolute_path` to `/xampp/htdocs/recapo.de/`
  * Set `library` to `/xampp/htdocs/recapo.de/library/`
  * Set `host` to `localhost`
  * Set `dbname` to `recapo`
  * Set `user` to `root`
  * Set `domain` to '' (empty string)
* Save file

## 5) Optional: Install imagick and ImageMagick
Follow this instructions to enable blurring or converting uploaded images to grayscale:
[https://ourcodeworld.com/articles/read/349/how-to-install-and-enable-the-imagick-extension-in-xampp-for-windows](https://ourcodeworld.com/articles/read/349/how-to-install-and-enable-the-imagick-extension-in-xampp-for-windows)

## 6) Start Apache & MySQL
* Switch back to the XAMPP Control Panel `C:\xampp\xampp-control.exe`
* Start Apache and MySQL with their start buttons
* Open [http://localhost/](http://localhost/) or [http://127.0.0.1/](http://127.0.0.1/) in your browser
* Create a new Recapo user

## Additional Notes
* phpMyAdmin: You can access the databases via [http://localhost/phpmyadmin](http://localhost/phpmyadmin) or [http://127.0.0.1/phpmyadmin](http://127.0.0.1/phpmyadmin)

## Security Notes
* XAMPP is meant for development, not for for production use
* list of missing security in XAMPP:
  * no password for MySQL administrator (root)
  * MySQL daemon is accessible via network
  * ProFTPD: user "daemon", password "lampp"
  * kown default users for Mercury & FileZilla
