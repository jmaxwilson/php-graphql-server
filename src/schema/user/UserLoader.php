<?php

namespace PHPGraphQLServer\Schema;

use Overblog\DataLoader\DataLoader;
use PHPGraphQLServer\Schema\User\UserDao;

$loadUserBatch = function ($keys) use ($dataLoaderPromiseAdapter) {
	$user_dao = new UserDao();
	$user_data = $user_dao->getUsers($keys);

	return $dataLoaderPromiseAdapter->createAll($user_data);
};

$userLoader = new DataLoader($loadUserBatch, $dataLoaderPromiseAdapter);