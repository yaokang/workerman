<?php
	use Workerman\Worker;

	use Workerman\lib\Timer;

	require_once './Autoloader.php';

	// 注意：这里与上个例子不通，使用的是websocket协议
	$ws_worker = new Worker("websocket://0.0.0.0:2000");

	// 启动4个进程对外提供服务
	$ws_worker->count = 4;

	$ws_worker->name = 'yaokang';

	$ws_worker->onConnect = function($connection){

		$connection->id = '用户'.$connection->id;

		foreach($connection->worker->connections as $yonghu){

			$yonghu->send($connection->id.'进入房间');

		}

	};

	//Workerman启动是触发
	// $ws_worker->onWorkerStart = function($ws_worker){

	// 	echo 'connection success'."\r\n";

	// 	//10秒想服务器的客户端发送一次消息
	// 	Timer::add(10,function()use($ws_worker){

	// 		foreach($ws_worker->connections as $connection){

	// 			$connection->send(time());

	// 			$connection->send($connection->getRemoteIp());

	// 		}

	// 	});

	// };

	// 当收到客户端发来的数据后返回hello $data给客户端
	$ws_worker->onMessage = function($connection, $data){

		if($data == 'shabi'){

			$connection->destroy();

			return;

		}

		foreach($connection->worker->connections as $yonghu){

			$yonghu->send($connection->id.":".$data);

		}


	};

	//客户端断开连接是触发
	$ws_worker->onClose = function($connection){

		foreach($connection->worker->connections as $yonghu){

			$yonghu->send($connection->id.'离开房间');

		}

	};

	//当客户端的连接上发生错误时触发
	$ws_worker->onError = function($connection,$code,$msg){

		echo "error: $code $msg";

	};


	//给所用用户发送消息
	function fasongall($msg){

		global $ws_worker;

		foreach($ws_worker->uidConnections as $connection){

			$connection->send($msg);

		}

	}


	// 运行worker
	Worker::runAll();

?>

