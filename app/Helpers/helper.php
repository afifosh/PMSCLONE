<?php

use App\Support\Money;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mime\MimeTypeExtensionGuesser;

if (!function_exists('formatDateTime')) {
  function formatDateTime($dateTime)
  {
    return $dateTime ? date('d M, Y', strtotime($dateTime)) : 'NULL';
  }
}

if (!function_exists('formatUNIXTimeStamp')) {
  function formatUNIXTimeStamp($dateTime)
  {
    return Carbon::parse($dateTime)->diffForHumans();
  }
}

if (!function_exists('slug')) {
  function slug($string)
  {
    return Str::slug($string);
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

if (!function_exists('transformModifiedData')) { // Get modified data from modidifications array
  function transformModifiedData($modifications)
  {
    foreach ($modifications as $key => $value) {
      $modifications[$key] = $value['modified'];
    }
    return $modifications;
  }
}

function collectModifiedFields($modifications)
{
  $mods = [];
  foreach ($modifications['fields']['modified'] as $key => $modified_field) {
    // dd($modified_field['value'], $modifications['fields']['original'][$key]['value']);
    if ($modified_field['value'] != $modifications['fields']['original'][$key]['value'])
      $mods['fields'][$modified_field['id']] = $modified_field;
  }

  return $mods;
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

function array_diff_assoc_recursive($array1, $array2)
{
  $difference = array();
  foreach ($array1 as $key => $value) {
    if (is_array($value)) {
      if (!isset($array2[$key]) || !is_array($array2[$key])) {
        $difference[$key] = $value;
      } else {
        $new_diff = array_diff_assoc_recursive($value, $array2[$key]);
        if (!empty($new_diff))
          $difference[$key] = $new_diff;
      }
    } elseif (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
      $difference[$key] = $value;
    }
  }
  return $difference;
}

function getAssetUrl($path)
{
  return Storage::url($path);
}

function remove_null_values($array)
{
  return array_filter($array, function ($value) {
    return !is_null($value);
  });
}

function filterInputIds(array $ids): array
{
  return array_unique(remove_null_values($ids));
}

function formatCurrency($amount)
{
  return number_format($amount) . ' ' . config('app.currency');
}

function optionParams($data)
{
  return collect($data ?? [])->mapWithKeys(function ($item) {
    return [$item['id'] => ['data-full_name' => $item['full_name'] ? $item['full_name'] : $item['name'], 'data-avatar' => $item['avatar']]];
  })->all();
}

function siteInfoTemplate()
{
  return [
    'App Name' => '{app_name}',
    'App Url' => '{app_url}',
  ];
}

function replaceSiteInfo($template)
{
  return str_replace(array_values(siteInfoTemplate()), [config('app.name'), config('app.url')], $template);
}

function replaceStrVariables($template, $data)
{
  $template = replaceSiteInfo($template);
  return str_replace(array_keys($data), array_values($data), $template);
}

function runtimeContractIdFormat($contract_id = '')
{
  if (is_numeric($contract_id)) {
    return 'CNT-' . str_pad($contract_id, 8, '0', STR_PAD_LEFT);
  } else {
    return '---';
  }
}

function runtimeChangeReqIdFormat($change_req_id = '')
{
  if (is_numeric($change_req_id)) {
    return 'CHRQ-' . str_pad($change_req_id, 4, '0', STR_PAD_LEFT);
  } else {
    return '---';
  }
}

function runtimeTransIdFormat($transaction_id = '')
{
  if (is_numeric($transaction_id)) {
    return 'TRX-' . str_pad($transaction_id, 4, '0', STR_PAD_LEFT);
  } else {
    return '---';
  }
}

function runtimeInvIdFormat($invoice_id = '')
{
  if (is_numeric($invoice_id)) {
    return 'INV-' . str_pad($invoice_id, 6, '0', STR_PAD_LEFT);
  } else {
    return '---';
  }
}

function runtimeDpInvIdFormat($invoice_id = '')
{
  if (is_numeric($invoice_id)) {
    return 'DP-' . str_pad($invoice_id, 6, '0', STR_PAD_LEFT);
  } else {
    return '---';
  }
}

function runtimeTAInvIdFormat($invoice_id = '')
{
  if (is_numeric($invoice_id)) {
    return 'TAINV-' . str_pad($invoice_id, 6, '0', STR_PAD_LEFT);
  } else {
    return '---';
  }
}

function moneyToInt($amount)
{
  return round($amount * 1000, 0, config('app.rounding_mode'));
}

function cMoney(mixed $amount, string $currency = null, bool $convert = null): Money
{
  if (is_null($currency)) {
    /** @var string $currency */
    $currency = config('money.defaults.currency');
  }

  if (is_null($convert)) {
    /** @var bool $convert */
    $convert = config('money.defaults.convert');
  }

  return new Money($amount, currency($currency), $convert);
}

function iCan($permission)
{
  return auth()->user()->can($permission);
}
