<?php
/* ==========================================================================
   DOCTOR-A MED CLINIC - TELEGRAM BOT WEBHOOK REGISTRATION SCRIPT (PHP)
   Open this script once in your browser: https://doctoramed.uz/set_webhook.php
   ========================================================================== */

header('Content-Type: application/json; charset=utf-8');

define('BOT_TOKEN', '8827883515:AAFa8BGzDkLslpcU5OFdMzQi8xbGHqC8ozg');
define('WEBHOOK_URL', 'https://doctoramed.uz/bot.php');

$api_url = "https://api.telegram.org/bot" . BOT_TOKEN . "/setWebhook?url=" . urlencode(WEBHOOK_URL);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$res_data = json_decode($response, true);

echo json_encode([
    'title' => 'Doctor-A Telegram Bot Webhook Setup',
    'webhook_url' => WEBHOOK_URL,
    'telegram_response' => $res_data,
    'status' => (isset($res_data['ok']) && $res_data['ok']) ? 'SUCCESS' : 'FAILED',
    'message' => (isset($res_data['ok']) && $res_data['ok']) 
        ? 'Webhook muvaffaqiyatli o\'rnatildi! Endi botingiz https://doctoramed.uz/bot.php orqali ishlaydi.' 
        : 'Xatolik yuz berdi. Bot tokenini yoki HTTPS havolani tekshiring.'
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
