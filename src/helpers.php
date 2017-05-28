<?php 

if (getenv('AWS_LAMBDA') === true && !function_exists('storage_path')) {
	function storage_path($path = '') {
		return '/tmp/storage/' . $path;
	}
}

