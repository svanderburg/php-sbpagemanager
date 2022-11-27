<?php
global $currentPage, $dbh;

if(\SBPageManager\View\HTML\visitedPageManagerPage($currentPage))
	\SBPageManager\View\HTML\displayDynamicMenuSection($dbh, 1, $currentPage);
?>
