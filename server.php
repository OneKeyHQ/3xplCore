<?php

// ENV.SERVER_MEMORY_LIMIT: set memory limit. default 4096M.
// ENV.SERVER_WORKER_NUM: decide how many requests can be processed in parallel. default 200.

declare(strict_types=1);

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

require_once __DIR__ . '/Init.php';
require_once __DIR__ . '/Engine/DebugHelpers.php';

$envFilePath = __DIR__ . '/.env';
$_ENV = parse_ini_file($envFilePath);

ini_set('memory_limit', $_ENV['SERVER_MEMORY_LIMIT'] ?? '4096M');

function parseBlock($moduleName, $block)
{
  $module = new (module_name_to_class($moduleName))();
  $module->process_block((int)$block);
  $res = $module->get_return_events();
  return $res;
}

$server = new Swoole\HTTP\Server("0.0.0.0", 9501);

$server->set([
  'worker_num' => intval($_ENV['SERVER_WORKER_NUM'] ?? 200),
]);

$server->on("Start", function(Server $server)
{
    echo "3xpl http server is started at http://0.0.0.0:9501\n";
});

$server->on("Request", function(Request $request, Response $response)
{

  if ($request->getMethod() !== 'GET') {
    $response->status(400);
    $response->header("Content-Type", "application/json");
    $response->end(json_encode(['error' => 'Invalid request method']));
    return;
  }

  $path = $request->server['request_uri'];
  $params = $request->get ?? [];
  $queryString = http_build_query($params);
  $url = $path . ($queryString ? '?' . $queryString : '');
  $currentTime = date('Y-m-d H:i:s');

  echo "$currentTime Get $url\n";

  $params = $request->get;

  if (!isset($params['module']) || !isset($params['block'])) {
      $response->status(400);
      $response->header("Content-Type", "application/json");
      $response->end(json_encode(['error' => 'Missing module or block parameter']));
      return;
  }

  $res = parseBlock($params['module'], $params['block']);
  $response->status(200);
  $response->header("Content-Type", "application/json");
  $response->end(json_encode($res));

});

$server->start();