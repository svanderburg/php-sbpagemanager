<?php
namespace SBPageManager\Model\Page;
use Iterator;
use PDO;
use SBLayout\Model\Application;
use SBLayout\Model\Route;
use SBLayout\Model\Page\ContentPage;
use SBPageManager\Model\Page\Content\PageManagerContents;
use SBPageManager\Model\PagePermissionChecker;
use SBPageManager\Model\Page\Iterator\PageManagerIterator;
use SBPageManager\Model\Labels\PageManagerLabels;

class PageManager extends PageManagerNode
{
	public array $overrides;

	public function __construct(PDO $dbh, int $numOfLevels, PagePermissionChecker $checker, array $overrides = array(), PageManagerLabels $labels = null, PageManagerContents $contents = null, string $invalidOperationMessage = "Invalid operation:", string $operationParam = "__operation", int $index = 0)
	{
		parent::__construct("", $dbh, $numOfLevels, $checker, $labels, $contents, $invalidOperationMessage, $operationParam, $index);
		$this->overrides = $overrides;
	}

	public function createDetailPage(array $query): ?ContentPage
	{
		$currentId = $query[0];

		if(array_key_exists($currentId, $this->overrides))
			return $this->overrides[$currentId];
		else
			return parent::createDetailPage($query);
	}

	public function subPageIterator(): Iterator
	{
		return new PageManagerIterator($this->dbh, $this);
	}
}
?>
