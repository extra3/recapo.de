<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
  exit(403);
}

$login = $_app->__get('login');
$userID = $login->info['userID'];
$imageID = $_params['ITEMID'];
$image = \Recapo\Model\Image::getImageByID($imageID);
$sha1 = $image['sha1'];
$extension = $image['extension'];
if ($sha1 != 'dummy') {
  $filepath = '../web/userimages/' . $userID . '/' . $image['projectID'] . '/' . $sha1 . '.' . $extension;
} else {
  $filepath = '../web/img/' . $sha1 . '.' . $extension;
}

if (\Recapo\Model\Image::deleteImageFromUserByID($userID, $imageID) && is_file($filepath)) {
  if ($sha1 != 'dummy') { // do not delete dummyfile from filesystem
    unlink($filepath);
  }
  //$_app->halt(200);
  $_app->redirect($_app->urlFor('/project/image', array('ID' => $image['projectID'])));
} else {
  $_app->halt(500, 'Fehler beim Löschen. Evtl. ist die Grafik nicht mehr vorhanden oder Sie haben keine Berechtigung.');
}
