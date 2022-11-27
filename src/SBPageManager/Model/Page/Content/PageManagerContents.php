<?php
namespace SBPageManager\Model\Page\Content;
use SBLayout\Model\Page\Page;
use SBLayout\Model\Page\Content\Contents;

class PageManagerContents extends Contents
{
	public function __construct(string $pageManagerSection = "contents", array $sections = null, array $styles = array(), array $scripts = array())
	{
		// Configure the sections
		if($sections === null)
			$sections = array();

		$sections[$pageManagerSection] = dirname(__FILE__)."/../../../View/HTML/contents/page.php";

		// Append the editor JavaScript file
		$baseURL = Page::computeBaseURL();
		$htmlEditorJsPath = $baseURL."/scripts/htmleditor.js";
		array_push($scripts, $htmlEditorJsPath);

		parent::__construct($sections, dirname(__FILE__)."/controller/page.php", $styles, $scripts);
	}
}
?>
