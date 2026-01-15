=== Polylang Theme.co Integration ===
Contributors: ControlZetaDigital
Tags: polylang, integrator, multilanguage, support, tco, themeco, pro, theme, cornerstone
Donate link: https://donate.stripe.com/4gwg177xifrsfSgcMN
Requires at least: 5.4
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: v1.1.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple plugin that integrates Polylang multilanguage plugin with Theme.co\'s Pro theme and Cornerstone layout elements.

== Description ==
A simple plugin that integrates [Polylang](https://polylang.pro/) multilanguage plugin with layout, headers and footers contents created with Cornerstone builder in Pro themes by [Theme.co](https://theme.co/pro)

== Installation ==
For detailed installation instructions, please read the [standard installation procedure for WordPress plugins](https://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

1. Make sure [Polylang](https://polylang.pro/) and [Pro Theme](https://theme.co/pro) are installed and running.
2. Install and activate plugin.

== Frequently Asked Questions ==
1. How to use?

1.1 Language assignment
Assign each active language with Pro elements you have made in settings. You can find this plugin's settings in Wordpress admin menu *Languages* -> *Pro Theme Support*.
You can assign each CS element to one or multiple active languages. Just click on the flag to select or deselect the assignment. The native CS assignment will be applied alongside the language assignment of this plugin, so if you have multiple elements of the same type assigned to the same language, the assignment conditions and priorities you have defined in CS will be applied.

1.2 Language CS provider
This plugin also includes a custom looper provider that returns the languages configured in Polylang, allowing it to be used in Cornerstone with its respective functionality (see the [custom looper provider documentation](https://theme.co/docs/loopers#custom) for details). To call the provider, you should use the ***languages*** key in the *Custom* field. Its purpose is to facilitate the integration of a language selector in Cornerstone templates and layouts. The provider returns two variables: **current**, which contains the slug of the current language, and **languages**, which returns the list of languages configured on the website (for more information, refer to the [documentation for pll_the_languages](https://polylang.pro/doc/function-reference/)).

2. Do you wanna help?

If you want to collaborate in improving this plugin and have any ideas to do so, feel free to clone this repository, create your own branch, and make your pull requests.
Also, if you\'ve found it helpful and want to help me continue improving it, you can make a [donation here](https://donate.stripe.com/4gwg177xifrsfSgcMN).

== Screenshots ==
1. [Plugin Settings](https://github.com/ControlZetaDigital/polylang-tcopro/blob/main/settings.png)

== Changelog ==
1.1.3
- Fixed plugin header version (1.1.2 incorrectly showed as 1.1.1).
- Update readmes and tested up to 6.9

1.1.2
 - Fix: The use of 'prepare' in the SQL query within 'get_items' has been discontinued as placeholders were not being used.
 - Added a box in the sidebar with the link to the support page on GitHub.

1.1.1
 - Now plugin also check if Polylang Pro is enabled or not

1.1.0
 - Language assignment improved in order to assign a language to each Cornerstone element (headers, footers and layouts) and mantain CS native assignments.
 - Minor updates in plugin\'s file/directory structure and documentation.

1.0.0
 - Initialized project.