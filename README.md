# Welcome in the deploying tools

`made by Nat` 
              

The goal is to deploy a `Symfony` project on `Heroku` with custom `command line`.


![logos](/assets/ban.png)


## 10 Steps :

1. Prepare a notepad to paste the `3 infos` to answer my questions :)
   

2. You must have a `Symfony` project to deploy ;)
   

3. You must have a `Heroku` account with [billing](https://dashboard.heroku.com/account/billing) enabled by a credit card 💳(needed by ClearDb/mySql but don't worry it's free) 


4. Go in your Heroku [account](https://dashboard.heroku.com/account/) to get your credentials :
- your email attached to your [account](https://dashboard.heroku.com/account/) `example @ email . com 📝`
- your API Key `8XXXXXXX-4YYY-4ZZZ-4AAA-12BBBBBBBBBBB 📝`
  

5. You must create an new app on Heroku and copy the `app-name-of-your-project 📝`
   

6. Connect your Github and your Heroku accounts
link
![link](/assets/link.PNG)


`TIP: On the same page, choose the automatic deploy, when you push on Github, it quickly serves your changes on Heroku enable automatic deploy`


7. Install [Heroku CLI](https://devcenter.heroku.com/articles/heroku-cli)


8.  Run this command in your project dir 


<table><td><pre><code>
composer require nat/deploy
</code></pre></td></table>

8. Then run this command and follow the instructions


<table><td><pre><code>
php bin/console nat:heroku
</code></pre></td><td>
OR
</td><td><pre><code>
php bin/console nat:h
</code></pre></td></table>


 Push your files in your github (and Heroku if you didn't enable the automatic deploy).

 
If you need help, let me know ;)

<div class="badge-base LI-profile-badge" data-locale="fr_FR" data-size="medium" data-theme="dark" data-type="VERTICAL" data-vanity="nathalie-verdavoir" data-version="v1"><a class="badge-base__link LI-simple-link" href="https://fr.linkedin.com/in/nathalie-verdavoir?trk=profile-badge">Nathalie Verdavoir</a></div>