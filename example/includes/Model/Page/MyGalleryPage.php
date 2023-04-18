<?php
namespace Example\Model\Page;
use PDO;
use SBGallery\Model\Page\GalleryPage;
use SBGallery\Model\Page\Content\GalleryContents;
use SBGallery\Model\GalleryPermissionChecker;
use Example\Model\MyGalleryPermissionChecker;
use Example\Model\Page\Settings\MyGalleryPageSettings;

class MyGalleryPage extends GalleryPage
{
	public function __construct(PDO $dbh)
	{
		parent::__construct($dbh, new MyGalleryPageSettings(), new MyGalleryPermissionChecker(), new GalleryContents(null, "contents", "HTML", array("gallery.css")));
	}
}
?>
