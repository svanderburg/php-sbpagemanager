<?php
if($active)
{
	?>
	<a class="active-pagemanager-operation" href="<?= $url ?>"><strong><?= $subPage->title ?></strong></a>
	<?php
}
else
{
	?>
	<a class="pagemanager-operation" href="<?= $url ?>"><?= $subPage->title ?></a>
	<?php
}
?>
