<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 10/4/13
 * Time: 8:50 PM
 * To change this template use File | Settings | File Templates.
 */
namespace atk4_mysql_migrator;
class Grid_Migrations extends \Grid {
    function init() {
        parent::init();
        $this->js('reload')->reload();
        $this->addColumn('id');
        $this->addColumn('name');
        $this->addColumn('status');

        $m = $this->add('atk4_mysql_migrator\Model_Migration');
        $arr = $m->getAll();
        $this->setSource($arr);
    }
    function formatRow() {
        parent::formatRow();

        if (strtoupper(trim($this->current_row['status'])) == 'NEW') {
            $this->current_row_html['status'] = '<span style="color:grey">NEW</span>';
        } else if (strtoupper(trim($this->current_row['status'])) == 'OK') {
            $this->current_row_html['status'] = '<span style="color:green">OK</span>';
        } else if (strtoupper(trim($this->current_row['status'])) == 'FAIL') {
            $this->current_row_html['status'] = '<span style="color:red">FAIL</span>';
        }
    }
}