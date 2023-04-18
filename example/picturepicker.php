<?php
require_once("../vendor/autoload.php");
require_once("includes/config.php");
use SBGallery\Model\Gallery;
use Example\Model\Page\Settings\MyGalleryPageSettings;

$dbh = new PDO($config["dbDsn"], $config["dbUsername"], $config["dbPassword"]);

$galleryPageSettings = new MyGalleryPageSettings();
$myGallery = new Gallery($dbh, $galleryPageSettings->gallerySettings);
\SBGallery\View\HTML\displayPicturePickerPage($myGallery, "Gallery", array("styles/style.css", "styles/gallery.css"));
?>
