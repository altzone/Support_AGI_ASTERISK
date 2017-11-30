# Support_AGI_ASTERISK
PHP AGI Asterisk Gestion du support client

- Gestion du support téléphonique
- Mysql / PHP / AGI / ASTERISK

Features:
  - Accueil des clients
  - Vérification du numéro de contrat
  - Possibilité de bypasser la vérification du contrat si le numéro est connu
  - Vérification de la plage de support (plage horaire et jour)
  - Possibilité pour un client avec contrat mais en HNO de joindre le support en utilisant un identifiant bonus (911)
  - Vérification de panne connue sur le réseau et choix de joindre le support ou laisser un message
  - Possibilité au client de laisser un message au support
  - Enregistrement de la conversation (possibilité du client de refuser)
  - identification et avertissement de l'appel d'un client via Prowl pour les personnes d'astreinte
  - stockage des logs et évènement en MySQL
  - Envoi des messages laissés par les clients en IMAP ou par mail
  - Boucle d'appel sur les postes support et astreinte avec validation de la prise d'appel (evite les repondeurs)

  
