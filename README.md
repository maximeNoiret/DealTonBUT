# DealTonBUT
SAE S3 BUT2

Repository for [dealtonbut.app](https://dealtonbut.app)

## Local Hosting for dev
### 1. Setup Mariadb/MySQL
On ``systemd`` based systems:
```bash
$ sudo systemctl start mariadb   # Start Mariadb
$ sudo systemctl enable mariadb  # Auto start Mariadb at bootup
```  
then, once running:
```sql
$ mariadb

CREATE DATABASE DealTonBUT;
use DealTonBUT;

CREATE OR REPLACE USER 'guest'@'localhost' IDENTIFIED BY 'localPass';

GRANT SELECT, INSERT, UPDATE, DELETE ON *.* TO 'guest'@'localhost';

source [PATH_TO/structural_script.sql]
```

### 2. Enable php-mysql
In the php config file, found by doing ``php --ini``, find the lines with ``extension=mysqli`` and ``extension=pdo_mysql`` and uncomment them.

You also need to install the ``php-mysql`` package. See your distribution's package manager for that.

### 3. Setup .env
At the root of the project:
```bash
$ cp .env.dist .env
$ vim .env
```
Then fill up the information like such:
```
DB_HOSTNAME=localhost
DB_NAME=DealTonBUT
DB_USER=guest
DB_PASSWORD=localPass
```
> [!WARNING]
> Don't use such simple credentials in prod!

### 4. Running the php internal server
> [!NOTE]
> The internal server should only be used for developping/testing. Production should have a proper webserver, like Apache or Nginx.

Execute this command at the root of the project:
```
php -S localhost:8080 index.php
```

The website is now running at ``http://localhost:8080``
