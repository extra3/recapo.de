<?php
if($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$_view->set('sections', \Recapo\Model\Section::selectSections());
$_app->render($_route['tpl'][$_this]);
