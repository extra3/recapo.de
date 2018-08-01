<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
  exit(403);
}

$login = $_app->__get('login');
$projectID = $_params['ID'];
$_project = \Recapo\Model\Project::getProject($login->info['userID'], $_params['ID']);
$bannerFile = \Recapo\Model\Image::getBannerFileByProjectID($projectID);

$filepath = './userbanner/' . $bannerFile[0]['bannerFile'];

if (\Recapo\Model\Image::deleteBannerByProjectID($projectID) && is_file($filepath)) {
  unlink($filepath);
  //$_app->halt(200);
  $_app->redirect($_app->urlFor('/project/image', array('ID' => $_params['ID'])));
} else {
  $_app->halt(500, 'Fehler beim Löschen. Evtl. ist die Grafik nicht mehr vorhanden oder Sie haben keine Berechtigung.');
}
