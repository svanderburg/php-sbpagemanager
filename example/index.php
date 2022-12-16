<?php
error_reporting(E_STRICT | E_ALL);

require_once(dirname(__FILE__)."/../vendor/autoload.php");

use SBLayout\Model\Application;
use SBLayout\Model\Page\HiddenStaticContentPage;
use SBLayout\Model\Page\Content\Contents;
use SBLayout\Model\Section\ContentsSection;
use SBLayout\Model\Section\MenuSection;
use SBLayout\Model\Section\StaticSection;
use SBPageManager\Model\Page\PageManager;
use Example\Model\MyPagePermissionChecker;
use Example\Model\Page\MyGalleryPage;

require_once("includes/config.php");

$dbh = new PDO($config["dbDsn"], $config["dbUsername"], $config["dbPassword"], array(
	PDO::ATTR_PERSISTENT => true
));

$checker = new MyPagePermissionChecker();

$application = new Application(
	/* Title */
	"Test Content Management System",

	/* CSS stylesheets */
	array("default.css"),

	/* Sections */
	array(
		"header" => new StaticSection("header.php"),
		"menu" => new MenuSection(0),
		"submenu" => new MenuSection(1),
		"contents" => new ContentsSection(true)
	),

	/* Pages */
	new PageManager($dbh, 2, $checker, array(
		"400" => new HiddenStaticContentPage("Bad request", new Contents("error/400.php")),
		"403" => new HiddenStaticContentPage("Forbidden", new Contents("error/403.php")),
		"404" => new HiddenStaticContentPage("Page not found", new Contents("error/404.php")),
		"gallery" => new MyGalleryPage($dbh)
	))
);

\SBLayout\View\HTML\displayRequestedPage($application);
?>
