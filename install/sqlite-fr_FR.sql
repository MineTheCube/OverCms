-- Adminer 4.1.0 SQLite 3 dump

DROP TABLE IF EXISTS "cms_pages";
CREATE TABLE "cms_pages" (
  "id" integer(16) NOT NULL,
  "title" text NOT NULL,
  "slug" text NOT NULL,
  "home" integer NOT NULL,
  "is_parent" integer NOT NULL,
  "parent_id" integer NOT NULL,
  "p_view" integer NOT NULL,
  "p_edit" integer NOT NULL,
  "content" text NOT NULL,
  "type" text NOT NULL,
  "type_data" text NOT NULL,
  "state" integer NOT NULL,
  "header" text NOT NULL,
  "header_data" text NOT NULL,
  "position" integer NOT NULL
);

INSERT INTO "cms_pages" ("id", "title", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (1,	'Admin',	'admin',	'0',	'0',	'0',	'0',	'0',	'Panel d''administration bientôt disponible..',	'custom',	'',	1,	'',	'',	'0');
INSERT INTO "cms_pages" ("id", "title", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (2,	'Erreur',	'erreur',	'0',	'0',	'0',	'0',	'0',	'',	'native',	'error',	1,	'SimpleHeader',	'{"desc": "Tout ne s''est pas passé comme prévu.."}',	'0');
INSERT INTO "cms_pages" ("id", "title", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (3,	'Connexion',	'connexion',	'0',	'0',	'0',	'0',	'0',	'',	'native',	'login',	1,	'SimpleHeader',	'',	'0');
INSERT INTO "cms_pages" ("id", "title", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (4,	'Mot de passe oublié',	'mot-de-passe-oublie',	'0',	'0',	'0',	'0',	'0',	'',	'native',	'recovery',	1,	'SimpleHeader',	'',	'0');
INSERT INTO "cms_pages" ("id", "title", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (5,	'Inscription',	'inscription',	'0',	'0',	'0',	'0',	'0',	'',	'native',	'register',	1,	'SimpleHeader',	'',	'0');
INSERT INTO "cms_pages" ("id", "title", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (6,	'Mon compte',	'mon-compte',	'0',	'0',	'0',	1,	'0',	'',	'native',	'account',	1,	'SimpleHeader',	'',	'0');
INSERT INTO "cms_pages" ("id", "title", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (7,	'Espace membre',	'membre',	'0',	'0',	'0',	'0',	2,	'',	'native',	'member',	1,	'SimpleHeader',	'',	'0');
INSERT INTO "cms_pages" ("id", "title", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (8,	'Déconnexion',	'deconnexion',	'0',	'0',	'0',	1,	'0',	'',	'native',	'logout',	1,	'SimpleHeader',	'',	'0');

DROP TABLE IF EXISTS "cms_public_pages";
CREATE TABLE "cms_public_pages" (
  `id` int(16) NOT NULL,
  `title` varchar(64) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `home` int(8) NOT NULL,
  `is_parent` int(8) NOT NULL,
  `parent_id` int(16) NOT NULL,
  `p_view` int(8) NOT NULL,
  `p_edit` int(8) NOT NULL,
  `content` text NOT NULL,
  `type` varchar(32) NOT NULL,
  `type_data` varchar(64) NOT NULL,
  `state` int(8) NOT NULL,
  `header` text NOT NULL,
  `header_data` text NOT NULL,
  `position` int(16) NOT NULL
);

INSERT INTO "cms_public_pages" ("id", "title", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (1,	'Accueil',	'accueil',	1,	'0',	'0',	'0',	'0',	'<div>
    <h2>Bienvenue !</h2>
    <br>
    <p><b>{NAME}</b> est un serveur multi-plateforme, avec des modes de jeux totalement inédits !</p>
    <p>Redécouvrez <b>Minecraft</b> comme vous ne l''avez jamais vu en vous connectant sur le serveur en 1.8.4 avec l''adresse <code>play.minecraft.net</code> !</p>
    <hr>
    <h3 style="margin-bottom: 5px;"><span class="glyphicon glyphicon-user"></span>&nbsp; Une communauté mature</h3>
    <p>Profitez d''une communauté mature et d''entraide, active sur le jeu, mais aussi sur le forum et sur le serveur vocal: vous vous sentirez bien dès les premières minutes !</p>
    <br>
    <br>
    <h3 style="margin-bottom: 5px;"><span class="glyphicon glyphicon-thumbs-up"></span>&nbsp; Un staff professionnel</h3>
    <p>{NAME}, c''est aussi un staff compétant, actif et surtout à votre écoute: ils seront toujours là pour vous aider en cas de problème.</p>
    <br>
    <br>
    <h3 style="margin-bottom: 5px;"><span class="glyphicon glyphicon-hdd"></span>&nbsp; Sans aucun lag</h3>
    <p>Nous sommes hébergés sur des machines puissantes, profitez de services performants pour pouvoir jouer en toute tranquilité !</p>
</div>',	'custom',	'',	'0',	'Slider',	'',	'0');
INSERT INTO "cms_public_pages" ("id", "title", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (2,	'Membres du staff',	'membres-du-staff',	'0',	1,	'0',	'0',	'0',	'',	'custom',	'',	'0',	'',	'',	1);
INSERT INTO "cms_public_pages" ("id", "title", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (3,	'Admins',	'admins',	'0',	'0',	2,	'0',	'0',	'<div>
    <h2>Les administrateurs</h2> 
    <br>
    <div class="media">
        <a class="pull-left" href="#">
            <img src="https://minotar.net/avatar/Mine_The_Cube/70.png" alt="Picture" class="media-object">
        </a>
        <div class="media-body">
            <h4 class="media-heading">Mine_The_Cube</h4> 
            <p>C''est le principal administrateur de ce serveur, il gère l''ensemble du serveur: configuration, promotion, et gère aussi le staff.</p>
        </div>
    </div>
    <hr>
    <div class="media">
        <a class="pull-left" href="#">
            <img src="https://minotar.net/avatar/Notch/70.png" alt="Picture" class="media-object">
        </a>
        <div class="media-body">
            <h4 class="media-heading">Notch</h4> 
            <p>Il est principalement réputé pour être le principal concepteur et développeur du jeu Minecraft, qui rencontre un très grand succès.</p>
        </div>
    </div>
    <hr>
    <div class="media">
        <a class="pull-left" href="#">
            <img src="https://minotar.net/avatar/jeb_/70.png" alt="Picture" class="media-object">
        </a>
        <div class="media-body">
            <h4 class="media-heading">jeb_</h4> 
            <p>Jeb, de son vrai nom Jens Bergensten est un employé de Mojang AB qui travaille actuellement sur Minecraft et Minecraft Pocket Edition.</p>
        </div>
    </div>
</div>',	'custom',	'',	'0',	'SimpleHeader',	'{"desc": "Ceux qui dirigent tout !"}',	2);
INSERT INTO "cms_public_pages" ("id", "title", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (4,	'Modérateurs',	'moderateurs',	'0',	'0',	2,	'0',	'0',	'<div>
    <h2>Les modérateurs</h2> 
    <br>
    <div class="media">
        <a class="pull-left" href="#">
            <img src="https://minotar.net/avatar/FVDisco/70.png" alt="Picture" class="media-object">
        </a>
        <div class="media-body">
            <h4 class="media-heading">FVDisco</h4> 
            <p>FVDisco est l''ingénieur du serveur, il créé des systèmes complexes et évolutif pour garantir le bon fonctionnement du serveur.</p>
        </div>
    </div>
    <hr>
    <div class="media">
        <a class="pull-left" href="#">
            <img src="https://minotar.net/avatar/groopo/70.png" alt="Picture" class="media-object">
        </a>
        <div class="media-body">
            <h4 class="media-heading">groopo</h4> 
            <p>Même s''il ne parle pas français, c''est un bon modérateur car il... enfin c''est un bon modérateur quand même !</p>
        </div>
    </div>
    <hr>
    <div class="media">
        <a class="pull-left" href="#">
            <img src="http://i.imgur.com/Th4MnmC.png" alt="Picture" class="media-object">
        </a>
        <div class="media-body">
            <h4 class="media-heading">On recrute</h4> 
            <p>Toi aussi tu peux faire partie du staff ! Pour cela il suffit de spammer un membre du staff pour qu''il te mette administrateur.</p>
        </div>
    </div>
</div>',	'custom',	'',	'0',	'SimpleHeader',	'{"desc": "Les modérateurs pour vous surveiller."}',	3);
INSERT INTO "cms_public_pages" ("id", "title", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (5,	'Nouveautés',	'nouveautes',	'0',	'0',	'0',	'0',	2,	'',	'plugin',	'Blog',	'0',	'SimpleHeader',	'{"desc": "Toutes les dernières nouvelles du serveur"}',	4);

DROP TABLE IF EXISTS "cms_sidebars";
CREATE TABLE "cms_sidebars" (
  "id" integer NOT NULL,
  "name" text NOT NULL,
  "plugin" text COLLATE 'NOCASE' NOT NULL,
  "position" integer NOT NULL,
  "data" text NOT NULL
);

INSERT INTO "cms_sidebars" ("id", "name", "plugin", "position", "data") VALUES (1,	'Status du serveur',	'MCStatus',	1,	'{"ip": "play.onecraft.net", "port": "25565"}');
INSERT INTO "cms_sidebars" ("id", "name", "plugin", "position", "data") VALUES (2,	'TeamSpeak 3',	'TeamspeakConnect',	2,	'{"address": "ts.onecraft.net"}');

DROP TABLE IF EXISTS "cms_user_status";
CREATE TABLE "cms_user_status" (
  `id` int(16) NOT NULL,
  `type` varchar(16) NOT NULL,
  `author_id` int(16) NOT NULL,
  `profil_id` int(16) NOT NULL,
  `content` text NOT NULL,
  `state` int(8) NOT NULL,
  `date` int(16) NOT NULL
);

INSERT INTO "cms_user_status" ("id", "type", "author_id", "profil_id", "content", "state", "date") VALUES (1,	'comment',	2,	2,	'Cet espace est 100% fonctionnel et agréable à regarder.',	'0',	1415642792);

DROP TABLE IF EXISTS "cms_users";
CREATE TABLE "cms_users" (
  "id" integer NOT NULL,
  "username" text COLLATE 'NOCASE' NOT NULL,
  "password" text NOT NULL,
  "email" text NOT NULL,
  "date_creation" integer NOT NULL,
  "permission" integer NOT NULL,
  "picture_url" text NOT NULL,
  "profil" text NOT NULL,
  "token" text NOT NULL
);

INSERT INTO "cms_users" ("id", "username", "password", "email", "date_creation", "permission", "picture_url", "profil", "token") VALUES (1,	'admin',	'515b0a7f27833a052a4e3385b1787cbe70c9a868347cae9fe5cc836defdd0294',	'contact@onecraft.net',	1408459504,	9,	'http://icons.iconarchive.com/icons/chrisl21/minecraft/256/Tnt-icon.png',	'{"gender":"man","country":"France","birthday":"1981-03-20","city":"Paris"}',	'');
INSERT INTO "cms_users" ("id", "username", "password", "email", "date_creation", "permission", "picture_url", "profil", "token") VALUES (2,	'Notch',	'9d98b2bc87f494c024603d2be437c43046c3f3fa5bc7388e0812947567629323',	'notch@minecraft.net',	1408836614,	1,	'http://www.appsfuze.com/static/images/apps/a/b/a/aba976f6-fb8e-e011-986b-78e7d1fa76f8.png',	'{"city":"New York","country":"Su\u00e8de","birthday":"1951-02-28","gender":"man"}',	'');
INSERT INTO "cms_users" ("id", "username", "password", "email", "date_creation", "permission", "picture_url", "profil", "token") VALUES (3,	'jeb_',	'9d98b2bc87f494c024603d2be437c43046c3f3fa5bc7388e0812947567629323',	'jeb_@minecraft.net',	1411849545,	1,	'http://www.poweredbyredstone.net/wp-content/uploads/2012/07/jeb_twitter_reasonably_small.jpg',	'',	'');

DROP TABLE IF EXISTS "plugin_blog_comments";
CREATE TABLE "plugin_blog_comments" (
  `id` int(16) NOT NULL,
  `type` varchar(16) NOT NULL,
  `author_id` int(16) NOT NULL,
  `post_id` int(16) NOT NULL,
  `content` text NOT NULL,
  `state` int(8) NOT NULL,
  `date` int(16) NOT NULL
);

INSERT INTO "plugin_blog_comments" ("id", "type", "author_id", "post_id", "content", "state", "date") VALUES (1,	'comment',	2,	2,	'C''est quoi le titre de la musique stp ?',	'0',	1411942164);
INSERT INTO "plugin_blog_comments" ("id", "type", "author_id", "post_id", "content", "state", "date") VALUES (2,	'comment',	3,	2,	'Darude - Sandstorm',	'0',	1411952164);
INSERT INTO "plugin_blog_comments" ("id", "type", "author_id", "post_id", "content", "state", "date") VALUES (3,	'comment',	2,	3,	'Super article comme toujours !',	'0',	1415641273);

DROP TABLE IF EXISTS "plugin_blog_posts";
CREATE TABLE "plugin_blog_posts" (
  `id` int(16) NOT NULL,
  `title` varchar(64) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `bbcode` text NOT NULL,
  `html` text NOT NULL,
  `picture` text NOT NULL,
  `author_id` int(16) NOT NULL,
  `date` int(16) NOT NULL,
  `state` int(8) NOT NULL
);

INSERT INTO "plugin_blog_posts" ("id", "title", "slug", "bbcode", "html", "picture", "author_id", "date", "state") VALUES (1,	'Ce n''est que le début',	'ce-nest-que-le-debut',	'[h2]Novembre 1998[/h2]

Au début de novembre 1998 le gouvernement de transition indonésien convoque une séance extraordinaire de afin de préparer les prochaines élections et discuter de l''agenda politique à mettre en place.
L''agitation étudiante resurgit car les étudiants ne reconnaissent pas le gouvernement de B. J. Habibie et ne font pas confiance aux membres de la DPR/MPR de l''Ordre nouveau.

[h2]Une session extraordinaire[/h2]

La population et les étudiants refusent la session extraordinaire et s''opposent à la double fonction de l''armée indonésienne. Tout au long de cette séance extraordinaire la population se joint aux étudiants participant aux manifestation quotidiennes dans les rues de Jakarta et d''autres grandes villes du pays.',	'<h2>Novembre 1998</h2>
<br />
Au début de novembre 1998 le gouvernement de transition indonésien convoque une séance extraordinaire de afin de préparer les prochaines élections et discuter de l''agenda politique à mettre en place.<br />
L''agitation étudiante resurgit car les étudiants ne reconnaissent pas le gouvernement de B. J. Habibie et ne font pas confiance aux membres de la DPR/MPR de l''Ordre nouveau.<br />
<br />
<h2>Une session extraordinaire</h2>
<br />
La population et les étudiants refusent la session extraordinaire et s''opposent à la double fonction de l''armée indonésienne. Tout au long de cette séance extraordinaire la population se joint aux étudiants participant aux manifestation quotidiennes dans les rues de Jakarta et d''autres grandes villes du pays.',	'',	1,	1409828760,	'0');
INSERT INTO "plugin_blog_posts" ("id", "title", "slug", "bbcode", "html", "picture", "author_id", "date", "state") VALUES (2,	'Et ça continue !',	'et-ca-continue',	'[youtube]MmB9b5njVbA[/youtube]

Environ un mois qu''on m''arracherait facilement le foudroyeur. Veuf ou non, évidemment. Prosateur strict et toujours à se quereller et s''injurier. Ce don me fit quelque peine, beaucoup de points...',	'<div style="position: relative;display: block;height: 0;padding: 0;overflow: hidden;padding-bottom: 56.25%;"><iframe style="position: absolute;top: 0;bottom: 0;left: 0;width: 100%;height: 100%;border: 0;" src="//www.youtube.com/embed/MmB9b5njVbA" frameborder="0" allowfullscreen></iframe></div><br />
<br />
Environ un mois qu''on m''arracherait facilement le foudroyeur. Veuf ou non, évidemment. Prosateur strict et toujours à se quereller et s''injurier. Ce don me fit quelque peine, beaucoup de points...',	'',	1,	1411666320,	'0');
INSERT INTO "plugin_blog_posts" ("id", "title", "slug", "bbcode", "html", "picture", "author_id", "date", "state") VALUES (3,	'Quelques nouveautés..',	'quelques-nouveautes',	'Incontinent, qui plus est, la violette mignonne se dresse sur ses pattes... Répétons-le sans cesse : il n''allait pas sonner, oh, s''il avait eu cette réaction...
Était-ce là du mysticisme, ont généralisé l''usage de ses membres, appelle à lui:
[list][*]Arts[/*]
[*]Géographie[/*]
[*]Histoire[/*]
[*]Sciences[/*]
[*]Société[/*]
[*]Sport[/*]
[*]Technologies[/*][/list]

 [center][img]http://oyster.ignimgs.com/mediawiki/apis.ign.com/minecraft/c/cc/Sheep.png[/img][/center]

Languissamment intéressé par les messes basses qui s''échangeaient autour de lui l''aisance des petits. Papa ne comprendra guère toute cette peine à ses trente mille francs ? Amassés pendant des mois, nous éprouverions s''il était là.',	'Incontinent, qui plus est, la violette mignonne se dresse sur ses pattes... Répétons-le sans cesse : il n''allait pas sonner, oh, s''il avait eu cette réaction...<br />
Était-ce là du mysticisme, ont généralisé l''usage de ses membres, appelle à lui:<br />
<ul><li>Arts</li>
<li>Géographie</li>
<li>Histoire</li>
<li>Sciences</li>
<li>Société</li>
<li>Sport</li>
<li>Technologies</li></ul><br />
<br />
 <p style="text-align:center;margin: 0;"><img src="http://oyster.ignimgs.com/mediawiki/apis.ign.com/minecraft/c/cc/Sheep.png" alt=" " /></p>
<br />
Languissamment intéressé par les messes basses qui s''échangeaient autour de lui l''aisance des petits. Papa ne comprendra guère toute cette peine à ses trente mille francs ? Amassés pendant des mois, nous éprouverions s''il était là.',	'',	1,	1411920540,	'0');

-- 