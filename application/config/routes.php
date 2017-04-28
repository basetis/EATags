<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "home";
$route['404_override'] = '';
$route['contact'] = "home/contact";
$route['privacy'] = "home/privacy";
$route['legal'] = "home/legal";
$route['cookies'] = "home/cookies";
$route['features'] = "home/features";
$route['feat.wordpress.post'] = "home/feat/eat_wp_post";
$route['feat.wordpress.draft'] = "home/feat/eat_wp_draft";
$route['feat.tweet'] = "home/feat/eat_tweet";
$route['feat.flickr'] = "home/feat/eat_flickr";
$route['feat.toc'] = "home/feat/eat_toc";
$route['feat.toc.notebook'] = "home/feat/eat_toc_notebook";
$route['feat.toc.tag'] = "home/feat/eat_toc_tag";
$route['feat.latex'] = "home/feat/eat_latex";
$route['feat.add.header'] = "home/feat/eat_add_header";
$route['feat.add.footer'] = "home/feat/eat_add_footer";
$route['feat.add.surround'] = "home/feat/eat_add_surround";
$route['feat.gmail.draft'] = "home/feat/eat_gmail_draft";
$route['about'] = "home/about";
$route['faqs'] = "home/faqs";
$route['eat-team'] = "home/eat_team";
$route['contacto'] = "home/contact";
$route['privacidad'] = "home/privacy";
$route['funcionalidades'] = "home/features";
$route['acerca'] = "home/about";
$route['cuenta'] = 'account';
$route['cuenta/perfil'] = 'account/profile';
$route['cuenta/funcionalidades'] = 'account/features';
$route['cuenta/funcionalidades/(:any)'] = 'account/features/$1';
$route['cuenta/conectar_evernote'] = 'account/evernote_login';
$route['auth/salir'] = 'auth/logout';
$route['auth/entrar'] = 'auth/login';
$route['auth/registro'] = 'auth/register';
$route['contacte'] = "home/contact";
$route['privacitat'] = "home/privacy";
$route['funcionalitats'] = "home/features";
$route['sobre'] = "home/about";
$route['compte'] = 'account';
$route['compte/perfil'] = 'account/profile';
$route['compte/funcionalitats'] = 'account/features';
$route['compte/funcionalitats/(:any)'] = 'account/features/$1';
$route['compte/connectar_evernote'] = 'account/evernote_login';
$route['auth/sortir'] = 'auth/logout';
$route['auth/registre'] = 'auth/register';

/* End of file routes.php */
/* Location: ./application/config/routes.php */