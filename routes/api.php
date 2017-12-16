<?php

/**
 * API Routes
 |------------------------------------------------------------------------
 | Here is where you defined all your API routes
 */
$r->addGroup('/api', function($r) {
	$r->get('/user', function() {
		echo "this is api user api!";
	});
});
