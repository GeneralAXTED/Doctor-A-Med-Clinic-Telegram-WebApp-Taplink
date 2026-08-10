# 🏥 Doctor-A Med Clinic — Telegram WebApp & Taplink (Laravel Framework)

Ushbu loyiha **"Doctor-A" Medical Hospital** (Namangan) rasmiy Taplink hamda Telegram Mini WebApp sahifasi uchun Laravel Framework arxitekturasida yaratilgan.

---

## 📁 Loyiha Strukturasi (Laravel Directory Structure)

```
portfolio/
├── app/
│   └── Http/
│       └── Controllers/
│           ├── DoctoraController.php      # WebApp sahifasini ko'rsatuvchi controller
│           └── TelegramBotController.php  # Webhook & Telegram API xavfsiz handleri
├── public/
│   ├── assets/
│   │   └── logo.png                       # Doctor-A rasmiy mandala logotipi
│   ├── css/
│   │   └── style.css                      # Brend dizayni, qorong'u/yorug' mavzular
│   ├── js/
│   │   └── app.js                         # WebApp interaktiv mantig'i va formalar
│   └── .htaccess                          # Apache / cPanel uchun rewrites
├── resources/
│   └── views/
│       └── doctora.blade.php              # Asosiy Blade shablon fayli
├── routes/
│   ├── web.php                            # Veb havolalar (/ hamda /doctora)
│   └── api.php                            # Telegram Webhook & API Proxy havolalari
├── .env.example                           # Konfiguratsiya namunasi
└── composer.json                          # Laravel bog'liqliklari
```

---

## 🚀 Deployment (Serverga Joylash) Qo'llanmasi — `https://doctoramed.uz/`

### 1-USUL: Yangi Laravel Loyihasi Sifatida Serverga Joylash

1. **Fayllarni serverga yuklang**: Loyihadagi barcha fayllarni hostingizga yuklang.
2. **`.env` Faylini Sozlang**: Serverda `.env` faylini yaratib, quyidagi qatorlarni qo'shing:
   ```env
   APP_NAME="Doctor-A Med Clinic"
   APP_ENV=production
   APP_URL=https://doctoramed.uz

   TELEGRAM_BOT_TOKEN=8392684494:AAEZkBUTWBazQcQXWYyP61tmXsUJgzS6XHE
   TELEGRAM_ADMIN_ID=1741528704
   TELEGRAM_WEBAPP_URL=https://doctoramed.uz/doctora/
   ```
3. **Webhook-ni 1 marta bosing**: Brauzeringizda ushbu havolani oching:
   👉 **`https://doctoramed.uz/api/telegram/set-webhook`**

---

### 2-USUL: Mavjud Laravel Loyihangizga Qo'shish

Agar sizda allaqachon `doctoramed.uz` saytingiz Laravelda bo'lsa:

1. **Views**: `resources/views/doctora.blade.php` ni o'z loyihangiz `resources/views/` papkasiga nusxalang.
2. **Controllers**: `app/Http/Controllers/DoctoraController.php` va `TelegramBotController.php` fayllarini nusxalang.
3. **Public Assets**: `public/css/style.css`, `public/js/app.js` va `public/assets/logo.png` fayllarini public papkasiga joylang.
4. **Routes**: `routes/web.php` va `routes/api.php` dagi routelarni o'z fayllaringizga ulang.
5. **Webhook**: `https://doctoramed.uz/api/telegram/set-webhook` ni brauzerda 1 marta ochib faollashtiring!

---

## 🛡️ Xavfsizlik Natijalari

- ✅ **Zero Frontend Leaks**: Frontend JS ichida bitta ham Telegram bot tokeni yoki maxfiy kalitlar yo'q.
- ✅ **Laravel API Proxy**: Barcha formalar serverdagi `TelegramBotController` orqali xavfsiz ishlaydi.
- ✅ **CSRF Protection**: Formaga Laravel `csrf-token` himoyasi qo'shilgan.
