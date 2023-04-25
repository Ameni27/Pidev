<?php

// Inclure l'autoloader de Composer
require_once 'vendor/autoload.php';

// Créer le transporteur SMTP
$transport = new \Symfony\Component\Mailer\Transport\Smtp\SmtpTransport('smtp.gmail.com', 587, 'tls');
$transport->setUsername('belhadj.ameni@esprit.tn');
$transport->setPassword('qpvxewekwtfyecrj');

// Créer le mailer
$mailer = new \Symfony\Component\Mailer\Mailer($transport);

// Créer le message
$email = (new \Symfony\Component\Mime\Email())
    ->from('belhadj.ameni@esprit.tn')
    ->to('amenibelhadj556@gmail.com')
    ->subject('Confirmation du rendez-vous')
    ->html('<html>
    <head>
      <style>
        /* Définir le style CSS pour le corps du message */
        body {
          font-family: Arial, sans-serif;
          font-size: 16px;
          line-height: 1.5;
          color: #333;
          background-color: #f9f9f9;
        }
        
        
        header {
          background-color: #007bff;
          color: #fff;
          padding: 20px;
        }
        
        /* Définir le style CSS pour le contenu */
        main {
          padding: 20px;
          background-color: #fff;
        }
        
        /* Définir le style CSS pour le lien de confirmation */
        a {
          color: #007bff;
        }
        
        /* Définir le style CSS pour le pied de page */
        footer {
          background-color: #007bff;
          color: #fff;
          padding: 20px;
          text-align: center;
        }
      </style>
    </head>
    <body>
      <header>
        <h1>Confirmation de rendez-vous médical</h1>
      </header>
      <main>
        <p>Bonjour,</p>
        <p>Nous sommes heureux de vous confirmer votre rendez-vous médical avec le docteur [Nom] le [Date] à [Heure] au cabinet [Adresse].</p>
        <p>Merci de bien vouloir confirmer votre présence en cliquant sur le lien suivant :</p>
        <p><a href="#">Confirmer ma présence</a></p>
      </main>
      <footer>
        <p>© 2023 Nom du cabinet médical</p>
      </footer>
    </body>
  </html>');

// Envoyer le message
$mailer->send($email);
