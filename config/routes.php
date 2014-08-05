<?php
return array(
	'_root_'  => '',  // The default route
	'_404_'   => '',    // The main 404 route

	'api/(:any)/(:num)/update' => 'api/$1/update/$2', // updates an element
	'api/(:any)/(:num)/delete' => 'api/$1/delete/$2', // deletes an element
	'api/(:any)/create' => 'api/$1/create', // Creates element
	'api/(:any)/(:num)' => 'api/$1/read/$2', // Gets one elements
	'api/(:any)' => 'api/$1/read/', // Gets all elements
);