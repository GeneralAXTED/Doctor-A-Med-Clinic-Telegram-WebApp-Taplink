<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// Safely require the TextProcessor if the file exists at root
if (file_exists(base_path('text_processor.php'))) {
    require_once base_path('text_processor.php');
}

class TelegramBotController extends Controller
{
    private function getBotToken()
    {
        return config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN', 'YOUR_BOT_TOKEN_HERE'));
    }

    private function getAdminId()
    {
        return config('services.telegram.admin_id', env('TELEGRAM_ADMIN_ID', 1741528704));
    }

    private function getWebAppUrl()
    {
        return config('services.telegram.webapp_url', env('TELEGRAM_WEBAPP_URL', 'https://doctoramed.uz/doctora/'));
    }

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
        $text = trim($message['text'] ?? $message['caption'] ?? '');
        $from = $message['from'] ?? [];
        $firstName = htmlspecialchars($from['first_name'] ?? 'Foydalanuvchi');
        $lastName = htmlspecialchars($from['last_name'] ?? '');
        $username = isset($from['username']) ? '@' . $from['username'] : 'ID: ' . ($from['id'] ?? 'unknown');
        $userId = $from['id'] ?? null;

        if (!$userId) {
            return response()->json(['status' => 'No user ID']);
        }

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
                        ['text' => '🏥 Doctor-A WebApp-ni ochish', 'web_app' => ['url' => $this->getWebAppUrl()]]
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
        if ($userId == $this->getAdminId() && isset($message['reply_to_message'])) {
            $replyTo = $message['reply_to_message'];
            $replyText = $replyTo['text'] ?? $replyTo['caption'] ?? '';

            if (preg_match('/ID:\s*<code>(\d+)<\/code>/i', $replyText, $matches)) {
                $targetId = $matches[1];
                $this->sendMessage($targetId, "🏥 <b>Doctor-A Klinikasi Adminidan javob:</b>\n\n" . htmlspecialchars($text));
                $this->sendMessage($this->getAdminId(), "✅ Javobingiz foydalanuvchiga yetkazildi!");
                return response()->json(['status' => 'OK']);
            }
        }

        // 3. Direct Messages from Normal Users -> Forward to Admin ID
        if ($userId != $this->getAdminId()) {
            $translatedText = "";
            // Detect and translate text processing sections
            if (stripos($text, 'qisqacha mazmuni') !== false || stripos($text, 'asosiy content') !== false) {
                if (class_exists('TextProcessor')) {
                    try {
                        $translatedSections = \TextProcessor::processAndTranslateSections($text, 'en');
                        if (!empty($translatedSections)) {
                            $translatedText = "\n\n🇬🇧 <b>Translation (English):</b>\n" . $translatedSections;
                        }
                    } catch (\Exception $ex) {
                        // Safe ignore
                    }
                }
            }

            $adminMsg = "📩 <b>YANGI MUROJAAT / XABAR:</b>\n"
                . "👤 <b>Yuboruvchi:</b> {$firstName} {$lastName}\n"
                . "📲 <b>Profil:</b> {$username}\n"
                . "🆔 <b>ID:</b> <code>{$userId}</code>\n\n"
                . "💬 <b>Xabar:</b>\n" . htmlspecialchars($text)
                . $translatedText;

            $this->sendMessage($this->getAdminId(), $adminMsg);
            $this->sendMessage($chatId, "✅ Xabaringiz adminga yetkazildi. Tez orada javob beramiz!");
        }

        return response()->json(['status' => 'OK']);
    }

    /**
     * Secure Proxy Endpoint for Frontend WebApp Form Submissions
     * Endpoint: POST /api/telegram/send-booking
     */
    public function sendBookingNotification(Request $request)
    {
        $name = htmlspecialchars($request->input('name', 'Noma\'lum'));
        $phone = htmlspecialchars($request->input('phone', 'Noma\'lum'));
        $service = htmlspecialchars($request->input('service', 'Tanlanmadi'));
        $note = htmlspecialchars($request->input('note', 'Mavjud emas'));
        $tgUser = htmlspecialchars($request->input('tg_user', 'Mavjud emas'));

        $htmlText = "🚨 <b>YANGI QABULGA YOZILISH ARIZASI (WebApp)</b> 🚨\n\n"
            . "🏥 <b>Klinika:</b> Doctor-A Med Clinic\n"
            . "👤 <b>Bemor:</b> {$name}\n"
            . "📞 <b>Telefon:</b> <code>{$phone}</code>\n"
            . "🩺 <b>Kerakli bo'lim:</b> <b>{$service}</b>\n"
            . "📝 <b>Qo'shimcha izoh:</b> {$note}\n"
            . "📲 <b>Telegram Profil:</b> {$tgUser}\n"
            . "⏰ <b>Vaqt:</b> " . now()->format('Y-m-d H:i:s');

        $response = $this->sendMessage($this->getAdminId(), $htmlText);

        return response()->json([
            'status' => 'success',
            'message' => 'Ariza adminga muvaffaqiyatli yuborildi'
        ]);
    }

    /**
     * One-Click Webhook Setup Route
     * Endpoint: GET /api/telegram/set-webhook
     */
    public function setWebhook()
    {
        $webhookUrl = url('/api/telegram/webhook');
        $response = Http::get("https://api.telegram.org/bot{$this->getBotToken()}/setWebhook", [
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

        return Http::post("https://api.telegram.org/bot{$this->getBotToken()}/sendMessage", $payload);
    }
}
