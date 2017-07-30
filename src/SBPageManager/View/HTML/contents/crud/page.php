<?php
global $crudModel;

if($crudModel->checker->checkWritePermissions())
{
	?>
	<p>
		<a href="<?php print($_SERVER["SCRIPT_NAME"]); ?>/gallery">Gallery</a>
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
	\SBData\View\HTML\displayEditableForm($crudModel->form,
		"Submit",
		"One or more fields are incorrectly specified and marked with a red color!",
		"This field is incorrectly specified!");
	?>
	<script type="text/javascript">
	sbeditor.initEditors();
	</script>
	<?php
}
else
	print($crudModel->contents);
?>
