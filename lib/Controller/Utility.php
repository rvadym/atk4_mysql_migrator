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
    public $migrations_dir = '_files/migrations/';
    public $dumps_dir      = '_files/dumps/';
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
    function getDirPath($dir) {
        $path = $this->api->pm->base_directory.$dir;
        $this->checkDir($path);
        $this->checkDirRights($path);
        return $path;
    }
    function getMigrationsDirPath() {
        return $this->getDirPath($this->migrations_dir);
    }
    function getDumpsDirPath() {
        return $this->getDirPath($this->dumps_dir);
    }
    function getDirPathByType($type) {
        switch ($type) {
            case 'migration':
                return $this->getMigrationsDirPath();
                break;
            case 'dump':
                return $this->getDumpsDirPath();
                break;
            default:
                throw $this->exception('Don\'t know how to save type '.$this->type);
        }
    }

    /* **********************
     *
     *         FILES
     *
     */
    function fileExist($path) {
        return file_exists($path);
    }
    function createFile($path,$content) {
        if ($this->fileExist($path)) throw $this->exception('this file already exist','Exception_FileAlreadyExist');
        if(file_put_contents($path,$content) === false) throw $this->exception('cannot create file');
        chmod($path,'0777');
    }
    function getAllFiles($type) {
        $arr = array();
        $path = $this->getDirPathByType($type);
        $files = $this->readDir($path);
        foreach ($files as $file) {
            $text = $this->readFile($path.$file);
            //echo(nl2br(htmlspecialchars($text)));echo '<hr>';
            $arr[] = $this->parseFile($text);
        }
        return $arr;
    }
    function readDir($path) {
        try {
            $arr = scandir($path);
        } catch (\Exception $e) {
            if ($this->api->getConfig('atk4_mysql_migrator/debug',false)) {
                throw $e;
            } else {
                throw $this->exception('Cannot scan dir');
            }
        }
        unset($arr[0]);
        unset($arr[1]);
        return $arr;
    }
    function readFile($path) {
        try {
            $text = file_get_contents($path);
        } catch (\Exception $e) {
            if ($this->api->getConfig('atk4_mysql_migrator/debug',false)) {
                throw $e;
            } else {
                throw $this->exception('Cannot read the file');
            }
        }
        return $text;
    }
    function parseFile($text) {
        $arr = array(
            'id'          => str_replace("\n",'',trim($this->getByTag('MIGR_ID',$text))),
            'name'        => $this->getByTag('MIGR_NAME',$text),
            'description' => $this->getByTag('MIGR_DESCR',$text),
            'query'       => $this->getByTag('MIGR_QUERY',$text),
            //'status'=> $this->getByTag('MIGR_STATUS',$text),
        );
        return $arr;
    }
    private function getByTag($tag,$text) {
        preg_match_all('/<'.$tag.'>(.*?)<\/'.$tag.'>/s', $text, $matches);
        return $matches[1][0];
    }
}