(() => {
  'use strict';

  const header = document.querySelector('.index-page .header, .garden-home .header');
  if (header) {
    // Keep the brand in its normal header position; only switch the header surface on scroll.
    const updateHeader = () => header.classList.toggle('is-scrolled', window.scrollY > 28);
    updateHeader();
    window.addEventListener('scroll', updateHeader, { passive: true });
  }

  // Hero slider: advances every 3 seconds, pauses while the visitor is interacting.
  const slider = document.querySelector('.promo-slider');
  const slides = slider ? [...slider.querySelectorAll('.slide-radio')] : [];
  // The third hero slide uses a darker image treatment, so switch the transparent
  // header navigation to white only while that slide is active.
  const updateHeaderForActiveSlide = () => {
    if (!header || !slider) return;
    const activeSlide = slider.querySelector('.slide-radio:checked');
    header.classList.toggle('is-third-slide', activeSlide?.id === 'promo-2');
  };
  updateHeaderForActiveSlide();
  if (slider && slides.length > 1) {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let timer = null;
    let paused = false;
    const activeIndex = () => Math.max(0, slides.findIndex((radio) => radio.checked));
    const stopTimer = () => {
      if (timer !== null) window.clearInterval(timer);
      timer = null;
    };
    const startTimer = () => {
      stopTimer();
      if (paused || document.hidden || reduceMotion.matches) return;
      timer = window.setInterval(() => {
        const next = (activeIndex() + 1) % slides.length;
        slides[next].checked = true;
        slides[next].dispatchEvent(new Event('change', { bubbles: true }));
      }, 3000);
    };
    slider.addEventListener('mouseenter', () => { paused = true; stopTimer(); });
    slider.addEventListener('mouseleave', () => { paused = false; startTimer(); });
    slider.addEventListener('focusin', () => { paused = true; stopTimer(); });
    slider.addEventListener('focusout', (event) => {
      if (!slider.contains(event.relatedTarget)) { paused = false; startTimer(); }
    });
    slides.forEach((radio) => radio.addEventListener('change', () => {
      updateHeaderForActiveSlide();
      startTimer();
    }));
    document.addEventListener('visibilitychange', startTimer);
    reduceMotion.addEventListener?.('change', startTimer);
    startTimer();
  }

  // Password strength cloud: only appears once the user starts typing.
  const password = document.querySelector('[data-password-strength]');
  const rulesPanel = document.querySelector('[data-password-rules]');
  const meter = document.querySelector('[data-password-meter]');
  const ruleElements = rulesPanel ? [...rulesPanel.querySelectorAll('[data-rule]')] : [];
  const form = password?.form;

  const evaluatePassword = (value) => ({
    length: [...value].length >= 8,
    letter: /\p{L}/u.test(value),
    number: /\p{N}/u.test(value),
    symbol: /[^\p{L}\p{N}]/u.test(value),
  });

  const updatePassword = () => {
    if (!password || !rulesPanel) return;
    const value = password.value;
    rulesPanel.hidden = value.length === 0;
    if (!value.length) {
      if (meter) meter.style.width = '0%';
      password.setCustomValidity('');
      ruleElements.forEach((item) => item.classList.remove('is-met'));
      return;
    }
    const checks = evaluatePassword(value);
    let passed = 0;
    ruleElements.forEach((item) => {
      const valid = Boolean(checks[item.dataset.rule]);
      item.classList.toggle('is-met', valid);
      item.setAttribute('aria-checked', valid ? 'true' : 'false');
      if (valid) passed += 1;
    });
    if (meter) {
      meter.style.width = `${passed * 25}%`;
      const progress = meter.parentElement;
      if (progress) progress.setAttribute('aria-valuenow', String(passed));
    }
    password.setCustomValidity(passed === 4 ? '' : 'Choose a password that meets all four requirements.');
  };
  password?.addEventListener('input', updatePassword);
  password?.addEventListener('blur', updatePassword);

  document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
    toggle.addEventListener('click', () => {
      const targetId = toggle.getAttribute('data-password-toggle');
      const target = targetId ? document.getElementById(targetId) : null;
      if (!target) return;
      const reveal = target.type === 'password';
      target.type = reveal ? 'text' : 'password';
      toggle.textContent = reveal ? 'Hide' : 'Show';
      toggle.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
    });
  });

  const confirm = document.querySelector('[data-confirm-password]');
  const checkConfirm = () => {
    if (!password || !confirm || !confirm.value) {
      confirm?.setCustomValidity('');
      return;
    }
    confirm.setCustomValidity(password.value === confirm.value ? '' : 'Passwords do not match.');
  };
  confirm?.addEventListener('input', checkConfirm);
  password?.addEventListener('input', checkConfirm);
  form?.addEventListener('submit', (event) => {
    updatePassword();
    checkConfirm();
    if (!form.checkValidity()) {
      event.preventDefault();
      form.reportValidity();
    }
  });
})();
