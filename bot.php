<?php

include __DIR__.'/vendor/autoload.php';
include __DIR__.'/todo.php';
include __DIR__.'/token.php';

$Todo = new Todo();

$discord = new \Discord\Discord([
	'token' => $token,
]);

$discord->on('ready', function ($discord) {
	echo "Bot is ready.", PHP_EOL;

	// Listen for events here
	$discord->on('message', function ($message) {
		if(mb_substr($message,0,7) === '//todo '){
			echo 'command received:' . $message;
			$commands = substr($message, 7, 256);
			$args = explode(" ", $commands);
			$command = $args[0];
			switch($command){
				case 'add':
				break;
				case 'all':
				break;
				case 'remove':
				break;
				case 'destory':
				break;
				case 'help':
				case 'h':
				break;
				default:
				break;
			}
		}
		// echo "Recieved a message from {$message->author->username}: {$message->content}", PHP_EOL;
	});
});

$discord->run();