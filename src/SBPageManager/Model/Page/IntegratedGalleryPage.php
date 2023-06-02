<?php
namespace SBPageManager\Model\Page;
use PDO;
use SBGallery\Model\Page\GalleryPage;
use SBGallery\Model\Page\Content\GalleryContents;
use SBGallery\Model\Page\Settings\GalleryPageSettings;
use SBPageManager\Model\PagePermissionChecker;
use SBPageManager\Model\GalleryPermissionCheckerAdapter;

class IntegratedGalleryPage extends GalleryPage
{
	public function __construct(PDO $dbh, GalleryPageSettings $settings, PagePermissionChecker $checker, GalleryContents $contents = null)
	{
		parent::__construct($dbh, $settings, new GalleryPermissionCheckerAdapter($checker), $contents);
	}

	public function checkAccessibility(): bool
	{
		return $this->checker->checkWritePermissions();
	}
}
?>
