document.getElementById('ajoutVehiculeForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);
  const data = Object.fromEntries(formData.entries())

  try {
    const response = await fetch('index.php?page=ajouter_vehicule.', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(data)
    });

    const result = await response.json();
    document.getElementById('message').textContent = result.message;
  } catch (error) {
    document.getElementById('message').textContent = "Erreur lors de l'ajout";
    console.error(error);
  }
});