<?php
namespace SBPageManager\Model\CRUD;
use Exception;
use PDO;
use SBLayout\Model\BadRequestException;
use SBLayout\Model\PageForbiddenException;
use SBLayout\Model\Route;
use SBLayout\Model\Page\Page;
use SBData\Model\Field\HiddenField;
use SBData\Model\Field\TextField;
use SBCrud\Model\CRUDForm;
use SBCrud\Model\CRUD\CRUDInterface;
use SBCrud\Model\Page\CRUDPage;
use SBGallery\Model\Field\HTMLEditorWithGalleryField;
use SBPageManager\Model\PagePermissionChecker;
use SBPageManager\Model\Entity\PageEntity;

class PageCRUDInterface extends CRUDInterface
{
	public Route $route;

	public CRUDPage $crudPage;

	public PDO $dbh;

	public string $contents;

	public ?CRUDForm $form = null;

	public function __construct(Route $route, CRUDPage $crudPage)
	{
		parent::__construct($crudPage);
		$this->route = $route;
		$this->crudPage = $crudPage;
		$this->dbh = $crudPage->dbh;
	}

	private function constructPageForm(bool $isRootPage): void
	{
		if($isRootPage)
			$pageIdField = new HiddenField(false, 255);
		else
			$pageIdField = new TextField("Id", true, 20, 255);

		$baseURL = Page::computeBaseURL();

		$this->form = new CRUDForm(array(
			"__operation" => new HiddenField(true),
			"PAGE_ID" => $pageIdField,
			"Title" => new TextField("Title", true, 20, 255),
			"Contents" => new HTMLEditorWithGalleryField("editor1", "Contents", $baseURL."/picturepicker.php", $baseURL."/iframepage.html", $baseURL."/image/editor", false),
			"PARENT_ID" => new HiddenField(false)
		), $this->operationParam);
	}

	private function composePageSuffix(string $pageId): string
	{
		if($pageId === "")
			return "";
		else
			return "/".$pageId;
	}

	private function createPage(): void
	{
		$this->constructPageForm(false);

		$parentId = $this->crudPage->parentPage->pageId;

		$row = array(
			"PARENT_ID" => $parentId
		);
		$this->form->importValues($row);
		$this->form->setOperation("insert_page");
	}

	private function insertPage(): void
	{
		$this->constructPageForm(true);
		$this->form->importValues($_REQUEST);
		$this->form->checkFields();

		if($this->form->checkValid())
		{
			$page = $this->form->exportValues();

			if($page["PARENT_ID"] !== "")
				$page["PAGE_ID"] = $page["PARENT_ID"]."/".$page["PAGE_ID"];
			
			PageEntity::insert($this->dbh, $page);

			header("Location: ".$_SERVER["SCRIPT_NAME"].$this->composePageSuffix($page["PAGE_ID"]));
			exit();
		}
	}

	private function updatePage(): void
	{
		$oldPageId = $this->crudPage->parentPage->pageId;

		$this->constructPageForm($oldPageId !== "");
		$this->form->importValues($_REQUEST);
		$this->form->checkFields();

		if($this->form->checkValid())
		{
			$page = $this->form->exportValues();

			if($page["PARENT_ID"] !== "")
				$page["PAGE_ID"] = $page["PARENT_ID"]."/".$page["PAGE_ID"];

			if($oldPageId === "" && $page["PAGE_ID"] !== "")
				throw new BadRequestException("The root page cannot be renamed!");

			PageEntity::update($this->dbh, $page, $oldPageId);

			header("Location: ".$_SERVER["SCRIPT_NAME"].$this->composePageSuffix($page["PAGE_ID"]));
			exit();
		}
	}

	private function moveUpPage(): void
	{
		$pageId = $this->crudPage->parentPage->pageId;
		PageEntity::moveUp($this->dbh, $pageId);

		header("Location: ".$_SERVER["PHP_SELF"]);
		exit();
	}

	private function moveDownPage(): void
	{
		$pageId = $this->crudPage->parentPage->pageId;
		PageEntity::moveDown($this->dbh, $pageId);

		header("Location: ".$_SERVER["PHP_SELF"]);
		exit();
	}

	private function removePage(): void
	{
		$pageId = $this->crudPage->parentPage->pageId;

		if($pageId === "")
			throw new BadRequestException("The root page cannot be removed!");

		PageEntity::remove($this->dbh, $pageId);

		header("Location: ".$this->route->composeParentPageURL($_SERVER["SCRIPT_NAME"]));
		exit();
	}

	private function viewPage(): void
	{
		// Do nothing
	}

	private function viewEditablePage(): void
	{
		$this->constructPageForm($this->crudPage->pageId === "");

		$page = $this->crudPage->entity;

		if($page['PAGE_ID'] !== "")
			$page['PAGE_ID'] = basename($page['PAGE_ID']);

		$this->form->importValues($page);
		$this->form->setOperation("update_page");
	}

	public function executeCRUDOperation(?string $operation): void
	{
		if($operation === null)
			$this->viewEditablePage();
		else
		{
			if($this->crudPage->checker->checkWritePermissions())
			{
				switch($operation)
				{
					case "create_page":
						$this->createPage();
						break;
					case "insert_page":
						$this->insertPage();
						break;
					case "update_page":
						$this->updatePage();
						break;
					case "remove_page":
						$this->removePage();
						break;
					case "moveup_page":
						$this->moveUpPage();
						break;
					case "movedown_page":
						$this->moveDownPage();
						break;
				}
			}
			else
				throw new PageForbiddenException("No permissions to modify a page!");
		}
	}
}
?>
