[![English](/assets/en.png)](https://github.com/Nathalie-Verdavoir/deploy-heroku/blob/master/README.md)   [![Français](/assets/fr.png)](https://github.com/Nathalie-Verdavoir/deploy-heroku/blob/master/README.fr.md)


# Bienvenue dans l'outil de déploiement de Nat 


              
![Commandline](https://img.shields.io/badge/-commandline-%235391FE.svg?style=for-the-badge&logo=powershell&logoColor=white&colorB=purple)![Symfony](https://img.shields.io/badge/Symfony-white.svg?style=for-the-badge&logo=symfony&logoColor=black)[![heroku](https://img.shields.io/badge/Heroku-430098?logo=heroku&style=for-the-badge&logoColor=white)](https://dashboard.heroku.com/)


Le but est déployer un projet `Symfony` sur `Heroku` avec un `ligne de commande` personnalisée.


![logos](/assets/ban.png)


## 10 étapes :

![1](/assets/1.png) Prepare un bloc-notes 📝 pour copier les  `3 infos` pour répondre aux questions que l'outil te posera :)
   

![2](/assets/2.png) Il est necessaire d'avoir un projet  `Symfony` à déployer ;)
   

![3](/assets/3.png) Tu dois avoir un compte `Heroku` avec une [facturation](https://dashboard.heroku.com/account/billing) activée par carte de credit 💳(uniquement nécessaire pour l'accès à l'addon ClearDb qui hebergera la base de données, mais ça reste entièrement gratuit) 


![4](/assets/4.png) Depuis ton compte [compte](https://dashboard.heroku.com/account/) Heroku copie les infos suivantes :
- ton email attaché à ton [compte](https://dashboard.heroku.com/account/) `example @ email . com 📝`
- la clé API `8XXXXXXX-4YYY-4ZZZ-4AAA-12BBBBBBBBBBB 📝`
  

![5](/assets/5.png) Tu dois également créer une nouvelle application et copier son nom sur le block-notes `app-name-of-your-project 📝`
   

![6](/assets/6.png) Connecte ton compte Github et ton compte Heroku puis clique le bouton "search" pour faire apparaître la liste de tes dépôts, sélectionne celui du projet à déployer.

![link](/assets/link.PNG)


`CONSEIL: sur la même page, active la fonction de déploiement automatique, quand tu pousseras tes changements sur ta branche distante principale, tes changements apparaitront dans les secondes qui suivent sur ton projet en ligne  "enable automatic deploy"`


![7](/assets/7.png) Si ton ordinateur ne dispose pas des [commandes Heroku](https://devcenter.heroku.com/articles/heroku-cli) installe-les. 


![8](/assets/8.png) Lance la commande suivante à la racine de ton projet pour installer l'outil. 

<table><td><pre><code>
composer require nat/deploy
</code></pre></td></table>

![9](/assets/9.png) Puis quand tout est prêt lance la commande suivante et suis les instructions.

<table><td><pre><code>
php bin/console nat:heroku
</code></pre></td><td>
OU
</td><td><pre><code>
php bin/console nat:h
</code></pre></td></table>
Pendant le processus, il peut apparaître des erreurs ou des lenteurs, n'hésite pas à les faire remonter.

- Quand ça dit : `waiting for you to log in Browser`, ça a dû ouvrir un onglet dans ton navigateur et tu vas devoir te connecter à Heroku, quand c'est fait, reviens sur la console pour la suite du processus.
  

![10](/assets/10.png) A la fin, tu peux vérifer si : 
- [x] .htaccess est bien dans le dossier public
- [x] .env.php est bien à la racine du projet
- [x] Procfile est bien à la racine du projet aussi
- [x] ClearDb est active dans l'onglet "resources" de Heroku
- [x] les variables sont bien paramétrées dans l'onglet "settings" de Heroku (click reveal config vars)


Il ne reste qu'à migrer la base de données locale vers ClearDb (mysql workbench peut faire ça facilement).

Pour terminer de déployer, pousse ton projet avec les derniers fichiers créés (et déploie si tu n'as pas choisis de l'automatiser).



Tu peux supprimer l'outil en laçant la commande :

<table><td><pre><code>
composer remove nat/deploy
</code></pre></td></table>


Si tu as besoin d'aide, fais-le moi savoir ;)

<div class="badge-base LI-profile-badge" data-locale="fr_FR" data-size="medium" data-theme="dark" data-type="VERTICAL" data-vanity="nathalie-verdavoir" data-version="v1"><a class="badge-base__link LI-simple-link" href="https://fr.linkedin.com/in/nathalie-verdavoir?trk=profile-badge">Nathalie Verdavoir</a></div>
