document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('preferencesForm');
  const checkboxes = form.querySelectorAll('input[type="checkbox"]');
  const messageDiv = document.getElementById('message');

  // Charger préférences existantes
  fetch('php/get_user_preferences.php')
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const prefs = data.preferences || [];
        checkboxes.forEach(cb => {
          cb.checked = prefs.includes(cb.value);
        });
      } else {
        messageDiv.textContent = "Erreur de chargement des préférences";
      }
    })
    .catch(() => {
      messageDiv.textContent = "Erreur réseau";
    });

  // Envoyer la maj des préférences au serveur
  form.addEventListener('change', () => {
    const selected = Array.from(checkboxes)
      .filter(cb => cb.checked)
      .map(cb => cb.value);

    fetch('php/update_preferences.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({preferences: selected})
    })
    .then(response => response.json())
    .then(data => {
      messageDiv.textContent = data.message || '';
    })
    .catch(() => {
      messageDiv.textContent = "Erreur lors de la mise à jour des préférences";
    });
  });
});