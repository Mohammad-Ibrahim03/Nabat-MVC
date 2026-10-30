(() => {
  'use strict';
  const block = document.querySelector('[data-google-auth]');
  if (!block) return;
  const message = block.querySelector('[data-google-auth-message]');
  const csrfToken = block.dataset.csrfToken || '';
  let submitting = false;

  function showMessage(text, isError = true) {
    if (!message) return;
    message.hidden = false;
    message.textContent = text;
    message.classList.toggle('error', isError);
    message.setAttribute('role', isError ? 'alert' : 'status');
  }

  window.handleGoogleCredential = async (response) => {
    if (submitting) return;
    if (!response || typeof response.credential !== 'string' || !response.credential) {
      showMessage('Google did not return a sign-in credential. Please try again.');
      return;
    }
    submitting = true;
    showMessage('Verifying your Google account…', false);
    try {
      const result = await fetch('index.php?route=google-auth', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8', 'Accept': 'application/json' },
        body: new URLSearchParams({ credential: response.credential, csrf_token: csrfToken })
      });
      const data = await result.json();
      if (!result.ok || !data.ok) {
        showMessage(data.message || 'Google sign-in failed. Please try again.');
        submitting = false;
        return;
      }
      window.location.assign(data.redirect || 'index.php');
    } catch (_error) {
      showMessage('Could not reach Nabta. Check your connection and try again.');
      submitting = false;
    }
  };
})();
