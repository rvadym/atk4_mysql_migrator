<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 10/3/13
 * Time: 8:33 PM
 * To change this template use File | Settings | File Templates.
 */
namespace atk4_mysql_migrator;
class Page_MysqlMigrations extends \Page {
    private $errors_count = 0;
    function init() {
        parent::init();
        $this->utility = $this->add('atk4_mysql_migrator\Controller_Utility');

        $this->checkDB();
        $this->checkTables();
        $this->checkDirs();
    }
    function page_index() {
        if ($this->errors_count > 0) return;
        $tt = $this->add('Tabs');
        $tt->addTabUrl('./migrations');
    }
    function page_migrations() {
        if ($this->errors_count > 0) return;
        $b = $this->add('Button')->set('Create migration');
        $b->add('VirtualPage')
            ->bindEvent('Create migration','click')
            ->set(function($page){
                $page->add('Form_CreateMigration');
            });

    }



    /* ******************************
     *
     *          PRIVATE
     *
     */
    private function checkDB() {
        try {
            $this->utility->checkDB();
        } catch (Exception_MySQLNotConfigured $e) {
            $this->errors_count++;
            $this->add('View_Error')->set('MySQL is not configured');
        }
    }
    private function checkTables() {
        try {
            $this->utility->checkTables();
        } catch (\Exception_DB $e) {
            $this->errors_count++;
            $this->add('View_Error')->setHTML('
                <h3>Tables required by this Addon are not created in database</h3>
                <p>Execute this line to create required table.
                <p>CREATE TABLE `atk4_mysql_migrator_migration`( `id` INT(11) NOT NULL AUTO_INCREMENT, `name` VARCHAR(255) NOT NULL, PRIMARY KEY (`id`) ) CHARSET=utf8 COLLATE=utf8_general_ci;
            ');
        }
    }
    private function checkDirs() {
        try {
            $this->utility->checkMigrationsDir();
        } catch (Exception_MigrationsDirNotExist $e) {
            $this->errors_count++;
            $this->add('View_Error')->setHTML('<h3>There is no \'migrations\' dir.</h3>');
        } catch (Exception_NoMigrationsDirAccess $e) {
            $this->errors_count++;
            $this->add('View_Error')->setHTML('<h3>\'migrations\' dir is not writable.</h3>');
        }
        try {
            $this->utility->checkDumpsDir();
        } catch (Exception_DumpsDirNotExist $e) {
            $this->errors_count++;
            $this->add('View_Error')->setHTML('<h3>There is no \'dumps\' dir.</h3>');
        } catch (Exception_NoDumpsDirAccess $e) {
            $this->errors_count++;
            $this->add('View_Error')->setHTML('<h3>\'dumps\' dir is not writable.</h3>');
        }
    }
}