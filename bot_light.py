import os
import time
import requests
import json
import re
from text_processor import process_and_translate_sections

# Zero-dependency environment variables loader
def load_env():
    if os.path.exists(".env"):
        with open(".env", "r", encoding="utf-8") as f:
            for line in f:
                line = line.strip()
                if line and not line.startswith("#") and "=" in line:
                    key, val = line.split("=", 1)
                    os.environ[key.strip()] = val.strip()

load_env()

# Secure Configuration Loading
BOT_TOKEN = os.getenv("BOT_TOKEN", "YOUR_BOT_TOKEN_HERE")
ADMIN_ID = int(os.getenv("ADMIN_ID", 1741528704))
BASE_URL = f"https://api.telegram.org/bot{BOT_TOKEN}"
WEBAPP_URL = os.getenv("WEBAPP_URL", "https://doctoramed.uz/doctora/")

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
    text = msg.get("text") or msg.get("caption") or "[Fayl/Media]"
    user_id = from_user.get("id")

    user_tag = f"@{username}" if username else f"ID: {user_id}"

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
                f"ID: <code>{user_id}</code>"
            )
            send_message(ADMIN_ID, admin_alert)
        return

    # Handle Messages from Normal Users -> Forward to Admin
    if chat_id != ADMIN_ID:
        translated_text = ""
        # If message contains text processing sections, capture and translate them safely
        if "qisqacha mazmuni" in text.lower() or "asosiy content" in text.lower():
            try:
                translated_sections = process_and_translate_sections(text, "en")
                if translated_sections:
                    translated_text = f"\n\n🇬🇧 <b>Translation (English):</b>\n{translated_sections}"
            except Exception as ex:
                print("Error translating sections:", ex)

        admin_notification = (
            f"📩 <b>YANGI MUROJAAT / XABAR:</b>\n"
            f"👤 <b>Yuboruvchi:</b> {first_name}\n"
            f"📲 <b>Profil:</b> {user_tag}\n"
            f"🆔 <b>ID:</b> <code>{user_id}</code>\n\n"
            f"💬 <b>Xabar:</b>\n{text}"
            f"{translated_text}"
        )
        send_message(ADMIN_ID, admin_notification)
        send_message(chat_id, "✅ Xabaringiz adminga yetkazildi. Tez orada javob beramiz!")
    else:
        # If message is from Admin replying to a forwarded user message
        reply_to_message = msg.get("reply_to_message")
        if reply_to_message:
            target_text = reply_to_message.get("text") or reply_to_message.get("caption") or ""
            # Regex to find ID in the format: ID: <code>12345</code> or ID: 12345
            match = re.search(r'(?:ID:|🆔 <b>ID:</b>)\s*(?:<code>)?(\d+)(?:<\/code>)?', target_text)
            if match:
                try:
                    target_user_id = int(match.group(1))
                    send_message(target_user_id, f"🏥 <b>Doctor-A Klinikasi Adminidan javob:</b>\n\n{text}")
                    send_message(ADMIN_ID, "✅ Javobingiz foydalanuvchiga yetkazildi!")
                    return
                except Exception as e:
                    print("Error parsing target user ID in reply:", e)

        # Admin sent a message directly (not as a reply to a user message)
        send_message(ADMIN_ID, "ℹ️ Admin rejimi faol. Barcha foydalanuvchilar va WebApp murojaatlari shu yerga keladi.")

def main():
    print("[INFO] Doctor-A Telegram Bot (Light Weight) ishga tushdi!")
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
