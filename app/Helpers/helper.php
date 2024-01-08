<?php

use App\Support\Money;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mime\MimeTypeExtensionGuesser;

if (!function_exists('absint')) {
  /**
   * Convert a value to a non-negative integer.
   *
   * @param mixed $value The value to be converted.
   * @return int Non-negative integer.
   */
  function absint($value) {
      return abs(intval($value));
  }
}

if (!function_exists('sanitize_title')) {
  /**
   * Sanitizes a string into a slug.
   *
   * @param string $title The string to be sanitized.
   * @param string $fallback_title Optional. A title to use if $title is empty.
   * @param string $context Optional. The operation for which the string is sanitized.
   * @return string The sanitized string.
   */
  function sanitize_title($title, $fallback_title = '', $context = 'save') {
      if ($context === 'save') {
          $title = Str::ascii($title); // Converts accents to ASCII
      }

      $title = Str::slug($title); // Convert to URL-friendly slug

      if (empty($title)) {
          $title = Str::slug($fallback_title);
      }

      return $title;
  }
}

if (!function_exists('wpforms_panel_field_toggle_control')) {
/**
 * Create toggle control.
 *
 * It's like a regular checkbox but with a modern visual appearance.
 *
 * @since 1.6.8
 *
 * @param array  $args       Arguments array.
 *
 *    @type bool   $status        If `true`, control will display the current status next to the toggle.
 *    @type string $status-on     Status `On` text. By default `On`.
 *    @type string $status-off    Status `Off` text. By default `Off`.
 *    @type bool   $label-hide    If `true` then label will not display.
 *    @type string $tooltip       Tooltip text.
 *    @type string $input-class   CSS class for the hidden `<input type=checkbox>`.
 *    @type string $control-class CSS class for the wrapper `<span>`.
 *
 * @param string $input_id   Input ID.
 * @param string $field_name Field name.
 * @param string $label      Label text. Can contain HTML in order to display additional badges.
 * @param mixed  $value      Value.
 * @param string $data_attr  Attributes.
 *
 * @return string
 */
function wpforms_panel_field_toggle_control( $args, $input_id, $field_name, $label, $value, $data_attr ) {

	$checked = checked( true, (bool) $value, false );
	$status  = '';

	if ( ! empty( $args['status'] ) ) {
		$status_on  = ! empty( $args['status-on'] ) ? $args['status-on'] : esc_html__( 'On', 'wpforms-lite' );
		$status_off = ! empty( $args['status-off'] ) ? $args['status-off'] : esc_html__( 'Off', 'wpforms-lite' );
		$status     = sprintf(
			'<label
				for="%s"
				class="wpforms-toggle-control-status"
				data-on="%s"
				data-off="%s">
				%s
			</label>',
			esc_attr( $input_id ),
			esc_attr( $status_on ),
			esc_attr( $status_off ),
			esc_html( $value ? $status_on : $status_off )
		);
	}

	$label_html  = empty( $args['label-hide'] ) && ! empty( $label ) ?
		sprintf(
			'<label for="%s" class="wpforms-toggle-control-label">%s</label>',
			esc_attr( $input_id ),
			$label
		) : '';
	$label_html .= isset( $args['tooltip'] ) ?
		sprintf(
			'<i class="fa fa-question-circle-o wpforms-help-tooltip" title="%s"></i>',
			esc_attr( $args['tooltip'] )
		) : '';

	$label_left    = ! empty( $args['label-left'] ) ? $label_html . $status : '';
	$label_right   = empty( $args['label-left'] ) ? $status . $label_html : '';
	$title         = isset( $args['title'] ) ? ' title="' . esc_attr( $args['title'] ) . '"' : '';
	$control_class = ! empty( $args['control-class'] ) ? $args['control-class'] : '';
	$input_class   = ! empty( $args['input-class'] ) ? $args['input-class'] : '';

	return sprintf(
		'<span class="wpforms-toggle-control %8$s" %9$s>
			%1$s
			<input type="checkbox" id="%2$s" name="%3$s" class="%7$s" value="1" %4$s %5$s>
			<label class="wpforms-toggle-control-icon" for="%2$s"></label>
			%6$s
		</span>',
		$label_left,
		esc_attr( $input_id ),
		esc_attr( $field_name ),
		$checked,
		$data_attr,
		$label_right,
		wpforms_sanitize_classes( $input_class ),
		wpforms_sanitize_classes( $control_class ),
		$title
	);
}
}


if (!function_exists('wp_kses')) {
    /**
     * Sanitize HTML content by allowing only specific HTML tags and attributes.
     *
     * @param string $content The content to be filtered.
     * @param array $allowed_tags Allowed tags and their allowed attributes.
     * @return string Sanitized content.
     */
    function wp_kses($content, array $allowed_tags) {
        return strip_tags($content, array_keys($allowed_tags));
    }
}

if (!function_exists('wpforms_builder_preview_get_allowed_tags')) {
    /**
     * Get the list of allowed HTML tags and attributes.
     *
     * @return array
     */
    function wpforms_builder_preview_get_allowed_tags() {
        static $allowed_tags = null;

        if (!is_null($allowed_tags)) {
            return $allowed_tags;
        }

        $attributes = ['align', 'class', 'type', 'id', 'for', 'style', 'src', 'rel', 'href', 'target', 'value', 'width', 'height'];
        $tags = ['label', 'iframe', 'style', 'button', 'strong', 'small', 'table', 'span', 'abbr', 'code', 'pre', 'div', 'img', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ol', 'ul', 'li', 'em', 'hr', 'br', 'th', 'tr', 'td', 'p', 'a', 'b', 'i'];

        $allowed_attributes = array_fill_keys($attributes, []);
        $allowed_tags = array_fill_keys($tags, $allowed_attributes);

        return $allowed_tags;
    }
}

if (!function_exists('wpforms_sanitize_classes')) {
  /**
   * Sanitize string of CSS classes.
   *
   * @since 1.2.1
   *
   * @param array|string $classes CSS classes.
   * @param bool         $convert True will convert strings to array and vice versa.
   *
   * @return string|array
   */
  function wpforms_sanitize_classes( $classes, $convert = false ) {

    $array = is_array( $classes );
    $css   = [];

    if ( ! empty( $classes ) ) {
      if ( ! $array ) {
        $classes = explode( ' ', trim( $classes ) );
      }
      foreach ( array_unique( $classes ) as $class ) {
        if ( ! empty( $class ) ) {
          $css[] = sanitize_html_class( $class );
        }
      }
    }

    if ( $array ) {
      return $convert ? implode( ' ', $css ) : $css;
    }

    return $convert ? $css : implode( ' ', $css );
  }
}

if (!function_exists('checked')) {
  /**
   * Determine whether a checkbox or radio button should be checked.
   *
   * @param mixed $checked Expected value to check.
   * @param mixed $current Current value to compare against.
   * @param bool $echo Whether to echo or just return the string.
   * @return string
   */
  function checked($checked, $current = true, $echo = true) {
      if ((string) $checked === (string) $current) {
          $checked = 'checked="checked"';
      } else {
          $checked = '';
      }

      if ($echo) {
          echo $checked;
      } else {
          return $checked;
      }
  }
}

if (!function_exists('esc_url')) {
  /**
   * Sanitizes a URL.
   *
   * @param string $url The URL to be cleaned.
   * @return string The cleaned URL.
   */
  function esc_url($url) {
      if (is_null($url) || trim($url) === '') {
          return '';
      }

      // Replace spaces and remove illegal characters from the URL
      $url = str_replace(' ', '%20', trim($url));
      $url = filter_var($url, FILTER_SANITIZE_URL);

      // Validate the URL
      $validator = Validator::make(['url' => $url], [
          'url' => 'url'
      ]);

      if ($validator->fails()) {
          return '';
      }

      // Additional logic can be implemented here if needed

      return $url;
  }
}


if (!function_exists('wpforms_utm_link')) {
  /**
   * Appends UTM parameters to a URL.
   *
   * @param string $link The URL to be modified.
   * @param string $medium The UTM medium.
   * @param string $content The UTM content.
   * @param string $term The UTM term.
   * @return string The URL with UTM parameters.
   */
  function wpforms_utm_link($link, $medium, $content = '', $term = '') {
      $utm_parameters = [
          'utm_campaign' => 'your_campaign', // Replace with your logic
          'utm_source'   => strpos($link, 'https://wpforms.com') === 0 ? 'WordPress' : 'wpformsplugin',
          'utm_medium'   => rawurlencode($medium),
          'utm_content'  => rawurlencode($content),
          'utm_term'     => rawurlencode($term),
          'utm_locale'   => app()->getLocale(), // Get the current locale
      ];

      // Filter out any empty UTM parameters
      $utm_parameters = array_filter($utm_parameters);

      // Build query string
      $query_string = http_build_query($utm_parameters);

      // Append query string to URL
      $delimiter = parse_url($link, PHP_URL_QUERY) ? '&' : '?';
      return $link . $delimiter . $query_string;
  }
}


if (!function_exists('selected')) {
  /**
   * Determine whether a checkbox or radio button should be checked.
   *
   * @param mixed $checked Expected value to check.
   * @param mixed $current Current value to compare against.
   * @param bool $echo Whether to echo or just return the string.
   * @return string
   */
  function selected($checked, $current = true, $echo = true) {
      if ((string) $checked === (string) $current) {
          $checked = 'checked="checked"';
      } else {
          $checked = '';
      }

      if ($echo) {
          echo $checked;
      } else {
          return $checked;
      }
  }
}

if (!function_exists('wpforms_html_attributes')) {
/**
 * Format, sanitize, and return/echo HTML element ID, classes, attributes,
 * and data attributes.
 *
 * @since 1.3.7
 *
 * @param string $id    HTML id attribute value.
 * @param array  $class A list of classnames for the class attribute.
 * @param array  $datas Data attributes.
 * @param array  $atts  Any additional HTML attributes and their values.
 * @param bool   $echo  Whether to echo the output or just return it. Defaults to return.
 *
 * @return string|void
 */
function wpforms_html_attributes( $id = '', $class = [], $datas = [], $atts = [], $echo = false ) {

	$id    = trim( $id );
	$parts = [];

	if ( ! empty( $id ) ) {
		$id = sanitize_html_class( $id );

		if ( ! empty( $id ) ) {
			$parts[] = 'id="' . $id . '"';
		}
	}

	if ( ! empty( $class ) ) {
		$class = wpforms_sanitize_classes( $class, true );

		if ( ! empty( $class ) ) {
			$parts[] = 'class="' . $class . '"';
		}
	}

	if ( ! empty( $datas ) ) {
		foreach ( $datas as $data => $val ) {
			$parts[] = 'data-' . sanitize_html_class( $data ) . '="' . esc_attr( $val ) . '"';
		}
	}

	if ( ! empty( $atts ) ) {
		foreach ( $atts as $att => $val ) {
			if ( '0' === (string) $val || ! empty( $val ) ) {
				if ( $att[0] === '[' ) {
					// Handle special case for bound attributes in AMP.
					$escaped_att = '[' . sanitize_html_class( trim( $att, '[]' ) ) . ']';
				} else {
					$escaped_att = sanitize_html_class( $att );
				}
				$parts[] = $escaped_att . '="' . esc_attr( $val ) . '"';
			}
		}
	}

	$output = implode( ' ', $parts );

	if ( $echo ) {
		echo trim( $output ); // phpcs:ignore
	} else {
		return trim( $output );
	}
}
}

if (!function_exists('sanitize_html_class')) {
  /**
   * Sanitize HTML class names.
   *
   * @param string $classname The class name to sanitize.
   * @param string $fallback Optional fallback class name.
   * @return string Sanitized class name.
   */
  function sanitize_html_class($classname, $fallback = '') {
      // Strip out any percent-encoded characters.
      $sanitized = preg_replace('|%[a-fA-F0-9][a-fA-F0-9]|', '', $classname);

      // Limit to A-Z, a-z, 0-9, '_', '-'.
      $sanitized = preg_replace('/[^A-Za-z0-9_-]/', '', $sanitized);

      if ('' === $sanitized && $fallback) {
          return sanitize_html_class($fallback);
      }

      return $sanitized;
  }
}

if (!function_exists('esc_textarea')) {
  function esc_textarea($text) {
      $blog_charset = config('app.charset', 'UTF-8'); // Assuming you have charset in your config, or default to UTF-8
      $safe_text = htmlspecialchars($text, ENT_QUOTES, $blog_charset);

      // Use Laravel's built-in event (or custom logic) instead of apply_filters
      // ...

      return $safe_text;
  }
}

if (!function_exists('wpforms_is_empty_string')) {
  function wpforms_is_empty_string($string) {
      return is_string($string) && $string === '';
  }
}

if (!function_exists('esc_html__')) {
  /**
   * Translate the given message and escape it for HTML.
   *
   * @param  string  $key
   * @param  array   $replace
   * @param  string  $locale
   * @return string
   */
  function esc_html__($key, $replace = [], $locale = null) {
      return esc_html($key);
  }
}

if (!function_exists('esc_html_e')) {
  /**
   * Displays translated text that has been escaped for safe use in HTML output.
   *
   * @param string $text Text to translate.
   * @param string $domain Optional. Text domain for translation.
   */
  function esc_html_e($text, $domain = 'default') {
    return esc_html($text);
  }
}


if (!function_exists('esc_attr__')) {
  /**
   * Translate the given message and escape it for HTML.
   *
   * @param  string  $key
   * @param  array   $replace
   * @param  string  $locale
   * @return string
   */
  function esc_attr__($key, $replace = [], $locale = null) {
      return esc_attr($key);
  }
}

if (!function_exists('esc_html')) {
    /**
     * Escape HTML entities in a string.
     *
     * @param string $text
     * @return string
     */
    function esc_html($text) {
        return e($text);
    }
}

if (!function_exists('esc_attr')) {
    /**
     * Escape HTML attributes.
     *
     * @param string $text
     * @return string
     */
    function esc_attr($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}


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
  return round($amount * 100, 0, config('app.rounding_mode'));
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
