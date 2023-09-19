<?php

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

/**
 * return a formtted value with a currency symbol ($1,230.00)
 * @param string $number current users setting
 * @param string $spanid if we want to wrap the figure in a span
 * @return string css setting
 */
function runtimeMoneyFormat($number = '', $span_id = "") {

  // $number = runtimeNumberFormat($number);

  //are we wrapping in a span
  if ($span_id != '') {
      $number = '<span id="' . $span_id . '">' . $number . '</span>';
  }
  return $number;

  // return config('system.currency_symbol_left') . $number . config('system.currency_symbol_right');
}

/**
 * Takes the current url and updates the page number to
 * to the specified one. If no page number existed in url
 * it will simply be added
 * @param string $name The name of the user
 * @param int $id The user id
 * @return bool
 */
function loadMoreButtonUrl($page = '', $type = '') {
  //get an array of all the current url queries
  $queries = request()->query();
  //update/add page number
  $queries['page'] = $page;
  $queries['source'] = $type;
  $queries['action'] = 'load';

  //return a full url with updated value
  $url = request()->fullUrlWithQuery($queries);

  //remove unwanted (system_languages%5B2%5D=afrikaans) etc from the url. These are coming from the languages dropdown
  $url = preg_replace('/&system_languages%5B[\d]+%5D=[\w]+/', '', $url);
  $url = preg_replace('/&visibility_left_menu_toggle_button=[\w]+/', '', $url);
  $url = preg_replace('/&system_language=[\w]+/', '', $url);
  $url = preg_replace('/&user_has_due_reminder=[\w]+/', '', $url);
  $url = preg_replace('/&toggle=[\w]+/', '', $url);

  //return url
  return $url;
}

/**
 * clean a language string to remove html tags and trim shite spaces
 * @return string
 */

 function cleanLang($str = '') {
  //remove double quotes
  $str = str_replace('"', '', $str);
  //trim html
  return trim(strip_tags($str));
}

/**
 * set the bootstrap col-size for crumbs. If none is provided, set the default size col-lg-5
 * this was an afterthought, so some controllers are not setting this, hence the default size
 * @return string
 */
function runtimeCrumbsColumnSize($size = '') {
  if ($size != '') {
      return $size;
  } else {
      return 'col-lg-5';
  }
}


/**
 * Format the datepicker date accoring to the system (settings_system_datepicker_format)
 * @return string bootstrap label class
 */
function runtimeDatepickerDate($date = '') {
    if ($date != '') {
        $date_format = (config('system.settings_system_datepicker_format') == 'mm-dd-yyyy') ? 'm-d-Y' : 'd-m-Y';
        return \Carbon\Carbon::parse($date)->format($date_format);
    }
    return;
}

/**
 * Check if select2 should allow users own tags
 * @return string select2 css setting for allowing tags or null
 */
function runtimeAllowUserTags() {
  // if (config('system.settings_tags_allow_users_create') == 'yes') {
  if(true){
      return 'select2-tags';
  } else {
      return 'select2-basic';
  }
}

/**
 * whether or not to allow modals to close on body click
 * this setting s coming from the admin settings
 * @return string bootstrap data attibutes for disallowing modals to close on body click
 */
function runtimeAllowCloseModalOptions() {
  return;
  if (config('system.settings_system_close_modals_body_click') == 'yes') {
      return;
  }
  return 'data-keyboard="false" data-backdrop="static"';
}


/**
 * returns "selected" for dropdown list preselection
 * in the example below, if the database value is "active' this option will be "selected"
 * @example:
 *     <option value="active" {{ runtimePreselected('active', project['project_status']) }}>Active</option>
 *     <option value="suspended" {{ runtimePreselected('suspended', project['project_status']) }}>Suspended</option>
 * @param $option_value string [the hard coded value of the select option (e.g. <option="yes" ....)
 * @param $actual_value string [actual value i.e. from database (e.g. <option value="no"...)]
 * @return string hidden | visible
 */
function runtimePreselected($actual_value = 'bar', $option_value = 'foo') {
  if ($option_value == $actual_value) {
      return 'selected';
  }
  return;
}

/**
 * Apply correct language for values language coming from the database
 * e.g. project status etc
 * if no lang was found, return origianl text
 * @return string bootstrap label class
 */
function runtimeLang($lang = '') {
  $language = strtolower($lang);
  $language = str_replace(' ', '_', $lang);
  if (Lang::has("lang.$language")) {
      return __("lang.$language");
  } else {
      return str_replace('_', ' ', $lang);
  }
}

/**
 * dynamic url for changing bill tax type
 * @param obj $bill estimate or invoice
 * @return string url
 */
function runtimeBillTaxTypeURL($bill = '') {

  //estimate
  if ($bill->bill_type == 'estimate') {
      return url('/admin/estimates/' . $bill->bill_estimateid . '/change-tax-type');
  }

  //invoice
  if ($bill->bill_type == 'invoice') {
      return url('/admin/invoices/' . $bill->bill_invoiceid . '/change-tax-type');
  }
}


/**
 * return formatted invoice id (e.g. INV000024)
 * @param numeric bill_invoiceid
 * @return string checked | null
 */
function runtimeInvoiceIdFormat($bill_invoiceid = '') {
  //add the zero's
  $prefix = 'INV-'; //config('system.settings_invoices_prefix');
  //return
  if (is_numeric($bill_invoiceid)) {
      return $prefix . str_pad($bill_invoiceid, 6, '0', STR_PAD_LEFT);

  } else {
      return '---';
  }
}

function runtimeContractIdFormat($contract_id = '') {
  if (is_numeric($contract_id)) {
      return 'CNT-' . str_pad($contract_id, 8, '0', STR_PAD_LEFT);
  } else {
      return '---';
  }
}

/**
 * display correct invoice status
 * @return string hidden|null
 */
function runtimeInvoiceAttachedProject($type = 'attached', $value = '') {
  if ($type == 'project-title' && !is_numeric($value)) {
      return 'hidden';
  }
  if ($type == 'alternative-title' && is_numeric($value)) {
      return 'hidden';
  }
}


/**
 * allow for dynamic manipulation of hard coded urls
 * @param string $url url string from balde or response etc
 * @return string url parses by laravel url helper
 */
function _url($url = '') {

  /**
   * --------------------------------------------------------------------------------------------------
   * [REMAPPING URLS]
   *
   * remapping changes a hard coded url like
   *           <a href="{{ _url('/projects') }}"... becomes same as <a href="{{ _url('/moviess') }}"
   *
   * mapping comes from an array place inside settings.php (example below)
   *     'url_mapping' => [
   *         'projects' => 'movies',
   *      ],
   *
   * ---------------------------------------------------------------------------------------------------
   */
  if (config()->has('settings.url_mapping')) {
      $mapping = config('settings.url_mapping');
      if (is_array($mapping)) {
          foreach ($mapping as $key => $value) {
              if ($value != '') {
                  $url = str_replace($key, $value, $url);
              }
          }
      }
  }

  //process and return the url as normal
  return url($url);
}

/**
 * display correct invoice status visibility (on invoice page)
 * @return string hidden|null
 */
function runtimeInvoiceStatus($lable = 'foo', $value = 'bar') {
  if ($lable == $value) {
      return '';
  }
  return 'hidden';
}


/**
 * bootstrap class, based on value
 * @param string value the status of the task
 * @param string type lable|background
 * @return string bootstrap label class
 */
function runtimeInvoiceStatusColors($value = '', $type = 'label') {

  //default colour
  $colour = 'default';

  //get the css value from config
  foreach (config('settings.invoice_statuses')??[] as $key => $css) {
      if ($value == $key) {
          $colour = $css;
      }
  }

  //return the css
  return bootstrapColors($colour, $type);
}
/**
 * used by runtime functions to return the css
 * @param string value the status of the task
 * @param string type lable|background
 * @return string
 */
function bootstrapColors($colour = '', $type = '') {

  switch ($colour) {
  case 'default':
      if ($type == 'label') {
          return 'label-outline-default';
      }
      if ($type == 'background') {
          return 'bg-default';
      }
      if ($type == 'text') {
          return 'text-default';
      }
      break;
  case 'info':
      if ($type == 'label') {
          return 'label-outline-info';
      }
      if ($type == 'background') {
          return 'bg-info';
      }
      if ($type == 'text') {
          return 'text-info';
      }
      break;
  case 'success':
      if ($type == 'label') {
          return 'label-outline-success';
      }
      if ($type == 'background') {
          return 'bg-success';
      }
      if ($type == 'text') {
          return 'text-success';
      }
      break;

  case 'warning':
      if ($type == 'label') {
          return 'label-outline-warning';
      }
      if ($type == 'background') {
          return 'bg-warning';
      }
      if ($type == 'text') {
          return 'text-warning';
      }
      break;
  case 'danger':
      if ($type == 'label') {
          return 'label-outline-danger';
      }
      if ($type == 'background') {
          return 'bg-danger';
      }
      if ($type == 'text') {
          return 'text-danger';
      }
      break;
  case 'megna':
      if ($type == 'label') {
          return 'label-outline-megna';
      }
      if ($type == 'background') {
          return 'bg-megna';
      }
      if ($type == 'text') {
          return 'text-megna';
      }
      break;
  case 'purple':
      if ($type == 'label') {
          return 'label-outline-purple';
      }
      if ($type == 'background') {
          return 'bg-purple';
      }
      if ($type == 'text') {
          return 'text-purple';
      }
      break;
  case 'green':
      if ($type == 'label') {
          return 'label-outline-green';
      }
      if ($type == 'background') {
          return 'bg-green';
      }
      if ($type == 'text') {
          return 'text-green';
      }
      break;
  case 'lime':
      if ($type == 'label') {
          return 'label-outline-lime';
      }
      if ($type == 'background') {
          return 'bg-lime';
      }
      if ($type == 'text') {
          return 'text-lime';
      }
      break;
  case 'brown':
      if ($type == 'label') {
          return 'label-outline-brown';
      }
      if ($type == 'background') {
          return 'bg-brown';
      }
      if ($type == 'text') {
          return 'text-brown';
      }
      break;
  case 'primary':
      if ($type == 'label') {
          return 'label-outline-purple';
      }
      if ($type == 'background') {
          return 'bg-purple';
      }
      if ($type == 'text') {
          return 'text-purple';
      }
      break;
  default:
      if ($type == 'label') {
          return 'label-outline-info';
      }
      if ($type == 'background') {
          return 'bg-info';
      }
      if ($type == 'text') {
          return 'text-info';
      }
      break;
  }
}

/**
 * Format the date accoring to the system setting
 * @return string bootstrap label class
 */
function runtimeDate($date = '', $alternative = '---') {

  if ($date == '0000-00-00' || $date == '0000-00-00 00:00:00' || $date == '---') {
      return $alternative;
  }

  if ($date != '') {
      $date_format = config('system.settings_system_date_format');
      return \Carbon\Carbon::parse($date)->format($date_format);
  }

  return $alternative;
}

/**
 * for correcting symbols that are not showing, such as euro
 * @param string $number current users setting
 * @param string $spanid if we want to wrap the figure in a span
 * @return string css setting
 */
function runtimeMoneyFormatPDF($number = '', $span_id = "") {

  //$number = runtimeNumberFormat($number);

  //are we wrapping in a span
  if ($span_id != '') {
      $number = '<span id="' . $span_id . '">' . $number . '</span>';
  }
  return $number;

  return runtimePDFCharacters(config('system.currency_symbol_left')) . $number . runtimePDFCharacters(config('system.currency_symbol_right'));
}

/**
 * add css class 'hidden' to an element
 * @return string hidden|null
 */
function runtimeVisibility($type = 'invoice-recurring-icon', $value = '', $value2 = '') {

  //invoice recurring icon
  if ($type == 'invoice-recurring-icon') {
      return ($value == 'yes') ? '' : 'hidden';
  }

  //invoice actions menu - view child invoices
  if ($type == 'invoice-view-child-invoices') {
      return ($value == 'yes') ? '' : 'hidden';
  }

  //invoice actions menu - stop recurring
  if ($type == 'invoice-stop-recurring') {
      return ($value == 'yes') ? '' : 'hidden';
  }

  //invoice coluns - inline tax
  if ($type == 'invoice-column-inline-tax') {
      return ($value == 'inline') ? '' : 'hidden';
  }

  //attach/detttach invoice dropdown links
  if ($type == 'attach-invoice') {
      return (is_numeric($value)) ? 'hidden' : '';
  }
  if ($type == 'dettach-invoice') {
      return (is_numeric($value)) ? '' : 'hidden';
  }

  //topnav timer
  if ($type == 'topnav-timer') {
      return ($value == 'show') ? '' : 'hidden';
  }

  //edit client modal -module settings
  if ($type == 'client_app_modules_pemissions') {
      return ($value == 'custom') ? '' : 'hidden';
  }

  //proposal or invoce statuses
  if ($type == 'document-status') {
      return ($value == $value2) ? 'hidden' : '';
  }

  //file folders management
  if ($type == 'settings-file-folders-manage') {
      return ($value == 'disabled') ? 'hidden' : '';
  }

  //estimate automation icon
  if ($type == 'estimate-automation-icon') {
      return ($value == 'disabled') ? 'hidden' : '';
  }
}


/**
 * dynamic url for attaching files to invoices and estimates
 * @param obj $bill estimate or invoice
 * @return string url
 */
function runtimeURLBillAttachFiles($bill = '') {

  //estimate
  if ($bill->bill_type == 'estimate') {
      return url('/admin/estimates/' . $bill->bill_estimateid . '/attach-files');
  }

  //invoice
  if ($bill->bill_type == 'invoice') {
      return url('/admin/invoices/' . $bill->bill_invoiceid . '/attach-files');
  }
}


/**
 * appends additional query string data to a url.
 * e.g. ?invoiceresource_type=project&?invoiceresource_id=28
 * Data is set via the [index] middleware
 * @param string $var current users setting
 * @return string css setting
 */
function urlResource($url = '') {

  if (request()->filled('resource_query')) {
      if (strpos($url, '?') !== false) {
          $url = $url . '&' . request('resource_query');
      } else {
          $url = $url . '?' . request('resource_query');
      }
  }

  //return complete ur;
  return url($url);
}

/**
 * enable or disablethe tax drp down, for line items that are marked as tax exempt
 * @param string $value determining value
 */
function runtimeLineItemTaxStatus($value = '') {

    //hide the button
    if ($value == 'exempt') {
        return 'disabled';
    }
    return;
}

function clean($string){
return $string;
}
function runtimeChangeReqIdFormat($change_req_id = '') {
  if (is_numeric($change_req_id)) {
      return 'CHRQ-' . str_pad($change_req_id, 4, '0', STR_PAD_LEFT);
  } else {
      return '---';
  }
}

function runtimeTransIdFormat($transaction_id = '') {
  if (is_numeric($transaction_id)) {
      return 'TRX-' . str_pad($transaction_id, 4, '0', STR_PAD_LEFT);
  } else {
      return '---';
  }
}
