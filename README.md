Projet de symfony, Lavergne Guillaume, Peyrelongue Guillaume.
Vous trouverez dans ce document : 
-les étapes d'installation
-les point d'entrées de l'API
-l'explication globale de notre projet : fonctionnalitées possible, choix techniques etc...
-les fonctionnalités que nous souhaitions rajouter mais que nous n'avons pas pu mettre en place

/* LES ETAPES D'INSTALLATION *\

Tout d'abord, afin d'installer et d'initialiser notre projet, veuillez vous assurer de posséder chacune des ces choses:
    -Un éditeur de text (Visual Studio Code)

    -PostgreSQL 15 ainsi qu'un outil d'administration de ce dernier tel que PG admin. Si vous n'avez pas postgresql, voilà un lien vers un executable afin d'obtenir Postgresql :
        https://www.postgresql.org/download/windows/
        Lors de cette installation, vous aurez deux choses à préciser :
            -La version de Postgresql est la version 15
            -Si cela n'est pas fait par défaut, précisez que vous souhaitez l'installation de "Pgadmin" lorsque cela vous le propose.
        
        Lors de cette installation, postgresql peut vous demander d'initialiser un mot de passe, il est important de le retenir !
        

    -PHP : Assurez vous que pgsql est actif. Pour cela, rendez-vous dans le php.ini et décommentez les lignes suivantes : pdopgsql, pgsql.
            Si votre php est installé avec wamp, lancez ce dernier. Utilisez le raccourcis dans la barre d'outil puis passez votre curseur sur "php" => "extensions php"
            et cochez les deux variables "pdopgsql" et "pgsql". Il est possible malgré wamp, les variable ne se décommentent pas, pour cela, veuillez les décommenter manuellement.
            Voici un exemple de route vers le fichier php.ini : C:\wamp64\bin\php\php8.2.0\php.ini.

1 : L'initialisation du projet

Une fois le projet récupéré, ouvrez un nouveau terminal de commande et executez la commande suivante : composer install
Cette commande est censée vous installer l'entiéreté des dépendances nécessaires au projet.

Dirigez vous maintenant dans le fichier ".env" à la racine de votre projet et modifier la ligne "DATABASE_URL" de maniére à insérer la ligne suivante:
"postgresql://{utilisateur}:{mot-de-passe}@{localhost}:{port}/edt?serverVersion=15&charset=utf8"

{utilisateur} : nom de l'utilisateur de votre base de donnée, par défaut "postgres"
{mot-de-passe} : mot de passe renseigné lors de l'installation, sinon par défaut : "postgres"
{localhost} : votre adresse ip de localhost
{port} : port de la base de donnée. Pour savoir quel est ce dernier suivez les instructions suivantes :

        -Ouvrez pgadmin (chaque demande de mot de passe qui s'ouvre a partir de maintenant est votre mot de passe ou "postgres" par défaut)
        -Cliquez sur "server" -> "PostgreSQL 15"
        -Faites un clic-droit sur PostgreSQL 15 et cliquez sur "propriété", puis allez dans "connection" vous y trouverez votre port.

Un exemple de ligne possible pourrait être : postgresql://postgres:postgres@127.0.0.1:5433/edt?serverVersion=15&charset=utf8

2 : Intialisation de la base de donnée

Connectez vous sur pgadmin et cliquez sur "server" -> "PostgreSQL 15" -> "databases". Si des mots de passes vous sont demandés, il s'agit de votre mot de passe ou de "postgres" par défaut. Faites maintenant clic-droit sur "databases" et faite "create". Créez une base de donnée dont le nom est "edt".

Dirigez vous maintenant dans le terminal de votre editeur de code et executez la commande suivante : php bin/console doctrine:schema:update;
Maintenant que votre base de donnée est intialisée, faite clic-droit sur votre base "edt" et faites "refresh". Effectuez maintenant le raccourcis de touche "ALT + Q + SHIFT" ou cliquez sur "query tool" représenté par un logo de base de donnée en dessous de votre barre d'outil du logiciel. Une page blanche c'est normalement ouverte sur la droite de votre ecran.
Copiez maintenant le fichier 'insert_values.sql' fournit dans le projet et cliquez sur la flèche au-dessus de la page blance "execute/refresh" afin d'exécuter la création de la fonction. Remplacez maintenant le contenu de votre page blanche par la ligne "SELECT insert_values()" et cliquez de nouveau sur la flèche afin d'executer les insertions.
Les valeurs à retenir sont:
    -User admin => mdp:admin, email:admin@gmail.com
    -User professeur => mdp:professeur, email:drudru@gmail.com
    -User eleve => mdp:eleve, email:guillaumelaverge@gmail.com

Il est d'ailleurs important de mentionner le fait que symfony ne s'adapte pas à la BDD, de cette manière, les identifiants de nos tuples insérés ne sont pas suivis par Symfony. Le compteur d'identifiant de symfony vas alors commencer à 0, alors que nos valeurs en BDD occupent déjà ces identifiants. Il faudra donc répéter plusieurs fois une insertion dans la base avant que le compteur d'identifiant dépasse les valeurs en bases de données.

Votre logiciel est maintenant prêt pour utilisation, executez la commande "php -S localhost:8000" après avoir fait "cd public" si vous n'y étiez pas déjà.
Le premier lien que vous devez utiliser est "http://localhost:8000/login".

/* EXPLICATION DE NOS CHOIX D'ENTITES ET OBJECTIFS */

Pour nos entités, nous sommes partis avec les entités demandées sur le schéma de présentation du projet, à l'exception d'un attribut : "type" dans "cours".
Afin que ce dernier ne puisse pas être défini n'importe comment, avec des ortographe différent (exemple : tp != Tp != TP) , et que nous evitions l'utilisations d'une énumeration en dur dans le code, qui limiterait le champs des possibilités pour un admin l'empêchant de rajouter un type s'il le souhaite, nous avons décider de matérialiser "type" par une entité.
Cette entité est liée à un cours de la maniére suivante : un cours possède un type, un type peut être lié à plusieurs cours. Voici sa définition :
    int id; //L'idientifiant du type
    string nom not null; //Le nom du type

Après cela, nous avons décidé de créer deux autres entités afin de pouvoir réaliser les objectifs que nous avions : "avisCours" et "user".

AvisCours : 
    int id; //L'identifiant de l'avis
    int cours_id; // L'identifiant du cours que l'avis cible
    int note; //Une note allant de 0 à 5
    string email_etudiant;//Email de l'etudiant ayant posté l'avis
    string commentaire;//Commentaire laissé par l'étudiant

User:
    int id;//L'identifiant de l'utilisateur
    string email;//L'email de l'utilisateur, permettant de le lier à ces avis notamment
    json roles;//Roles de l'utilisateur définissant ces droits
    string password;//Mot de passe de l'utilisateur


AvisCours est une entité permettant de noter un cours. Nous avons décidé de séparer ces notes de l'entité "avis" se concentrant sur les avis donnés aux professeurs afin de ne pas avoir une table avec plusieurs utilitées.

User est une entité permettant de séparer les droits selon le rôle. En effet cela répond à une envie que nous avions : développer le sens de l'application et ne pas la restreindre à une application seulement utile pour les élèves. Grâce à cette entité, nous sommes capable de séparer les utilisation de l'application : les élèves qui peuvent donner leurs avis ou voir l'emplois du temps, les professeurs qui créent des cours, modifie les leurs, et l'administrateur qui gère les professeurs, les avis etc...

/* VERIFICATIONS (ASSERT) *\

Nous allons ici expliquer globalement les objectifs de nos assert, notamment ceux de l'entité cours, afin que vous compreniez le fonctionement de l'application et notre cheminement de pensée. Un cours se voit porter les restrictions suivantes :
    -Un cours ne peut pas dépasser les 2H de durée, et doit porter sur minimum 1H de travail. Cela contient également les cours tenant sur plusieurs jours
    -Un cours doit commencer après 8h00 et finir avant 18H:00
    -Un cours ne peut impliquer un professeur n'enseignant pas la matière du cours
    -Un cours ne peut se dérouler dans une salle déjà occupée à ce moment
    -Plusieurs cours ne peuvent impliquer le même professeur en même temps à différents endroits
    -Un cours ne peut entamer sur la pause repas se déroulant de 12H30 à 14H00
    -Un cours ne peut avoir une date de début inférieur à sa date de fin
    -Un cours ne peut être définit entérieurement à la date actuelle

Pour certaines de ces verifications nous avons du passer par la création d'assert custom représentés par le fichier "Validator".

Nos choix : nous n'avons pas vérifié qu'un cours puisse être posé le week-end ou non car nous ne souhaitions pas apposer de restriction sur cela aux professeur. De plus, l'application pourrait être utilisée afin d'apposé, dans le futur, des cours privés à un éléve, cours particulier se déroulant parfois le week-end. De même pour les horaires de début et fin de cours et de pauses que nous avons définit ici en brut dans nos verifications mais que nous aimerions laisser libre dans le futur. Nous avons tout de même mit ces restrictions afin de ne pas avoir une v1 de notre application n'ayant aucun sens, avec des cours le samedi soir de 23H30 à 00H00...

Pour ce qui est des autres entités, rien de très interessant n'est à signaler sur leurs asserts, ce sont tous des asserts trés basiaques : NotBlank , NotNull etc...

/* LES POINTS D'ENTREES API *\

/* TYPE *\
URL  : http://localhost:8000/api/type
Méthode : GET
Objectif : Récupérer tout les types

URL  : http://localhost:8000/api/type/{id}
Méthode : GET
Objectif : Récupérer un type en particulier

URL  : http://localhost:8000/api/type/create/{nom}
Méthode : POST
Objectif : Créer un type en passant par URL son nom

URL  : http://localhost:8000/api/type/{id}
Méthode : DELETE
Objectif : Supprime un type

/* SALLE *\

URL  : http://localhost:8000/api/salle
Méthode : GET
Objectif : Récupére toutes les salles

URL  : http://localhost:8000/api/salle/{id}
Méthode : GET
Objectif : Récupére une salle

URL  : http://localhost:8000/api/salle/create/{nom}
Méthode : POST
Objectif : Ajoute une salle en passant son nom dans l'url

URL  : http://localhost:8000/api/salle/{id}
Méthode : DELETE
Objectif : Delete une salle

/* PROFESSEURS *\

URL  : http://localhost:8000/api/professeurs
Méthode : GET
Objectif : Recupere tout les professeurs

URL  : http://localhost:8000/api/professeurs/{id}
Méthode : GET
Objectif : Recupere un professeur

URL  : http://localhost:8000/api/professeurs/{id}/avis
Méthode : GET
Objectif : Recupere les avis d'un professeurs

URL  : http://localhost:8000/api/professeurs/{id}/avis/stats
Méthode : GET
Objectif : Recupere les statistiques relatives aux avis d'un professeurs

URL  : http://localhost:8000/api/professeurs/{id}/avis
Méthode : POST
Body : {   
        "note" : 0,
        "commentaire" : "",
        "emailEtudiant" : ""
        }
Objectif : Ajoute un avis à un professeur

/* Matiere *\

URL  : http://localhost:8000/api/matiere
Méthode : GET
Objectif : Recupere toutes les matiéres

URL  : http://localhost:8000/api/matiere/{id}
Méthode : GET
Objectif : Recupere une matiére

URL  : http://localhost:8000/api/matiere/create
Méthode : POST
Body : {
        "titre": "",
        "reference" : ""
        }
Objectif : Créer une matiére

URL  : http://localhost:8000/api/matiere/{id}
Méthode : DELETE
Objectif : Delete une matiére

/* Cours *\

URL  : http://localhost:8000/api/cours
Méthode : GET
Objectif : Récupére touts les cours

URL  : http://localhost:8000/api/cours/{id}
Méthode : GET
Objectif : Récupére un cours

URL  : http://localhost:8000/api/cours/{id}/avis/stats
Méthode : GET
Objectif : Récupére les statistiques des avis d'un cours

URL  : http://localhost:8000/api/cours/create
Méthode : POST
Body : {
        "id_professeur" : 1,
        "id_salle": 2,
        "id_type": 1,
        "id_matiere": 4,
        "dateHeureDebut" : "2023-03-10 15:00:00",
        "dateHeureFin" : "2023-03-10 16:00:00"
        }
Objectif : Ajoute un cours

URL  : http://localhost:8000/api/cours/getByDate
Méthode : POST
Body : {
        "date" : "2023-03-07"
        }
Objectif : Récupére les cours d'une date

URL  : http://localhost:8000/api/cours/getBySalleAndDate
Méthode : POST
Body : {
        "dateDeb" : "2023-03-08 08:00:00",
        "dateFin" : "2023-03-08 17:30:00",
        "salle" : 6
        }
Objectif : Retourne un boolean permettant de savoir si la salle est disponible lors d'une periode d'une journée

URL  : http://localhost:8000/api/cours/{id}
Méthode : DELETE
Objectif : Supprime un cours

URL  : http://localhost:8000/api/avisCours
Méthode : GET
Objectif : Récupére touts les avis sur des cours

URL  : http://localhost:8000/api/avisCours/{id}
Méthode : GET
Objectif : Récupére un avis sur un cours

URL  : http://localhost:8000/api/avisCours/create
Méthode : POST
Body : {
        "commentaire" : "Pas mal ce cours, mais bon, ca reste un cours donc pas fou",
        "emailEtudiant" : "guigui@gmail.fr",
        "cours_id" : "1",
        "note" : 2
        }
Objectif : Créer un avis sur un cours

URL  : http://localhost:8000/api/avisCours/{id}
Méthode : DELETE
Objectif : Supprime un avis sur un cours

/* Avis *\

URL  : http://localhost:2000/api/avis/{id}
Méthode : DELETE
Objectif : Supprime un avis sur un professeur

URL  : http://localhost:2000/api/avis/{id}
Méthode : PUT
Body : {   
        "note" : 4,
        "commentaire" : "je suis commentaire",
        "emailEtudiant" : "testa@gmail.com",
        }
Objectif : Modifie un avis sur un professeurs

Petite précision : le nombre d'API de l'entité AVIS est faible car la pluspart des requêtes nécessaires sont définis dans les API de "professeur"

/* EXPLICATION DES PAGES ET LEURS RESTRICTIONS *\
Connexion en tant qu’élève
Dans le menu de navigation on trouve 4 modules :
- Agenda
- Matières
- Note tes Profs
- Avis Cours
En haut à droite de la page nous retrouvons l’adresse mail avec laquelle nous nous sommes connectés, ainsi qu’un logo qui change en fonction du rôle de l’utilisateur.

Agenda : 
Permet de voir l’emploi du temps chaque jour de la semaine. Nous pouvons changer de jour en faisant suivant ou précèdent.
Lors d’un clic sur un cours une pop-up s’affiche avec ses informations, et nous avons la possibilité de le noter.
Quand nous cliquons sur « Noter le cours » un formulaire apparait et nous pouvons alors créer un avis.
Une fois créée, nous arrivons sur la page Avis cours
Avis Cours :
Nous retrouvons tous les avis sur les cours. Si l’un de ses avis a été fait pas l’utilisateur (avec la même adresse mail) il peut l’éditer ou le supprimer.

Matières :
L’élève peut voir toutes les matières avec la moyenne des notes des avis obtenus, ainsi que les enseignants titulaires de chaque matière.

Note tes Profs : 
L’élève peut donner un avis sur chaque professeur. Quand il clique sur avis, il peut en ajouter un et voir tous se qui ont été mit (un avis par email). Il peut aussi s’il le souhaite supprimer son avis et en recréer un.

Connexion en tant que professeur
Dans le menu de navigation on trouve 5 modules :
- Professeurs
- Matières
- Cours
- Créer un cours
- Avis cours 
- Déconnexion
Professeurs: 
Permet de voir liste de tous les professeurs. S’il le souhaite le professeur peut changer ses informations personnelles.
Matières: 
Comme pour l’élève, le professeur peut voir toutes les matières avec la moyenne des notes des avis obtenus, ainsi que les enseignants titulaires de chaque matière.
Cours: 
Le professeur voit tous les cours existant, il peut modifier ou supprimer ses cours s’il le souhaite (s’il y a un changement de salle, ou une absence prévue par exemple). 
Créer un cours : formulaire pour créer un nouveau cours
Avis cours : Le professeur peut aller voir les avis et s’il y en a sur son cours (il pourrait les prendre en compte et améliorer les choses qui ne vont pas par exemple) 

Le liens administrateur http:\\localhost:8000\admin dont le liens est présent lors du login en tant qu'admin, permet quant à lui d'intéragir avec n'importe quelle entité.