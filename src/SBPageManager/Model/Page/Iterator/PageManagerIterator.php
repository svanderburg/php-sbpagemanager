<?php
namespace SBPageManager\Model\Page\Iterator;
use PDO;
use Iterator;
use ArrayIterator;
use SBPageManager\Model\Page\PageManager;

class PageManagerIterator implements Iterator
{
	public PageManager $parentPage;

	public ArrayIterator $arrayIterator;

	public PageManagerNodeIterator $pageManagerNodeIterator;

	public function __construct(PDO $dbh, PageManager $parentPage)
	{
		$this->parentPage = $parentPage;
		$this->arrayIterator = new ArrayIterator($parentPage->overrides);
		$this->pageManagerNodeIterator = new PageManagerNodeIterator($dbh, $parentPage);
	}

	public function current(): mixed
	{
		if($this->arrayIterator->valid())
			return $this->arrayIterator->current();
		else
			return $this->pageManagerNodeIterator->current();
	}

	public function key(): mixed
	{
		if($this->arrayIterator->valid())
			return $this->arrayIterator->key();
		else
			return $this->pageManagerNodeIterator->key();
	}

	public function next(): void
	{
		if($this->arrayIterator->valid())
			$this->arrayIterator->next();
		else
			$this->pageManagerNodeIterator->next();
	}

	public function rewind(): void
	{
		$this->arrayIterator->rewind();
		$this->pageManagerNodeIterator->rewind();
	}

	public function valid(): bool
	{
		return $this->arrayIterator->valid() || $this->pageManagerNodeIterator->valid();
	}
}
?>
