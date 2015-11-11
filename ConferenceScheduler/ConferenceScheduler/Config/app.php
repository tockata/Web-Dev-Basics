<?php

$config['default_controller'] = 'Index';
$config['default_method'] = 'index';

$config['session']['auto_start'] = true;
$config['session']['type'] = 'native';
$config['session']['name'] = 'app_session';
$config['session']['lifetime'] = 60 * 60 * 15;
$config['session']['path'] = '/';
$config['session']['domain'] = '';
$config['session']['secure'] = false;

return $config;