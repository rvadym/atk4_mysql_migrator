<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 10/4/13
 * Time: 8:50 PM
 * To change this template use File | Settings | File Templates.
 */
namespace atk4_mysql_migrator;

include __DIR__.'/../../vendor/geshi/geshi.php';

class Grid_Migration extends \Grid {
    function init() {
        parent::init();
        $this->js('reload')->reload();
        $this->addColumn('field');
        $this->addColumn('value');
        $this->setFormatter('value','wrap');

        $m = $this->add('atk4_mysql_migrator/Model_Migration');
        $m->load($_GET['id']);
        $this->setSource($m->get());
    }
    function formatRow() {
        $this->current_row['field'] = $this->current_row['id'];
        $this->current_row['value'] = $this->current_row['name'];
        if ($this->current_row['id'] == 'query') {
            $geshi = new \GeSHi($this->current_row['value'],'mysql');
            $this->current_row_html['value'] = $geshi->parse_code();
        } else {
            $this->current_row_html['value'] = '';
        }
    }
}