<?php
require_once("crud/model/CRUDModel.class.php");
require_once("data/model/Form.class.php");
require_once("data/model/field/TextField.class.php");
require_once("data/model/field/HiddenField.class.php");
require_once("gallery/model/field/HTMLEditorWithGalleryField.class.php");
require_once(dirname(__FILE__)."/../PagePermissionChecker.interface.php");
require_once(dirname(__FILE__)."/../entities/PageEntity.class.php");

class PageCRUDModel extends CRUDModel
{
	public $crudPage;

	public $dbh;

	public $contents;

	public $form = null;

	public function __construct(CRUDPage $crudPage, PDO $dbh, PagePermissionChecker $checker)
	{
		parent::__construct($crudPage);
		$this->crudPage = $crudPage;
		$this->dbh = $dbh;
		$this->checker = $checker;
	}

	private function constructPageForm($nonRoot)
	{
		$baseURL = Page::computeBaseURL();

		$this->form = new Form(array(
			"__operation" => new HiddenField(true),
			"PAGE_ID" => new TextField("Id", $nonRoot, 20, 255),
			"Title" => new TextField("Title", true, 20, 255),
			"Contents" => new HTMLEditorWithGalleryField("editor1", "Contents", $baseURL."/picturepicker.php", $baseURL."/iframepage.html", $baseURL."/lib/sbeditor/editor/image", false),
			"PARENT_ID" => new HiddenField(false)
		));
	}

	private function composePageId()
	{
		/* Compose the page id from the path components */
		$pageId = "";

		foreach($this->keyFields as $id => $field)
		{
			if($pageId === "")
				$pageId = $field->value;
			else
				$pageId .= "/".$field->value;
		}

		return $pageId;
	}

	private function composePageSuffix($pageId)
	{
		if($pageId === "")
			return $pageId;
		else
			return "/".$pageId;
	}

	private function createPage()
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

	private function insertPage()
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

	private function updatePage()
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

	private function moveUpPage()
	{
		$pageId = $this->composePageId();
		PageEntity::moveUp($this->dbh, $pageId);

		header("Location: ".$_SERVER["HTTP_REFERER"]);
		exit();
	}

	private function moveDownPage()
	{
		$pageId = $this->composePageId();
		PageEntity::moveDown($this->dbh, $pageId);

		header("Location: ".$_SERVER["HTTP_REFERER"]);
		exit();
	}

	private function removePage()
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

	private function viewPage()
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

	private function viewEditablePage()
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

	public function executeOperation()
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
