<?php
require_once("crud/model/page/StaticContentCRUDPage.class.php");
require_once(dirname(__FILE__)."/../crud/PageCRUDModel.class.php");
require_once(dirname(__FILE__)."/../PagePermissionChecker.interface.php");

class PageManagerLeaf extends StaticContentCRUDPage
{
	public $dbh;

	public $checker;

	public function __construct(PDO $dbh, PagePermissionChecker $checker, array $keyFields)
	{
		$baseURL = Page::computeBaseURL();

		$contentsPath = dirname(__FILE__)."/../../view/html/contents/crud/";
		$htmlEditorJsPath = $baseURL."/lib/sbeditor/editor/scripts/htmleditor.js";

		parent::__construct("Error",
			/* Key fields */
			$keyFields,
			/* Default contents */
			new Contents($contentsPath."page.inc.php", null, null, array($htmlEditorJsPath)),
			/* Error contents */
			new Contents($contentsPath."error.inc.php"),
			/* Contents per operation */
			array(),
			null);

		$this->dbh = $dbh;
		$this->checker = $checker;
	}

	public function constructCRUDModel()
	{
		return new PageCRUDModel($this, $this->dbh, $this->checker);
	}
}
?>
