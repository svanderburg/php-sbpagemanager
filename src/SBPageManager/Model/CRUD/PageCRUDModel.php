<?php
namespace SBPageManager\Model\CRUD;
use Exception;
use PDO;
use SBData\Model\Form;
use SBData\Model\Field\HiddenField;
use SBData\Model\Field\TextField;
use SBLayout\Model\Page\Page;
use SBCrud\Model\CRUDModel;
use SBCrud\Model\CRUDPage;
use SBGallery\Model\Field\HTMLEditorWithGalleryField;
use SBPageManager\Model\PagePermissionChecker;
use SBPageManager\Model\Entity\PageEntity;

class PageCRUDModel extends CRUDModel
{
	public CRUDPage $crudPage;

	public PDO $dbh;

	public PagePermissionChecker $checker;

	public string $contents;

	public ?Form $form = null;

	public function __construct(CRUDPage $crudPage, PDO $dbh, PagePermissionChecker $checker)
	{
		parent::__construct($crudPage);
		$this->crudPage = $crudPage;
		$this->dbh = $dbh;
		$this->checker = $checker;
	}

	private function constructPageForm($nonRoot): void
	{
		$baseURL = Page::computeBaseURL();

		$this->form = new Form(array(
			"__operation" => new HiddenField(true),
			"PAGE_ID" => new TextField("Id", $nonRoot, 20, 255),
			"Title" => new TextField("Title", true, 20, 255),
			"Contents" => new HTMLEditorWithGalleryField("editor1", "Contents", $baseURL."/picturepicker.php", $baseURL."/iframepage.html", $baseURL."/image/editor", false),
			"PARENT_ID" => new HiddenField(false)
		));
	}

	private function composePageId(): string
	{
		/* Compose the page id from the path components */
		$pageId = "";

		foreach($this->keyFields as $id => $field)
		{
			if($pageId === "")
				$pageId = $field->exportValue();
			else
				$pageId .= "/".$field->exportValue();
		}

		return $pageId;
	}

	private function composePageSuffix(string $pageId): string
	{
		if($pageId === "")
			return $pageId;
		else
			return "/".$pageId;
	}

	private function createPage(): void
	{
		$this->crudPage->title = "Create page";
		$this->constructPageForm(true);

		$parentId = $this->composePageId();

		$row = array(
			"__operation" => "insert_page",
			"PARENT_ID" => $parentId
		);
		$this->form->importValues($row);
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
		$oldPageId = $this->composePageId();

		$this->constructPageForm($oldPageId !== "");
		$this->form->importValues($_REQUEST);
		$this->form->checkFields();

		if($this->form->checkValid())
		{
			$page = $this->form->exportValues();

			if($page["PARENT_ID"] !== "")
				$page["PAGE_ID"] = $page["PARENT_ID"]."/".$page["PAGE_ID"];

			if($oldPageId === "" && $page["PAGE_ID"] !== "")
				throw new Exception("The root page cannot be renamed!");

			PageEntity::update($this->dbh, $page, $oldPageId);

			header("Location: ".$_SERVER["SCRIPT_NAME"].$this->composePageSuffix($page["PAGE_ID"]));
			exit();
		}
	}

	private function moveUpPage(): void
	{
		$pageId = $this->composePageId();
		PageEntity::moveUp($this->dbh, $pageId);

		header("Location: ".$_SERVER["HTTP_REFERER"]);
		exit();
	}

	private function moveDownPage(): void
	{
		$pageId = $this->composePageId();
		PageEntity::moveDown($this->dbh, $pageId);

		header("Location: ".$_SERVER["HTTP_REFERER"]);
		exit();
	}

	private function removePage(): void
	{
		$pageId = $this->composePageId();

		if($pageId === "")
			throw new Exception("The root page cannot be removed!");

		PageEntity::remove($this->dbh, $pageId);

		$parentId = dirname($pageId);

		if($parentId === ".")
			$parentId = "";
		else
			$parentId = "/".$parentId;
		
		header("Location: ".$_SERVER["SCRIPT_NAME"].$parentId);
		exit();
	}

	private function viewPage(): void
	{
		$pageId = $this->composePageId();

		/* Query the requested page */
		$stmt = PageEntity::queryOne($this->dbh, $pageId);
		
		if(($row = $stmt->fetch()) === false)
		{
			header("HTTP/1.1 404 Not Found");
			throw new Exception("Page cannot be found!");
		}
		else
		{
			$this->crudPage->title = $row["Title"];
			$this->contents = $row["Contents"];
		}
	}

	private function viewEditablePage(): void
	{
		$pageId = $this->composePageId();
		$this->constructPageForm($pageId !== "");

		/* Query the requested page */
		$stmt = PageEntity::queryOne($this->dbh, $pageId);

		if(($row = $stmt->fetch()) === false)
		{
			header("HTTP/1.1 404 Not Found");
			throw new Exception("Page cannot be found!");
		}
		else
		{
			$this->crudPage->title = $row["Title"];
			$row['__operation'] = "update_page";
			if($row['PAGE_ID'] !== "")
				$row['PAGE_ID'] = basename($row['PAGE_ID']);
			$this->form->importValues($row);
		}
	}

	public function executeOperation(): void
	{
		if($this->checker->checkWritePermissions())
		{
			if(array_key_exists("__operation", $_REQUEST))
			{
				switch($_REQUEST["__operation"])
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
					default:
						$this->viewEditablePage();
				}
			}
			else
				$this->viewEditablePage();
		}
		else
			$this->viewPage();
	}
}
?>
