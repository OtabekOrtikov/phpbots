<?php
$api = '6666210193:AAFUeN_m6D4NfTwgpVko-yHJsXlPoVD2Oo0';
define('API_KEY', $api);

function bot($method, $datas = []) {
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
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

if ($text == '/start') {
    bot('sendMessage', [
        'chat_id' => $id,
        'text' => "Welcome! Do you want to send a message to the channel?",
        'parse_mode' => 'html',
        'reply_markup' => json_encode([
            'resize_keyboard' => true,
            'keyboard' => [
                [['text' => "✍🏻Write a message"]],
            ],
        ]),
    ]);
}

if ($text == "✍🏻Write a message") {
    bot('sendMessage', [
        'chat_id' => $id,
        'text' => "Write a message containing at least 5 words",
        'parse_mode' => 'html',
        'reply_markup' => json_encode([
            'resize_keyboard' => true,
            'keyboard' => [
                [['text' => "↩Back"]],
            ],
        ]),
    ]);
}

if ($text && $text != "✍🏻Write a message" && $text != "/start" && strpos($text, "/") === false) {
    $words = file('words.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $foundWord = false;
    foreach ($words as $word) {
        if (strpos($text, $word) !== false) {
            bot('sendMessage', [
                'chat_id' => $id,
                'text' => 'Watch your language!',
                'parse_mode' => 'html',
            ]);
            $foundWord = true;
            break;
        }
    }
    if (!$foundWord) {
        bot('sendMessage', [
            'chat_id' => $channelID,
            'text' => $text,
            'parse_mode' => 'html',
        ]);
    }
}

if ($text == "↩Back") {
    bot('sendMessage', [
        'chat_id' => $id,
        'text' => 'To leave a message, press the button ✍🏻Write a message',
        'parse_mode' => 'html',
        'reply_markup' => json_encode([
            'resize_keyboard' => true,
            'keyboard' => [
                [['text' => "✍🏻Write a message"]],
            ],
        ]),
    ]);
}
?>
