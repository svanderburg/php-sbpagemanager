<?php
global $currentPage, $crudInterface;

if($currentPage->checker->checkWritePermissions())
{
	?>
	<p>
		<a href="<?= $_SERVER["SCRIPT_NAME"] ?>/gallery">Gallery</a>
		<?php
		if(array_key_exists("query", $GLOBALS) && count($GLOBALS["query"]) > 0)
		{
			?>
			| <a href="?__operation=remove_page">Remove page</a>
			| <a href="?__operation=moveup_page">Move up</a>
			| <a href="?__operation=movedown_page">Move down</a>
			<?php
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
