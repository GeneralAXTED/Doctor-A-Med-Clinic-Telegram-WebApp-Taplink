<!DOCTYPE html>
<html lang="uz" data-theme="light">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Doctor-A Med Clinic | Namangan - Rasmiy Telegram WebApp</title>
  <meta name="description"
    content="Doctor-A Med Clinic Namangan. Telegram WebApp & Taplink. 40+ tajribali shifokorlar, 20+ bo'limlar, zamonaviy MRT, MSKT, UZI. Online navbatga yoziling!">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Telegram WebApp SDK -->
  <script src="https://telegram.org/js/telegram-web-app.js"></script>

  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

  <div class="app-container">

    <!-- Top ECG Pulse Header Bar -->
    <div class="ecg-header-bar">
      <div class="top-bar-controls">
        <div class="status-badge" id="statusBadge">
          <span class="status-dot"></span>
          <span id="statusText">Hozir Ochiq · 08:00 - 20:00</span>
        </div>
        <div class="mrt-service-pill">
          <i class="fa-solid fa-microscope"></i> MRT · MSKT · RENTGEN 24/7
        </div>
        <button class="theme-toggle-btn" id="themeToggleBtn" title="Mavzuni o'zgartirish">
          <i class="fa-solid fa-moon" id="themeIcon"></i>
        </button>
      </div>

      <!-- Animated Heartbeat Line -->
      <svg class="ecg-line-svg" viewBox="0 0 500 20" preserveAspectRatio="none">
        <path class="ecg-path"
          d="M 0 10 L 150 10 L 160 2 L 170 18 L 180 0 L 190 20 L 200 10 L 350 10 L 360 2 L 370 18 L 380 0 L 390 20 L 400 10 L 500 10"
          fill="none" stroke-width="2" />
      </svg>
    </div>

    <!-- Hero Profile Section -->
    <header class="profile-hero">
      <div class="brand-logo-box">
        <img src="{{ asset('assets/logo.png') }}" alt="Doctor-A Med Clinic Logo" class="brand-logo-img">
      </div>

      <h1 class="brand-title">Doctor<span>-A</span></h1>

      <div class="mrt-banner-badge">
        <i class="fa-solid fa-bolt" style="color: var(--primary-red);"></i>
        <span>MRT · MSKT · RENTGEN 24/7</span>
      </div>

      <!-- Telegram User Welcome Strip (if inside WebApp) -->
      <div id="tgUserWelcome"
        style="display: none; font-size: 0.82rem; font-weight: 600; color: var(--primary-red); background: var(--primary-red-light); padding: 4px 12px; border-radius: 12px; margin-top: 4px;">
        👋 Xush kelibsiz, <span id="tgUserName">Foydalanuvchi</span>!
      </div>
    </header>

    <!-- Main Taplink Action Buttons -->
    <div class="action-buttons-list">

      <!-- Primary Booking / Call CTA -->
      <button class="tap-button tap-button-featured" id="openCallModalBtn">
        <div class="btn-icon-wrapper">
          <i class="fa-solid fa-phone-volume"></i>
        </div>
        <div class="btn-text-group">
          <span class="btn-title">TELEFON RAQAMLAR</span>
        </div>
        <i class="fa-solid fa-chevron-right arrow-icon"></i>
      </button>

      <!-- WhatsApp Direct Chat Link -->
      <a href="https://wa.me/998507841070" target="_blank" class="tap-button">
        <div class="btn-icon-wrapper" style="background: rgba(37, 211, 102, 0.12); color: #25D366;">
          <i class="fa-brands fa-whatsapp"></i>
        </div>
        <div class="btn-text-group">
          <span class="btn-title">WHATSAPP</span>
        </div>
        <i class="fa-solid fa-arrow-up-right-from-square arrow-icon"></i>
      </a>

      <!-- Telegram Channel -->
      <a href="https://t.me/doctoramedclinic" target="_blank" class="tap-button">
        <div class="btn-icon-wrapper" style="background: rgba(0, 136, 204, 0.12); color: #0088cc;">
          <i class="fa-paper-plane fa-solid"></i>
        </div>
        <div class="btn-text-group">
          <span class="btn-title">TELEGRAM</span>
        </div>
        <i class="fa-solid fa-arrow-up-right-from-square arrow-icon"></i>
      </a>

      <!-- Instagram Profile -->
      <a href="https://www.instagram.com/doctoramedclinic/" target="_blank" class="tap-button">
        <div class="btn-icon-wrapper" style="background: rgba(225, 48, 108, 0.12); color: #e1306c;">
          <i class="fa-brands fa-instagram"></i>
        </div>
        <div class="btn-text-group">
          <span class="btn-title">INSTAGRAM</span>
        </div>
        <i class="fa-solid fa-arrow-up-right-from-square arrow-icon"></i>
      </a>

      <!-- Navigation & Maps (Google Maps) -->
      <a href="https://maps.app.goo.gl/ELzSYWwwFr4Xc7pE6" target="_blank" class="tap-button">
        <div class="btn-icon-wrapper" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
          <i class="fa-solid fa-location-dot"></i>
        </div>
        <div class="btn-text-group">
          <span class="btn-title">LOKATSIYA</span>
        </div>
        <i class="fa-solid fa-diamond-turn-right arrow-icon"></i>
      </a>

      <!-- Official Website -->
      <a href="https://doctoramed.uz/" target="_blank" class="tap-button">
        <div class="btn-icon-wrapper" style="background: rgba(230, 25, 25, 0.12); color: var(--primary-red);">
          <i class="fa-solid fa-globe"></i>
        </div>
        <div class="btn-text-group">
          <span class="btn-title">WEB-SAHIFA</span>
        </div>
        <i class="fa-solid fa-arrow-up-right-from-square arrow-icon"></i>
      </a>

    </div>

    <!-- Social Links Row Footer -->
    <div class="social-grid">
      <a href="https://t.me/doctoramedclinic" target="_blank" class="social-link telegram">
        <i class="fa-brands fa-telegram social-icon"></i>
        <span class="social-name">Telegram</span>
      </a>
      <a href="https://www.instagram.com/doctoramedclinic/" target="_blank" class="social-link instagram">
        <i class="fa-brands fa-instagram social-icon"></i>
        <span class="social-name">Instagram</span>
      </a>
      <a href="https://wa.me/998507841070" target="_blank" class="social-link whatsapp">
        <i class="fa-brands fa-whatsapp social-icon"></i>
        <span class="social-name">WhatsApp</span>
      </a>
      <a href="https://www.facebook.com/people/Doctor-A-Clinic/61592680256763/" target="_blank"
        class="social-link facebook">
        <i class="fa-brands fa-facebook-f social-icon"></i>
        <span class="social-name">Facebook</span>
      </a>
      <a href="https://doctoramed.uz/" target="_blank" class="social-link youtube">
        <i class="fa-solid fa-globe social-icon"></i>
        <span class="social-name">Website</span>
      </a>
    </div>

    <!-- Footer Copyright -->
    <footer class="app-footer">
      <p>© {{ date('Y') }} "Doctor-A" Med Clinic. Barcha huquqlar himoyalangan.</p>
      <p>Namangan shahar, Boburshox ko'chasi, 2-uy.</p>
    </footer>

  </div>

  <!-- Sticky Bottom Contact Dock -->
  <div class="sticky-contact-bar">
    <button class="sticky-btn sticky-btn-call" id="stickyCallBtn">
      <i class="fa-solid fa-phone"></i> Qo'ng'iroq
    </button>
    <button class="sticky-btn sticky-btn-tg" id="stickyMsgBtn">
      <i class="fa-solid fa-paper-plane"></i> Adminga Xabar
    </button>
  </div>

  <!-- Modal 1: Select Phone Number -->
  <div class="modal-overlay" id="callModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Raqamni tanlang</h3>
        <button class="modal-close-btn" id="closeCallModal">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <p style="font-size: 0.85rem; color: var(--text-muted);">Doctor-A klinikasi operatorlari bilan bog'lanish:</p>
      <div class="modal-phone-options">
        <a href="tel:+998692260000" class="phone-choice-btn">
          <span>📞 +998 (69) 226 00 00</span>
          <i class="fa-solid fa-phone-flip" style="color: var(--primary-red);"></i>
        </a>
        <a href="tel:+998692268888" class="phone-choice-btn">
          <span>📞 +998 (69) 226 88 88</span>
          <i class="fa-solid fa-phone-flip" style="color: var(--primary-red);"></i>
        </a>
        <a href="https://wa.me/998507841070" target="_blank" class="phone-choice-btn" style="border-color: #25D366; color: #25D366;">
          <span>💬 WhatsApp: +998 50 784 10 70</span>
          <i class="fa-brands fa-whatsapp"></i>
        </a>
        <button class="submit-booking-btn" style="background: var(--medical-blue); margin-top: 6px;"
          id="openFormFromCallModal">
          <i class="fa-solid fa-calendar-plus"></i> Online Yozilish Formasi
        </button>
      </div>
    </div>
  </div>

  <!-- Modal 2: Online Appointment Form -->
  <div class="modal-overlay" id="bookingModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Qabulga Yozilish</h3>
        <button class="modal-close-btn" id="closeBookingModal">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <form class="booking-form" id="bookingForm">
        <div class="form-group">
          <label class="form-label">Ismingiz va Familiyangiz</label>
          <input type="text" class="form-input" id="patientName" placeholder="Masalan: Alisher Vohidov" required>
        </div>
        <div class="form-group">
          <label class="form-label">Telefon Raqamingiz</label>
          <input type="tel" class="form-input" id="patientPhone" placeholder="+998 90 123 45 67" required>
        </div>
        <div class="form-group">
          <label class="form-label">Kerakli Bo'lim / Shifokor</label>
          <select class="form-select" id="serviceSelect">
            <option value="Oftalmolog">👁️ Oftalmolog (Ko'z shifokori)</option>
            <option value="Kardiolog">❤️ Kardiolog</option>
            <option value="Neyroxirurg">🧠 Neyroxirurg</option>
            <option value="Ginekolog">👩 Ginekolog</option>
            <option value="Urolog">🩺 Urolog</option>
            <option value="MRT / MSKT">🔬 MRT - MSKT Diagnostika</option>
            <option value="Boshqa bo'lim">Boshqa mutaxassis</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Qo'shimcha izoh yoki savol (Ixtiyoriy)</label>
          <input type="text" class="form-input" id="patientNote" placeholder="Sizni nima bezovta qilyapti?">
        </div>
        <button type="submit" class="submit-booking-btn" id="submitBookingBtn">
          <i class="fa-solid fa-paper-plane"></i> Adminga yuborish
        </button>
      </form>
    </div>
  </div>

  <!-- Modal 3: Direct Message / Inquiry Form -->
  <div class="modal-overlay" id="directMsgModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Adminga Murojaat Yuborish</h3>
        <button class="modal-close-btn" id="closeDirectMsgModal">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <form class="booking-form" id="directMsgForm">
        <div class="form-group">
          <label class="form-label">Ismingiz</label>
          <input type="text" class="form-input" id="msgSenderName" placeholder="Ismingizni kiriting" required>
        </div>
        <div class="form-group">
          <label class="form-label">Telefon Raqamingiz</label>
          <input type="tel" class="form-input" id="msgSenderPhone" placeholder="+998 90 123 45 67" required>
        </div>
        <div class="form-group">
          <label class="form-label">Murojaatingiz / Xabaringiz</label>
          <textarea class="form-input" id="msgContent" rows="4" placeholder="Savolingiz yoki taklifingizni yozing..."
            style="resize: vertical;" required></textarea>
        </div>
        <button type="submit" class="submit-booking-btn" style="background: var(--primary-red);">
          <i class="fa-solid fa-paper-plane"></i> Xabarni Adminga Yuborish
        </button>
      </form>
    </div>
  </div>

  <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
