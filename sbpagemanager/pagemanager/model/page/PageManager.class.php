<?php
require_once("crud/model/page/DynamicContentCRUDPage.class.php");
require_once("PageManagerLeaf.class.php");
require_once(dirname(__FILE__)."/../crud/PageCRUDModel.class.php");
require_once(dirname(__FILE__)."/../PagePermissionChecker.interface.php");
require_once("data/model/field/TextField.class.php");

class PageManager extends DynamicContentCRUDPage
{
	public $dbh;

	public $checker;

	public $overrides;

	public function __construct(PDO $dbh, $numOfLevels, PagePermissionChecker $checker, array $overrides = null, $index = 0, array $keyFields = null)
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

		$contentsPath = dirname(__FILE__)."/../../view/html/contents/crud/";
		$htmlEditorJsPath = $baseURL."/lib/sbeditor/editor/scripts/htmleditor.js";

		parent::__construct("Error",
			/* Parameter name */
			$index,
			/* Key fields */
			$keyFields,
			/* Default contents */
			new Contents($contentsPath."page.inc.php", null, null, array($htmlEditorJsPath)),
			/* Error contents */
			new Contents($contentsPath."error.inc.php"),
			/* Contents per operation */
			array(),
			$dynamicSubPage);

		$this->dbh = $dbh;
		$this->checker = $checker;
		$this->overrides = $overrides;
	}

	public function constructCRUDModel()
	{
		return new PageCRUDModel($this, $this->dbh, $this->checker);
	}

	public function lookupSubPage(Application $application, array $ids, $index = 0)
	{
		if(count($ids) == $index)
			return parent::lookupSubPage($application, $ids, $index);
		else
		{
			if($index == 0)
			{
				$currentId = $ids[$index]; // Take the first id of the array

				if($this->overrides !== null && array_key_exists($currentId, $this->overrides)) // If an override has been provided, do a lookup for that page
				{
					$page = $this->overrides[$currentId];
					return $page->lookupSubPage($application, $ids, $index + 1);
				}
				else
					return parent::lookupSubPage($application, $ids, $index);
			}
			else
				return parent::lookupSubPage($application, $ids, $index);
		}
	}
}
?>
