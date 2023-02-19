<?php

return [
  /**
   * secret
   */
  "secret" => env('ONLYOFFICE_SECRET', null),
  "doc_server_url" => 'http://146.190.123.183',
  "doc_server_api_url" => 'http://146.190.123.183/web-apps/apps/api/documents/api.js',

  'supported_files' => [
    'csv', 'djvu', 'doc', 'docm', 'docx', 'docxf', 'dot', 'dotm', 'dotx', 'epub', 'fb2', 'fodp', 'fods', 'fodt', 'htm', 'html', 'mht', 'odp', 'ods', 'odt', 'oform', 'otp', 'ots', 'ott', 'oxps', 'pdf', 'pot', 'potm', 'potx', 'pps', 'ppsm', 'ppsx', 'ppt', 'pptm', 'pptx', 'rtf', 'txt', 'xls', 'xlsb', 'xlsm', 'xlsx', 'xlt', 'xltm', 'xltx', 'xml', 'xps'
  ]

];
