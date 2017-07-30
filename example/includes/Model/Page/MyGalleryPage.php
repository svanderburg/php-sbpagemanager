<?php
namespace Example\Model\Page;
use PDO;
use SBGallery\Model\Page\GalleryPage;
use Example\Model\MyGallery;
use Example\Model\MyGalleryPermissionChecker;

class MyGalleryPage extends GalleryPage
{
	private $dbh;

	public function __construct(PDO $dbh)
	{
		parent::__construct("Gallery");
		$this->dbh = $dbh;
	}

	public function constructGallery()
	{
		return new MyGallery($this->dbh);
	}

	public function constructGalleryPermissionChecker()
	{
		return new MyGalleryPermissionChecker();
	}
}
?>
