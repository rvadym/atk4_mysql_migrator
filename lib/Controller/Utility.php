<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 10/3/13
 * Time: 8:41 PM
 * To change this template use File | Settings | File Templates.
 */
namespace atk4_mysql_migrator;
class Controller_Utility extends \AbstractController {
    public $migrations_dir = '_files/migrations';
    public $dumps_dir      = '_files/dumps';
    function init() {
        parent::init();
        // add add-on locations to pathfinder
		$this->loc = $this->api->locate('addons',__NAMESPACE__,'location');
		$addon_location = $this->api->locate('addons',__NAMESPACE__);
		$this->api->pathfinder->addLocation($addon_location,array(
            'php'=>'lib',
            'template'=>'templates',
            'css'=>'templates/css',
		))->setParent($this->loc);
    }

    /* **********************
     *
     *          DB
     *
     */
    function checkDB() {
        if (!(
            $this->api->getConfig('dsn',false)                        ||
            strpos($this->api->getConfig('dsn',''), 'mysql://') !== 0 ||
            is_set($this->api->db)                                    ||
            is_object($this->api->db)
        )) {
            throw $this->exception('MySQL DB is not configured','atk4_mysql_migrator\Exception_MySQLNotConfigured');
        }
    }
    function checkTables() {
        $m = $this->add('atk4_mysql_migrator\Model_MigrationPoint');
        $m->tryLoadAny();
        $m->unload();
    }

    /* **********************
     *
     *         DIRS
     *
     */
    function checkDirs() {
        $this->checkMigrationsDir();
        $this->checkDumpsDir();
    }
    function checkDir($path) {
        if (!is_dir($path)) throw $this->exception('is not dir','atk4_mysql_migrator\Exception_DirNotExist');
    }
    function checkDirRights($path) {
        if (!is_writable($path)) throw $this->exception('dir is not writable','atk4_mysql_migrator\Exception_NoDirAccess');
    }
    function checkMigrationsDir() {
        try {
            $this->checkDir($this->getMigrationsDirPath());
        } catch (Exception_DirNotExist $e) {
            throw $this->exception('migrations folder not exist','atk4_mysql_migrator\Exception_MigrationsDirNotExist');
        }
        try {
            $this->checkDirRights($this->getMigrationsDirPath());
        } catch (Exception_NoDirAccess $e) {
            throw $this->exception('migrations dir is not writable','atk4_mysql_migrator\Exception_NoMigrationsDirAccess');
        }
    }
    function checkDumpsDir() {
        try {
            $this->checkDir($this->getDumpsDirPath());
        } catch (Exception_DirNotExist $e) {
            throw $this->exception('dumps folder not exist','atk4_mysql_migrator\Exception_DumpsDirNotExist');
        }
        try {
            $this->checkDirRights($this->getDumpsDirPath());
        } catch (Exception_NoDirAccess $e) {
            throw $this->exception('dumps dir is not writable','atk4_mysql_migrator\Exception_NoDumpsDirAccess');
        }
    }
    function getMigrationsDirPath() {
        return $this->api->pm->base_directory.$this->migrations_dir;
    }
    function getDumpsDirPath() {
        return $this->api->pm->base_directory.$this->dumps_dir;
    }

    /* **********************
     *
     *         FILES
     *
     */
    function fileExist($path) {
        return file_exists($path);
    }
    function createFile($path,$name,$content) {
        if ($this->fileExist($path)) throw $this->exception('this file already exist','Exception_FileAlreadyExist');
    }
}