<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Home::index');
$routes->get('/', 'Login::index');

$routes->post('/checklogin', 'Login::checkLogin');
$routes->get('/library.php', 'Library::index');
$routes->post('/library.php', 'Library::index');
$routes->get('/library/librarysingle.php', 'Library::librarysingle');
$routes->get('/library/libraryseries.php', 'Library::libraryseries');
$routes->post('/library/download', 'Library::download');
$routes->get('/add_devotional.php', 'Devotional::index');
$routes->post('/devotional/addtag', 'Devotional::addtag');
$routes->get('/tagview.php', 'Tagview::index');
$routes->post('/tagview.php', 'Tagview::index');
$routes->get('/settings.php', 'Settings::index');
$routes->post('/settings.php', 'Settings::index');
$routes->get('/users.php', 'Users::index');
$routes->get('/users/checkuser', 'Users::checkuser');
$routes->get('/users/create_users.php', 'Users::create_users');
$routes->post('/users/create_users.php', 'Users::create_users');
$routes->get('/login/logout.php', 'Login::logout');


