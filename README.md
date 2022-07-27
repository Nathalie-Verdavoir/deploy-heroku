# Welcome in the deploying tools

`made by Nat` 
              

The goal is to deploy a `Symfony` project on `Heroku` with custom `command line`.


![logos](/assets/ban.png)


## 10 Steps :

![1](/assets/1.png) Prepare a notepad to paste the `3 infos` to answer my questions :)
   

![2](/assets/2.png) You must have a `Symfony` project to deploy ;)
   

![3](/assets/3.png) You must have a `Heroku` account with [billing](https://dashboard.heroku.com/account/billing) enabled by a credit card 💳(needed by ClearDb/mySql but don't worry it's free) 


![4](/assets/4.png) Go in your Heroku [account](https://dashboard.heroku.com/account/) to get your credentials :
- your email attached to your [account](https://dashboard.heroku.com/account/) `example @ email . com 📝`
- your API Key `8XXXXXXX-4YYY-4ZZZ-4AAA-12BBBBBBBBBBB 📝`
  

![5](/assets/5.png) You must create an new app on Heroku and copy the `app-name-of-your-project 📝`
   

![6](/assets/6.png) Connect your Github and your Heroku accounts and click the "search" button to show your repositories list, then select the good one in this list

![link](/assets/link.PNG)


`TIP: On the same page, choose the automatic deploy, when you push on Github, it quickly serves your changes on Heroku enable automatic deploy`


![7](/assets/7.png) Install [Heroku CLI](https://devcenter.heroku.com/articles/heroku-cli) if you've never done it. 


![8](/assets/8.png) Run this command in your project dir 

<table><td><pre><code>
composer require nat/deploy
</code></pre></td></table>

![9](/assets/9.png) Then run this command and follow the instructions

<table><td><pre><code>
php bin/console nat:heroku
</code></pre></td><td>
OR
</td><td><pre><code>
php bin/console nat:h
</code></pre></td></table>
During the process, it may appear some errors or lags :

- When it is saying that is `waiting for you to log in Browser`, it should open your browser and you will have to click login and enter your credentials in the form, then come back to your console to continue the process.
  

![10](/assets/10.png) Now you can check this : 
- [x] .htaccess is in public directory
- [x] .env.php is at root of you project
- [x] Procfile is at root of you project
- [x] ClearDb is enabled in Heroku Resources
- [x] APP_ENV is set in Heroku Settings (click reveal config vars)
- [x] APP_SECRET is set too in the same Settings
- [x] DATABASE_URL is equal to CLEARDB_DATABASE_URL
- [x] If you have some of them, other specific vars of your project are set as well (CORS_ALLOW_ORIGIN, MAILER_DSN, etc...). If they are not set, please set them by yourself. 


Now you can export your local database to import it in you clearDb (adobe mysql workbench is fine to do it) then  Push your files in your github (and Heroku if you didn't enable the automatic deploy).


You can delete this tool by running 

<table><td><pre><code>
composer remove nat/deploy
</code></pre></td></table>


If you need help, let me know ;)

<div class="badge-base LI-profile-badge" data-locale="fr_FR" data-size="medium" data-theme="dark" data-type="VERTICAL" data-vanity="nathalie-verdavoir" data-version="v1"><a class="badge-base__link LI-simple-link" href="https://fr.linkedin.com/in/nathalie-verdavoir?trk=profile-badge">Nathalie Verdavoir</a></div>
