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
$inline = $data->callback_query->data;
$inmsgid = $data->callback_query->inline_message_id;

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

if($text == "/start") {
    bot('sendMessage', [
        'chat_id'=>$id,
        'text'=>"Добро пожаловать! Вы хотите отправить сообщение на канал? Подключенный канал: {$channelName}",
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
if($text && $text != "✍🏻Написать сообщение" && $text != "/start" && strpos($text, "/") == false) {
    if (strpos($text, 'fuck') !== false || strpos($text, 'whore') !== false) {
        bot('sendMessage', [
            'chat_id'=>$id,
            'text'=>'Watch your language!',
            'parse_mode'=>'html',
        ]);
    } else {
        bot('sendMessage', [
            'chat_id'=>$channelID,
            'text'=>json_encode($lastMID),
            'parse_mode'=>'html',
        ]);
    }
}
?>