<?php
namespace SBPageManager\Model\Page;
use PDO;
use SBCrud\Model\Page\OperationPage;
use SBPageManager\Model\PagePermissionChecker;
use SBPageManager\Model\Page\Content\PageManagerContents;

class PageManagerOperationPage extends OperationPage
{
	public PDO $dbh;

	public PageManagerNode $parentPage;

	public PagePermissionChecker $checker;

	public function __construct(PageManagerNode $parentPage, PDO $dbh, string $title, PageManagerContents $contents, PagePermissionChecker $checker, string $operationParam = "__operation")
	{
		parent::__construct($title, $contents, $operationParam);
		$this->parentPage = $parentPage;
		$this->dbh = $dbh;
		$this->checker = $checker;
	}
}
?>
