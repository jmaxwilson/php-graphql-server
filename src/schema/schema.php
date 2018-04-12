<?php

namespace PHPGraphQLServer\Schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use PHPGraphQLServer\Schema\User\UserDao;

include "user/UserType.php";

// MAIN QUERY
$queryType = new ObjectType([
	'name' => 'Query',
	'fields' => [
		'User' => [
			'type' => $userType,
			'args' => [
				'id' => ['type' => Type::id()],
			],
			'resolve' => function($user, $args) use ($userLoader) {
				return $userLoader->load($args['id']);
			}
		],
		'UserList' => [
			'type' => Type::listOf($userType),
			'args' => [
				'ids' => ['type'=>Type::listOf(Type::id())],
				'first' => ['type'=>Type::int()],
				'last' => ['type'=>Type::int()]
			],
			'resolve' => function ($userList, $args) use ($userLoader)
			{
				if (!empty($args['ids']))
				{
					return $userLoader->loadMany($args['ids']);
				}
				else if (!empty($args['first']))
				{
					$user_dao = new UserDao();
					$ids = $user_dao->getFirstXIds($args['first']);
					return $userLoader->loadMany($ids);
				}
				else if (!empty($args['last']))
				{
					$user_dao = new UserDao();
					$ids = $user_dao->getLastXIds($args['last']);
					return $userLoader->loadMany($ids);
				}
			},
		],
	],
]);

// MUTATIONS
$mutationType = new ObjectType([
	'name' => 'Mutation',
	'fields' => [
		'createUser' => [
			'args' => [
				'username' => ['type' => Type::string()],
				'first_name' => ['type' => Type::string()],
				'last_name' => ['type' => Type::string()],
				'email' => ['type' => Type::string()],
			],
			'type' => $userType,
			'resolve' => function ($root, $args) use ($userLoader) {
				$user_dao = new UserDao();
				$new_user_id = $user_dao->createUser($args['username'], $args['first_name'], $args['last_name'], $args['email']);

				return $userLoader->load($new_user_id);
			},
		],
		'updateUserEmail' => [
			'args' => [
				'id' => ['type' => Type::id()],
				'email' => ['type' => Type::string()]
			],
			'type' => $userType,
			'resolve' => function ($root, $args) use ($userLoader) {
				$user_dao = new UserDao();
				$new_user_id = $user_dao->updateUserEmail($args['id'], $args['email']);

				return $userLoader->load($args['id']);
			},
		]
	],
]);



$schema = new Schema([
	'query'		=> $queryType,
	'mutation'	=> $mutationType,
]);

//$schema->assertValid();
