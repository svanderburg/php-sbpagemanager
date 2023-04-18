<?php
namespace SBPageManager\Model\Page\Iterator;
use PDO;
use PDOStatement;
use Iterator;
use SBPageManager\Model\Page\PageManagerNode;
use SBPageManager\Model\Entity\PageEntity;

class PageManagerNodeIterator implements Iterator
{
	public PageManagerNode $parentPage;

	public PDO $dbh;

	public PDOStatement $stmt;

	public $row;

	public bool $authenticated;

	public bool $reachedEnd;

	public function __construct(PDO $dbh, PageManagerNode $parentPage)
	{
		$this->dbh = $dbh;
		$this->parentPage = $parentPage;
		$this->authenticated = $parentPage->checker->checkWritePermissions();
	}

	public function current(): mixed
	{
		if($this->row === false)
			return $this->parentPage->crudPageManager->operationPages["create_page"];
		else
			return $this->parentPage->createDetailPage(array($this->parentPage->index => basename($this->row["PAGE_ID"])));
	}

	public function key(): mixed
	{
		if($this->row === false)
			return "create_page";
		else
			return basename($this->row["PAGE_ID"]);
	}

	public function next(): void
	{
		if($this->row === false)
			$this->reachedEnd = true;
		else
			$this->row = $this->stmt->fetch();
	}

	public function rewind(): void
	{
		$this->stmt = PageEntity::querySubPages($this->dbh, $this->parentPage->pageId);
		$this->row = $this->stmt->fetch();
		$this->reachedEnd = false;
	}

	public function valid(): bool
	{
		return ($this->authenticated && !$this->reachedEnd) || (!$this->authenticated && $this->row !== false);
	}
}
?>
