<?php
namespace SBPageManager\Model\Page;
use PDO;
use SBLayout\Model\PageNotFoundException;
use SBLayout\Model\Page\ContentPage;
use SBData\Model\Value\Value;
use SBCrud\Model\Page\CRUDMasterPage;
use SBPageManager\Model\Entity\PageEntity;
use SBPageManager\Model\Page\Content\PageManagerContents;
use SBPageManager\Model\PagePermissionChecker;

class PageManagerNode extends CRUDMasterPage
{
	public string $pageId;

	public PDO $dbh;

	public int $numOfLevels;

	public PagePermissionChecker $checker;

	public string $invalidOperationMessage;

	public string $operationParam;

	public int $index;

	public array $entity;

	public function __construct(string $pageId, PDO $dbh, int $numOfLevels, PagePermissionChecker $checker, PageManagerContents $contents = null, string $invalidOperationMessage = "Invalid operation:", string $operationParam = "__operation", int $index = 0)
	{
		if($contents === null)
			$contents = new PageManagerContents();

		parent::__construct("Page manager", $index, $contents, array(
			"create_page" => new PageManagerOperationPage($this, "Create page", $contents, $checker),
			"insert_page" => new PageManagerOperationPage($this, "Insert page", $contents, $checker),
			"update_page" => new PageManagerOperationPage($this, "Update page", $contents, $checker),
			"remove_page" => new PageManagerOperationPage($this, "Remove page", $contents, $checker),
			"moveup_page" => new PageManagerOperationPage($this, "Move up", $contents, $checker),
			"movedown_page" => new PageManagerOperationPage($this, "Move down", $contents, $checker)
		), $invalidOperationMessage, $operationParam);

		$this->pageId = $pageId;
		$this->dbh = $dbh;
		$this->numOfLevels = $numOfLevels;
		$this->checker = $checker;
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
			return new PageManagerNode($subPageId, $this->dbh, $this->numOfLevels, $this->checker, $this->contents, $this->invalidOperationMessage, $this->operationParam, $this->index + 1);
		else
			return null;
	}
}
?>
