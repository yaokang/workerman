<?php 

	use Workerman\Worker;
	use Workerman\lib\Timer;

	require_once './Autoloader.php';

	//创建一个Worker监听2345端口,使用http协议通讯
	$http_worker = new Worker('http://0.0.0.0:2345');

	//启动四个进程对外提供服务
	//$http_worker->count = 4;

	$http_worker->name = 'yaokang';

	$http_worker->onWorkerStart = function($http_worker){

		echo "Worker starting...\n";

		Timer::add(10,function()use($http_worker){

			foreach($http_worker->connections as $connection){

				$connection->send(time());
			}

		});

	};

	//接收到浏览器发送的数据时回复hello world给浏览器
	$http_worker->onMessage = function($connection, $data){

		// 向浏览器发送hello world
		$connection->send('hello world');
		
	};

	Worker::runAll();


?>