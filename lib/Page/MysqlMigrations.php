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
        $this->migrator = $this->add('atk4_mysql_migrator\Controller_Migrator');

        $this->checkDB();
        $this->checkTables();
        $this->checkDirs();
    }
    function page_index() {
        if ($this->errors_count > 0) return;
        $tt = $this->add('Tabs');
        $tt->addTabUrl('./migrations');
        $tt->addTabUrl('./dumps');
    }
    function page_migrations() {
        if ($this->errors_count > 0) return;
        $page_name = $this->name;
        $bs = $this->add('ButtonSet');
        $this->addCreateMigrationButton($bs,$page_name);
        $this->addRunAllMigrationsButton($bs,$page_name);

        $this->add('atk4_mysql_migrator\Grid_Migrations','migrgrid');
    }
    function page_dumps() {
        if ($this->errors_count > 0) return;

        $this->add('atk4_mysql_migrator\Grid_Dumps','dumpgrid');
    }
    function page_migrations_view() {
        $this->add('atk4_mysql_migrator/Grid_Migration');
    }
    function page_dumps_view() {
        $this->add('atk4_mysql_migrator/Grid_Dump',array('page_name'=>$this->name));
    }



    /* ******************************
     *
     *          PRIVATE
     *
     */

    // UI
    private function addCreateMigrationButton($button_set,$page_name) {
        $b = $button_set->add('Button')->set('Create migration');
        $b->add('VirtualPage')
            ->bindEvent('Create migration','click')
            ->set(function($page) use ($page_name){
                $page->add('atk4_mysql_migrator\Form_Migration');
                $page->js(true)->closest(".ui-dialog")->on("dialogbeforeclose",
                    $page->js(null,'function(event, ui){
                             //alert("Text will be changed now!");
                             '. $page->js()->_selector('#'.$page_name.'_migrgrid')->trigger('reload') .';
                         }
                    ')
                );
            });
    }
    private function addRunAllMigrationsButton($button_set,$page_name) {
        $b = $button_set->add('Button')->set('Run All Migrations');
        $b->add('VirtualPage')
            ->bindEvent('Run All Migrations','click')
            ->set(function($page) use ($page_name){
                $page->add('atk4_mysql_migrator\View_RunMigratioins');
                $page->js(true)->closest(".ui-dialog")->on("dialogbeforeclose",
                    $page->js(null,'function(event, ui){
                             //alert("Text will be changed now!");
                             '. $page->js()->_selector('#'.$page_name.'_migrgrid')->trigger('reload') .';
                         }
                    ')
                );
            });
    }


    // utility
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
                <p>
                <pre>
                CREATE TABLE `atk4_mysql_migrator_migration`(
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(255) NOT NULL,
                    `statuses_json` TEXT NULL,
                    PRIMARY KEY (`id`)
                ) CHARSET=utf8 COLLATE=utf8_general_ci;
                </pre>
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