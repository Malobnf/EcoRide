document.addEventListener('DOMContentLoaded', () => {
  initMenuToggle();
  remplirAnneesImmat();
  initVehiculesPage();
});

function remplirAnneesImmat() {
  const select = document.getElementById('annee_immat');
  if (!select) return;

  for (let an = 2025; an >= 1960; an--) {
    const option = document.createElement('option');
    option.value = an;
    option.textContent = an;
    select.appendChild(option);
  }
}

function initVehiculesPage() {
  const listeVehicules     = document.getElementById("listeVehicules");
  const formAjoutVehicule  = document.getElementById("formAjoutVehicule");
  const ajouterVehiculeBtn = document.getElementById("ajouterVehiculeBtn");
  const formModifVehicule  = document.getElementById("formModifVehicule");
  const messageEl          = document.getElementById("vehiculeMessage");

  if (ajouterVehiculeBtn && formAjoutVehicule) {
    ajouterVehiculeBtn.addEventListener('click', () => {
      formAjoutVehicule.classList.toggle('hidden');
    });
  }

  if (formAjoutVehicule) {
    formAjoutVehicule.addEventListener('submit', async (e) => {
      e.preventDefault();

      const data = new FormData(formAjoutVehicule);
      try {
        const res  = await fetch('index.php?page=ajouter_vehicule', { method: 'POST', body: data });
        const text = await res.text();
        let result;
        try {
          result = JSON.parse(text);
        } catch {
          messageEl.textContent = "Erreur serveur lors de l'ajout.";
          return;
        }

        if (result.success) {
          messageEl.textContent = result.message || "Véhicule ajouté.";
          formAjoutVehicule.reset();
          formAjoutVehicule.classList.add('hidden');
          chargerVehicules();
        } else {
          messageEl.textContent = result.message || "Erreur lors de l'ajout.";
        }
      } catch (error) {
        console.error(error);
        messageEl.textContent = "Erreur réseau lors de l'ajout.";
      }
    });
  }

  if (formModifVehicule) {
    formModifVehicule.addEventListener('submit', async (e) => {
      e.preventDefault();

      const formData = new FormData(formModifVehicule);

      try {
        const res  = await fetch('index.php?page=modifier_vehicule', { method: 'POST', body: formData });
        const text = await res.text();
        let result;
        try {
          result = JSON.parse(text);
        } catch {
          alert("Erreur serveur lors de la modification.");
          return;
        }

        if (result.success) {
          alert("Modifications enregistrées.");
          formModifVehicule.classList.add("hidden");
          formModifVehicule.reset();
          chargerVehicules();
        } else {
          alert(result.message || "Erreur lors de la modification");
        }

      } catch (error) {
        console.error(error);
        alert("Erreur réseau");
      }
    });
  }

  async function chargerVehicules() {
    if (!listeVehicules) return;

    try {
      const res  = await fetch('index.php?page=mes_vehicules');
      const text = await res.text();
      let vehicules;
      try {
        vehicules = JSON.parse(text);
      } catch {
        listeVehicules.innerHTML = '<p>Erreur serveur.</p>';
        return;
      }

      listeVehicules.innerHTML = "";

      if (!Array.isArray(vehicules) || vehicules.length === 0) {
        listeVehicules.innerHTML = '<p>Aucun véhicule enregistré.</p>';
        return;
      }

      vehicules.forEach(v => {
        const div = document.createElement("div");
        div.className = 'vehicule-card';
        div.innerHTML = `
          <p><strong>${v.marque} ${v.modele}</strong> - ${v.plaque} (${v.couleur}, ${v.annee_immat ?? ''})</p>
          <button class="modifierVehiculeBtn btn-secondary" data-id="${v.id}">Modifier</button>
          <button class="supprimer-vehicule-btn btn-ghost" data-id="${v.id}">Supprimer</button>
        `;
        listeVehicules.appendChild(div);
      });

      document.querySelectorAll(".supprimer-vehicule-btn").forEach(btn => {
        btn.addEventListener('click', async () => {
          if (confirm("Supprimer ce véhicule ?")) {
            await fetch('index.php?page=supprimer_vehicule&id=' + btn.dataset.id);
            chargerVehicules();
          }
        });
      });

      document.querySelectorAll(".modifierVehiculeBtn").forEach(btn => {
        btn.addEventListener('click', async () => {
          const id = btn.dataset.id;
          const res    = await fetch('index.php?page=get_vehicule&id=' + id);
          const data   = await res.json();

          if (data.success && data.vehicule && formModifVehicule) {
            formModifVehicule.querySelector('[name="id"]').value      = data.vehicule.id;
            formModifVehicule.querySelector('[name="marque"]').value  = data.vehicule.marque;
            formModifVehicule.querySelector('[name="modele"]').value  = data.vehicule.modele;
            formModifVehicule.querySelector('[name="plaque"]').value  = data.vehicule.plaque;
            formModifVehicule.querySelector('[name="couleur"]').value = data.vehicule.couleur;

            formModifVehicule.classList.remove('hidden');
            formModifVehicule.scrollIntoView({behavior: 'smooth'});
          } else {
            alert("Erreur : " + (data.message || "Impossible de charger le véhicule."));
          }
        });
      });

    } catch (error) {
      console.error(error);
      listeVehicules.innerHTML = '<p>Impossible de charger les véhicules.</p>';
    }
  }

  chargerVehicules();
}
