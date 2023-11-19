<?php
$api = '6666210193:AAFUeN_m6D4NfTwgpVko-yHJsXlPoVD2Oo0';
define('API_KEY', $api);
$adminID = '1013297198';

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
$channelName = "@test1Otabek";
$channelInfo = json_decode(file_get_contents("https://api.telegram.org/bot" . API_KEY . "/getChat?chat_id={$channelName}"), TRUE);
$channelID = $channelInfo['result']['id'];
$channelTitle = $channelInfo['result']['title'];
$channelUser = $channelInfo['result']['username'];
$isMember = false;
$userFirstName = $msg->chat->first_name;
$userName = $msg->chat->username;

$bot_api_url = 'https://api.telegram.org/bot' . API_KEY . '/';
$bot_api_method = 'getChatMember';

// Prepare the request URL
$request_url = $bot_api_url . $bot_api_method . '?chat_id=' . $channelID . '&user_id=' . $id;

// Send the request to the Telegram Bot API
$response = file_get_contents($request_url);

// Decode the response JSON
$dataMem = json_decode($response, true);

// Check if the user is a member of the channel
if ($dataMem['ok'] && ($dataMem['result']['status'] == 'member' || $dataMem['result']['status'] == 'creator' || $dataMem['result']['status'] == 'administrator')) {
    $isMember = true;
    
    
} else {
    $isMember = false;
}

if ($text == '/start') {
    if ($isMember) {
        bot('sendMessage', [
            'chat_id' => $id,
            'text' => "👋🏻Добро пожаловать!👋🏻\n Вы хотите отправить сообщение на канал? Нажмите кнопку \"✍🏻Написать сообщение\"",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'resize_keyboard' => true,
                'keyboard' => [
                    [['text' => "✍🏻Написать сообщение"]],
                ],
            ]),
        ]);
        $words = file('words.txt', FILE_IGNORE_NEW_LINES);
        $capwords = array();

        foreach ($words as $word) {
            $capwords[] = ucwords($word);
        }

        file_put_contents('capwords.txt', implode("\n", $capwords));
        
    } else {
        bot('sendMessage', [
            'chat_id' => $id,
            'text' => '⛔️Чтобы продолжить, вам должны подписаться на наш канал⛔️',
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => '🗂Подписываться', 'url' => 'https://t.me/uwedconfession']
                    ]
                ],
            ]),
        ]);
    }
}

if ($text == "✍🏻Написать сообщение" || $text == "✅Подтвердить") {
    if ($isMember) {
        bot('sendMessage', [
            'chat_id' => $id,
            'text' => "✍🏻Пожалуйста, напишите сообщение!✍🏻",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'resize_keyboard' => true,
                'keyboard' => [
                    [['text' => "↩Назад"]],
                ],
            ]),
        ]);
        
    } else {
        bot('sendMessage', [
            'chat_id' => $id,
            'text' => '⛔️Чтобы продолжить, вам должны подписаться на наш канал⛔️',
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => '🗂Подписываться', 'url' => 'https://t.me/uwedconfession']
                    ]
                ],
            ])
        ]);
    }
}
if ($text && $text != "✍🏻Написать сообщение" && $text != "/start" && strpos($text, "/") == false && $text != "↩Назад" && $text != "✅Подтвердить") {
    if ($isMember) {
        $words = file('words.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $foundWord = false;
        foreach ($words as $word) {
            if (strpos($text, $word) !== false || strpos($text, "@") !== false || strpos($text, "t.me") !== false || strpos($text, "http") !== false) {
                bot('sendMessage', [
                    'chat_id' => $id,
                    'text' => '❌Вы использовали запрещенное слово или включили ссылку!❌',
                    'parse_mode' => 'html',
                ]);
                $foundWord = true;
                break;
            }
        }
        if (!$foundWord) {
            $count = (int)file_get_contents('count.txt');
            file_put_contents('count.txt', $count + 1);
            bot('sendMessage', [
                'chat_id' => $channelID,
                'text' => "#{$count} 💭\n\n{$text}",
                'parse_mode' => 'markdown',
            ]);
            bot('sendMessage', [
                'chat_id' => $adminID,
                'text' => "💭Новое сообщение на канале:\n№{$count}\n🤵🏻Отправитель: @{$userName}\n🔗Username: {$userName}\n💭Текст: {$text}",
                'parse_mode' => 'markdown',
            ]);
            bot('sendMessage', [
                'chat_id' => $id,
                'text' => "✅Ваше сообщение было отправлено на адрес {$channelName}✅\n\n💭Ваш текст: {$text}",
                'parse_mode' => 'markdown',
                'reply_markup' => json_encode([
                    'resize_keyboard' => true,
                    'keyboard' => [
                        [['text' => "✍🏻Написать сообщение"]],
                    ],
                ]),
            ]);
        }  
    } else {
        bot('sendMessage', [
            'chat_id' => $id,
            'text' => '⛔️Чтобы продолжить, вам должны подписаться на наш канал⛔️',
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => '🗂Подписываться', 'url' => 'https://t.me/uwedconfession']
                    ]
                ],
            ])
        ]);
    }
}



if ($text == "↩Назад") {
    if ($isMember) {
        bot('sendMessage', [
            'chat_id' => $id,
            'text' => '📂Чтобы оставить сообщение, нажмите кнопку "✍🏻Написать сообщение"',
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'resize_keyboard' => true,
                'keyboard' => [
                    [['text' => "✍🏻Написать сообщение"]],
                ],
            ]),
        ]);
    } else {
        bot('sendMessage', [
            'chat_id' => $id,
            'text' => '⛔️Чтобы продолжить, вам должны подписаться на наш канал⛔️',
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => '🗂Подписываться', 'url' => 'https://t.me/uwedconfession']
                    ]
                ],
            ])
        ]);
    }
}
?>
