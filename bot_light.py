import time
import requests
import json

# ==========================================================================
# DOCTOR-A MED CLINIC - TELEGRAM BOT (ZERO DEPENDENCY / PURE PYTHON REQUESTS)
# Token: 8827883515:AAFa8BGzDkLslpcU5OFdMzQi8xbGHqC8ozg
# Admin ID: 1741528704
# ==========================================================================

BOT_TOKEN = "8827883515:AAFa8BGzDkLslpcU5OFdMzQi8xbGHqC8ozg"
ADMIN_ID = 1741528704
BASE_URL = f"https://api.telegram.org/bot{BOT_TOKEN}"

# Change this to your live HTTPS link or Ngrok/Vercel URL
WEBAPP_URL = "https://doctoramedclinic.uz" 

def send_message(chat_id, text, reply_markup=None):
    url = f"{BASE_URL}/sendMessage"
    payload = {
        "chat_id": chat_id,
        "text": text,
        "parse_mode": "HTML"
    }
    if reply_markup:
        payload["reply_markup"] = json.dumps(reply_markup)
    try:
        res = requests.post(url, json=payload, timeout=10)
        return res.json()
    except Exception as e:
        print("Error sending message:", e)
        return None

def handle_update(update):
    if "message" not in update:
        return

    msg = update["message"]
    chat_id = msg["chat"]["id"]
    from_user = msg.get("from", {})
    first_name = from_user.get("first_name", "Foydalanuvchi")
    username = from_user.get("username", "")
    text = msg.get("text", "")

    user_tag = f"@{username}" if username else f"ID: {from_user.get('id')}"

    # Handle /start Command
    if text.startswith("/start"):
        welcome_text = (
            f"<b>Assalomu alaykum, {first_name}!</b>\n\n"
            f"🏥 <b>\"Doctor-A\" Med Clinic</b> rasmiy Telegram botiga xush kelibsiz!\n\n"
            f"Klinikamizda 40 dan ortiq tajribali shifokorlar va zamonaviy texnologiyalar yordamida "
            f"tashxis qo'yish (MRT, MSKT, UZI, Rentgen) va murakkab jarrohlik amaliyotlarini amalga oshiramiz.\n\n"
            f"📍 <b>Manzil:</b> Namangan shahar, Boburshox ko'chasi, 2-uy.\n"
            f"📞 <b>Murojaat:</b> +998 (69) 226 00 00 / +998 (69) 226 88 88\n\n"
            f"Quyidagi tugma orqali <b>Taplink WebApp</b> interaktiv ilovamizni oching va online navbatga yoziling:"
        )

        keyboard = {
            "inline_keyboard": [
                [{"text": "🏥 Doctor-A WebApp-ni ochish", "web_app": {"url": WEBAPP_URL}}],
                [
                    {"text": "💬 WhatsApp", "url": "https://wa.me/998507841070"},
                    {"text": "📍 Google Xarita", "url": "https://maps.app.goo.gl/ELzSYWwwFr4Xc7pE6"}
                ],
                [
                    {"text": "📢 Telegram Kanal", "url": "https://t.me/doctoramedclinic"}
                ]
            ]
        }

        send_message(chat_id, welcome_text, reply_markup=keyboard)

        # Notify Admin
        if chat_id != ADMIN_ID:
            admin_alert = (
                f"👤 <b>Yangi foydalanuvchi botni boshladi:</b>\n"
                f"Ism: {first_name}\n"
                f"Profil: {user_tag}\n"
                f"ID: <code>{from_user.get('id')}</code>"
            )
            send_message(ADMIN_ID, admin_alert)
        return

    # Handle Messages from Normal Users -> Forward to Admin
    if chat_id != ADMIN_ID:
        admin_notification = (
            f"📩 <b>YANGI MUROJAAT / XABAR:</b>\n"
            f"👤 <b>Yuboruvchi:</b> {first_name}\n"
            f"📲 <b>Profil:</b> {user_tag}\n"
            f"🆔 <b>ID:</b> <code>{from_user.get('id')}</code>\n\n"
            f"💬 <b>Xabar:</b>\n{text}"
        )
        send_message(ADMIN_ID, admin_notification)
        send_message(chat_id, "✅ Xabaringiz adminga yetkazildi. Tez orada javob beramiz!")
    else:
        # Admin sent a message directly
        send_message(ADMIN_ID, "ℹ️ Admin rejimi faol. Barcha foydalanuvchilar va WebApp murojaatlari shu yerga keladi.")

def main():
    print("[INFO] Doctor-A Telegram Bot ishga tushdi!")
    print(f"Token: {BOT_TOKEN[:12]}...")
    print(f"Admin ID: {ADMIN_ID}")

    offset = 0
    while True:
        try:
            url = f"{BASE_URL}/getUpdates?offset={offset}&timeout=20"
            res = requests.get(url, timeout=25).json()

            if res.get("ok"):
                for update in res.get("result", []):
                    offset = update["update_id"] + 1
                    handle_update(update)
        except Exception as e:
            print("Polling error:", e)
            time.sleep(3)

if __name__ == "__main__":
    main()
