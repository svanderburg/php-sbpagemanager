<?php
use SBPageManager\Model\CRUD\PageCRUDInterface;

global $crudInterface, $route, $currentPage, $dbh;

$crudInterface = new PageCRUDInterface($route, $currentPage, $dbh);
$crudInterface->executeOperation();
?>
