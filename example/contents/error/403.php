<?php
if($GLOBALS["error"] === null)
{
	?>
	<p>
		You are not allowed to view this page!
	</p>
	<?php
}
else
{
	?>
	<p><?= $GLOBALS["error"] ?></p>
	<?php
}
