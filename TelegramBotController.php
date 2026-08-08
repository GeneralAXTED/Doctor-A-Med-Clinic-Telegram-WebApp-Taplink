<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramBotController extends Controller
{
    private $botToken = '8392684494:AAEZkBUTWBazQcQXWYyP61tmXsUJgzS6XHE';
    private $adminId = 1741528704;
    private $webAppUrl = 'https://doctoramed.uz/doctora/';

    /**
     * Webhook Handler Route
     * Endpoint: POST /api/telegram/webhook
     */
    public function handleWebhook(Request $request)
    {
        $update = $request->all();

        if (!isset($update['message'])) {
            return response()->json(['status' => 'No message payload']);
        }

        $message = $update['message'];
        $chatId = $message['chat']['id'];
        $text = trim($message['text'] ?? '');
        $from = $message['from'];
        $firstName = htmlspecialchars($from['first_name'] ?? 'Foydalanuvchi');
        $lastName = htmlspecialchars($from['last_name'] ?? '');
        $username = isset($from['username']) ? '@' . $from['username'] : 'ID: ' . $from['id'];
        $userId = $from['id'];

        // 1. Handle /start Command
        if ($text === '/start') {
            $welcomeText = "<b>Assalomu alaykum, {$firstName}!</b>\n\n"
                . "🏥 <b>\"Doctor-A\" Med Clinic</b> rasmiy Telegram botiga xush kelibsiz!\n\n"
                . "Klinikamizda 40 dan ortiq tajribali shifokorlar va zamonaviy MRT, MSKT, UZI, Rentgen xizmatlari mavjud.\n\n"
                . "📍 <b>Manzil:</b> Namangan shahar, Boburshox ko'chasi 2-uy.\n"
                . "📞 <b>Murojaat:</b> +998 (69) 226 00 00 / +998 (69) 226 88 88\n\n"
                . "Quyidagi tugma orqali WebApp ilovamizni oching va online navbatga yoziling:";

            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => '🏥 Doctor-A WebApp-ni ochish', 'web_app' => ['url' => $this->webAppUrl]]
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

            $this->sendMessage($chatId, $welcomeText, $keyboard);
            return response()->json(['status' => 'OK']);
        }

        // 2. Handle Admin Reply to User
        if ($userId == $this->adminId && isset($message['reply_to_message'])) {
            $replyTo = $message['reply_to_message'];
            $replyText = $replyTo['text'] ?? '';

            if (preg_match('/ID:\s*<code>(\d+)<\/code>/i', $replyText, $matches)) {
                $targetId = $matches[1];
                $this->sendMessage($targetId, "🏥 <b>Doctor-A Klinikasi Adminidan javob:</b>\n\n" . htmlspecialchars($text));
                $this->sendMessage($this->adminId, "✅ Javobingiz foydalanuvchiga yetkazildi!");
                return response()->json(['status' => 'OK']);
            }
        }

        // 3. Direct Messages from Normal Users -> Forward to Admin ID 1741528704
        if ($userId != $this->adminId) {
            $adminMsg = "📩 <b>YANGI MUROJAAT / XABAR:</b>\n"
                . "👤 <b>Yuboruvchi:</b> {$firstName} {$lastName}\n"
                . "📲 <b>Profil:</b> {$username}\n"
                . "🆔 <b>ID:</b> <code>{$userId}</code>\n\n"
                . "💬 <b>Xabar:</b>\n" . htmlspecialchars($text);

            $this->sendMessage($this->adminId, $adminMsg);
            $this->sendMessage($chatId, "✅ Xabaringiz adminga yetkazildi. Tez orada javob beramiz!");
        }

        return response()->json(['status' => 'OK']);
    }

    /**
     * One-Click Webhook Setup Route
     * Endpoint: GET /api/telegram/set-webhook
     */
    public function setWebhook()
    {
        $webhookUrl = url('/api/telegram/webhook');
        $response = Http::get("https://api.telegram.org/bot{$this->botToken}/setWebhook", [
            'url' => $webhookUrl
        ]);

        return response()->json([
            'status' => 'Webhook Setup Result',
            'webhook_url' => $webhookUrl,
            'telegram_response' => $response->json()
        ]);
    }

    private function sendMessage($chatId, $text, $keyboard = null)
    {
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ];

        if ($keyboard) {
            $payload['reply_markup'] = $keyboard;
        }

        return Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", $payload);
    }
}
