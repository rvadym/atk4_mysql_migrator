# ATK MySql Migrator
----

### This addon allows you to easily manage you databases on different servers.
When you want to move you project from one server to another you just need to create migration
on existing server and then apply it to the new one.

## Installation

* Clone this repo to 'addons' folder
* add page 'Page_MysqlMigrations' to you page or just extend it
* Create folder '_files' in you project base_path
* Create folder 'migrations' in '_files' folder
* Create folder 'dumps' in '_files' folder
* Make those folders writable
* Create table in your database

Run this query

    CREATE TABLE `atk4_mysql_migrator_migration`(
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `statuses_json` TEXT NULL,
        PRIMARY KEY (`id`)
    ) CHARSET=utf8 COLLATE=utf8_general_ci;

## Usage
Add this to you config file to specify unique name for you sql dumps

    $config['atk4_mysql_migrator']['project_unique_id'] = 'your_project_name_or_any_other_words';

Enjoy :)