<?php
class PageEntity
{
	public static function queryOne(PDO $dbh, $id)
	{
		$stmt = $dbh->prepare("select * from page where PAGE_ID = ?");
		if(!$stmt->execute(array($id)))
			throw new Exception($stmt->errorInfo()[2]);

		return $stmt;
	}

	public static function querySubPages(PDO $dbh, $parentId)
	{
		$stmt = $dbh->prepare("select PAGE_ID, Title from page where PARENT_ID = ? order by Ordering");
		if(!$stmt->execute(array($parentId)))
			throw new Exception($stmt->errorInfo()[2]);

		return $stmt;
	}

	public static function insert(PDO $dbh, array $page)
	{
		$dbh->beginTransaction();

		$stmt = $dbh->prepare("select max(Ordering) from page");
		if(!$stmt->execute())
		{
			$dbh->rollBack();
			throw new Exception($stmt->errorInfo()[2]);
		}

		if(($row = $stmt->fetch()) === false)
			$ordering = 1;
		else
			$ordering = $row[0] + 1;

		$stmt = $dbh->prepare("insert into page values (?, ?, ?, ?, ?)");
		if(!$stmt->execute(array($page["PAGE_ID"], $page["Title"], $page["Contents"], $ordering, $page["PARENT_ID"])))
			throw new Exception($stmt->errorInfo()[2]);

		$dbh->commit();
	}

	public static function update(PDO $dbh, array $page, $id)
	{
		$dbh->beginTransaction();

		$stmt = $dbh->prepare("update page set ".
			"PAGE_ID = ?, ".
			"Title = ?, ".
			"Contents = ? ".
			"where PAGE_ID = ?");
		if(!$stmt->execute(array($page["PAGE_ID"], $page["Title"], $page["Contents"], $id)))
		{
			$dbh->rollBack();
			throw new Exception($stmt->errorInfo()[2]);
		}

		// Update the ids of all pages having the given page as parent
		$stmt = $dbh->prepare("update page set PARENT_ID = ? where PARENT_ID = ?");
		if(!$stmt->execute(array($page["PAGE_ID"], $id)))
		{
			$dbh->rollBack();
			throw new Exception($stmt->errorInfo()[2]);
		}

		if($id !== $page["PAGE_ID"])
		{
			// Rename ids of all pages referring to this page
			$stmt = $dbh->prepare("select PAGE_ID from page where PAGE_ID like ?");
			$param = $id."/%";
			if(!$stmt->execute(array($param)))
			{
				$dbh->rollBack();
				throw new Exception($stmt->errorInfo()[2]);
			}

			while(($row = $stmt->fetch()) !== false)
			{
				$newPageId = $page["PAGE_ID"]."/".basename($row["PAGE_ID"]);

				$update_stmt = $dbh->prepare("update page set PAGE_ID = ? where PAGE_ID = ?");
				if(!$update_stmt->execute(array($newPageId, $row["PAGE_ID"])))
				{
					$dbh->rollBack();
					throw new Exception($stmt->errorInfo()[2]);
				}
			}
		}

		$dbh->commit();
	}

	public static function remove(PDO $dbh, $id)
	{
		$dbh->beginTransaction();

		$stmt = $dbh->prepare("select * from page where PARENT_ID = ?");
		if(!$stmt->execute(array($id)))
		{
			$dbh->rollBack();
			throw new Exception($stmt->errorInfo()[2]);
		}

		if($stmt->fetch() === false)
		{
			$stmt = $dbh->prepare("delete from page where PAGE_ID = ?");
			if(!$stmt->execute(array($id)))
			{
				$dbh->rollBack();
				throw new Exception($stmt->errorInfo()[2]);
			}
		}
		else
		{
			$dbh->rollBack();
			throw new Exception("Cannot delete a page that still has sub pages!");
		}

		$dbh->commit();
	}

	public static function queryPredecessor(PDO $dbh, $parentId, $ordering)
	{
		$stmt = $dbh->prepare("select PAGE_ID, Ordering ".
			"from page ".
			"where PARENT_ID = ? and Ordering in (select max(Ordering) from page where PARENT_ID = ? and Ordering < ?)");

		if(!$stmt->execute(array($parentId, $parentId, $ordering)))
			throw new Exception($stmt->errorInfo()[2]);
		return $stmt;
	}

	public static function querySuccessor(PDO $dbh, $parentId, $ordering)
	{
		$stmt = $dbh->prepare("select PAGE_ID, Ordering ".
			"from page ".
			"where PARENT_ID = ? and Ordering in (select min(Ordering) from page where PARENT_ID = ? and Ordering > ?)");

		if(!$stmt->execute(array($parentId, $parentId, $ordering)))
			throw new Exception($stmt->errorInfo()[2]);
		return $stmt;
	}

	private static function switchPageOrdering(PDO $dbh, array $firstPage, array $secondPage)
	{
		$stmt = $dbh->prepare("update page set Ordering = ? where PAGE_ID = ?");
		if(!$stmt->execute(array($secondPage["Ordering"], $firstPage["PAGE_ID"])))
			throw new Exception($stmt->errorInfo()[2]);

		$stmt = $dbh->prepare("update page set Ordering = ? where PAGE_ID = ?");
		if(!$stmt->execute(array($firstPage["Ordering"], $secondPage["PAGE_ID"])))
			throw new Exception($stmt->errorInfo()[2]);
	}

	public static function moveUp(PDO $dbh, $id)
	{
		$dbh->beginTransaction();

		try
		{
			$stmt = PageEntity::queryOne($dbh, $id);

			if(($page = $stmt->fetch()) !== false)
			{
				$stmt = PageEntity::queryPredecessor($dbh, $page["PARENT_ID"], $page["Ordering"]);

				if(($previousPage = $stmt->fetch()) !== false)
					PageEntity::switchPageOrdering($dbh, $page, $previousPage);
			}

			$dbh->commit();
		}
		catch(Exception $ex)
		{
			$dbh->rollBack();
			throw $ex;
		}
	}

	public static function moveDown(PDO $dbh, $id)
	{
		$dbh->beginTransaction();

		try
		{
			$stmt = PageEntity::queryOne($dbh, $id);

			if(($page = $stmt->fetch()) !== false)
			{
				$stmt = PageEntity::querySuccessor($dbh, $page["PARENT_ID"], $page["Ordering"]);

				if(($nextPage = $stmt->fetch()) !== false)
					PageEntity::switchPageOrdering($dbh, $page, $nextPage);
			}

			$dbh->commit();
		}
		catch(Exception $ex)
		{
			$dbh->rollBack();
			throw $ex;
		}
	}
}
?>
