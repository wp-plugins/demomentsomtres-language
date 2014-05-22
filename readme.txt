=== DeMomentSomTres Language ===
Contributors: marcqueralt
Tags: multilanguage, network, seo
Donate link: http://demomentsomtres.com/english/wordpress-plugins/demomentsomtres-language/
Requires at least: 3.8
tested up to: 3.9
Stable tag: head

DeMomentSomTres Language allows to have different instances of a blog using different languages on a network installation.

== Description ==

DeMomentSomTres Language allows to have different instances of a blog using different languages on a network installation.
It includes a widget to change to initial home page in another language.
Every post contains a link to any of its translation in an area below the title and content metadata.

== Installation ==

Upload the DeMomentSomTres Idioma plugin.

You need to assure that your main blog is not used because of the blog forced prefix on the contents. To prevent using main network site redirection plugin can be used to send contents to default site.

== FAQ ==

=== when I access my page with www destination is lost ===
You should add the following code just after `RewriteBase /` in .htaccess file

`RewriteCond %{HTTP_HOST} ^www\.(.*)$ [NC]
RewriteRule ^(.*)$ http://%1/$1 [R=301,L]`

== Changelog ==
= 1.5 =
* Value of blog properties public parameter is 2 in some cases. Changes in order to allow 2 or 1. 
* Reciprocal update active by default.

= 1.4 =
* get_blog_list() deprecated and changed by wp_get_sites() in function QuBicIdioma_obtenir_blocs().

= 1.3 =
* libraries compatibility upgrade

=1.2.1=
* skip some problematic redirects

=1.2.05=
* javascript optimization: javascript loaded only if required
* css optimization: css loaded only if required
* redirect errors
* force reciprocal update on content save by default

=1.2.04=
* redirect landing site to default site via 301 instead of 302.

=1.2.03=
* post translation widget can be shown even if empty

=1.2.02=
* bug solved: debug info showed to solve 1.2.01 bug not removed.

=1.2.01=
* bug solved: when siteurl contains upper cases it started a redirection cycle.

=1.2=
* shortcode [DeMomentSomTres-Language class="optional classes"] to show all translations of the main content.
* widget Language: post translations
* shortcode mode to avoid traditional filters.

=1.1.11=

* bug when installed in a directory instead of the root of the web.

=1.1.10=

* avoid showing translation on widgets calling custom types.

=1.1.9=

* redirect to language keeping url tail

=1.1.8=

* minor bugs solving

=1.1.7=

* Landing mode optimization

=1.1.6=

* Use default language prefix if none is found

=1.1.5=

* bug: post language selector shown even if post type is not translatable.

=1.1=

* Landing site mode: allows to jump to the other sites based on the browser language

=1.0.2=

* Duplicate '/' sign in address solved

=1.0.1=

* Debugging mistake solved

=1.0=

* Added to wordpress.org subversion

=0.8=

* Rebranding of component to new company name: DeMomentSomTres.
* Translate Idioma to Language.
* Solved Warning on admin when no language is configured.

=0.7=

* Additional text only widget on div qibdip_Idioma_Text.

=0.6=

* Set reciprocal translations. From one bloc you can update all translations relationships. It assumes that the user is allowed to do all operations.
* Rename of links div in post

=0.5=

* Page: Add fields to translation allowing to link to other translations of pages.
* Custom posts: Add fields to translations
* Take into account post types settings on links

=0.4=

* Settings: Allows to choose the post_types affected by translation. Limited functionality to administration.
* Take blog status into account

=0.3=

* Post: Link to post translation
* Header: Link on header to go to other site translations

=0.2=

* File structure change
* Widget Language Chooser
* Bloc change based on language

=0.1=

* Initial release

=Next steps=

* Afegir un enllaç a la taula de translations per tal d'anar a la pàgina d'edició del post en una altre bloc.
* Associar traduccions de categories
* Associar traduccions de tags
* Associar traduccions d'entrades de menú
* Associar traduccions de links
* Mapa d'associacions per a una determinada tipologia d'elements
* Modificar el generador d'url per a que el títol digui traducció al XXX: títol.
* Footer link per a promoció SEO