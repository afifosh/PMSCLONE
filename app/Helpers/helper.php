<?php

use Illuminate\Support\Carbon;
use Symfony\Component\Mime\MimeTypeExtensionGuesser;

if (!function_exists('formatDateTime')) {
  function formatDateTime($dateTime)
  {
    return date('d M, Y', strtotime($dateTime));
  }
}

if (!function_exists('formatUNIXTimeStamp')) {
  function formatUNIXTimeStamp($dateTime)
  {
    return Carbon::parse($dateTime)->diffForHumans();
  }
}

if (!function_exists('human_filesize')) {
  function human_filesize($bytes, $dec = 2): string
  {
    $size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    if ($factor == 0) $dec = 0;

    return sprintf("%.{$dec}f %s", $bytes / (1024 ** $factor), $size[$factor]);
  }
}

// if (!function_exists('get_ext')) {
//   function get_ext($mimeType)
//   {
//     $guesser = MimeTypeExtensionGuesser::getInstance();
//     return $guesser->guess($mimeType);
//   }
// }

if(!function_exists('transformModifiedData')) { // Get modified data from modidifications array
  function transformModifiedData($modifications)
  {
    foreach ($modifications as $key => $value) {
      $modifications[$key] = $value['modified'];
    }
    return $modifications;
  }
}

function getCompanyStatusIcon($status)
{
  switch ($status) {
    case 'pending':
      return 'fa-solid fa-circle-exclamation';
      break;
    case 'approved':
      return 'fa-regular fa-circle-check';
      break;
    case 'rejected':
      return 'fa-regular fa-circle-xmark';
      break;
    default:
      return 'fa-regular fa-circle-check';
      break;
  }
}

function getCompanyStatusColor($status)
{
  switch ($status) {
    case 'pending':
      return 'warning';
      break;
    case 'approved':
      return 'success';
      break;
    case 'rejected':
      return 'danger';
      break;
    default:
      return 'warning';
      break;
  }
}
