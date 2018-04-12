<?php

namespace PHPGraphQLServer\Schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

$userType = new ObjectType([
	'name' => 'User',
	'description' => 'A User',
	'fields' => [
		'id'	=> Type::id(),
		'username' => [
			'type' => Type::string(),
			'description' => 'The User\'s username'
		],
		'first_name' => [
			'type' => Type::string(),
			'description' => 'The User\'s first name'
		],
		'last_name' => [
			'type' => Type::string(),
			'description' => 'The User\'s last name'
		],
		'email' => [
			'type' => Type::string(),
			'description' => 'The User\'s email address'
		],
		'date_created' => [
			'type' => Type::string(),
			'description' => 'The date and time that this user was created'
		],
	],
]);

include "UserLoader.php";