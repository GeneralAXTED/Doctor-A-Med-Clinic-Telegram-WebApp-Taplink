/* ==========================================================================
   DOCTOR-A MED CLINIC - TAPLINK & TELEGRAM WEBAPP LOGIC
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {

  // Telegram Config
  const BOT_TOKEN = '8392684494:AAEZkBUTWBazQcQXWYyP61tmXsUJgzS6XHE';
  const ADMIN_ID = '1741528704';

  // 1. Initialize Telegram WebApp SDK if running inside Telegram
  const tg = window.Telegram?.WebApp;
  let tgUser = null;

  if (tg) {
    try {
      tg.ready();
      tg.expand();
      if (tg.initDataUnsafe?.user) {
        tgUser = tg.initDataUnsafe.user;
        const userNameEl = document.getElementById('tgUserName');
        const userWelcomeEl = document.getElementById('tgUserWelcome');
        if (userNameEl && userWelcomeEl) {
          userNameEl.textContent = `${tgUser.first_name || ''} ${tgUser.last_name || ''}`.trim() || (tgUser.username ? `@${tgUser.username}` : 'Foydalanuvchi');
          userWelcomeEl.style.display = 'inline-block';
        }
        // Auto fill form inputs if available
        const patientNameEl = document.getElementById('patientName');
        const msgSenderNameEl = document.getElementById('msgSenderName');
        const defaultName = `${tgUser.first_name || ''} ${tgUser.last_name || ''}`.trim();
        if (patientNameEl && defaultName) patientNameEl.value = defaultName;
        if (msgSenderNameEl && defaultName) msgSenderNameEl.value = defaultName;
      }
    } catch (e) {
      console.log('Telegram WebApp SDK init:', e);
    }
  }

  // Helper function to send instant HTML message to Admin Telegram ID
  async function sendToAdminTelegram(htmlText) {
    const endpoint = `https://api.telegram.org/bot${BOT_TOKEN}/sendMessage`;
    try {
      const response = await fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          chat_id: ADMIN_ID,
          text: htmlText,
          parse_mode: 'HTML'
        })
      });
      return await response.json();
    } catch (error) {
      console.error('Failed to send Telegram message to Admin:', error);
      return null;
    }
  }

  // 2. Data Store for Medical Services
  const medicalData = [
    // Mutaxassislar (Specialists)
    { name: 'Oftalmolog', category: 'specialist', icon: 'fa-eye', badge: 'Yangi Bo\'lim' },
    { name: 'Allergolog', category: 'specialist', icon: 'fa-hand-dots', badge: 'Mutaxassis' },
    { name: 'Androlog', category: 'specialist', icon: 'fa-mars', badge: 'Erkaklar salomatligi' },
    { name: 'Angioxirurg', category: 'specialist', icon: 'fa-heart-pulse', badge: 'Qon tomirlari' },
    { name: 'Dermatolog', category: 'specialist', icon: 'fa-allergies', badge: 'Teri kasalliklari' },
    { name: 'Endokrinolog', category: 'specialist', icon: 'fa-dna', badge: 'Gormonal tizim' },
    { name: 'Endourolog', category: 'specialist', icon: 'fa-user-nurse', badge: 'Endo-Urologiya' },
    { name: 'Endoskopist', category: 'specialist', icon: 'fa-stethoscope', badge: 'Diagnostik vrach' },
    { name: 'Flebolog', category: 'specialist', icon: 'fa-wave-square', badge: 'Vena qon tomir' },
    { name: 'Gastroenterolog', category: 'specialist', icon: 'fa-stomach', badge: 'Oshqozon-ichak' },
    { name: 'Ginekolog', category: 'specialist', icon: 'fa-venus', badge: 'Ayollar salomatligi' },
    { name: 'Kardiolog', category: 'specialist', icon: 'fa-heart-circle-bolt', badge: 'Yurak kasalliklari' },
    { name: 'LOR (Otorinolaringolog)', category: 'specialist', icon: 'fa-ear-listen', badge: 'Quloq, burun, tomoq' },
    { name: 'Nefrolog', category: 'specialist', icon: 'fa-hospital-user', badge: 'Buyrak salomatligi' },
    { name: 'Nevrolog', category: 'specialist', icon: 'fa-brain', badge: 'Asab tizimi' },
    { name: 'Neyroxirurg', category: 'specialist', icon: 'fa-head-side-virus', badge: 'Bosh va orqa miya' },
    { name: 'Ortoped', category: 'specialist', icon: 'fa-bone', badge: 'Suyak va bo\'g\'im' },
    { name: 'Pulmonolog', category: 'specialist', icon: 'fa-lungs', badge: 'Nafas yo\'llari' },
    { name: 'Psixolog', category: 'specialist', icon: 'fa-comments', badge: 'Ruhiy salomatlik' },
    { name: 'Plastik jarroh', category: 'specialist', icon: 'fa-user-doctor', badge: 'Estetik va plastika' },
    { name: 'Radiolog', category: 'specialist', icon: 'fa-x-ray', badge: 'Nurlar diagnostikasi' },
    { name: 'Reanimatolog', category: 'specialist', icon: 'fa-truck-medical', badge: 'Intensiv terapiya' },
    { name: 'Rentgenolog', category: 'specialist', icon: 'fa-radiation', badge: 'Rentgen diagnostika' },
    { name: 'Revmatolog', category: 'specialist', icon: 'fa-person-dots-from-line', badge: 'Bo\'g\'im kasalliklari' },
    { name: 'Stomatolog', category: 'specialist', icon: 'fa-tooth', badge: 'Tish davolash' },
    { name: 'Sport travmatologi', category: 'specialist', icon: 'fa-person-running', badge: 'Sport jarohatlari' },
    { name: 'Terapevt', category: 'specialist', icon: 'fa-user-nurse', badge: 'Umumiy amaliyot' },
    { name: 'Travmatolog', category: 'specialist', icon: 'fa-crutch', badge: 'Jarohat va shikast' },
    { name: 'Urolog', category: 'specialist', icon: 'fa-notes-medical', badge: 'Peshob yo\'llari' },
    { name: 'UZIST', category: 'specialist', icon: 'fa-microscope', badge: 'Ultratovush diagnost' },

    // Diagnostika (Diagnostics)
    { name: 'MRT', category: 'diagnostic', icon: 'fa-circle-dot', badge: 'Magnit-Rezonans' },
    { name: 'MSKT', category: 'diagnostic', icon: 'fa-compact-disc', badge: 'Kompyuter Tomografiya' },
    { name: 'RENTGEN', category: 'diagnostic', icon: 'fa-x-ray', badge: 'Raqamli Rentgen' },
    { name: 'KOLONOSKOPIYA', category: 'diagnostic', icon: 'fa-eye', badge: 'Ichak diagnostikasi' },
    { name: 'Flurografiya', category: 'diagnostic', icon: 'fa-lungs', badge: 'O\'pka tekshiruvi' },
    { name: 'Salpingografiya', category: 'diagnostic', icon: 'fa-notes-medical', badge: 'Ginekologik' },
    { name: 'Ekskretor Urografiya', category: 'diagnostic', icon: 'fa-vial-virus', badge: 'Urologik' },
    { name: 'Kolposkopiya', category: 'diagnostic', icon: 'fa-microscope', badge: 'Ginekologik apparat' },
    { name: 'UZI (Ultratovush)', category: 'diagnostic', icon: 'fa-wave-square', badge: '4D UZI' },
    { name: 'Doppler', category: 'diagnostic', icon: 'fa-heart-circle-check', badge: 'Qon tomir Doppleri' },
    { name: 'EXO (Ekokardiyografiya)', category: 'diagnostic', icon: 'fa-heart-pulse', badge: 'Yurak UZI' },
    { name: 'Holter EKG', category: 'diagnostic', icon: 'fa-clock-rotate-left', badge: '24 Soatlik EKG' },
    { name: 'EEG (Elektroensefalografiya)', category: 'diagnostic', icon: 'fa-brain', badge: 'Asab EKG' },
    { name: 'FGDS (Gastroendoskopiya)', category: 'diagnostic', icon: 'fa-disease', badge: 'Oshqozon tekshiruvi' },
    { name: 'PRP Cortexil', category: 'diagnostic', icon: 'fa-syringe', badge: 'Plazmoterapiya' },
    { name: 'LOR kombayn', category: 'diagnostic', icon: 'fa-headset', badge: 'Germaniya apparati' },
    { name: 'Excimer Laser', category: 'diagnostic', icon: 'fa-bolt', badge: 'Lazer apparati' },

    // Jarrohlik (Surgery)
    { name: 'Umumiy jarrohlik', category: 'surgery', icon: 'fa-scalpel', badge: 'Jarrahlik amaliyoti' },
    { name: 'Endokrinologik jarrohlik', category: 'surgery', icon: 'fa-dna', badge: 'Qalqonsimon bez' },
    { name: 'Plastik jarrohlik', category: 'surgery', icon: 'fa-face-smile', badge: 'Estetik operatsiyalar' },
    { name: 'Neyrologik jarrohlik', category: 'surgery', icon: 'fa-brain', badge: 'Bosh miya va umurtqa' },
    { name: 'Urologik jarrohlik', category: 'surgery', icon: 'fa-hospital', badge: 'Peshob ayirish' },
    { name: 'Ginekologik jarrohlik', category: 'surgery', icon: 'fa-venus', badge: 'Kichik chanoq' },
    { name: 'LOR jarrohlik', category: 'surgery', icon: 'fa-ear-listen', badge: 'Burun va tomoq' },
    { name: 'Travmatologik jarrohlik', category: 'surgery', icon: 'fa-bone', badge: 'Suyak va bo\'g\'im' },
    { name: 'Rinoplastika', category: 'surgery', icon: 'fa-person-dots-from-line', badge: 'Burun plastikasi' },
    { name: 'Endoskopik jarrohlik', category: 'surgery', icon: 'fa-magnifying-glass-plus', badge: 'Kichik kesimli' },
    { name: 'Videolaparoskopik jarrohlik', category: 'surgery', icon: 'fa-video', badge: 'Laparoskopiya' },
    { name: 'Angioxirurgik jarrohlik', category: 'surgery', icon: 'fa-heart-pulse', badge: 'Qon tomir' },
    { name: 'Abdominal jarrohlik', category: 'surgery', icon: 'fa-stomach', badge: 'Qorin bo\'shlig\'i' }
  ];

  // DOM Elements Selection
  const itemsGrid = document.getElementById('itemsGrid');
  const searchInput = document.getElementById('searchInput');
  const filterTabs = document.querySelectorAll('.tab-btn');
  const totalItemsCount = document.getElementById('totalItemsCount');
  const themeToggleBtn = document.getElementById('themeToggleBtn');
  const themeIcon = document.getElementById('themeIcon');
  
  // Modals
  const callModal = document.getElementById('callModal');
  const bookingModal = document.getElementById('bookingModal');
  const directMsgModal = document.getElementById('directMsgModal');
  
  const openCallModalBtn = document.getElementById('openCallModalBtn');
  const stickyCallBtn = document.getElementById('stickyCallBtn');
  const stickyMsgBtn = document.getElementById('stickyMsgBtn');
  const openDirectMsgModalBtn = document.getElementById('openDirectMsgModalBtn');
  
  const closeCallModal = document.getElementById('closeCallModal');
  const closeBookingModal = document.getElementById('closeBookingModal');
  const closeDirectMsgModal = document.getElementById('closeDirectMsgModal');
  
  const bookEyeDoctorBtn = document.getElementById('bookEyeDoctorBtn');
  const openFormFromCallModal = document.getElementById('openFormFromCallModal');
  const bookingForm = document.getElementById('bookingForm');
  const directMsgForm = document.getElementById('directMsgForm');
  const serviceSelect = document.getElementById('serviceSelect');

  let currentCategory = 'all';
  let currentSearchQuery = '';

  // 3. Render Medical Items Safely
  function renderItems() {
    if (!itemsGrid) return;
    itemsGrid.innerHTML = '';

    const filtered = medicalData.filter(item => {
      const matchesCategory = (currentCategory === 'all') || (item.category === currentCategory);
      const matchesSearch = item.name.toLowerCase().includes(currentSearchQuery.toLowerCase()) ||
                            item.badge.toLowerCase().includes(currentSearchQuery.toLowerCase());
      return matchesCategory && matchesSearch;
    });

    if (totalItemsCount) {
      totalItemsCount.textContent = `${filtered.length} ta natija`;
    }

    if (filtered.length === 0) {
      itemsGrid.innerHTML = `
        <div style="grid-column: span 2; text-align: center; padding: 24px; color: var(--text-muted);">
          <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 8px;"></i>
          <p>Hech narsa topilmadi</p>
        </div>
      `;
      return;
    }

    filtered.forEach(item => {
      const card = document.createElement('div');
      card.className = 'item-card';
      card.innerHTML = `
        <div class="item-icon-box">
          <i class="fa-solid ${item.icon}"></i>
        </div>
        <div class="item-name">${item.name}</div>
        <div class="item-badge">${item.badge}</div>
      `;

      card.addEventListener('click', () => {
        openBookingModalWithService(item.name);
      });

      itemsGrid.appendChild(card);
    });
  }

  // 4. Search & Filter Handlers (Safely Guarded)
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      currentSearchQuery = e.target.value;
      renderItems();
    });
  }

  if (filterTabs && filterTabs.length > 0) {
    filterTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        filterTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        currentCategory = tab.dataset.filter;
        renderItems();
      });
    });
  }

  // 5. Modal Handlers (Safely Guarded)
  function openModal(modal) {
    if (modal) modal.classList.add('active');
  }

  function closeModal(modal) {
    if (modal) modal.classList.remove('active');
  }

  if (openCallModalBtn) openCallModalBtn.addEventListener('click', () => openModal(callModal));
  if (stickyCallBtn) stickyCallBtn.addEventListener('click', () => openModal(callModal));
  if (stickyMsgBtn) stickyMsgBtn.addEventListener('click', () => openModal(directMsgModal));
  if (openDirectMsgModalBtn) openDirectMsgModalBtn.addEventListener('click', () => openModal(directMsgModal));

  if (closeCallModal) closeCallModal.addEventListener('click', () => closeModal(callModal));
  if (closeBookingModal) closeBookingModal.addEventListener('click', () => closeModal(bookingModal));
  if (closeDirectMsgModal) closeDirectMsgModal.addEventListener('click', () => closeModal(directMsgModal));

  if (bookEyeDoctorBtn) {
    bookEyeDoctorBtn.addEventListener('click', () => {
      openBookingModalWithService('Oftalmolog');
    });
  }

  if (openFormFromCallModal) {
    openFormFromCallModal.addEventListener('click', () => {
      closeModal(callModal);
      openModal(bookingModal);
    });
  }

  function openBookingModalWithService(serviceName) {
    if (serviceSelect) {
      let found = false;
      for (let i = 0; i < serviceSelect.options.length; i++) {
        if (serviceSelect.options[i].value.toLowerCase().includes(serviceName.toLowerCase())) {
          serviceSelect.selectedIndex = i;
          found = true;
          break;
        }
      }
      if (!found) {
        const newOption = new Option(serviceName, serviceName, true, true);
        serviceSelect.add(newOption);
      }
    }
    openModal(bookingModal);
  }

  window.addEventListener('click', (e) => {
    if (callModal && e.target === callModal) closeModal(callModal);
    if (bookingModal && e.target === bookingModal) closeModal(bookingModal);
    if (directMsgModal && e.target === directMsgModal) closeModal(directMsgModal);
  });

  // 6. Form Submission 1: Qabulga Yozilish (Booking Form)
  if (bookingForm) {
    bookingForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      const name = document.getElementById('patientName')?.value || 'Noma\'lum';
      const phone = document.getElementById('patientPhone')?.value || 'Noma\'lum';
      const service = serviceSelect?.value || 'Tanlanmadi';
      const note = document.getElementById('patientNote')?.value || 'Mavjud emas';

      let tgUserTag = 'Mavjud emas';
      if (tgUser) {
        tgUserTag = tgUser.username ? `@${tgUser.username} (ID: ${tgUser.id})` : `ID: ${tgUser.id}`;
      }

      const htmlText = `
🚨 <b>YANGI QABULGA YOZILISH ARIZASI</b> 🚨

🏥 <b>Klinika:</b> Doctor-A Med Clinic
👤 <b>Bemor:</b> ${name}
📞 <b>Telefon:</b> <code>${phone}</code>
🩺 <b>Kerakli bo'lim:</b> <b>${service}</b>
📝 <b>Qo'shimcha izoh:</b> ${note}
📲 <b>Telegram Profil:</b> ${tgUserTag}
⏰ <b>Vaqt:</b> ${new Date().toLocaleString('uz-UZ')}
      `.trim();

      const submitBtn = document.getElementById('submitBookingBtn');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Yuborilmoqda...';
      }

      // Direct Telegram API Notification to Admin ID 1741528704
      await sendToAdminTelegram(htmlText);

      if (tg) {
        try {
          tg.sendData(JSON.stringify({ type: 'booking', name, phone, service, note }));
        } catch (err) {
          console.log('tg.sendData:', err);
        }
      }

      alert(`Rahmat, ${name}! Arizangiz qabul qilindi va adminga yetkazildi. Operatorimiz tez orada siz bilan bog'lanadi.`);

      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Adminga yuborish';
      }

      closeModal(bookingModal);
      bookingForm.reset();
    });
  }

  // 7. Form Submission 2: Direct Message to Admin (Inquiry Form)
  if (directMsgForm) {
    directMsgForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      const name = document.getElementById('msgSenderName')?.value || 'Noma\'lum';
      const phone = document.getElementById('msgSenderPhone')?.value || 'Noma\'lum';
      const content = document.getElementById('msgContent')?.value || 'Mavjud emas';

      let tgUserTag = 'Mavjud emas';
      if (tgUser) {
        tgUserTag = tgUser.username ? `@${tgUser.username} (ID: ${tgUser.id})` : `ID: ${tgUser.id}`;
      }

      const htmlText = `
💬 <b>YANGI BEVOSITA MUROJAAT / XABAR</b> 💬

🏥 <b>Klinika:</b> Doctor-A Med Clinic
👤 <b>Yuboruvchi:</b> ${name}
📞 <b>Telefon:</b> <code>${phone}</code>
📲 <b>Telegram Profil:</b> ${tgUserTag}
✉️ <b>Xabar mazmuni:</b>
<i>"${content}"</i>

⏰ <b>Vaqt:</b> ${new Date().toLocaleString('uz-UZ')}
      `.trim();

      await sendToAdminTelegram(htmlText);

      alert(`Xabaringiz adminga muvaffaqiyatli yuborildi! Tez orada javob beramiz.`);

      closeModal(directMsgModal);
      directMsgForm.reset();
    });
  }

  // 8. Dark / Light Theme Toggle
  if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', () => {
      const currentTheme = document.documentElement.getAttribute('data-theme');
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
      
      document.documentElement.setAttribute('data-theme', newTheme);
      if (themeIcon) {
        themeIcon.className = newTheme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
      }
    });
  }

  // Initial render
  renderItems();
});
