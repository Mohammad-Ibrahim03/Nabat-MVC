(() => {
  const form = document.querySelector('[data-finder-form]');
  const result = document.querySelector('[data-finder-result]');
  if (!form || !result) return;
  form.addEventListener('submit', (event) => {
    event.preventDefault();
    const data = new FormData(form);
    const place = String(data.get('place'));
    const light = String(data.get('light'));
    const experience = String(data.get('experience'));
    let recommendation;
    if (place === 'garden' || place === 'balcony' || light === 'sun') {
      recommendation = { name: 'Lavender', text: 'A sun-loving option for a balcony, patio or outdoor corner. Let the soil dry slightly between waterings.' };
    } else if (light === 'low' || experience === 'beginner') {
      recommendation = { name: 'Peace Lily', text: 'A forgiving indoor favourite that prefers gentle, indirect light and evenly moist soil.' };
    } else {
      recommendation = { name: 'Monstera', text: 'A beautiful statement plant for bright, indirect light. Give it room to grow and water when the top soil feels dry.' };
    }
    result.innerHTML = `<span>✳</span><p class="store-eyebrow">YOUR NABTA MATCH</p><h2>${recommendation.name}</h2><p>${recommendation.text}</p><a class="store-button" href="index.php?route=products">Explore plants ↗</a>`;
  });
})();
