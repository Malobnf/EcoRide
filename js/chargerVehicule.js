document.addEventListener("DOMContentLoaded", async () => {
  const container = document.getElementById('voitureChoix');

  try {
    const res = await fetch("index.php?page=get_vehicules");
    const data = await res.json();

    if (!data.success) {
      container.innerHTML = `<p class="error">Erreur : ${data.message}</p>`;
      return;
    }

    const vehicules = data.vehicules;

    if (vehicules.length === 0) {
      container.innerHTML = `
      <p>Aucune voiture enregistrée.</p>
      <a href="index.php?page=ajouter_vehicule" class="ajout-btn"Enregistrer une voiture</a>
      `;
    } else if (vehicules.length === 1) {
      const v = vehicules[0];
      container.innerHTML = `
      <label>
        <input type="radio" names="voiture" value="${v.id}" checked required>
        ${v.marque} ${v.modele} - ${v.plaque}
      </label>
      <br>
      <a href="index.php?page=ajouter_vehicule" class="ajout-btn">Enregistrer un autre véhicule"</a>
      `;
    } else {
      container.innerHTML = vehicules.map(v => `
        <div class="vehicule-card">
          <label>
            <input type="radio" name="voiture" value="${v.id}" required>
            <strong>${v.marque} ${v.model}</strong><br>
            Plaque : ${v.plaque}<br>
            Couleur : ${v.couleur}
          </label>
        </div>
      `).join('') + `
        <a href="index.php?page=ajouter_vehicule" class="ajout-btn">Enregistrer un autre véhicule</a>
      `;
    }

  } catch (err) {
    container.innerHTML = `<p> class="error">Erreur lors du chargement des véhicules.</p>`
    console.error(err);
  }
});