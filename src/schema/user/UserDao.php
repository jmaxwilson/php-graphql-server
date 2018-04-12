<?php

namespace PHPGraphQLServer\Schema\User;

use PHPGraphQLServer\DataAccess\DataAccessObject;
use \Exception;

class UserDao extends DataAccessObject
{
	function __construct(PDO $db = null)
	{
		parent::__construct($db);

		try 
		{
			$this->selectRow("SELECT 1 FROM users;");
		}
		catch (Exception $e)
		{
			$sql = <<<SQL
			CREATE TABLE `users` (
			`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`username` VARCHAR(255) NOT NULL,
			`first_name` VARCHAR(255) NOT NULL,
			`last_name` VARCHAR(255) NOT NULL,
			`email` VARCHAR(255) NOT NULL,
			`date_created` DATETIME NOT NULL,
			PRIMARY KEY (`id`)
			);
SQL;
			$this->execute($sql);
		}
	}

	function getUsers($ids)
	{
		$sql = <<<SQL
		SELECT u.id,
		  	u.username, 
			u.first_name, 
			u.last_name,
			u.email,
			u.date_created
		FROM 
			users u
		WHERE 
			u.id IN (:ids)
SQL;

		$rows = $this->selectRows($sql, [':ids' => $ids]);
			
		$users = [];
		$found_user_ids = [];
		foreach($rows as $row)
		{
			$found_user_ids[] = $row['id'];
			$users[] = $row;
		}

		// GraphQL requires a row for every id requested
		$not_found_ids = array_diff($ids, $found_user_ids);
		foreach ($not_found_ids as $user_id)
		{
			$users[] = null;
		}

		return $users;
	}

	function getFirstXIds($num)
	{
		$sql = <<<SQL
		SELECT 
			u.id
		FROM 
			users u
		ORDER BY
			u.id
		LIMIT $num
SQL;
		
		return $this->selectValues($sql);

	}

	function getLastXIds($num)
	{
		$sql = <<<SQL
		SELECT 
			u.id
		FROM 
			users u
		ORDER BY
			u.id DESC
		LIMIT $num
SQL;
		
		return $this->selectValues($sql);

	}

	function createUser($username, $first_name, $last_name, $email)
	{
		return $this->insert('users', ['username'=>$username,'first_name'=>$first_name,'last_name'=>$last_name, 'email'=>$email, 'date_created' => 'NOW()']);
	}

	function updateUserEmail($id, $values)
	{
		return $this->update('user', ['email'=>$email], $id);
	}
}