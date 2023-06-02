<?php
namespace SBPageManager\Model;
use SBGallery\Model\GalleryPermissionChecker;

class GalleryPermissionCheckerAdapter implements GalleryPermissionChecker
{
	private PagePermissionChecker $checker;

	public function __construct(PagePermissionChecker $checker)
	{
		$this->checker = $checker;
	}

	public function checkWritePermissions(): bool
	{
		return $this->checker->checkWritePermissions();
	}
}
?>
