import logging
import sys
import requests
from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup, WebAppInfo
from telegram.ext import ApplicationBuilder, CommandHandler, MessageHandler, filters, ContextTypes

# ==========================================================================
# TELEGRAM BOT FOR DOCTOR-A MED CLINIC
# Token: 8827883515:AAFa8BGzDkLslpcU5OFdMzQi8xbGHqC8ozg
# Admin ID: 1741528704
# ==========================================================================

BOT_TOKEN = "8827883515:AAFa8BGzDkLslpcU5OFdMzQi8xbGHqC8ozg"
ADMIN_ID = 1741528704

# WebApp URL (Replace with your actual HTTPS deployment URL e.g. https://doctoramedclinic.uz or Vercel URL)
WEBAPP_URL = "https://doctoramedclinic.uz" 

logging.basicConfig(
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    level=logging.INFO
)

async def start_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    welcome_text = (
        f"<b>Assalomu alaykum, {user.first_name}!</b>\n\n"
        f"🏥 <b>\"Doctor-A\" Med Clinic</b> rasmiy Telegram botiga xush kelibsiz!\n\n"
        f"Klinikamizda 40 dan ortiq tajribali shifokorlar va zamonaviy texnologiyalar yordamida "
        f"tashxis qo'yish (MRT, MSKT, UZI, Rentgen) va murakkab jarrohlik amaliyotlarini amalga oshiramiz.\n\n"
        f"📍 <b>Manzil:</b> Namangan shahar, Boburshox ko'chasi, 2-uy.\n"
        f"📞 <b>Murojaat:</b> +998 (69) 226 00 00 / +998 (69) 226 88 88\n\n"
        f"Quyidagi tugma orqali <b>Taplink WebApp</b> interaktiv ilovamizni oching va online navbatga yoziling:"
    )

    keyboard = [
        [InlineKeyboardButton(text="🏥 Doctor-A WebApp-ni ochish", web_app=WebAppInfo(url=WEBAPP_URL))],
        [InlineKeyboardButton(text="📍 Yandex Xarita", url="https://yandex.uz/maps/-/CTGGnMIo"),
         InlineKeyboardButton(text="📢 Rasmiy Kanal", url="https://t.me/doctoramedclinic")]
    ]
    reply_markup = InlineKeyboardMarkup(keyboard)

    await update.message.reply_text(text=welcome_text, reply_markup=reply_markup, parse_mode='HTML')

    # Notify Admin about new user start
    if user.id != ADMIN_ID:
        admin_alert = (
            f"👤 <b>Yangi foydalanuvchi botni boshladi:</b>\n"
            f"Ism: {user.first_name} {user.last_name or ''}\n"
            f"Username: @{user.username or 'Mavjud emas'}\n"
            f"ID: <code>{user.id}</code>"
        )
        try:
            await context.bot.send_message(chat_id=ADMIN_ID, text=admin_alert, parse_mode='HTML')
        except Exception as e:
            logging.error(f"Error sending admin notification: {e}")

async def handle_messages(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    text = update.message.text or "[Fayl/Media]"

    # If message is from Admin replying to a forwarded user message
    if user.id == ADMIN_ID and update.message.reply_to_message:
        reply_to = update.message.reply_to_message
        # Extract user ID from original text if formatted
        try:
            target_text = reply_to.text or reply_to.caption or ""
            if "ID: " in target_text:
                target_user_id = int(target_text.split("ID: ")[1].split("\n")[0].replace("<code>","").replace("</code>","").strip())
                await context.bot.send_message(
                    chat_id=target_user_id,
                    text=f"🏥 <b>Doctor-A Klinikasi Adminidan javob:</b>\n\n{text}",
                    parse_mode='HTML'
                )
                await update.message.reply_text("✅ Javobingiz foydalanuvchiga yetkazildi!")
                return
        except Exception as e:
            logging.error(f"Error sending reply to user: {e}")

    # Forward message from normal users directly to Admin ID 1741528704
    if user.id != ADMIN_ID:
        admin_msg = (
            f"📩 <b>YANGI MUROJAAT / XABAR:</b>\n"
            f"👤 <b>Yuboruvchi:</b> {user.first_name} {user.last_name or ''}\n"
            f"📲 <b>Username:</b> @{user.username or 'yoq'}\n"
            f"🆔 <b>ID:</b> <code>{user.id}</code>\n\n"
            f"💬 <b>Xabar:</b>\n{text}"
        )
        await context.bot.send_message(chat_id=ADMIN_ID, text=admin_msg, parse_mode='HTML')
        await update.message.reply_text("✅ Xabaringiz adminga yetkazildi. Tez orada javob beramiz!")

def main():
    print("Doctor-A Telegram Bot starting...")
    app = ApplicationBuilder().token(BOT_TOKEN).build()

    app.add_handler(CommandHandler("start", start_command))
    app.add_handler(MessageHandler(filters.ALL & ~filters.COMMAND, handle_messages))

    print(f"Bot successfully running for token {BOT_TOKEN[:10]}... Admin ID: {ADMIN_ID}")
    app.run_polling()

if __name__ == '__main__':
    main()
