-- Adminer 4.1.0 SQLite 3 dump

DROP TABLE IF EXISTS "cms_extensions";
CREATE TABLE "cms_extensions" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "type" text COLLATE 'NOCASE' NOT NULL,
  "name" text COLLATE 'NOCASE' NOT NULL,
  "config" text NOT NULL,
  "events" text NOT NULL
);

INSERT INTO "cms_extensions" ("id", "type", "name", "config", "events") VALUES (1,  'plugin',   'SimpleHeader', '{}',   '');
INSERT INTO "cms_extensions" ("id", "type", "name", "config", "events") VALUES (2,  'plugin',   'Slider',   '{}',   '');
INSERT INTO "cms_extensions" ("id", "type", "name", "config", "events") VALUES (3,  'plugin',   'MC Status',    '{}',   'onPluginGetSidebarConfig;onPluginSaveSidebarConfig');
INSERT INTO "cms_extensions" ("id", "type", "name", "config", "events") VALUES (5,  'plugin',   'Blog', '{}',   'onPluginInstall;onPluginUninstall;onUserDelete');
INSERT INTO "cms_extensions" ("id", "type", "name", "config", "events") VALUES (15, 'plugin',   'JSONAPI',  '{"servers":[{"name":"Onecraft","ip":"127.0.0.1","port":"20070","username":"JSON_USER","password":"JSON_PWD"}]}',   '');
INSERT INTO "cms_extensions" ("id", "type", "name", "config", "events") VALUES (18, 'plugin',   'TeamspeakConnect', '{}',   '');
INSERT INTO "cms_extensions" ("id", "type", "name", "config", "events") VALUES (23, 'plugin',   'MineStats',    '{}',   '');
INSERT INTO "cms_extensions" ("id", "type", "name", "config", "events") VALUES (27, 'plugin',   'Test', '{}',   '');
INSERT INTO "cms_extensions" ("id", "type", "name", "config", "events") VALUES (28, 'plugin',   'Maintenance',  '{"enabled":"disabled","message":"Site en maintenance."}',  'onNewRequest');

DROP TABLE IF EXISTS "cms_pages";
CREATE TABLE "cms_pages" (
  `id` int(16) NOT NULL,
  `title` varchar(64) NOT NULL,
  `desc` varchar(64) NOT NULL,
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
  `header` varchar(64) NOT NULL,
  `header_data` text NOT NULL,
  `position` int(16) NOT NULL
);

INSERT INTO "cms_pages" ("id", "title", "desc", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (-1, 'Admin',    '', 'admin',    '0',    '0',    '0',    4,  4,  '', 'custom',   '', 1,  '', '', '0');
INSERT INTO "cms_pages" ("id", "title", "desc", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (-2, 'Erreur',   '', 'erreur',   '0',    '0',    '0',    '0',    3,  '', 'native',   'error',    1,  'SimpleHeader', '', '0');
INSERT INTO "cms_pages" ("id", "title", "desc", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (-3, 'Connexion',    '', 'connexion',    '0',    '0',    '0',    '0',    3,  '', 'native',   'login',    1,  'SimpleHeader', '', '0');
INSERT INTO "cms_pages" ("id", "title", "desc", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (-4, 'Mot de passe oublié',  '', 'mot-de-passe-oublie',  '0',    '0',    '0',    '0',    3,  '', 'native',   'recovery', 1,  'SimpleHeader', '', '0');
INSERT INTO "cms_pages" ("id", "title", "desc", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (-5, 'Inscription',  '', 'inscription',  '0',    '0',    '0',    '0',    3,  '', 'native',   'register', 1,  'SimpleHeader', '', '0');
INSERT INTO "cms_pages" ("id", "title", "desc", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (-6, 'Mon compte',   '', 'mon-compte',   '0',    '0',    '0',    1,  3,  '', 'native',   'account',  1,  'SimpleHeader', '', '0');
INSERT INTO "cms_pages" ("id", "title", "desc", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (-7, 'Espace membre',    '', 'membre',   '0',    '0',    '0',    '0',    3,  '', 'native',   'member',   1,  'SimpleHeader', '', '0');
INSERT INTO "cms_pages" ("id", "title", "desc", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (-8, 'Déconnexion',  '', 'deconnexion',  '0',    '0',    '0',    1,  3,  '', 'native',   'logout',   1,  'SimpleHeader', '', '0');

DROP TABLE IF EXISTS "cms_public_pages";
CREATE TABLE "cms_public_pages" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "title" text NOT NULL,
  "desc" text NOT NULL,
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

INSERT INTO "cms_public_pages" ("id", "title", "desc", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (1,   'Accueil',  '', 'accueil',  1,  '0',    '0',    '0',    3,  '<div>
<h2>Bienvenue !</h2>
&nbsp;

<p><b>OneCraft</b> est un serveur multi-plateforme, avec des modes de jeux totalement in&eacute;dits !</p>

<p>Red&eacute;couvrez <b>Minecraft</b> comme vous ne l&#39;avez jamais vu en vous connectant sur le serveur en 1.7.9 avec l&#39;adresse <code>play.onecraft.net</code> !</p>

<hr />
<h3 style="margin-bottom: 5px;"><span class="glyphicon glyphicon-user"></span>&nbsp; Une communaut&eacute; mature</h3>

<p>Profitez d&#39;une communaut&eacute; mature et d&#39;entraide, active sur le jeu, mais aussi sur le forum et sur le serveur vocal: vous vous sentirez bien d&egrave;s les premi&egrave;res minutes !</p>

<p>&nbsp;</p>

<p>&nbsp;</p>

<h3 style="margin-bottom: 5px;">&nbsp;<span class="glyphicon glyphicon-thumbs-up"></span> Un staff professionnel</h3>

<p>OneCraft, c&#39;est aussi un staff comp&eacute;tant, actif et surtout &agrave; votre &eacute;coute: ils seront toujours l&agrave; pour vous aider en cas de probl&egrave;me.</p>

<p>&nbsp;</p>

<p>&nbsp;</p>

<h3 style="margin-bottom: 5px;"><span class="glyphicon glyphicon-hdd"></span>&nbsp; Sans aucun lag</h3>

<p>Nous sommes h&eacute;berg&eacute;s sur des machines puissantes, profitez de services performants pour pouvoir jouer en toute tranquilit&eacute; !</p>
</div>
',  'custom',   '', '0',    'Slider',   '{"slides":[{"title":"Bienvenue sur OneCraft !","desc":"Un serveur comportant de nombreux types de jeux..","image":"1"},{"title":"Un slider d''exception !","desc":"Avec des lettres, des mots et m\u00eame du texte !","image":"2"},{"title":"A partir de 12900\u20ac","desc":"Avec condition de reprise, bonus \u00e9cologique inclus.","image":"7"}]}',  1);
INSERT INTO "cms_public_pages" ("id", "title", "desc", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (2,   'Membres du staff', '', 'membres-du-staff', '0',    1,  '0',    '0',    3,  '', 'custom',   '', '0',    '', '', 3);
INSERT INTO "cms_public_pages" ("id", "title", "desc", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (3,   'Admins',   'Respectez les !',  'admins',   '0',    '0',    2,  '0',    3,  '<h2><span class="glyphicon glyphicon-heart-empty" style="color:#000000;"></span>&nbsp;Les administrateurs</h2>

<p>&nbsp;</p>

<p>&nbsp;</p>

<h4><img alt="" src="https://minotar.net/avatar/Mine_The_Cube/70.png" style="margin-left: 10px; margin-right: 10px; float: left;" />MTC</h4>

<p>C&#39;est le principal administrateur de ce serveur, il g&egrave;re l&#39;ensemble du serveur: configuration, promotion, et g&egrave;re aussi le staff.</p>

<hr />
<h4><img alt="" src="https://minotar.net/avatar/Notch/70.png" style="margin-left: 10px; margin-right: 10px; float: left;" />Notch</h4>

<p>Il est principalement r&eacute;put&eacute; pour &ecirc;tre le principal concepteur et d&eacute;veloppeur du jeu Minecraft, qui rencontre un tr&egrave;s grand succ&egrave;s.</p>

<hr />
<h4><img alt="" src="https://minotar.net/avatar/jeb_/70.png" style="margin-left: 10px; margin-right: 10px; float: left;" />jeb_</h4>

<p>Jeb, de son vrai nom Jens Bergensten est un employ&eacute; de Mojang AB qui travaille actuellement sur Minecraft et Minecraft Pocket Edition.</p>
',  'custom',   '', '0',    'SimpleHeader', '', 4);
INSERT INTO "cms_public_pages" ("id", "title", "desc", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (4,   'Modérateurs',  'Toujours la pour vous aider.', 'moderateurs',  '0',    '0',    2,  '0',    3,  '<h2><span class="glyphicon glyphicon-flag" style="color:#000000;"></span>&nbsp;Les mod&eacute;rateurs</h2>

<p>&nbsp;</p>

<p>&nbsp;</p>

<h3><img alt="" src="https://minotar.net/avatar/FVDisco/70.png" style="float: left; margin-left: 10px; margin-right: 10px;" />FVDisco</h3>

<p>FVDisco est l&#39;ing&eacute;nieur du serveur, il cr&eacute;&eacute; des syst&egrave;mes complexes et &eacute;volutif pour garantir le bon fonctionnement du serveur.</p>

<hr />
<h3><img alt="" src="https://minotar.net/avatar/groopo/70.png" style="float: left; margin-left: 10px; margin-right: 10px;" />groopo</h3>

<p>M&ecirc;me s&#39;il ne parle pas fran&ccedil;ais, c&#39;est un bon mod&eacute;rateur car il... enfin c&#39;est un bon mod&eacute;rateur quand m&ecirc;me !</p>

<hr />
<h3><img alt="" src="http://i.imgur.com/Th4MnmC.png" style="float: left; margin-left: 10px; margin-right: 10px;" />On recrute</h3>

<p>Toi aussi tu peux faire partie du staff ! Pour cela il suffit de spammer un membre du staff pour qu&#39;il te mette administrateur.</p>
',  'custom',   '', '0',    'SimpleHeader', '', 5);
INSERT INTO "cms_public_pages" ("id", "title", "desc", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (5,   'Nouveautés',   'Ne ratez aucune nouvelle !',   'nouveautes',   '0',    '0',    '0',    '0',    3,  '', 'plugin',   'Blog', '0',    'SimpleHeader', '', 6);
INSERT INTO "cms_public_pages" ("id", "title", "desc", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (18,  'Nous rejoindre',   'Venez jouer avec nous !',  'nous-rejoindre',   '0',    '0',    '0',    '0',    3,  '<h1><span class="glyphicon glyphicon-fire" style="color:rgb(0, 0, 0);"></span>&nbsp;N&#39;attendez plus !</h1>

<p>&nbsp;</p>

<p>Venez profiter d&#39;un serveur unique en son&nbsp;genre: OneCraft PVP/Factions ! Cr&eacute;ez votre clan, prosp&eacute;rez et devenez le joueur le plus influent du serveur !</p>

<hr />
<p>Rejoignez nous d&egrave;s maintenant avec l&#39;IP suivante, sans launcher:</p>

<pre>
play.onecraft.net</pre>

<p>&nbsp;</p>

<p><img alt="" src="/files/images/large%20banner-6eca1a431a.png" /></p>
',  'custom',   '', '0',    'SimpleHeader', '', 2);
INSERT INTO "cms_public_pages" ("id", "title", "desc", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (25,  'Typography',   '', 'typography',   '0',    '0',    27, '0',    3,  '<h1>Head 1</h1>

<h2>Head&nbsp;2</h2>

<h3>Head 3</h3>

<h4>Head 4</h4>

<p>&nbsp;</p>

<pre>
Format</pre>

<p>&nbsp;</p>

<h2 style="font-style:italic;">Italic</h2>

<h3 style="color:#aaa;font-style:italic;">Subtitle</h3>

<p><cite>Paragraph</cite></p>

<p>&nbsp;</p>

<table class="table table-bordered">
    <thead>
        <tr>
            <th scope="col">A.1</th>
            <th scope="col">A.2</th>
            <th scope="col">A.3</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>B.1</td>
            <td>B.2</td>
            <td>B.3</td>
        </tr>
        <tr>
            <td>C.1</td>
            <td>C.2</td>
            <td>C.3</td>
        </tr>
    </tbody>
</table>

<p style="text-align: center;">&nbsp;</p>

<p style="text-align: center;"><img alt="" src="/files/images/onecraft%20-%20250x250-246c83bf78.png" /></p>
',  'custom',   '', '0',    'SimpleHeader', '', 8);
INSERT INTO "cms_public_pages" ("id", "title", "desc", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (26,  'Forum',    '', 'forum',    '0',    '0',    27, '0',    3,  'target_blank', 'link', 'http://onecraft.net/forum/',   '0',    'SimpleHeader', '', 10);
INSERT INTO "cms_public_pages" ("id", "title", "desc", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (27,  'Test', '', 'test', '0',    1,  '0',    '0',    3,  '', 'custom',   '', '0',    'SimpleHeader', '', 7);
INSERT INTO "cms_public_pages" ("id", "title", "desc", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (28,  'MineStats',    'Stats du serveur via JSONAPI', 'minestats',    '0',    '0',    27, '0',    3,  '', 'plugin',   'MineStats',    '0',    'SimpleHeader', '', 9);
INSERT INTO "cms_public_pages" ("id", "title", "desc", "slug", "home", "is_parent", "parent_id", "p_view", "p_edit", "content", "type", "type_data", "state", "header", "header_data", "position") VALUES (29,  'Test', '', 'test-2',   '0',    '0',    '0',    '0',    3,  '', 'plugin',   'Test', '0',    'SimpleHeader', '', 11);

DROP TABLE IF EXISTS "cms_sidebar";
CREATE TABLE "cms_sidebar" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" text NOT NULL,
  "plugin" text COLLATE 'NOCASE' NOT NULL,
  "position" integer NOT NULL,
  "data" text NOT NULL
);

INSERT INTO "cms_sidebar" ("id", "name", "plugin", "position", "data") VALUES (2,   'TeamSpeak 3',  'TeamspeakConnect', 3,  '{"ip":"ts.onecraft.net"}');
INSERT INTO "cms_sidebar" ("id", "name", "plugin", "position", "data") VALUES (3,   'Statut du serveur',    'MC Status',    1,  '{"ip":"play.onecraft.net","port":""}');

DROP TABLE IF EXISTS "cms_user_status";
CREATE TABLE "cms_user_status" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "type" text NOT NULL,
  "author_id" integer NOT NULL,
  "profil_id" integer NOT NULL,
  "content" text NOT NULL,
  "state" integer NOT NULL,
  "date" integer NOT NULL
);

INSERT INTO "cms_user_status" ("id", "type", "author_id", "profil_id", "content", "state", "date") VALUES (1,   'comment',  2,  2,  'Cet espace est 100% fonctionnel et agréable à regarder.',  '0',    1415642792);
INSERT INTO "cms_user_status" ("id", "type", "author_id", "profil_id", "content", "state", "date") VALUES (3,   'comment',  12, 12, 'Bonjour à tous !', '0',    1425566991);

DROP TABLE IF EXISTS "cms_users";
CREATE TABLE "cms_users" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "username" text COLLATE 'NOCASE' NOT NULL,
  "password" text NOT NULL,
  "email" text COLLATE 'NOCASE' NOT NULL,
  "date_creation" integer NOT NULL,
  "permission" integer NOT NULL,
  "picture_url" text NOT NULL,
  "profil" text NOT NULL,
  "token" text NOT NULL
);

INSERT INTO "cms_users" ("id", "username", "password", "email", "date_creation", "permission", "picture_url", "profil", "token") VALUES (1, 'admin',    '515b0a7f27833a052a4e3385b1787cbe70c9a868347cae9fe5cc836defdd0294', 'contact@onecraft.net', 1408459504, 9,  'http://icons.iconarchive.com/icons/chrisl21/minecraft/256/Tnt-icon.png',   '{"country":"France","birthday":"1981-03-20","city":"Paris","gender":"man"}',   '');
INSERT INTO "cms_users" ("id", "username", "password", "email", "date_creation", "permission", "picture_url", "profil", "token") VALUES (2, 'Notch',    '9d98b2bc87f494c024603d2be437c43046c3f3fa5bc7388e0812947567629323', 'notch@minecraft.net',  1408836614, 1,  'http://www.appsfuze.com/static/images/apps/a/b/a/aba976f6-fb8e-e011-986b-78e7d1fa76f8.png',    '{"city":"New York","country":"Su\u00e8de","birthday":"1951-02-28","gender":"man"}',    '');
INSERT INTO "cms_users" ("id", "username", "password", "email", "date_creation", "permission", "picture_url", "profil", "token") VALUES (3, 'jeb_', '9d98b2bc87f494c024603d2be437c43046c3f3fa5bc7388e0812947567629323', 'jeb_@minecraft.net',   1411849545, 1,  'http://www.poweredbyredstone.net/wp-content/uploads/2012/07/jeb_twitter_reasonably_small.jpg', '', '');

DROP TABLE IF EXISTS "plugin_blog_comments";
CREATE TABLE "plugin_blog_comments" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "type" text NOT NULL,
  "author_id" integer NOT NULL,
  "post_id" integer NOT NULL,
  "content" text NOT NULL,
  "state" integer NOT NULL,
  "date" integer NOT NULL
);

INSERT INTO "plugin_blog_comments" ("id", "type", "author_id", "post_id", "content", "state", "date") VALUES (1,    'comment',  2,  2,  'C''est quoi le titre de la musique stp ?', '0',    1411942164);
INSERT INTO "plugin_blog_comments" ("id", "type", "author_id", "post_id", "content", "state", "date") VALUES (2,    'comment',  3,  2,  'Darude - Sandstorm',   '0',    1411952164);
INSERT INTO "plugin_blog_comments" ("id", "type", "author_id", "post_id", "content", "state", "date") VALUES (3,    'comment',  2,  3,  'Super article comme toujours !',   '0',    1415641273);
INSERT INTO "plugin_blog_comments" ("id", "type", "author_id", "post_id", "content", "state", "date") VALUES (4,    'comment',  1,  3,  'J''adore le mouton !', '0',    1425311330);

DROP TABLE IF EXISTS "plugin_blog_posts";
CREATE TABLE "plugin_blog_posts" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "title" text NOT NULL,
  "slug" text NOT NULL,
  "bbcode" text NOT NULL,
  "html" text NOT NULL,
  "picture" text NOT NULL,
  "author_id" integer NOT NULL,
  "date" integer NOT NULL,
  "state" integer NOT NULL
);

INSERT INTO "plugin_blog_posts" ("id", "title", "slug", "bbcode", "html", "picture", "author_id", "date", "state") VALUES (1,   'Ce n''est que le début',   'ce-nest-que-le-debut', '[h2]Novembre 1998[/h2]

Au d&eacute;but de novembre 1998 le gouvernement de transition indon&eacute;sien convoque une s&eacute;ance extraordinaire de afin de pr&eacute;parer les prochaines &eacute;lections et discuter de l&#039;agenda politique &agrave; mettre en place.
L&#039;agitation &eacute;tudiante resurgit car les &eacute;tudiants ne reconnaissent pas le gouvernement de B. J. Habibie et ne font pas confiance aux membres de la DPR/MPR de l&#039;Ordre nouveau.

[h2]Une session extraordinaire[/h2]

La population et les &eacute;tudiants refusent la session extraordinaire et s&#039;opposent &agrave; la double fonction de l&#039;arm&eacute;e indon&eacute;sienne. Tout au long de cette s&eacute;ance extraordinaire la population se joint aux &eacute;tudiants participant aux manifestation quotidiennes dans les rues de Jakarta et d&#039;autres grandes villes du pays.',   '<h2>Novembre 1998</h2>
<br />
Au d&eacute;but de novembre 1998 le gouvernement de transition indon&eacute;sien convoque une s&eacute;ance extraordinaire de afin de pr&eacute;parer les prochaines &eacute;lections et discuter de l&#039;agenda politique &agrave; mettre en place.<br />
L&#039;agitation &eacute;tudiante resurgit car les &eacute;tudiants ne reconnaissent pas le gouvernement de B. J. Habibie et ne font pas confiance aux membres de la DPR/MPR de l&#039;Ordre nouveau.<br />
<br />
<h2>Une session extraordinaire</h2>
<br />
La population et les &eacute;tudiants refusent la session extraordinaire et s&#039;opposent &agrave; la double fonction de l&#039;arm&eacute;e indon&eacute;sienne. Tout au long de cette s&eacute;ance extraordinaire la population se joint aux &eacute;tudiants participant aux manifestation quotidiennes dans les rues de Jakarta et d&#039;autres grandes villes du pays.',   'http://i.imgur.com/FTu4als.png',   1,  1409828760, '0');
INSERT INTO "plugin_blog_posts" ("id", "title", "slug", "bbcode", "html", "picture", "author_id", "date", "state") VALUES (2,   'Et ça continue !', 'et-ca-continue',   '[youtube]MmB9b5njVbA[/youtube]

Environ un mois qu&#039;on m&#039;arracherait facilement le foudroyeur. Veuf ou non, &eacute;videmment. Prosateur strict et toujours &agrave; se quereller et s&#039;injurier. Ce don me fit quelque peine, beaucoup de points...', '<div style="position: relative;display: block;height: 0;padding: 0;overflow: hidden;padding-bottom: 56.25%;"><iframe style="position: absolute;top: 0;bottom: 0;left: 0;width: 100%;height: 100%;border: 0;" src="//www.youtube.com/embed/MmB9b5njVbA" frameborder="0" allowfullscreen></iframe></div><br />
<br />
Environ un mois qu&#039;on m&#039;arracherait facilement le foudroyeur. Veuf ou non, &eacute;videmment. Prosateur strict et toujours &agrave; se quereller et s&#039;injurier. Ce don me fit quelque peine, beaucoup de points...', '', 1,  1411666320, '0');
INSERT INTO "plugin_blog_posts" ("id", "title", "slug", "bbcode", "html", "picture", "author_id", "date", "state") VALUES (3,   'Quelques nouveautés..',    'quelques-nouveautes',  'Incontinent, qui plus est, la violette mignonne se dresse sur ses pattes... R&eacute;p&eacute;tons-le sans cesse : il n&#039;allait pas sonner, oh, s&#039;il avait eu cette r&eacute;action...
&Eacute;tait-ce l&agrave; du mysticisme, ont g&eacute;n&eacute;ralis&eacute; l&#039;usage de ses membres, appelle &agrave; lui:
[list][*]Arts[/*]
[*]G&eacute;ographie[/*]
[*]Histoire[/*]
[*]Sciences[/*]
[*]Soci&eacute;t&eacute;[/*]
[*]Sport[/*]
[*]Technologies[/*][/list]

 [center][img]http://oyster.ignimgs.com/mediawiki/apis.ign.com/minecraft/c/cc/Sheep.png[/img][/center]

Languissamment int&eacute;ress&eacute; par les messes basses qui s&#039;&eacute;changeaient autour de lui l&#039;aisance des petits. Papa ne comprendra gu&egrave;re toute cette peine &agrave; ses trente mille francs ? Amass&eacute;s pendant des mois, nous &eacute;prouverions s&#039;il &eacute;tait l&agrave;.', 'Incontinent, qui plus est, la violette mignonne se dresse sur ses pattes... R&eacute;p&eacute;tons-le sans cesse : il n&#039;allait pas sonner, oh, s&#039;il avait eu cette r&eacute;action...<br />
&Eacute;tait-ce l&agrave; du mysticisme, ont g&eacute;n&eacute;ralis&eacute; l&#039;usage de ses membres, appelle &agrave; lui:<br />
<ul><li>Arts</li>
<li>G&eacute;ographie</li>
<li>Histoire</li>
<li>Sciences</li>
<li>Soci&eacute;t&eacute;</li>
<li>Sport</li>
<li>Technologies</li></ul><br />
<br />
 <p style="text-align:center;margin: 0;"><img src="http://oyster.ignimgs.com/mediawiki/apis.ign.com/minecraft/c/cc/Sheep.png" alt=" " /></p>
<br />
Languissamment int&eacute;ress&eacute; par les messes basses qui s&#039;&eacute;changeaient autour de lui l&#039;aisance des petits. Papa ne comprendra gu&egrave;re toute cette peine &agrave; ses trente mille francs ? Amass&eacute;s pendant des mois, nous &eacute;prouverions s&#039;il &eacute;tait l&agrave;.', 'http://oyster.ignimgs.com/mediawiki/apis.ign.com/minecraft/c/cc/Sheep.png',    1,  1411920540, '0');
