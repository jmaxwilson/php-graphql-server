<?

namespace PHPGraphQLServer;

require '../vendor/autoload.php';

use GraphQL\GraphQL;
use GraphQL\Server\StandardServer;
use GraphQL\Error\Debug;
use Overblog\DataLoader\Promise\Adapter\Webonyx\GraphQL\SyncPromiseAdapter;
use Overblog\PromiseAdapter\Adapter\WebonyxGraphQLSyncPromiseAdapter;

try {

	$graphQLPromiseAdapter = new SyncPromiseAdapter();
	$dataLoaderPromiseAdapter = new WebonyxGraphQLSyncPromiseAdapter($graphQLPromiseAdapter);

	require "../schema/schema.php";

	GraphQL::setPromiseAdapter($graphQLPromiseAdapter);

	$server_config = [
		'schema'		=> $schema,
		'context'		=> null, // This can contain an array or object with context info like User, locale, etc
		'queryBatching'	=> true,
		'debug'			=> Debug::INCLUDE_DEBUG_MESSAGE //| Debug::INCLUDE_TRACE,
	];

	$gql_server = new StandardServer($server_config);
	$gql_server->handleRequest(); // parses PHP globals and emits response
}
catch (\Exception $e)
{
	StandardServer::send500Error($e);
}
