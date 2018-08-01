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

  $userimagesDirectory = '../web/userimages/';
  $userIDSubDirectory = $userimagesDirectory . $_project['userID'] . '/';
  $targetDirectory =  $userIDSubDirectory . $_params['ID'] . '/';
  if (!file_exists($userIDSubDirectory)) {
    // erzeuge Verzeichnis
    if (!mkdir($userIDSubDirectory)) {
      $_app->halt(500, 'Fehler: Konnte Verzeichnis nicht anlegen.');
    }
  }
  if (!file_exists($targetDirectory)) {
    // erzeuge Verzeichnis
    if (!mkdir($targetDirectory)) {
      $_app->halt(500, 'Fehler: Konnte Verzeichnis nicht anlegen.');
    }
  }
  if (!is_writable($targetDirectory)) {
    $_app->halt(500, 'Fehler: Verzeichnis nicht beschreibbar');
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
    $newFilename = sha1_file($file);
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

    try {
      if ( extension_loaded('imagick') && (isset($_POST['blurCheckbox']) || isset($_POST['grayCheckbox'])) ) {
        $filePath = $targetDirectory . $newFilename . '.' . $extension;

        $image = new Imagick(realpath($filePath));
        if (isset($_POST['blurCheckbox'])) {
          $image->gaussianBlurImage(5, 5);
        }
        if (isset($_POST['grayCheckbox'])) {
          $image->transformImageColorspace(Imagick::COLORSPACE_GRAY);
          //$image->modulateImage(100,0,100); // alternative
        }
        if($f=fopen($filePath, "wb")){
          $image->writeImageFile($f);
        }
      }
    } catch (Exception $e) {
      // blur or gray with imagick doesn't work, so stay with the original uploaded image
    }


    if (\Recapo\Model\Image::insertImage($_project['userID'], $_params['ID'], $newFilename,  $extension, htmlspecialchars($oldFilename))) {
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
