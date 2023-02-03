<?php

/* 
hey sir, would you mind make a sample madeline code to print all dialogs in the telegram account

including the chat title, peer type (channel, user, or channel) and the id of that peer

for ex :

array[dialogs]=

[1] chat title - peerUser - peerID,

[2] chat title - peerChannel - peerID,

...

[n] chat title - peerChat - peerID





thankyou

0 upvotes
Message

*/
include __DIR__ . '/vendor/autoload.php';

// if (!file_exists('madeline.php')) {
//     copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
// }
// require_once 'madeline.php';

use \danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\Connection;
use danog\MadelineProto\Settings\Logger;
use danog\MadelineProto\Stream\Proxy\SocksProxy;

const USER_SESSION = "myuser.madeline";

const BOT_SESSION = "mybot.madeline";

const MAX_DIALOGS = 10;

$connection = new Connection;
$connection->addProxy(
    SocksProxy::class,
    [
        'address'  => '127.0.0.1',
        'port'     =>  1090,
        'username' => '',
        'password' => ''
    ]
);

$connection->setTimeout(10.0);


$user = new API(__DIR__ . "/session/" . USER_SESSION);

$bot = new API(__DIR__ . "/session/" . BOT_SESSION);




$user->updateSettings($connection);
$bot->updateSettings($connection);


print("Welcome to the bot\n");


$user->loop(function() use ($user, $bot) {   
    yield $user->echo( "Starting user session " . USER_SESSION . PHP_EOL);

    $user->start();
    
    $admin = yield $user->getSelf();
    
    yield $user->echo("Starting bot session " . BOT_SESSION . PHP_EOL);
    $bot->start();     
    
        
    yield $bot->messages->sendMessage(peer: 'djnotes', message: "Started getting dialogs");
    $count = 0;
    foreach (yield $user->getFullDialogs() as $dialog){
        if ($dialog['_'] != 'dialog') continue; //Might also be dialogFolder
        if($count > MAX_DIALOGS) break; //Break out of loop after retrieving MAX_DIALOGS dialogs
        print_r($dialog);
        try{
            //TODO: Get chat title, peer type (peerChannel, peerUser), peer ID
        $info = yield $user->getInfo($dialog['peer']);
        $peerType = $dialog['peer']['_'];
        $topMessageId = $dialog['top_message'];

        switch ($dialog['peer']['_']){
            case 'peerUser': 
                $peerId = $dialog['peer']['user_id'];
                break;
            case 'peerChannel':
                $peerId = $dialog['peer']['channel_id'];
                break;
            case 'peerChat':
                $peerId = $dialog['peer']['chat_id'];
            default:
                $peerId = "";
        }
        // $messages = yield $user->channels->getMessages(channel: $peerId, id: $topMessageId);

        yield $bot->messages->sendMessage(peer: $admin, message: "$topMessageId - $peerType - $peerId");
        } catch(\Exception $e){
            yield $user->echo("{$e->getFile()}: {$e->getLine()}: {$e->getMessage()}");
        }

        $count++;
    }
    
});


