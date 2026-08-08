[Live view](https://generalaxted.github.io/Doctor-A-Med-Clinic-Telegram-WebApp-Taplink/)

# 🏥 Doctor-A Med Clinic — Telegram WebApp & Taplink

[O'zbekcha](#-ozbekcha) | [English](#-english)

---

## 🇺🇿 O'zbekcha

**Doctor-A Med Clinic** (Namangan) uchun mo'ljallangan zamonaviy, animatsiyali va interaktiv **Telegram WebApp / Taplink** ilovasi. 

Ushbu loyiha orqali foydalanuvchilar shifokorlar qabuliga yozilishlari, 60+ mutaxassisliklar va diagnostika turlarini qidirishlari hamda bevosita Telegram bot orqali adminga murojaat yuborishlari mumkin.

### 🌟 Asosiy Imkoniyatlar

- ⚡ **Telegram WebApp Integration**: Telegram Mini App ko'rinishida to'g'ridan-to'g'ri Telegram ichida ochiladi va foydalanuvchi profili avtomatik aniqlanadi.
- 📬 **Adminga Avtomatik Bildirishnoma**: Barcha navbatga yozilish va murojaatlar darhol **Admin Telegram ID (1741528704)** ga yetkaziladi.
- 👁️ **Oftalmologiya Yangi Bo'limi**: Maxsus vizual promo card va simptomlar bo'yicha tezkor yozilish.
- 🔍 **Jonli Qidiruv & Filtrlash**: 29+ shifokorlar, 17+ diagnostik tekshiruvlar (MRT, MSKT, UZI 4D, Holter EKG va b.) va 13+ jarrohlik turlari bo'yicha lahzalik izlash.
- 🎨 **Interaktiv Dizayn**: ECG (yurak puls) animatsiyasi, glassmorphism kartalari, kun/tun (dark/light) rejimi.
- 📍 **Yandex Maps Navigatsiya**: [Yandex Maps](https://yandex.uz/maps/-/CTGGnMIo) va o'rnatilgan interaktiv xarita.

---

### 📂 Loyiha Strukturasi

```
portfolio/
├── index.html               # Asosiy WebApp HTML5 strukturasi va Telegram SDK
├── style.css                # Brend qizil ranglar, animatsiyalar va glassmorphism
├── app.js                   # Qidiruv, modal va Telegram API bildirishnoma mantig'i
├── bot.php                  # Standart PHP Telegram Webhook handleri
├── set_webhook.php          # 1-bosqichli avtomatik Webhook o'rnatish skripti (PHP)
├── TelegramBotController.php# Laravel Framework uchun maxsus Controller
└── assets/                  # Doctor-A logotipi va fotorealistik tibbiy rasmlar
```

---

### 🚀 Laravel va Hostingga Joylash Qo'llanmasi (`https://doctoramed.uz/doctora/`)

#### 1-USUL: Laravel loyihangiz public papkasiga joylash (Eng oson)
1. WebApp loyihasidagi barcha fayllarni Laravel loyihangizning `public/doctora/` papkasi ichiga tashlang.
2. Brauzerda quyidagi havolani 1 marta oching:
   👉 **`https://doctoramed.uz/doctora/set_webhook.php`**
3. Tayyor! Webhook o'rnatiladi.

#### 2-USUL: Laravel Framework (Routes & Controller) orqali boshqarish
1. `TelegramBotController.php` faylini Laravel loyihangizning `app/Http/Controllers/` papkasiga nusxalang.
2. `routes/api.php` faylingizga ushbu routelarni qo'shing:
   ```php
   use App\Http\Controllers\TelegramBotController;

   Route::post('/telegram/webhook', [TelegramBotController::class, 'handleWebhook']);
   Route::get('/telegram/set-webhook', [TelegramBotController::class, 'setWebhook']);
   ```
3. Webhook-ni faollashtirish uchun brauzerda bosing:
   👉 **`https://doctoramed.uz/api/telegram/set-webhook`**

---

### 🚀 Tezkor Ishga Tushirish

#### 1. Mahalliy muhitda ko'rish:
```bash
python -m http.server 8080
```
Brauzerda oching: `http://localhost:8080`

#### 2. Telegram Bot-ni yurgazish:
```bash
python bot_light.py
```

#### 3. Telegram WebApp qilib ulash:
1. Loyihani [Vercel](https://vercel.com) yoki hostingga yuklab **HTTPS** havola oling (masalan: `https://doctor-a-med.vercel.app`).
2. Telegramda **[@BotFather](https://t.me/BotFather)** botiga kiring ➔ `/mybots` ➔ Botingizni tanlang.
3. **Bot Settings** ➔ **Menu Button** ➔ HTTPS havolani joylang va saqlang.

---
---

## 🇬🇧 English

Modern, interactive, and beautifully animated **Telegram WebApp & Taplink** application for **Doctor-A Med Clinic** (Namangan, Uzbekistan).

This application allows patients to book appointments online, search across 60+ medical specialties and diagnostic tests, and send direct messages to the clinic administration via Telegram.

### 🌟 Key Features

- ⚡ **Telegram WebApp Integration**: Seamlessly opens as a native Telegram Mini App with auto-detection of user profiles.
- 📬 **Instant Admin Notifications**: All booking requests and inquiries are routed directly to the **Admin Telegram ID (1741528704)** via Telegram Bot API.
- 👁️ **Ophthalmology Announcement**: Feature banner with symptom tags and one-tap booking.
- 🔍 **Live Search & Category Filters**: Search instantly across 29+ specialist doctors, 17+ diagnostic tests (MRI, CT, 4D Ultrasound, Holter ECG, etc.), and 13+ surgical procedures.
- 🎨 **Rich Visual Aesthetic**: Heartbeat ECG line animations, glassmorphic UI, dynamic dark/light theme switcher.
- 📍 **Yandex Maps Navigation**: Direct link to [Yandex Maps Location](https://yandex.uz/maps/-/CTGGnMIo) and embedded map preview.

---

### 📂 File Architecture

```
portfolio/
├── index.html         # Main WebApp HTML layout & Telegram WebApp SDK
├── style.css          # Brand design system, glassmorphism & CSS animations
├── app.js             # Dynamic search, modal handlers & Telegram Bot API client
├── bot_light.py       # Zero-dependency Python Telegram Bot polling server
├── bot.py             # Standard python-telegram-bot server script
└── assets/            # Generated high-resolution medical graphics
```

---

### 🚀 Quick Start Guide

#### 1. Run locally:
```bash
python -m http.server 8080
```
Open in browser: `http://localhost:8080`

#### 2. Run Telegram Bot server:
```bash
python bot_light.py
```

#### 3. Attach as Telegram Mini App:
1. Deploy the repository to [Vercel](https://vercel.com) or Netlify to get an **HTTPS** URL (e.g. `https://doctor-a-med.vercel.app`).
2. Go to **[@BotFather](https://t.me/BotFather)** in Telegram ➔ `/mybots` ➔ Select your bot (`8827883515:AAFa...`).
3. Click **Bot Settings** ➔ **Menu Button** ➔ Paste your HTTPS WebApp link.

---

### 📄 License & Contact

© 2026 **Doctor-A Med Clinic**. All rights reserved.  
📍 **Address:** Namangan, Boburshox street, 2. Landmark: "Jahon bozori".  
📞 **Contact:** +998 (69) 226 00 00 / +998 (69) 226 88 88  
🌐 **Website:** [doctoramedclinic.uz](https://doctoramedclinic.uz)
