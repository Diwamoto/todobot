<?php

include __DIR__.'/vendor/autoload.php';
include __DIR__.'/todo.php';
include __DIR__.'/util.php';

$discord = new \Discord\Discord([
	'token' => '',
]);

$discord->on('ready', function ($discord) {
	echo "Bot is ready.", PHP_EOL;

	// Listen for events here
	$discord->on('message', function ($message) {
		//$discord->channel->sendMessage('aaa');
		$Todo = new Todo();
		$content = $message->content;
		if(mb_substr($content,0,6) === '//todo'){
			$commands = substr($content, 7, 256);
			$args = explode(" ", $commands);
			$command = $args[0];
			array_shift($args);
			switch($command){
				case 'add':
					$data = $args[0];
					$option = [
						'save' => true,//ファイルに書き出すかどうか
						'user' => [
							'name' => $message->author->username//作成者
						]
					];
					if(!empty($args[1])){//担当
						$option['user']['assign'] = $args[1];
					}else{
						$option['user']['assign'] = $message->author->username;
					}
					$Todo->add($data,$option);
					$message->reply('todoを保存しました。');
					//メッセージ送信
				break;
				case 'list':
				case 'all':
				case 'find':
					if($command == 'all' || $command == 'list'){
						$todos = $Todo->find('all');
					}else{
						if(is_int($args[0])){
							$conditions = [
								'id' => $args[0],
							];
						}else{
							$conditions = [
								'assign' => $args[0],
							];
						}
						$todos = $Todo->find('all',[
							'conditions' => $conditions,
						]);
					}
					if(empty($todos)){
						$message->reply('todoが存在しません！');
						break;
					}
					$msg = '```';
					foreach($todos as $key => $todo){
						$msg = $msg . 'id:' . $key . '  ' .  $todo['assign'] . ':"' . $todo['data'] . '"  made by ' . $todo['name'] . PHP_EOL;
					}
					$msg = '```';
					if($command == 'all' || $command == 'list'){
						$message->reply(PHP_EOL . 'todo一覧' . PHP_EOL . $msg);
					}else{
						$message->reply(PHP_EOL . '検索結果' . PHP_EOL . $msg);
					}
				break;
				case 'ok':
				case 'remove':
					$Todo->remove($args[0],['save' => true]);
					if($command == 'ok'){
						$message->reply('いい仕事しましたね！');
					}else{
						$message->reply($args[0] . 'のtodoを削除しました。');
					}
				break;
				case 'destroy':
					$Todo->reset();
					$message->reply('todoを全削除しました。');
				break;
				default:
					if($config['default']){
						$message->reply(PHP_EOL .'コマンド一覧'.PHP_EOL.PHP_EOL.
					'//todo add {todo} ({user}):	(userの)todoを追加します。'.PHP_EOL.
					'//todo all:					todoを全取得します。'.PHP_EOL.
					'//todo find {id,name}:			todoを検索します。'.PHP_EOL.
					'//todo remove {id}:			todoを削除します。'.PHP_EOL.
					'//todo destory:				todoを全削除します。管理者権限が必要です。'.PHP_EOL.
					'//todo help:					ヘルプです。'.PHP_EOL);
					}else{
						$message->reply('コマンドが見つかりません。//todo helpで全てのコマンドを取得できます。');
					}
				break;
				case 'help':
				case 'h':
					$message->reply(PHP_EOL .'コマンド一覧'.PHP_EOL.PHP_EOL.
					'//todo add {todo} ({name}):	(userの)todoを追加します。'.PHP_EOL.
					'//todo all:					todoを全取得します。'.PHP_EOL.
					'//todo find {id,name}:			todoを検索します。'.PHP_EOL.
					'//todo remove {id}:			todoを削除します。'.PHP_EOL.
					'//todo destory:				todoを全削除します。管理者権限が必要です。'.PHP_EOL.
					'//todo help:					ヘルプです。'.PHP_EOL);
				break;
			}
		}
		// echo "Recieved a message from {$message->author->username}: {$message->content}", PHP_EOL;
	});
});

$discord->run();