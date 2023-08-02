<?php return array (
  'providers' => 
  array (
    0 => 'Modules\\Core\\Providers\\CoreServiceProvider',
    1 => 'Modules\\Core\\Providers\\PurifierServiceProvider',
    2 => 'Modules\\Core\\Providers\\OAuthServiceProvider',
    3 => 'Webpatser\\Countries\\CountriesServiceProvider',
  ),
  'eager' => 
  array (
    0 => 'Modules\\Core\\Providers\\CoreServiceProvider',
    1 => 'Webpatser\\Countries\\CountriesServiceProvider',
  ),
  'deferred' => 
  array (
    'purifier' => 'Modules\\Core\\Providers\\PurifierServiceProvider',
    'Modules\\Core\\Contracts\\OAuth\\StateStorage' => 'Modules\\Core\\Providers\\OAuthServiceProvider',
  ),
  'when' => 
  array (
    'Modules\\Core\\Providers\\PurifierServiceProvider' => 
    array (
    ),
    'Modules\\Core\\Providers\\OAuthServiceProvider' => 
    array (
    ),
  ),
);