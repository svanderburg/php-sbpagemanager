<?php
namespace SBPageManager\Model\Page;
use PDO;
use SBLayout\Model\Application;
use SBLayout\Model\Route;
use SBLayout\Model\Page\Page;
use SBLayout\Model\Page\Content\Contents;
use SBData\Model\Field\TextField;
use SBCrud\Model\CRUDModel;
use SBCrud\Model\Page\DynamicContentCRUDPage;
use SBPageManager\Model\PagePermissionChecker;
use SBPageManager\Model\CRUD\PageCRUDModel;

class PageManager extends DynamicContentCRUDPage
{
	public PDO $dbh;

	public PagePermissionChecker $checker;

	public ?array $overrides;

	public function __construct(PDO $dbh, int $numOfLevels, PagePermissionChecker $checker, array $overrides = null, int $index = 0, array $keyFields = null)
	{
		/* Compose sub pages */
		if($keyFields === null)
			$keyFields = array();
		
		$propagatedKeyFields = $keyFields;
		$propagatedKeyFields[$index] = new TextField("Id", true, 20, 255);
		
		if($index < $numOfLevels)
			$dynamicSubPage = new PageManager($dbh, $numOfLevels, $checker, null, $index + 1, $propagatedKeyFields);
		else
			$dynamicSubPage = new PageManagerLeaf($dbh, $checker, $propagatedKeyFields);
		
		/* Compose page */
		$baseURL = Page::computeBaseURL();

		$contentsPath = dirname(__FILE__)."/../../View/HTML/contents/crud/";
		$htmlEditorJsPath = $baseURL."/scripts/htmleditor.js";

		parent::__construct("Error",
			/* Parameter name */
			$index,
			/* Key fields */
			$keyFields,
			/* Default contents */
			new Contents($contentsPath."page.php", null, null, array($htmlEditorJsPath)),
			/* Error contents */
			new Contents($contentsPath."error.php"),
			/* Contents per operation */
			array(),
			$dynamicSubPage);

		$this->dbh = $dbh;
		$this->checker = $checker;
		$this->overrides = $overrides;
	}

	public function constructCRUDModel(): CRUDModel
	{
		return new PageCRUDModel($this, $this->dbh, $this->checker);
	}

	/**
	 * @see Page#examineRoute()
	 */
	public function examineRoute(Application $application, Route $route, int $index = 0): void
	{
		if($route->indexIsAtRequestedPage($index))
			parent::examineRoute($application, $route, $index);
		else
		{
			if($index == 0)
			{
				$currentId = $route->getId($index); // Take the first id of the array

				if($this->overrides !== null && array_key_exists($currentId, $this->overrides)) // If an override has been provided, do a lookup for that page
				{
					$page = $this->overrides[$currentId];
					$page->examineRoute($application, $route, $index + 1);
				}
				else
					parent::examineRoute($application, $route, $index);
			}
			else
				parent::examineRoute($application, $route, $index);
		}
	}
}
?>
