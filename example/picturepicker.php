<?php
require_once("../vendor/autoload.php");

use Example\Model\MyGallery;

require_once("includes/config.php");

$dbh = new PDO($config["dbDsn"], $config["dbUsername"], $config["dbPassword"], array(
	PDO::ATTR_PERSISTENT => true
));

$myGallery = new MyGallery($dbh);
\SBGallery\View\HTML\displayPicturePickerPage($myGallery);
?>
