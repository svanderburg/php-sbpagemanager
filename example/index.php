<?php
error_reporting(E_STRICT | E_ALL);

set_include_path("./lib/sblayout:./lib/sbdata:./lib/sbcrud:./lib/sbgallery:./lib/sbeditor:./lib/sbpagemanager:./includes");

require_once("layout/model/Application.class.php");
require_once("layout/model/section/StaticSection.class.php");
require_once("layout/model/section/MenuSection.class.php");
require_once("layout/model/section/ContentsSection.class.php");
require_once("layout/model/page/StaticContentPage.class.php");
require_once("layout/model/page/PageAlias.class.php");
require_once("layout/model/page/HiddenStaticContentPage.class.php");
require_once("layout/model/page/DynamicContentPage.class.php");

require_once("model/page/MyGalleryPage.class.php");
require_once("model/MyPagePermissionChecker.class.php");
require_once("pagemanager/model/page/PageManager.class.php");

require_once("layout/view/html/index.inc.php");

$dbh = new PDO("mysql:host=localhost;dbname=pagemanager", "root", "admin", array(
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
		"header" => new StaticSection("header.inc.php"),
		"menu" => new StaticSection("menu.inc.php"),
		"submenu" => new StaticSection("submenu.inc.php"),
		"contents" => new ContentsSection(true)
	),

	/* Pages */
	new PageManager($dbh, 2, $checker, array(
		"403" => new HiddenStaticContentPage("Forbidden", new Contents("error/403.inc.php")),
		"404" => new HiddenStaticContentPage("Page not found", new Contents("error/404.inc.php")),
		"gallery" => new MyGalleryPage($dbh)
	))
);

displayRequestedPage($application);
?>
