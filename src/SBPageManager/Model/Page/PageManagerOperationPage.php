<?php
namespace SBPageManager\Model\Page;
use PDO;
use SBCrud\Model\Page\OperationPage;
use SBPageManager\Model\PagePermissionChecker;
use SBPageManager\Model\Page\Content\PageManagerContents;
use SBPageManager\Model\Labels\PageManagerLabels;

class PageManagerOperationPage extends OperationPage
{
	public PDO $dbh;

	public PageManagerNode $parentPage;

	public PagePermissionChecker $checker;

	public PageManagerLabels $labels;

	public function __construct(PageManagerNode $parentPage, PDO $dbh, string $title, PageManagerContents $contents, PagePermissionChecker $checker, PageManagerLabels $labels, string $operationParam = "__operation")
	{
		parent::__construct($title, $contents, $operationParam, dirname(__FILE__)."/../../View/HTML/menuitems/pagemanageroperationpage.php");
		$this->parentPage = $parentPage;
		$this->dbh = $dbh;
		$this->checker = $checker;
		$this->labels = $labels;
	}
}
?>
