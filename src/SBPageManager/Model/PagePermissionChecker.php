<?php
namespace SBPageManager\Model;

/**
 * Provides a facility that can be used to check whether a user has write
 * permissions to edit pages.
 */
interface PagePermissionChecker
{
	/**
	 * Checks whether the user has the permissions to modify the page.
	 *
	 * @return bool true if the user has permissions, else false
	 */
	public function checkWritePermissions();
}
?>
