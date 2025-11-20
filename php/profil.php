<?php
declare(strict_types=1);

require_once __DIR__ . '/session_boot.php';

// Vérifier la session
if (empty($_SESSION['utilisateur_id'])) {
    header('Location: index.php?page=connexion_html');
    exit;
}

// Si connexion admin, redirection sur le tableau de bord
if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: index.php?page=admin');
    exit;
}

require_once __DIR__ . '/db.php';
$pdo = getPdo();

$preferences      = [];
$descriptionMongo = null;

// --- Chargement description / préférences via MongoDB si dispo ---
if (class_exists('MongoDB\\Client') && getenv('MONGODB_URI')) {
    require_once __DIR__ . '/mongo.php';
    if (isset($userProfilesCol)) {
        try {
            $profileDoc = $userProfilesCol->findOne(
                ['_id' => (int) $_SESSION['utilisateur_id']],
                ['projection' => ['preferences' => 1, 'description' => 1]]
            );
            if ($profileDoc) {
                if (!empty($profileDoc['preferences']) && is_array($profileDoc['preferences'])) {
                    $preferences = $profileDoc['preferences'];
                }
                if (!empty($profileDoc['description'])) {
                    $descriptionMongo = (string) $profileDoc['description'];
                }
            }
        } catch (Throwable $e) {
            error_log('[profil] Mongo read error: ' . $e->getMessage());
        }
    }
}

// --- Infos utilisateur en MySQL ---
$stmt = $pdo->prepare('
    SELECT nom, prenom, email, telephone, description, credits, role
    FROM utilisateurs
    WHERE id = ?
');
$stmt->execute([$_SESSION['utilisateur_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit;
}

// --- Véhicules de l'utilisateur (pour la petite liste dans le profil) ---
$vehicules = [];
try {
    $vehReq = $pdo->prepare('
        SELECT id, marque, modele, couleur
        FROM vehicules
        WHERE utilisateur_id = ?
        ORDER BY marque, modele
    ');
    $vehReq->execute([$_SESSION['utilisateur_id']]);
    $vehicules = $vehReq->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('[profil] Erreur chargement véhicules : ' . $e->getMessage());
}

// --- Description publique : Mongo en priorité, sinon MySQL ---
$descriptionPublic = $descriptionMongo ?? ($user['description'] ?? '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil – EcoRide</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap"
          rel="stylesheet">

    <script>
        const userRole = <?= json_encode($user['role']) ?>;
    </script>
    <script src="../js/profil.js" defer></script>
    <script src="../js/gestionCovoit.js" defer></script>
    <script src="../js/script.js" defer></script>
    <script src="../js/rechercheCovoit.js" defer></script>
</head>
<body>

<header>
    <div class="deroulant">☰</div>
    <div class="marque"><a href="index.php?page=accueil">EcoRide</a></div>
    <div class="profile-icone">
        <a href="index.php?page=profil" id="icone-profil" rel="profil">
            <i class="fas fa-circle-user fa-2x"></i>
        </a>
    </div>

    <nav class="side-menu" id="sideMenu">
        <a href="index.php?page=accueil">Accueil</a>
        <a href="index.php?page=covoit">Recherche</a>
        <a href="index.php?page=creer-trajet_html">Proposer un trajet</a>
        <a class="current-page">Profil</a>
        <a href="index.php?page=contact">Contact</a>
        <a href="index.php?page=deconnexion">Déconnexion</a>
    </nav>
</header>

<!-- Barre supérieure : crédits + actions -->
<div class="profil-top-bar">
    <div class="profil-credits">
        Crédits : <span id="userCredits"><?= htmlspecialchars((string) $user['credits']) ?></span>
        <i class="fa-solid fa-coins"></i>
    </div>

    <div class="profil-actions">
        <a href="index.php?page=mes_trajets_html" class="btn-primary">Mes trajets</a>
        <a href="index.php?page=mes_vehicules_html" class="btn-primary btn-outline">Mes véhicules</a>

        <?php if (in_array($user['role'], ['admin', 'employe'], true)): ?>
            <button id="gestionCovoitBtn" class="btn-secondary">Gestion covoiturages</button>
        <?php endif; ?>

        <button id="adminTabBtn" class="btn-secondary" style="display:none;">
            Gestion admin
        </button>
    </div>
</div>

<main class="profil-main">
    <div class="profile-pic"></div>

    <div class="user-full-name" id="userFullName">
        <?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?>
    </div>

    <div class="rating">★★★★</div>

    <div class="reviews">
        <div class="review">Super conducteur, trajet agréable !</div>
        <div class="review">Ponctuel et très sympathique.</div>
        <div class="review">Je recommande sans hésiter.</div>
    </div>

    <!-- Bloc informations publiques -->
<div class="profil-section">
    <h3 class="profil-section-title">Informations publiques</h3>
    <div class="profil-section-content">
        <p><strong>Nom :</strong> <?= htmlspecialchars($user['nom']) ?></p>
        <p><strong>Prénom :</strong> <?= htmlspecialchars($user['prenom']) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Téléphone :</strong> <?= htmlspecialchars($user['telephone']) ?></p>

        <p><strong>Véhicules :</strong></p>
        <?php if (count($vehicules) > 0): ?>
            <ul class="vehicule-list">
                <?php foreach ($vehicules as $v): ?>
                <li>
                    <?= htmlspecialchars($v['marque']) ?> <?= htmlspecialchars($v['modele']) ?>
                    - <?= htmlspecialchars($v['couleur']) ?>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun véhicule enregistré.</p>
        <?php endif; ?>

        <p><strong>À propos :</strong><br><?= nl2br(htmlspecialchars($descriptionPublic)) ?></p>
    </div>
</div>

<!-- Bloc Préférences -->
<div class="profil-section">
    <h3 class="profil-section-title">Préférences</h3>
    <div class="profil-section-content">
        <form id="preferencesForm">
            <label><input type="checkbox" name="preferences[]" value="non_fumeur"
                <?= in_array('non_fumeur', $preferences ?? [], true) ? 'checked' : '' ?>> Non-fumeur</label><br>

            <label><input type="checkbox" name="preferences[]" value="animaux_ok"
                <?= in_array('animaux_ok', $preferences ?? [], true) ? 'checked' : '' ?>> J'aime les animaux</label><br>

            <label><input type="checkbox" name="preferences[]" value="musique"
                <?= in_array('musique', $preferences ?? [], true) ? 'checked' : '' ?>> J'aime la musique</label><br>

            <label><input type="checkbox" name="preferences[]" value="discussion"
                <?= in_array('discussion', $preferences ?? [], true) ? 'checked' : '' ?>> J'aime discuter</label><br>

            <button type="submit" class="btn-primary" style="margin-top:20px;">
                Enregistrer les préférences
            </button>
        </form>

        <div id="prefMessage"></div>
    </div>
</div>
   

        <div id="prefMessage"></div>
    </div>

    <!-- Bouton pour ouvrir la modale de modification du profil -->
    <button id="profilBtn" class="btn-link">Modifier le profil</button>
</main>

<!-- Modale : modification du profil -->
<div id="editProfilePopup" class="modal-overlay hidden">
    <div class="modal-content-trajets">
        <span class="close-modal" id="closeEditProfile">
            <i class="fas fa-circle-xmark"></i>
        </span>
        <h3>Modifier le profil</h3>

        <form id="editProfileForm">
            <div class="edit-field">
                <label>Nom :</label>
                <span id="editNomText"><?= htmlspecialchars($user['nom']) ?></span>
                <input type="text"
                       id="editNomInput"
                       name="nom"
                       class="hidden"
                       value="<?= htmlspecialchars($user['nom']) ?>">
                <i class="fas fa-pen edit-icon" data-target="editNom"></i>
            </div>

            <div class="edit-field">
                <label>Prénom :</label>
                <span id="editPrenomText"><?= htmlspecialchars($user['prenom']) ?></span>
                <input type="text"
                       id="editPrenomInput"
                       name="prenom"
                       class="hidden"
                       value="<?= htmlspecialchars($user['prenom']) ?>">
                <i class="fas fa-pen edit-icon" data-target="editPrenom"></i>
            </div>

            <div class="edit-field">
                <label>Email :</label>
                <span id="editEmailText"><?= htmlspecialchars($user['email']) ?></span>
                <input type="email"
                       id="editEmailInput"
                       name="email"
                       class="hidden"
                       value="<?= htmlspecialchars($user['email']) ?>">
                <i class="fas fa-pen edit-icon" data-target="editEmail"></i>
            </div>

            <div class="edit-field">
                <label>Téléphone :</label>
                <span id="editTelText"><?= htmlspecialchars($user['telephone']) ?></span>
                <input type="tel"
                       id="editTelInput"
                       name="telephone"
                       class="hidden"
                       value="<?= htmlspecialchars($user['telephone']) ?>">
                <i class="fas fa-pen edit-icon" data-target="editTel"></i>
            </div>

            <div class="edit-field">
                <label>Description :</label>
                <span id="editDescText"><?= htmlspecialchars($user['description'] ?? '') ?></span>
                <textarea id="editDescInput"
                          name="description"
                          class="hidden"><?= htmlspecialchars($user['description'] ?? '') ?></textarea>
                <i class="fas fa-pen edit-icon" data-target="editDesc"></i>
            </div>

            <button type="submit" id="saveProfileBtn">Sauvegarder les modifications</button>
        </form>
    </div>
</div>

<!-- Modale : gestion des covoiturages (admin/employé) -->
<div id="gestionCovoitModalOverlay" class="modal-overlay hidden">
    <div class="modal-content-trajets">
        <span class="close-modal" id="closeGestionCovoitModal">
            <i class="fas fa-circle-xmark"></i>
        </span>
        <h3>Gestion covoiturages</h3>

        <div class="modal-tabs">
            <button class="tab-button active" data-tab="avis">Avis</button>
            <button class="tab-button" data-tab="conflits">Conflits</button>
        </div>

        <div class="modal-tab-content" id="avis">
            <div id="listeAvis"></div>
        </div>

        <div class="modal-tab-content hidden" id="conflits">
            <div id="listeConflits"></div>
        </div>
    </div>
</div>

<!-- Popup de confirmation d'annulation de trajet -->
<div id="popupConfirm" class="hidden">
    <p>Souhaitez-vous vraiment annuler ce trajet ?</p>
    <button id="confirmerAnnulation">Oui</button>
    <button id="annulerAnnulation">Non</button>
</div>

<!-- Bouton de déconnexion -->
<button id="logoutBtn"
        class="btn-ghost"
        onclick="window.location.href='index.php?page=deconnexion'">
    Déconnexion
</button>

</body>
</html>
