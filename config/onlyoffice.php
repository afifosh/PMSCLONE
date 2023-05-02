<?php

return [
  /**
   * secret
   */
  "secret" => env('ONLYOFFICE_SECRET', null),
  "doc_server_url" => env('ONLYOFFICE_HOST_URL', 'http://167.71.55.200'),
  "doc_server_api_url" => env('ONLYOFFICE_HOST_API_URL', 'http://167.71.55.200/web-apps/apps/api/documents/api.js'),

  'supported_files' => [
    'csv', 'djvu', 'doc', 'docm', 'docx', 'docxf', 'dot', 'dotm', 'dotx', 'epub', 'fb2', 'fodp', 'fods', 'fodt', 'htm', 'html', 'mht', 'odp', 'ods', 'odt', 'oform', 'otp', 'ots', 'ott', 'oxps', 'pdf', 'pot', 'potm', 'potx', 'pps', 'ppsm', 'ppsx', 'ppt', 'pptm', 'pptx', 'rtf', 'txt', 'xls', 'xlsb', 'xlsm', 'xlsx', 'xlt', 'xltm', 'xltx', 'xml', 'xps'
  ],

  'allowed_file_size' => env('ONLYOFFICE_ALLOWED_FILE_SIZE', 10),

];
