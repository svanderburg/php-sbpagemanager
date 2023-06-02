<?php
namespace Example\Model\Page;
use PDO;
use SBGallery\Model\Page\Content\GalleryContents;
use SBGallery\Model\GalleryPermissionChecker;
use SBPageManager\Model\Page\IntegratedGalleryPage;
use Example\Model\MyPagePermissionChecker;
use Example\Model\Page\Settings\MyGalleryPageSettings;

class MyGalleryPage extends IntegratedGalleryPage
{
	public function __construct(PDO $dbh)
	{
		parent::__construct($dbh, new MyGalleryPageSettings(), new MyPagePermissionChecker(), new GalleryContents(null, "contents", "HTML", array("gallery.css")));
	}
}
?>
