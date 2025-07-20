document.addEventListener('DOMContentLoaded', () => {
  initModInfo();
});

// Modification des informations personnelles
function initModInfo() {
  const formModif = document.getElementById('form-modif');
  if (!formModif) return;

  // Fix ajout input HTML — on crée un seul input et on l'ajoute une seule fois
  const inputHTML = document.createElement('input');
  inputHTML.setAttribute('type', 'text');
  inputHTML.setAttribute('name', 'modifInput');
  inputHTML.setAttribute('placeholder', 'Modifiez le champ');

  formModif.appendChild(inputHTML);

  formModif.addEventListener('submit', (e) => {
    e.preventDefault();
    alert("Formulaire soumis");
  });
}

async function initPage() {
    try {
      const res = await fetch('infos_utilisateur.php');
      const data = await res.json();

      if (data.success) {
        document.getElementById('userFullName').textContent = `${data.prenom} ${data.nom}`;
      } else {
        document.getElementById('userFullName').textContent = "Utilisateur";
      }
    } catch (err) {
      console.error("Erreur lors du chargement des informations.", err);
    }
  }