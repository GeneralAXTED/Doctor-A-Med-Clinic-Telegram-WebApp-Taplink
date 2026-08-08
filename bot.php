<?php
/* ==========================================================================
   DOCTOR-A MED CLINIC - TELEGRAM BOT WEBHOOK HANDLER (PHP)
   Token: 8827883515:AAFa8BGzDkLslpcU5OFdMzQi8xbGHqC8ozg
   Admin ID: 1741528704
   Site: https://doctoramed.uz/
   ========================================================================== */

header('Content-Type: application/json');

define('BOT_TOKEN', '8827883515:AAFa8BGzDkLslpcU5OFdMzQi8xbGHqC8ozg');
define('ADMIN_ID', 1741528704);
define('WEBAPP_URL', 'https://doctoramed.uz/');

// Read raw JSON update from Telegram Webhook
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
    echo json_encode(['status' => 'No update payload received']);
    exit;
}

// Telegram API Request Helper
function sendTelegramRequest($method, $data) {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

// Process incoming message
if (isset($update['message'])) {
    $message = $update['message'];
    $chat_id = $message['chat']['id'];
    $text = isset($message['text']) ? trim($message['text']) : '';
    $from = $message['from'];
    $first_name = isset($from['first_name']) ? htmlspecialchars($from['first_name']) : 'Foydalanuvchi';
    $last_name = isset($from['last_name']) ? htmlspecialchars($from['last_name']) : '';
    $username = isset($from['username']) ? '@' . $from['username'] : 'ID: ' . $from['id'];
    $user_id = $from['id'];

    // 1. Handle /start Command
    if ($text === '/start') {
        $welcome_text = "<b>Assalomu alaykum, " . $first_name . "!</b>\n\n"
            . "🏥 <b>\"Doctor-A\" Med Clinic</b> rasmiy Telegram botiga xush kelibsiz!\n\n"
            . "Klinikamizda 40 dan ortiq tajribali shifokorlar va zamonaviy texnologiyalar yordamida "
            . "tashxis qo'yish (MRT, MSKT, UZI, Rentgen) va murakkab jarrohlik amaliyotlarini amalga oshiramiz.\n\n"
            . "📍 <b>Manzil:</b> Namangan shahar, Boburshox ko'chasi, 2-uy.\n"
            . "📞 <b>Murojaat:</b> +998 (69) 226 00 00 / +998 (69) 226 88 88\n\n"
            . "Quyidagi tugma orqali <b>Taplink WebApp</b> interaktiv ilovamizni oching va online navbatga yoziling:";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🏥 Doctor-A WebApp-ni ochish', 'web_app' => ['url' => WEBAPP_URL]]
                ],
                [
                    ['text' => '💬 WhatsApp', 'url' => 'https://wa.me/998507841070'],
                    ['text' => '📍 Google Xarita', 'url' => 'https://maps.app.goo.gl/ELzSYWwwFr4Xc7pE6']
                ],
                [
                    ['text' => '📢 Telegram Kanal', 'url' => 'https://t.me/doctoramedclinic']
                ]
            ]
        ];

        sendTelegramRequest('sendMessage', [
            'chat_id' => $chat_id,
            'text' => $welcome_text,
            'parse_mode' => 'HTML',
            'reply_markup' => $keyboard
        ]);

        // Notify Admin about new user
        if ($user_id != ADMIN_ID) {
            $admin_alert = "👤 <b>Yangi foydalanuvchi botni boshladi:</b>\n"
                . "Ism: " . $first_name . " " . $last_name . "\n"
                . "Profil: " . $username . "\n"
                . "ID: <code>" . $user_id . "</code>";

            sendTelegramRequest('sendMessage', [
                'chat_id' => ADMIN_ID,
                'text' => $admin_alert,
                'parse_mode' => 'HTML'
            ]);
        }
        exit;
    }

    // 2. Handle Admin Reply to User
    if ($user_id == ADMIN_ID && isset($message['reply_to_message'])) {
        $reply_to = $message['reply_to_message'];
        $reply_text = isset($reply_to['text']) ? $reply_to['text'] : '';

        if (preg_match('/ID:\s*<code>(\d+)<\/code>/i', $reply_text, $matches)) {
            $target_id = $matches[1];
            sendTelegramRequest('sendMessage', [
                'chat_id' => $target_id,
                'text' => "🏥 <b>Doctor-A Klinikasi Adminidan javob:</b>\n\n" . htmlspecialchars($text),
                'parse_mode' => 'HTML'
            ]);

            sendTelegramRequest('sendMessage', [
                'chat_id' => ADMIN_ID,
                'text' => "✅ Javobingiz foydalanuvchiga yetkazildi!"
            ]);
            exit;
        }
    }

    // 3. Handle Direct Messages from Normal Users -> Forward to Admin ID 1741528704
    if ($user_id != ADMIN_ID) {
        $admin_msg = "📩 <b>YANGI MUROJAAT / XABAR:</b>\n"
            . "👤 <b>Yuboruvchi:</b> " . $first_name . " " . $last_name . "\n"
            . "📲 <b>Profil:</b> " . $username . "\n"
            . "🆔 <b>ID:</b> <code>" . $user_id . "</code>\n\n"
            . "💬 <b>Xabar:</b>\n" . htmlspecialchars($text);

        sendTelegramRequest('sendMessage', [
            'chat_id' => ADMIN_ID,
            'text' => $admin_msg,
            'parse_mode' => 'HTML'
        ]);

        sendTelegramRequest('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "✅ Xabaringiz adminga yetkazildi. Tez orada javob beramiz!"
        ]);
    }
}

echo json_encode(['status' => 'OK']);
