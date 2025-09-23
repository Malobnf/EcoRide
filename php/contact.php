<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
  <script src="../js/script.js"></script>
  <title>EcoRide</title>
</head>
  <style>


    /* MAIN PROFILE */
    main {
      padding: 40px 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }

    .profile-pic {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      background-color: #ccc;
      background-image: url('https://via.placeholder.com/150');
      background-size: cover;
      background-position: center;
      margin-bottom: 20px;
    }

    .rating {
      margin-bottom: 10px;
      font-size: 24px;
      color: gold;
    }

    .reviews {
      margin-bottom: 30px;
      max-width: 600px;
    }

    .review {
      background-color: white;
      padding: 15px;
      margin: 10px auto;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .user-info p {
      margin: 5px 0;
    }
  </style>

<body>

  <header>
    <div class="deroulant">☰</div>
    <div class="marque"><a href="index.php?page=accueil">EcoRide</a></div>
    <div class="profile-icone">
      <a href="index.php?page=profil" id="icone-profil" rel= "profil">
        <i class="fas fa-circle-user fa-2x"></i>
      </a>
    </div>

    <nav class="side-menu" id="sideMenu">
      <a href="index.php?page=accueil">Accueil</a>
      <a href="index.php?page=covoit">Recherche</a>
      <a href="index.php?page=creer-trajet_html">Proposer un trajet</a>
      <a href="index.php?page=profil">Profil</a>
      <a class="current-page">Contact</a>
    </nav>
  </header>

  <main>
    <h3>Contacter Ecoride</h3>
    <p><strong>Formulaire de contact</p>
    <form id="contact-form" action="#" method="POST" enctype="multipart/form-data">
      <div class="row">
        <label class="required" for="name">Votre nom :</label></br>
        <input id="name" class="input" name="name" type="text" value=""/></br>
        <span id="name_validation" class="error-message"></span>
      </div>

      <div class="row">
        <label class="required" for="email">Adresse e-mail :</label></br>
        <input id="email" class="input" name="email" type="text" value=""/></br>
        <span id="email_validation" class="error-message"></span>          
      </div>

      <p>Motif du message : </p>

      <nav>
        <ul>
          <li class="deroulant"><a href="#"></a>
            <ul classe="sous">
              <li><a href="#">Signaler un problème sur le site</a></li>
              <li><a href="#">Signaler un problème lors d'un covoiturage</a></li>
              <li><a href="#">Proposer une fonctionnalité/amélioration</a></li>
            </ul>
          </li>
        </ul>
      </nav>

      <div class="row">
        <label class="required" for="message">Votre message :</label></br>
        <textara id="message" class="input" name="message" rows="7" cols="30"/></textarea></br>
        <span id="message_validation" class="error-message"></span>          
      </div>

      <input id="submit-button" type="submit" value="Send email"/>
    </form>
  </main>

</body>
</html>
