# TwinProject

Pour télécharger le projet : "git clone https://github.com/ATOFLIX/TwinProject.git"

Il faut ensuite se placer le dossier du projet et exécuter la commande suivante : 
"composer update" afin de télécharger les dernières mises à jour et les paquets nécessaires au projet

Version de PHP : 7.3.12


Première version de l'interface de l'application qui inclut :

    Une BDD que vous pouvez générer avec la commande de migration suivante (Contient 1 seule table user) :
        -'php bin/console doctrine:migration:migrate'

    Un formulaire de connexion, afin de pouvoir se connecter soit en utilisateur, soit en adminitrateur.
        - Pour tester ajouter un utilisateurs via le chemin "http://127.0.0.1:8000/admin/inscription"
        Par défaut le rôle de ce nouvel utilisateur sera définit en 'ROLE_USER', vous pouvez changez 
        manuellement dans la BDD en 'ROLE_ADMIN' pour faire vos tests.

    Le formulaire d'inscription avec la gestion des comptes ne sera accessible que par l'administrateur en décommentant la ligne
    40 du fichier security.yaml.

    Bouton de déconnexion.

    Une page "Vue 3D" afin d'afficher le jumeau à l'aide de Three.js, accéssible seulement si on est connecté.
   
Deuxième version de l'interface de l'application qui inclut :
    
    Ce qui a été annoncé précédemment avec en plus : 
        - une reprise de la structure de la base de donnée avec les champs "nom" et "prénom" qui ont été ajouté
        - un nouveau controller accessible que par l'admin et lui permettant de gérer les comptes et un formulaire d'inscription géré               par l'admin qui aura l'autorisation d'ajouter, de supprimer et de modifier les données d'un compte.
        - nous essairons aussi de permettre à l'admin de changer le rôle d'un utilisateur à travers l'interface et non passer par la BDD

Troisième Version de l'interface :

    Page d'administration des comptes utilisateurs accessible uniquement par l'administrateur.
    Utilisation du bundle EasyAdmin.
    Connexion avec la BDD distante à l'adresse "90.63.226.129".

    Compte User-> username = USER / mdp = useruser
    Compte Admin-> username = ADMIN / mdp = adminadmin

Quatrième Version de l'interface :
    
    Page de configuration du robot fanuc.
    Entité 'robot' + Nouveau Controller 'RobotController'.
    Création d'un formulaire Symfony pour la modification ddes données du robot.