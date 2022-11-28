<?php
use SBPageManager\Model\CRUD\PageCRUDInterface;

global $crudInterface, $route, $currentPage;

$crudInterface = new PageCRUDInterface($route, $currentPage);
$crudInterface->executeOperation();
?>
