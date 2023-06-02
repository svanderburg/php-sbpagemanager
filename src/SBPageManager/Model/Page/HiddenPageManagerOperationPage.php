<?php
namespace SBPageManager\Model\Page;

class HiddenPageManagerOperationPage extends PageManagerOperationPage
{
	public function checkVisibility(): bool
	{
		return false;
	}
}
?>
