<?php
namespace Example\Model;
use SBPageManager\Model\PagePermissionChecker;

class MyPagePermissionChecker implements PagePermissionChecker
{
	public function checkWritePermissions()
	{
		return (!array_key_exists("view", $_GET) || $_GET["view"] !== "1");
	}
}
?>
