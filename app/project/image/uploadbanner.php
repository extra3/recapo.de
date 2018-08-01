<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
  exit(403);
}

$login = $_app->__get('login');
$_project = \Recapo\Model\Project::getProject($login->info['userID'], $_params['ID']);
if ($_project == false) {
  $_app->halt(500, 'Dieses Projekt ist nicht vorhanden oder es besteht keine Berechtigung.');
  exit();
}
$_project = $_project[0];


if (isset($_FILES['file']) && trim($_FILES['file']['name'])) {

  $targetDirectory = '../web/userbanner/';
  if (!file_exists($targetDirectory)) {
    // erzeuge Verzeichnis
    if (!mkdir($targetDirectory)) {
      $_app->halt(500, 'Fehler: Konnte Verzeichnis nicht anlegen.');
    }
  } elseif (!is_writable($targetDirectory)) {
    $_app->halt(500, 'Fehler: Verzeichnis nicht beschreibbar');
  } elseif ($_project['bannerFile'] != NULL && file_exists($targetDirectory . $_project['bannerFile'])) {
    // delete current banner
    unlink($targetDirectory . $_project['bannerFile']);
  }

  try {
    require 'Upload/Autoloader.php';
    \Upload\Autoloader::register();

    $storage = new \Upload\Storage\FileSystem($targetDirectory);
    $file = new \Upload\File('file', $storage);

    $oldFilename = $file->getName();
    $extension = $file->getExtension();
    // Optionally you can rename the file on upload
    //$newFilename = uniqid();
    //$newFilename = sha1_file($file);
    $newFilename = $_project['ID'];
    $file->setName($newFilename);

    // Validate file upload
    // MimeType List => http://www.iana.org/assignments/media-types/media-types.xhtml
    // MimeType List => http://www.webmaster-toolkit.com/mime-types.shtml
    $file->addValidations(array(
      // Ensure file is no larger than 5M (use "B", "K", M", or "G")
      new \Upload\Validation\Size('3M'),

      // Ensure file is of type "image/png"
      // \Upload\Validation\Mimetype('image/png'),

      //You can also add multi mimetype validation
      new \Upload\Validation\Mimetype(array('image/png', 'image/gif', 'image/jpeg'))
    ));

    // Access data about the file that has been uploaded
    /*$data = array(
      'name'       => $file->getName(),
      'extension'  => $file->getExtension(),
      'mime'       => $file->getMimetype(),
      'size'       => $file->getSize(),
      'md5'        => $file->getMd5(),
      'dimensions' => $file->getDimensions()
    );*/

    $file->upload();

    if (\Recapo\Model\Image::insertBannerByProjectID($_project['ID'], $newFilename . '.' . $extension)) {
      echo "Erfolgreich!";
      //$_app->redirect($_app->urlFor('/project/image', array('ID' => $_params['ID'])));
    } else {
      $_app->halt(500, 'Datenbankfehler');
    }


  } catch (RuntimeException $e) {
    $_app->halt(500, 'Fehler: ' . htmlentities($e->getMessage()));
  }
} else {
  $_app->halt('500', 'Keine Datei hochgeladen');
}
