<?php
require_once("gallery/model/page/GalleryPage.class.php");
require_once(dirname(__FILE__)."/../MyGallery.class.php");
require_once(dirname(__FILE__)."/../MyGalleryPermissionChecker.class.php");

class MyGalleryPage extends GalleryPage
{
	private $dbh;

	public function __construct(PDO $dbh)
	{
		parent::__construct();
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
