<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
  exit(403);
}

$post = $_app->request()->post();

if (isset($post['name']) && isset($post['value']) && isset($post['pk'])) {

  if ($post['name'] == 'name') {
    $userID = $_app->__get('login')->info['userID'];
    if (\Recapo\Model\Image::updateDisplayNameByImageID($userID, $post['pk'], $post['value'])) {
      $_app->halt(200, 'Erfolgreich die Daten geändert.');
    } else {
      $_app->halt(403, 'Keine Berechtigung diese Grafik zu ändern.');
    }
  } elseif ($post['name'] == 'caption') {
    $userID = $_app->__get('login')->info['userID'];
    if (\Recapo\Model\Image::updateCaptionByImageID($userID, $post['pk'], $post['value'])) {
      $_app->halt(200, 'Erfolgreich die Daten geändert.');
    } else {
      $_app->halt(403, 'Keine Berechtigung diese Grafik zu ändern.');
    }
  } elseif ($post['name'] == 'targetWidth' && isValidTargetWidth($post['value'])) {
    $userID = $_app->__get('login')->info['userID'];
    if (\Recapo\Model\Image::updateTargetWidthByImageID($userID, $post['pk'], $post['value'])) {
      $_app->halt(200, 'Erfolgreich die Daten geändert.');
    } else {
      $_app->halt(403, 'Keine Berechtigung diese Grafik zu ändern.');
    }
  }  elseif ($post['name'] == 'horizontalAlign' && isValidHorizontalAlign($post['value'])) {
    $userID = $_app->__get('login')->info['userID'];
    if (\Recapo\Model\Image::updateHorizontalAlignByImageID($userID, $post['pk'], $post['value'])) {
      $_app->halt(200, 'Erfolgreich die Daten geändert.');
    } else {
      $_app->halt(403, 'Keine Berechtigung diese Grafik zu ändern.');
    }
  } else {
    $_app->halt(500, 'Falsche Daten übergeben.');
  }

} else {
  $_app->halt(500, 'Fehler, Keine Daten übergeben.');
}

function isValidTargetWidth($inputValue) {
  switch ($inputValue) {
    case '25':
      return true;
      break;
    case '33':
      return true;
      break;
    case '50':
      return true;
      break;
    case '100':
      return true;
      break;
    default:
      return false;
      break;
  }
  return false;
}

function isValidHorizontalAlign($inputValue) {
  switch ($inputValue) {
    case 'left':
      return true;
      break;
    case 'right':
      return true;
      break;
    default:
      return false;
      break;
  }
  return false;
}
