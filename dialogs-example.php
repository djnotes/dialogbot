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

const USER_SESSION = "myuser.madeline";

const BOT_SESSION = "mybot.madeline";



$user = new API(__DIR__ . "/session/" . USER_SESSION);
$bot = new API(__DIR__ . "/session/" . BOT_SESSION);




$user->loop(function() use ($user, $bot) {   
    yield $user->echo( "Starting user session " . USER_SESSION);

    $user->start();
    
    $admin = yield $user->getSelf();
    
    yield $user->echo("Starting bot session " . BOT_SESSION);
    $bot->start();     
    
        
    yield $bot->messages->sendMessage(peer: $admin, message: "Started getting dialogs");
    foreach (yield $user->getFullDialogs() as $dialog){
        print_r($dialog);
        yield $bot->messages->sendMedia(peer: $admin, message: print_r($dialog, true));
    }
});


