=== DeMomentSomTres Language ===
Contributors: marcqueralt
Donate link: http://demomentsomtres.com/english/wordpress-plugins/demomentsomtres-language/
Tags: multilanguage, network, multisite, seo, language, translation, translate
Requires at least: 3.8
tested up to: 4.1
Stable tag: head

DeMomentSomTres Language allows to have different instances of a blog using different languages on a network installation.

== Description ==

DeMomentSomTres Language allows to have different instances of a blog using different languages on a network installation.

Using WordPress multisite install you can have a web instance for every language allowing specific SEO and sales strategies based on language.

It allows to change from a content to its translations via easy links.

= Features =
* Language configuration.
* Automatic language selection based on browser preferred language.
* Every content can be linked to all its translations on other instances.
* Translatable content types defined in settings.
* Language links can be presented via 'the_content' filter or using shortcodes and widgets.
* Body class customization based on language.

= History & raison d'être =
In 2011 we needed a multilanguage plugin allowing:

* Content translation.
* Integration with WordPress SEO by Yoast.
* Different communication and SEO strategies based on language.
* Automatic language selection based on browser language.

As we didn't find it, we decided to implement it and use it.

= More information =
[DeMomentSomTres Language in DeMomentSomTres.com](http://demomentsomtres.com/english/wordpress-plugins/demomentsomtres-language/)

= Usage =
The recommended usage mode (supose required languages catalan and english) is having 3 instances of WordPress:

1. Landing site configured in landing mode in order to redirect the users based on their languages preferences. For instance http://demomentsomtres.com/
1. Catalan site with catalan language set. Example address http://demomentsomtres.com/catala/
1. English site with englis language set and default site mark. Example address http://demomentsomtres.com/english/

A french user when looking for http://demomentsomtres.com will be redirected to http://demomentsomtres.com/english as it is the default mode.

When you're writting a content, a metabox whit the candidate translations is shown in order to link a component with all its translations.

= Contributors and translators =

Thanks to:

* Borisa Djuraskovic from [http://www.webhostinghub.com](http://www.webhostinghub.com) for the Serbo-Croatian translation

== Installation ==

It can be installed as any other WordPress plugin.

In order to work properly, WordPress Multisite MUST be installed and configured.

We have been recommending to use shortcode mode in order to avoid component collitions as the_content is not allways correctly configured. We have decided to mantain only shortcode mode and stop development of "non shortcode mode".

== FAQ ==

= When I access my page with www destination is lost =

You should add the following code just after `RewriteBase /` in .htaccess file

`RewriteCond %{HTTP_HOST} ^www\.(.*)$ [NC]
RewriteRule ^(.*)$ http://%1/$1 [R=301,L]`

= Can I use "non-shortcode" mode" =

We stopped development of the non-shortcode mode as it is not being used very much and there are problems with plugins using the_content filter not the right way.

= I want to have multiple multilanguage sites under the same installation =

Don't worry you can use site groups.

You need to enter Network manager > Language and setup the groups.

Group name can be any string or number and every group must contain a Landing site and as much languages as you want.

== Changelog ==
= 2.0.3 =
* saving config problem solved.
* Message shown in config is site is not public

= 2.0.2 =
* bug fix: Fatal Error line 1018 solved

= 2.0.1 =
* bug fix: Home links widget failure

= 2.0 =
* Code full rewrite to use classes and DeMomentSomTres Tools free plugin.
* Site groups to allow multiple landing sites.
* JQuery widget deprecated

= 1.8.3 =
* bug fix: wrong initial redirection if wordpress is installed in a directory different from root
* Https protocol management

= 1.8.2 =
* Serbo-Croatian language added thanks to Borisa Djuraskovic from [http://www.webhostinghub.com](http://www.webhostinghub.com)

= 1.8.1 =
* Frontpage link title updated

= 1.8 =
* At frontpage automatically redirects to other frontpages.

= 1.7 =
* Additional class based on language

= 1.6.1 =
* bug: recursive redirect

= 1.6 =
* Language added after the site name in 'My Sites' menu.
* 'My Sites' menu sort order based on settings.

= 1.5 =
* Value of blog properties public parameter is 2 in some cases. Changes in order to allow 2 or 1. 
* Reciprocal update active by default.

= 1.4 =
* get_blog_list() deprecated and changed by wp_get_sites() in function QuBicIdioma_obtenir_blocs().

= 1.3 =
* libraries compatibility upgrade

= 1.2.1 =
* skip some problematic redirects

= 1.2.05 =
* javascript optimization: javascript loaded only if required
* css optimization: css loaded only if required
* redirect errors
* force reciprocal update on content save by default

= 1.2.04 =
* redirect landing site to default site via 301 instead of 302.

= 1.2.03 =
* post translation widget can be shown even if empty

= 1.2.02 =
* bug solved: debug info showed to solve 1.2.01 bug not removed.

= 1.2.01 =
* bug solved: when siteurl contains upper cases it started a redirection cycle.

= 1.2 =
* shortcode [DeMomentSomTres-Language class="optional classes"] to show all translations of the main content.
* widget Language: post translations
* shortcode mode to avoid traditional filters.

= 1.1.11 =

* bug when installed in a directory instead of the root of the web.

= 1.1.10 =

* avoid showing translation on widgets calling custom types.

= 1.1.9 =

* redirect to language keeping url tail

= 1.1.8 =

* minor bugs solving

= 1.1.7 =

* Landing mode optimization

= 1.1.6 =

* Use default language prefix if none is found

= 1.1.5 =

* bug: post language selector shown even if post type is not translatable.

= 1.1 =

* Landing site mode: allows to jump to the other sites based on the browser language

= 1.0.2 =

* Duplicate '/' sign in address solved

= 1.0.1 =

* Debugging mistake solved

= 1.0 =

* Added to wordpress.org subversion

= 0.8 =

* Rebranding of component to new company name: DeMomentSomTres.
* Translate Idioma to Language.
* Solved Warning on admin when no language is configured.

= 0.7 =

* Additional text only widget on div qibdip_Idioma_Text.

= 0.6 =

* Set reciprocal translations. From one bloc you can update all translations relationships. It assumes that the user is allowed to do all operations.
* Rename of links div in post

= 0.5 =

* Page: Add fields to translation allowing to link to other translations of pages.
* Custom posts: Add fields to translations
* Take into account post types settings on links

= 0.4 =

* Settings: Allows to choose the post_types affected by translation. Limited functionality to administration.
* Take blog status into account

= 0.3 =

* Post: Link to post translation
* Header: Link on header to go to other site translations

= 0.2 =

* File structure change
* Widget Language Chooser
* Bloc change based on language

= 0.1 =

* Initial release
