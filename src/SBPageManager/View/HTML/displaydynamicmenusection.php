<?php
/**
 * @file
 * @brief View-HTML-DynamicMenuSection module
 * @defgroup View-HTML-DynamicMenuSection
 * @{
 */
namespace SBPageManager\View\HTML;
use PDO;
use SBPageManager\Model\PagePermissionChecker;
use SBPageManager\Model\Entity\PageEntity;

/**
 * Displays a menu section for a certain level with links retrieved from the database.
 *
 * @param $dbh Database connection handler
 * @param $level Level of the menu section
 * @param $checker Permission checker that checks whether the user is authorized to edit the page structure
 */
function displayDynamicMenuSection(PDO $dbh, int $level, PagePermissionChecker $checker)
{
	if(array_key_exists("query", $GLOBALS))
		$query = $GLOBALS["query"];
	else
		$query = array();

	if($level <= count($query))
	{
		if($level == 0)
			$parentId = "";
		else if($level > 0)
		{
			$parentId = reset($query);

			for($i = 1; $i < $level; $i++)
				$parentId .= "/".next($query);
		}

		$stmt = PageEntity::querySubPages($dbh, $parentId);

		while(($row = $stmt->fetch()) !== false)
		{
			if($level < count($query) && array_key_exists($level, $query) && basename($row["PAGE_ID"]) === $query[$level])
				$active = ' class="active"';
			else
				$active = "";
			?>
			<a href="<?php print($_SERVER["SCRIPT_NAME"]."/".$row["PAGE_ID"]); ?>"<?php print($active); ?>><?php print($row["Title"]); ?></a>
			<?php
		}

		if($checker->checkWritePermissions())
		{
			if($parentId !== "")
				$parentId = "/".$parentId;
			?>
			<a class="create-page" href="<?php print($_SERVER["SCRIPT_NAME"].$parentId); ?>?__operation=create_page">Create page</a>
			<?php
		}
	}
}

/**
 * @}
 */
?>
