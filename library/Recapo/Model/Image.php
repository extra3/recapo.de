<?php
/**
 * Recapo <http://recapo.de>
 *
 * @link      http://github.com/lherich/recapo.de
 * @copyright Copyright (c) 2014 Lucas Herich <info@recapo.de>
 * @license   MIT License <http://recapo.de/license>
 */

namespace Recapo\Model;

/**
 *
 */
class Image extends \Recapo\Model\Model
{
  static protected $_sql = array(
    'selectItemByID' => '
            SELECT *
            FROM image
            WHERE image.ID = :pID',
    'getImageByID' => '
            SELECT *
            FROM image
            WHERE image.ID = :pImageID',
    'getAllImagesByUserID' => '
            SELECT *
            FROM image
            WHERE image.userID = :pUserID',
    'getAllProjectImages' => '
            SELECT *
            FROM image
            WHERE image.projectID = :pProjectID',
    'getAllProjectImagesByUserID' => '
            SELECT *
            FROM image
            WHERE image.userID = :pUserID AND image.projectID = :pProjectID',
    'selectAllImagesForExportByProjectID' => '
            SELECT image.ID, image.name, image.extension, image.sha1, image.caption, image.targetWidth, image.horizontalAlign 
            FROM image
            WHERE image.projectID = :pProjectID',
    'getBannerFileByProjectID' => '
            SELECT bannerFile
            FROM project
            WHERE project.ID = :pProjectID',
    'insertImage' => '
            INSERT INTO image(userID, projectID, sha1, extension, name)
            VALUES(:pUserID, :pProjectID, :pSha1, :pExtension, :pDisplayName)',
    'insertBannerByProjectID' => '
            UPDATE project
            SET bannerFile = :pBannerFile
            WHERE project.ID = :pProjectID',
    'deleteBannerByProjectID' => '
            UPDATE project
            SET bannerFile = NULL
            WHERE project.ID = :pProjectID',
    'deleteImageByID' => '
            DELETE FROM image
            WHERE image.ID = :pImageID',
    'deleteImageFromUserByID' => '
            DELETE FROM image
            WHERE image.userID = :pUserID AND image.ID = :pImageID',
    'deleteImageBySha1' => '
            DELETE FROM image
            WHERE image.sha1 = :pSha1',
    'updateDisplayNameByImageID' => '
            UPDATE image
            SET name = :pDisplayName
            WHERE image.ID = :pImageID AND image.userID = :pUserID',
    'updateCaptionByImageID' => '
            UPDATE image
            SET caption = :pCaption
            WHERE image.ID = :pImageID AND image.userID = :pUserID',
    'updateTargetWidthByImageID' => '
            UPDATE image
            SET targetWidth = :pTargetWidth
            WHERE image.ID = :pImageID AND image.userID = :pUserID',
    'updateHorizontalAlignByImageID' => '
            UPDATE image
            SET horizontalAlign = :pHorizontalAlign
            WHERE image.ID = :pImageID AND image.userID = :pUserID',
    'issetImageByName' => '
            SELECT image.ID
            FROM image
            WHERE image.projectID = :pProjectID AND LOWER(image.name) = LOWER(:pName)
            LIMIT 1',
    'issetImageByID' => '
            SELECT image.ID
            FROM image
            WHERE image.projectID = :pProjectID AND image.ID = :pID',
  );


  public static function selectItemByID($pID)
  {
    return self::__selectOne(__FUNCTION__, array('pID' => $pID));
  }

  public static function getImageByID($imageID)
  {
    $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql['getImageByID']);
    $sth->bindValue('pImageID', $imageID);
    $sth->execute();

    return $sth->fetch();
  }

  public static function getAllImagesByUserID($userID)
  {
    $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql['getAllImagesByUserID']);
    $sth->bindValue('pUserID', $userID);
    $sth->execute();

    return $sth;
  }

  public static function getAllProjectImages($projectID)
  {
    $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql['getAllProjectImages']);
    $sth->bindValue('pProjectID', $projectID);
    $sth->execute();

    return $sth->fetchAll(\PDO::FETCH_ASSOC);;
  }

  public static function selectAllImagesForExportByProjectID($projectID)
  {
    $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql['selectAllImagesForExportByProjectID']);
    $sth->bindValue('pProjectID', $projectID);
    $sth->execute();

    return $sth->fetchAll(\PDO::FETCH_ASSOC);;
  }

  public static function getAllProjectImagesByUserID($userID, $projectID)
  {
    $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql['getAllProjectImagesByUserID']);
    $sth->bindValue('pUserID', $userID);
    $sth->bindValue('pProjectID', $projectID);
    $sth->execute();

    return $sth;
  }

  public static function insertImage($userID, $projectID, $sha1, $extension, $displayName)
  {
    $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql['insertImage']);
    $sth->bindValue('pUserID', $userID);
    $sth->bindValue('pProjectID', $projectID);
    $sth->bindValue('pSha1', $sha1);
    $sth->bindValue('pExtension', $extension);
    $sth->bindValue('pDisplayName', $displayName);
    $sth->execute();

    return $sth;
  }

  public static function insertImageReturnID($userID, $projectID, $sha1, $extension, $displayName)
  {
    $_db = \Slim\Slim::getInstance()->container['db'];
    $sth = $_db->prepare(self::$_sql['insertImage']);
    $sth->execute(array('pUserID' => $userID, 'pProjectID' => $projectID, 'pSha1' => $sha1, 'pExtension' => $extension, 'pDisplayName' => $displayName));

    return $_db->lastInsertId();
  }

  public static function insertBannerByProjectID($projectID, $bannerFile)
  {
    $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql['insertBannerByProjectID']);
    $sth->bindValue('pProjectID', $projectID);
    $sth->bindValue('pBannerFile', $bannerFile);
    $sth->execute();

    return $sth;
  }

  public static function deleteImageByID($imageID)
  {
    $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql['deleteImageByID']);
    $sth->bindValue('pImageID', $imageID);
    $sth->execute();

    return $sth;
  }

  public static function getBannerFileByProjectID($projectID)
  {
    $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql['getBannerFileByProjectID']);
    $sth->bindValue('pProjectID', $projectID);
    $sth->execute();

    return $sth->fetchAll(\PDO::FETCH_ASSOC);

    if (isset($sth[0])) {
      return $sth[0];
    } else {
      return false;
    }
  }

  public static function deleteBannerByProjectID($projectID)
  {
    $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql['deleteBannerByProjectID']);
    $sth->bindValue('pProjectID', $projectID);
    $sth->execute();

    return $sth;
  }

  public static function deleteImageFromUserByID($userID, $imageID)
  {
    $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql['deleteImageFromUserByID']);
    $sth->bindValue('pUserID', $userID);
    $sth->bindValue('pImageID', $imageID);
    $sth->execute();

    return $sth;
  }

  public static function updateDisplayNameByImageID($userID, $imageID, $displayName)
  {
    $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql['updateDisplayNameByImageID']);
    $sth->bindValue('pUserID', $userID);
    $sth->bindValue('pImageID', $imageID);
    $sth->bindValue('pDisplayName', $displayName);

    $sth->execute();

    return $sth;
  }

  public static function updateCaptionByImageID($userID, $imageID, $caption)
  {
    $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql['updateCaptionByImageID']);
    $sth->bindValue('pUserID', $userID);
    $sth->bindValue('pImageID', $imageID);
    $sth->bindValue('pCaption', $caption);
    $sth->execute();

    return $sth;
  }

  public static function updateTargetWidthByImageID($userID, $imageID, $targetWidth)
  {
    $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql['updateTargetWidthByImageID']);
    $sth->bindValue('pUserID', $userID);
    $sth->bindValue('pImageID', $imageID);
    $sth->bindValue('pTargetWidth', $targetWidth);
    $sth->execute();

    return $sth;
  }

  public static function updateHorizontalAlignByImageID($userID, $imageID, $horizontalAlign)
  {
    $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql['updateHorizontalAlignByImageID']);
    $sth->bindValue('pUserID', $userID);
    $sth->bindValue('pImageID', $imageID);
    $sth->bindValue('pHorizontalAlign', $horizontalAlign);
    $sth->execute();

    return $sth;
  }

  public static function issetImageByName($pProjectID, $pName)
  {
    $item = self::__selectOne(__FUNCTION__, array('pProjectID' => $pProjectID, 'pName' => $pName));
    if (isset($item['ID'])) {
      return $item['ID'];
    } else {
      return;
    }
  }

  public static function issetImageByID($pProjectID, $pID)
  {
    $item = self::__selectOne(__FUNCTION__, array('pProjectID' => $pProjectID, 'pID' => $pID));
    print_r(__FUNCTION__);
    exit();
    if (isset($item['ID'])) {
      return $item['ID'];
    } else {
      return;
    }
  }
}
