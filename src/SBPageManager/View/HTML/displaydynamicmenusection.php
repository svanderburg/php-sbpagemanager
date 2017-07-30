<?php
namespace SBPageManager\View\HTML;
use PDO;
use SBPageManager\Model\PagePermissionChecker;
use SBPageManager\Model\Entity\PageEntity;

function displayDynamicMenuSection(PDO $dbh, $level, PagePermissionChecker $checker)
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
			$parentId = $query[0];

			for($i = 1; $i < $level; $i++)
				$parentId .= "/".$query[$i];
		}

		$stmt = PageEntity::querySubPages($dbh, $parentId);

		while(($row = $stmt->fetch()) !== false)
		{
			if($level < count($query) && basename($row["PAGE_ID"]) === $query[$level])
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
?>
