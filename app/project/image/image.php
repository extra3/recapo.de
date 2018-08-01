<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
  exit(403);
}

$login = $_app->__get('login');
$_project = \Recapo\Model\Project::getProject($login->info['userID'], $_params['ID']);
if ($_project == false) {
  $_app->flash('warning', 'Dieses Projekt ist nicht vorhanden oder es besteht keine Berechtigung.');
  $_app->redirect($_app->urlFor('/projects'));
}
$_project = $_project[0];

if (!extension_loaded('imagick')) {
  $imagickAvailable = false;
} else {
  $imagickAvailable = true;
}

$_view->set('project', $_project);
//$_view->set('userID', $login->info['userID']);
$_view->set('imagickAvailable', $imagickAvailable);
$_view->set('images', \Recapo\Model\Image::getAllProjectImagesByUserID($login->info['userID'], $_params['ID']));
$_app->render($_route['tpl'][$_this]);
