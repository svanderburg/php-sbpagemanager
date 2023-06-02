<?php
global $currentPage, $crudInterface;

if($currentPage->checker->checkWritePermissions())
{
	?>
	<p>
		<?php
		if(array_key_exists("query", $GLOBALS) && count($GLOBALS["query"]) > 0)
		{
			global $route;
			\SBCrud\View\HTML\displayOperationToolbar($route, count($route->ids) - 1);
		}
		?>
	</p>
	<?php
	\SBData\View\HTML\displayEditableForm($crudInterface->form);
	?>
	<script type="text/javascript">
	sbeditor.initEditors();
	</script>
	<?php
}
else
	print($currentPage->entity["Contents"]);
?>
