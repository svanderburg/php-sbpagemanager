<?php
namespace SBPageManager\Model\Page;
use PDO;
use SBLayout\Model\Page\Page;
use SBLayout\Model\Page\Content\Contents;
use SBCrud\Model\Page\StaticContentCRUDPage;
use SBPageManager\Model\PagePermissionChecker;

class PageManagerLeaf extends StaticContentCRUDPage
{
	public $dbh;

	public $checker;

	public function __construct(PDO $dbh, PagePermissionChecker $checker, array $keyFields)
	{
		$baseURL = Page::computeBaseURL();

		$contentsPath = dirname(__FILE__)."/../../View/HTML/contents/crud/";
		$htmlEditorJsPath = $baseURL."/scripts/htmleditor.js";

		parent::__construct("Error",
			/* Key fields */
			$keyFields,
			/* Default contents */
			new Contents($contentsPath."page.php", null, null, array($htmlEditorJsPath)),
			/* Error contents */
			new Contents($contentsPath."error.php"),
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
