# TwinProject

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

