<?php 
$api = '6666210193:AAFUeN_m6D4NfTwgpVko-yHJsXlPoVD2Oo0';
define('API_KEY', $api);
function bot($method, $datas =[]){
    $url ="https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    if (curl_error($ch)) {
    var_dump(curl_error($ch));
    } else {
    return json_decode($res);
    }
}

$data = json_decode(file_get_contents('php://input'));
$msg = $data->message;
$text = $msg->text;
$id = $msg->chat->id;
$mid = $msg->message_id;
function getChannelInfo($botToken, $channelName) {
    // Set the URL and parameters for the API request
    $url = "https://api.telegram.org/bot{$botToken}/getChat?chat_id={$channelName}";

    // Execute the API request and decode the JSON response
    $response = json_decode(file_get_contents($url), TRUE);

    // Return the channel info
    return $response;
}
$channelName = "@test1Otabek";
// Get the channel info using the function we defined
$channelInfo = getChannelInfo($api, $channelName);

// Extract the chat ID, title, and description from the channel info
$channelID = $channelInfo['result']['id'];
$channelTitle = $channelInfo['result']['title'];


if ($text == '/start') {
    bot('sendMessage', [
        'chat_id'=>$id,
        'text'=>"Добро пожаловать! Вы хотите отправить сообщение на канал?",
        'parse_mode'=>'html',
        'reply_markup'=>json_encode([
            'resize_keyboard'=>true,
            'keyboard'=>[
                [['text'=>"✍🏻Написать сообщение"]],
            ],
        ]),
    ]);
}


if($text == "✍🏻Написать сообщение") {
    bot('sendMessage', [
        'chat_id'=>$id,
        'text'=>"Напишите сообщение, содержащее не менее 5 слов",
        'parse_mode'=>'html',
        'reply_markup'=>json_encode([
            'resize_keyboard'=>true,
            'keyboard'=>[
                [['text'=>"↩Назад"]],
            ],
        ]),
    ]);
}
$words = file('words.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if($text and $text != "✍🏻Написать сообщение" and $text != "/start" and strpos($text, "/") == false) {
    foreach ($words as $word) {
        if (strpos($text, $word) !== false) {
            bot('sendMessage', [
                'chat_id'=>$id,
                'text'=>'Watch your language!',
                'parse_mode'=>'html',
            ]);
        } else {
            bot('sendMessage', [
                'chat_id'=>$channelID,
                'text'=>$text,
                'parse_mode'=>'html',
            ]);
        }
    }
}

if($text == "↩Назад") {
    bot('sendMessage', [
        'chat_id'=>$id,
        'text'=>'Чтобы оставить сообщение нажмите кнопку ✍🏻Написать сообщение',
        'parse_mode'=>'html',
        'reply_markup'=>json_encode([
            'resize_keyboard'=>true,
            'keyboard'=>[
                [['text'=>"✍🏻Написать сообщение"]],
            ],
        ]),
    ]);
}
?>