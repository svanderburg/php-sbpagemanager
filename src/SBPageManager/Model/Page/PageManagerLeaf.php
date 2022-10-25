<?php
namespace SBPageManager\Model\Page;
use PDO;
use SBLayout\Model\Page\Page;
use SBLayout\Model\Page\Content\Contents;
use SBData\Model\ParameterMap;
use SBCrud\Model\CRUDModel;
use SBCrud\Model\Page\StaticContentCRUDPage;
use SBPageManager\Model\PagePermissionChecker;

class PageManagerLeaf extends StaticContentCRUDPage
{
	public PDO $dbh;

	public PagePermissionChecker $checker;

	public function __construct(PDO $dbh, PagePermissionChecker $checker, ParameterMap $keyParameterMap)
	{
		$baseURL = Page::computeBaseURL();

		$contentsPath = dirname(__FILE__)."/../../View/HTML/contents/crud/";
		$htmlEditorJsPath = $baseURL."/scripts/htmleditor.js";

		parent::__construct("Error",
			/* Key parameters */
			$keyParameterMap,
			/* Request parameters */
			new ParameterMap(),
			/* Default contents */
			new Contents($contentsPath."page.php", null, null, array($htmlEditorJsPath)),
			/* Error contents */
			new Contents($contentsPath."error.php"),
			/* Contents per operation */
			array(),
			array());

		$this->dbh = $dbh;
		$this->checker = $checker;
	}

	public function constructCRUDModel(): CRUDModel
	{
		return new PageCRUDModel($this, $this->dbh, $this->checker);
	}
}
?>
