<?php
namespace SBPageManager\Model\Labels;

class PageManagerLabels
{
	public string $id;

	public string $title;

	public string $contents;

	public string $cannotRenameRootPage;

	public string $cannotRemoveRootPage;

	public string $noPermissions;

	public function __construct(string $id = "Id",
		string $title = "Title",
		string $contents = "Contents",
		string $cannotRenameRootPage = "The root page cannot be renamed!",
		string $cannotRemoveRootPage = "The root page cannot be removed!",
		string $noPermissions = "No permissions to modify a page!")
	{
		$this->id = $id;
		$this->title = $title;
		$this->contents = $contents;
		$this->cannotRenameRootPage = $cannotRenameRootPage;
		$this->cannotRemoveRootPage = $cannotRemoveRootPage;
		$this->noPermissions = $noPermissions;
	}
}
?>
