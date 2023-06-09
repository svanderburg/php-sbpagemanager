<?php
namespace SBPageManager\Model\Page;
use Iterator;
use PDO;
use SBLayout\Model\PageNotFoundException;
use SBLayout\Model\Page\ContentPage;
use SBData\Model\Value\Value;
use SBCrud\Model\Page\CRUDMasterPage;
use SBPageManager\Model\Entity\PageEntity;
use SBPageManager\Model\Page\Content\PageManagerContents;
use SBPageManager\Model\PagePermissionChecker;
use SBPageManager\Model\Page\Iterator\PageManagerNodeIterator;
use SBPageManager\Model\Labels\PageManagerLabels;

class PageManagerNode extends CRUDMasterPage
{
	public string $pageId;

	public PDO $dbh;

	public int $numOfLevels;

	public PagePermissionChecker $checker;

	public PageManagerLabels $labels;

	public string $invalidOperationMessage;

	public string $operationParam;

	public int $index;

	public array $entity;

	public function __construct(string $pageId, PDO $dbh, int $numOfLevels, PagePermissionChecker $checker, PageManagerLabels $labels = null, PageManagerContents $contents = null, string $invalidOperationMessage = "Invalid operation:", string $operationParam = "__operation", int $index = 0)
	{
		if($contents === null)
			$contents = new PageManagerContents();

		if($labels === null)
			$labels = new PageManagerLabels();

		parent::__construct("Page manager", $index, $contents, array(
			"create_page" => new PageManagerOperationPage($this, $dbh, "Create page", $contents, $checker, $labels, $operationParam),
			"insert_page" => new HiddenPageManagerOperationPage($this, $dbh, "Insert page", $contents, $checker, $labels, $operationParam),
			"update_page" => new HiddenPageManagerOperationPage($this, $dbh, "Update page", $contents, $checker, $labels, $operationParam),
			"remove_page" => new PageManagerOperationPage($this, $dbh, "Remove page", $contents, $checker, $labels, $operationParam),
			"moveup_page" => new PageManagerOperationPage($this, $dbh, "Move up", $contents, $checker, $labels, $operationParam),
			"movedown_page" => new PageManagerOperationPage($this, $dbh, "Move down", $contents, $checker, $labels, $operationParam)
		), $invalidOperationMessage, $operationParam);

		$this->dbh = $dbh;
		$this->pageId = $pageId;
		$this->numOfLevels = $numOfLevels;
		$this->checker = $checker;
		$this->labels = $labels;
		$this->index = $index;
		$this->invalidOperationMessage = $invalidOperationMessage;
		$this->operationParam = $operationParam;

		/* Query the requested page */
		$stmt = PageEntity::queryOne($this->dbh, $pageId);

		if(($entity = $stmt->fetch()) === false)
			throw new PageNotFoundException();
		else
		{
			$this->entity = $entity;
			$this->title = $this->entity["Title"];
		}
	}

	public function createParamValue(): Value
	{
		return new Value(true, 255);
	}

	public function createDetailPage(array $query): ?ContentPage
	{
		if($this->pageId === "")
			$subPageId = $query[$this->index];
		else
			$subPageId = $this->pageId."/".$query[$this->index];

		if($this->index < $this->numOfLevels)
			return new PageManagerNode($subPageId, $this->dbh, $this->numOfLevels, $this->checker, $this->labels, $this->contents, $this->invalidOperationMessage, $this->operationParam, $this->index + 1);
		else
			return null;
	}

	public function subPageIterator(): Iterator
	{
		return new PageManagerNodeIterator($this->dbh, $this);
	}
}
?>
