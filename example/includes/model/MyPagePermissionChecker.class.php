<?php
require_once("pagemanager/model/PagePermissionChecker.interface.php");

class MyPagePermissionChecker implements PagePermissionChecker
{
	public function checkWritePermissions()
	{
		return (!array_key_exists("view", $_GET) || $_GET["view"] !== "1");
	}
}
?>
