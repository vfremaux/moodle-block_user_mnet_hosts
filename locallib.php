<?php

function user_mnet_hosts_make_accesskey($wwwroot, $full = false){
	
	$accesskey = preg_replace('/https?:\/\//', '', $wwwroot);
	$accesskey = str_replace('-', '', $accesskey);
	$accesskeyparts = explode('.', $accesskey);
	if (count($accesskeyparts) > 2){
		array_pop($accesskeyparts);	// remove ext
		array_pop($accesskeyparts);	// remove main domain
	}
	
	$accesstoken = strtoupper(implode('', $accesskeyparts));
	
	if ($full){
		return 'access'.$accesstoken;
	}
	
	return $accesstoken;
}