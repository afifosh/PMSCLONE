@php
    $configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Form Builder Pages')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />


    <link rel="stylesheet" href="{{ asset('wpforms/assets/css/builder/builder-basic.css') }}" />
    <link rel="stylesheet" href="{{ asset('wpforms/assets/css/builder/builder-fields.css') }}" />
    <link rel="stylesheet" href="{{ asset('wpforms/assets/css/builder/builder-fields-types.css') }}" />
    <link rel="stylesheet" href="{{ asset('wpforms/assets/pro/css/builder.css') }}" />
    <link rel="stylesheet" href="{{ asset('wpforms/assets/pro/css/builder-conditional-logic-core.css') }}" />
    <link rel="stylesheet" href="{{ asset('wpforms/assets/css/builder/builder-third-party.css.css') }}" />
    <link rel="stylesheet" href="{{ asset('wpforms/assets/lib/jquery.confirm/jquery-confirm.min.css') }}" />

@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-form-builder.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <script id="wpforms-builder-js-extra">
        var wpforms_builder = {
            "and": "And",
            "ajax_url": "https:\/\/pms.test\/admin\/create-field",
            "bulk_add_button": "Add New Choices",
            "bulk_add_show": "Bulk Add",
            "are_you_sure_to_close": "Are you sure you want to leave? You have unsaved changes",
            "bulk_add_hide": "Hide Bulk Add",
            "bulk_add_heading": "Add Choices (one per line)",
            "bulk_add_placeholder": "Blue\nRed\nGreen",
            "bulk_add_presets_show": "Show presets",
            "bulk_add_presets_hide": "Hide presets",
            "date_select_day": "DD",
            "date_select_month": "MM",
            "debug": "",
            "dynamic_choices": {
                "limit_message": "The {source} {type} contains over {limit} items ({total}). This may make the field difficult for your visitors to use and\/or cause the form to be slow.",
                "empty_message": "This field will not be displayed in your form since there are no {type} belonging to {source}.",
                "entities": {
                    "post_type": "posts",
                    "taxonomy": "terms"
                }
            },
            "cancel": "Cancel",
            "ok": "OK",
            "close": "Close",
            "conditionals_change": "Due to form changes, conditional logic rules will be removed or updated:",
            "conditionals_disable": "Are you sure you want to disable conditional logic? This will remove the rules for this field or setting.",
            "field": "Field",
            "field_locked": "Field Locked",
            "field_locked_msg": "This field cannot be deleted or duplicated.",
            "field_locked_no_delete_msg": "This field cannot be deleted.",
            "field_locked_no_duplicate_msg": "This field cannot be duplicated.",
            "fields_available": "Available Fields",
            "fields_unavailable": "No fields available",
            "heads_up": "Heads up!",
            "image_placeholder": "http:\/\/www.horizonadvksa.com\/demo\/wp-content\/plugins\/wpforms\/assets\/images\/builder\/placeholder-200x125.svg",
            "nonce_old": "94ba2ecb28",
            "nonce": "{{ csrf_token() }}",
            "admin_nonce": "8877d81660",
            "no_email_fields": "No email fields",
            "notification_delete": "Are you sure you want to delete this notification?",
            "notification_prompt": "Enter a notification name",
            "notification_ph": "Eg: User Confirmation",
            "notification_error": "You must provide a notification name",
            "notification_def_name": "Default Notification",
            "confirmation_delete": "Are you sure you want to delete this confirmation?",
            "confirmation_prompt": "Enter a confirmation name",
            "confirmation_ph": "Eg: Alternative Confirmation",
            "confirmation_error": "You must provide a confirmation name",
            "confirmation_def_name": "Default Confirmation",
            "save": "Save",
            "saving": "Saving",
            "saved": "Saved!",
            "save_exit": "Save and Exit",
            "save_embed": "Save and Embed",
            "saved_state": "",
            "layout_selector_show": "Show Layouts",
            "layout_selector_hide": "Hide Layouts",
            "layout_selector_layout": "Select your layout",
            "layout_selector_column": "Select your column",
            "loading": "Loading",
            "template_name": "",
            "template_slug": "",
            "template_modal_title": "",
            "template_modal_msg": "",
            "template_modal_display": "",
            "template_select": "Use Template",
            "template_confirm": "Changing templates on an existing form will DELETE existing form fields. Are you sure you want apply the new template?",
            "use_simple_contact_form": "Use Simple Contact Form Template",
            "embed": "Embed",
            "exit": "Exit",
            "exit_url": "http:\/\/www.horizonadvksa.com\/demo\/wp-admin\/admin.php?page=wpforms-overview",
            "exit_confirm": "Your form contains unsaved changes. Would you like to save your changes first.",
            "delete_confirm": "Are you sure you want to delete this field?",
            "delete_choice_confirm": "Are you sure you want to delete this choice?",
            "duplicate_confirm": "Are you sure you want to duplicate this field?",
            "duplicate_copy": "(copy)",
            "error_title": "Please enter a form name.",
            "error_choice": "This item must contain at least one choice.",
            "off": "Off",
            "on": "On",
            "or": "or",
            "other": "Other",
            "operator_is": "is",
            "operator_is_not": "is not",
            "operator_empty": "empty",
            "operator_not_empty": "not empty",
            "operator_contains": "contains",
            "operator_not_contains": "does not contain",
            "operator_starts": "starts with",
            "operator_ends": "ends with",
            "operator_greater_than": "greater than",
            "operator_less_than": "less than",
            "payments_entries_off": "Entry storage is currently disabled, but is required to accept payments. Please enable in your form settings.",
            "payments_on_entries_off": "This form is currently accepting payments. Entry storage is required to accept payments. To disable entry storage, please first disable payments.",
            "previous": "Previous",
            "provider_required_flds": "In order to complete your form's {provider} integration, please check that the dropdowns for all required (*) List Fields have been filled out.",
            "rule_create": "Create new rule",
            "rule_create_group": "Add New Group",
            "rule_delete": "Delete rule",
            "smart_tags": {
                "admin_email": "Site Administrator Email",
                "form_id": "Form ID",
                "form_name": "Form Name",
                "entry_id": "Entry ID",
                "entry_date format=\"d\/m\/Y\"": "Entry Date",
                "entry_details_url": "Entry Details URL",
                "page_title": "Embedded Post\/Page Title",
                "page_url": "Embedded Post\/Page URL",
                "page_id": "Embedded Post\/Page ID",
                "date format=\"m\/d\/Y\"": "Date",
                "query_var key=\"\"": "Query String Variable",
                "user_ip": "User IP Address",
                "user_id": "User ID",
                "user_display": "User Display Name",
                "user_full_name": "User Full Name",
                "user_first_name": "User First Name",
                "user_last_name": "User Last Name",
                "user_email": "User Email",
                "user_meta key=\"\"": "User Meta",
                "author_id": "Author ID",
                "author_display": "Author Name",
                "author_email": "Author Email",
                "url_referer": "Referrer URL",
                "url_login": "Login URL",
                "url_logout": "Logout URL",
                "url_register": "Register URL",
                "url_lost_password": "Lost Password URL",
                "unique_value": "Unique Value",
                "site_name": "Site Name"
            },
            "smart_tags_disabled_for_fields": ["entry_id"],
            "smart_tags_show": "Show Smart Tags",
            "smart_tags_hide": "Hide Smart Tags",
            "select_field": "--- Select Field ---",
            "select_choice": "--- Select Choice ---",
            "upload_image_title": "Upload or Choose Your Image",
            "upload_image_button": "Use Image",
            "upload_image_remove": "Remove Image",
            "provider_add_new_acc_btn": "Add",
            "pro": "1",
            "is_gutenberg": "1",
            "cl_fields_supported": ["checkbox", "email", "hidden", "net_promoter_score", "number", "number-slider",
                "payment-checkbox", "payment-multiple", "payment-select", "radio", "rating", "richtext", "select",
                "text", "textarea", "url"
            ],
            "redirect_url_field_error": "You should enter a valid absolute address to the Confirmation Redirect URL field.",
            "add_custom_value_label": "Add Custom Value",
            "choice_empty_label_tpl": "Choice {number}",
            "error_save_form": "Something went wrong while saving the form. Please reload the page and try again.",
            "error_contact_support": "Please contact the plugin support team if this behavior persists.",
            "ms_win_css_url": "http:\/\/www.horizonadvksa.com\/demo\/wp-content\/plugins\/wpforms\/assets\/css\/builder\/builder-ms-win.css",
            "error_select_template": "Please close the form builder and try again. If the error persists, contact our support team.",
            "blank_form": "Blank Form",
            "something_went_wrong": "Something went wrong",
            "field_cannot_be_reordered": "This field cannot be moved.",
            "empty_label": "Empty Label",
            "no_pages_found": "No results found",
            "number_slider_error_valid_default_value": "Please enter a valid value or change the Increment. The nearest valid values are {from} and {to}.",
            "currency": "USD",
            "currency_name": "U.S. Dollar",
            "currency_decimals": "2",
            "currency_decimal": ".",
            "currency_thousands": ",",
            "currency_symbol": "$",
            "currency_symbol_pos": "left",
            "disable_entries": "Disabling entry storage for this form will completely prevent any new submissions from getting saved to your site. If you still intend to keep a record of entries through notification emails, then please <a href=\"https:\/\/wpforms.com\/docs\/how-to-properly-test-your-wordpress-forms-before-launching-checklist\/?utm_campaign=plugin&utm_source=WordPress&utm_medium=Builder%20Notifications&utm_content=Testing%20A%20Form%20Documentation&utm_locale=en\" target=\"_blank\" rel=\"noopener noreferrer\">test your form<\/a> to ensure emails send reliably.",
            "akismet_not_installed": "This feature cannot be used at this time because the Akismet plugin <a href=\"http:\/\/www.horizonadvksa.com\/demo\/wp-admin\/plugin-install.php\" target=\"_blank\" rel=\"noopener noreferrer\">has not been installed<\/a>. For information on how to use this feature please <a href=\"https:\/\/wpforms.com\/docs\/setting-up-akismet-anti-spam-protection\/?utm_campaign=plugin&utm_source=WordPress&utm_medium=Builder%20Settings&utm_content=Akismet%20Documentation&utm_locale=en\" target=\"_blank\" rel=\"noopener noreferrer\">refer to our documentation<\/a>.",
            "akismet_not_activated": "This feature cannot be used at this time because the Akismet plugin <a href=\"http:\/\/www.horizonadvksa.com\/demo\/wp-admin\/plugins.php\" target=\"_blank\" rel=\"noopener noreferrer\">has not been activated<\/a>. For information on how to use this feature please <a href=\"https:\/\/wpforms.com\/docs\/setting-up-akismet-anti-spam-protection\/?utm_campaign=plugin&utm_source=WordPress&utm_medium=Builder%20Settings&utm_content=Akismet%20Documentation&utm_locale=en\" target=\"_blank\" rel=\"noopener noreferrer\">refer to our documentation<\/a>.",
            "akismet_no_api_key": "This feature cannot be used at this time because the Akismet plugin <a href=\"http:\/\/www.horizonadvksa.com\/demo\/wp-admin\/options-general.php?page=akismet-key-config&view=start\" target=\"_blank\" rel=\"noopener noreferrer\">has not been properly configured<\/a>. For information on how to use this feature please <a href=\"https:\/\/wpforms.com\/docs\/setting-up-akismet-anti-spam-protection\/?utm_campaign=plugin&utm_source=WordPress&utm_medium=Builder%20Settings&utm_content=Akismet%20Documentation&utm_locale=en\" target=\"_blank\" rel=\"noopener noreferrer\">refer to our documentation<\/a>.",
            "shortcuts_modal_title": "Keyboard Shortcuts",
            "shortcuts_modal_msg": "Handy shortcuts for common actions in the builder.",
            "empty_label_alternative_text": "Field #",
            "notifications_file_upload": {
                "wp_max_upload_size": 32,
                "no_choices_text": "You do not have any file upload fields"
            },
            "entry_information": {
                "default_file_name": "entry-details",
                "excluded_tags": ["date format=\"m\/d\/Y\"", "query_var key=\"\"", "user_meta key=\"\""],
                "localized": {
                    "all_fields": "All Fields"
                },
                "replacement_tags": {
                    "entry_date format=\"d\/m\/Y\"": "entry_date"
                },
                "excluded_field_types": ["captcha", "divider", "entry-preview", "html", "content",
                    "internal-information", "layout", "pagebreak"
                ]
            },
            "continue": "Continue",
            "done": "Done!",
            "uh_oh": "Uh oh!",
            "icon_choices": {
                "is_installed": false,
                "is_active": false,
                "default_icon": "face-smile",
                "default_icon_style": "regular",
                "default_color": "#066aab",
                "icons": [],
                "icons_per_page": 50,
                "strings": {
                    "install_prompt_content": "In order to use the Icon Choices feature, an icon library must be downloaded and installed. It&#039;s quick and easy, and you&#039;ll only have to do this once.",
                    "install_title": "Installing Icon Library",
                    "install_content": "This should only take a minute. Please don\u2019t close or reload your browser window.",
                    "install_success_content": "The icon library has been installed successfully. We will now save your form and reload the form builder.",
                    "install_error_content": "There was an error installing the icon library. Please try again later or <a href=\"https:\/\/wpforms.com\/account\/support\/?utm_campaign=plugin&#038;utm_source=WordPress&#038;utm_medium=builder-modal&#038;utm_content=Icon%20Library%20Install%20Failure&#038;utm_locale=en\" target=\"_blank\" rel=\"noreferrer noopener\">contact support<\/a> if the issue persists.",
                    "reinstall_prompt_content": "The icon library appears to be missing or damaged. It will now be reinstalled.",
                    "icon_picker_title": "Icon Picker",
                    "icon_picker_description": "Browse or search for the perfect icon.",
                    "icon_picker_search_placeholder": "Search 2000+ icons...",
                    "icon_picker_not_found": "Sorry, we didn&#039;t find any matching icons."
                }
            },
            "notification_clone": " - clone",
            "notification_by_status_enable_alert": "<p>You have just enabled this notification for <strong>%1$s<\/strong>. Please note that this email notification will only send for <strong>%1$s<\/strong>.<\/p><p>If you'd like to set up additional notifications for this form, please see our <a href=\"https:\/\/wpforms.com\/docs\/setup-form-notification-wpforms\/\" rel=\"nofollow noopener\" target=\"_blank\">tutorial<\/a>.<\/p>",
            "notification_by_status_switch_alert": "<p>You have just <strong>disabled<\/strong> the notification for <strong>%2$s<\/strong> and <strong>enabled<\/strong> the notification for <strong>%1$s<\/strong>. Please note that this email notification will only send for <strong>%1$s<\/strong>.<\/p><p>If you'd like to set up additional notifications for this form, please see our <a href=\"https:\/\/wpforms.com\/docs\/setup-form-notification-wpforms\/\" rel=\"nofollow noopener\" target=\"_blank\">tutorial<\/a>.<\/p>",
            "allow_only_one_email": "Notifications can only use 1 From Email. Please do not enter multiple addresses.",
            "stripe_recurring_email": "When recurring subscription payments are enabled, the Customer Email is required. Please go to the Stripe payment settings and select a Customer Email.",
            "stripe_ajax_required": "<p>AJAX form submissions are required when using the Stripe Credit Card field.<\/p><p>To proceed, please go to <strong>Settings \u00bb General \u00bb Advanced<\/strong> and check <strong>Enable AJAX form submission<\/strong>.<\/p>",
            "stripe_keys_required": "<p>Stripe account connection is required when using the Stripe Credit Card field.<\/p><p>To proceed, please go to <strong>WPForms Settings \u00bb Payments \u00bb Stripe<\/strong> and press <strong>Connect with Stripe<\/strong> button.<\/p>",
            "payments_enabled_required": "<p>Stripe Payments must be enabled when using the Stripe Credit Card field.<\/p><p>To proceed, please go to <strong>Payments \u00bb Stripe<\/strong> and check <strong>Enable Stripe payments<\/strong>.<\/p>",
            "allowed_label_html_tags": ["br", "strong", "b", "em", "i", "a"],
            "entry_preview_require_page_break": "Page breaks are required for entry previews to work. If you'd like to remove page breaks, you'll have to first remove the entry preview field.",
            "entry_preview_default_notice": "<strong>This is a preview of your submission. It has not been submitted yet!<\/strong>\nPlease take a moment to verify your information. You can also go back to make changes.",
            "entry_preview_require_previous_button": "You can't hide the previous button because it is required for the entry preview field on this page.",
            "content_field": {
                "collapse": "Collapse Editor",
                "expand": "Expand Editor",
                "editor_default_value": "<h4>Add Text and Images to Your Form With Ease<\/h4> <p>To get started, replace this text with your own.<\/p>",
                "content_editor_plugins": ["charmap", "colorpicker", "hr", "link", "image", "lists", "paste",
                    "tabfocus", "textcolor", "wordpress", "wpemoji", "wptextpattern", "wpeditimage"
                ],
                "content_editor_toolbar": ["formatselect", "bold", "italic", "underline", "strikethrough", "forecolor",
                    "link", "bullist", "numlist", "blockquote", "alignleft", "aligncenter", "alignright"
                ],
                "content_editor_css_url": "http:\/\/www.horizonadvksa.com\/demo\/wp-content\/plugins\/wpforms\/assets\/css\/builder\/content-editor.min.css",
                "editor_height": 204,
                "allowed_html": ["img", "h1", "h2", "h3", "h4", "h5", "h6", "p", "a", "ul", "ol", "li", "dl", "dt",
                    "dd", "hr", "br", "code", "pre", "strong", "b", "em", "i", "blockquote", "cite", "q", "del",
                    "span", "small", "table", "thead", "tbody", "th", "tr", "td", "abbr", "address", "sub", "sup",
                    "ins", "figure", "figcaption", "div"
                ],
                "invalid_elements": "form,input,textarea,select,option,script,embed,iframe",
                "quicktags_buttons": "strong,em,block,del,ins,img,ul,ol,li,code,link,close",
                "body_class": "wpforms-content-field-editor-body"
            },
            "content_input": {
                "supported_field_types": ["content"]
            },
            "file_upload": {
                "preview_title_single": "Click or drag a file to this area to upload.",
                "preview_title_plural": "Click or drag files to this area to upload.",
                "preview_hint": "You can upload up to {maxFileNumber} files."
            },
            "error_number_slider_increment": "Increment value should be greater than zero. Decimal fractions allowed.",
            "iif_redirect_url_field_error": "You should enter a valid absolute address to the CTA Link field or leave it empty.",
            "iif_dismiss": "Dismiss",
            "iif_more": "Learn More",
            "layout": {
                "not_allowed_fields": ["layout", "pagebreak", "entry-preview"],
                "not_allowed_alert_text": "The %s field can\u2019t be placed inside a Layout field.",
                "empty_label": "Layout",
                "got_it": "Got it!",
                "size_notice_text": "Field size cannot be changed when used in a layout.",
                "size_notice_tooltip": "When a field is placed inside a column, the field size always equals the column width.",
                "dont_show_again": "Don\u2019t Show Again",
                "legacy_layout_notice_title": "Layouts Have Moved!",
                "legacy_layout_notice_text": "We\u2019ve added a new field to help you build advanced form layouts more easily. Give the Layout Field a try! Layout CSS classes are still supported. <a href=\"https:\/\/wpforms.com\/docs\/how-to-use-the-layout-field-in-wpforms\/?utm_campaign=plugin&#038;utm_source=WordPress&#038;utm_medium=Field%20Options&#038;utm_content=How%20to%20Use%20the%20Layout%20Field%20Documentation&#038;utm_locale=en\" target=\"_blank\" rel=\"noopener noreferrer\">Learn More<\/a>",
                "enabled_cf_alert_text": "The Layout field cannot be used when Conversational Forms is enabled.",
                "delete_confirm": "Are you sure you want to delete the Layout field? Deleting this field will also delete the fields inside it."
            },
            "allow_deny_lists_intersect": "We\u2019ve detected the same text in your allowlist and denylist. To prevent a conflict, we\u2019ve removed the following text from the list you\u2019re currently viewing:",
            "revision_update_confirm": "You\u2019re about to save a form revision. Continuing will make this the current version.",
            "preview_url": "http:\/\/www.horizonadvksa.com\/demo?wpforms_form_preview=2481&new_window=1",
            "entries_url": "http:\/\/www.horizonadvksa.com\/demo\/wp-admin\/admin.php?page=wpforms-entries&view=list&form_id=2481"
        };
    </script>
    <script id="wpforms-admin-form-embed-wizard-js-extra">
        var wpforms_admin_form_embed_wizard = {
            "nonce": "76418e8fb8",
            "is_edit_page": "0",
            "video_url": "https:\/\/youtube.com\/embed\/_29nTiDvmLw?rel=0&showinfo=0"
        };
    </script>
    <script id="wpforms-builder-providers-js-extra">
        var wpforms_builder_providers = {
            "url": "\/demo\/wp-admin\/admin.php?page=wpforms-builder&view=providers&form_id=2481",
            "confirm_save": "We need to save your progress to continue to the Marketing panel. Is that OK?",
            "confirm_connection": "Are you sure you want to delete this connection?",
            "prompt_connection": "Enter a %type% nickname",
            "prompt_placeholder": "Eg: Newsletter Optin",
            "error_name": "You must provide a connection nickname.",
            "required_field": "Field required"
        };
    </script>
    <script id="wpforms-builder-stripe-js-extra">
        var wpforms_builder_stripe = {
            "field_slugs": ["stripe-credit-card"],
            "is_pro": "",
            "plan_placeholder": "Plan Name",
            "disabled_recurring": "You can only use one payment type at a time. If you'd like to enable Recurring Payments, please disable One-Time Payments.",
            "disabled_one_time": "You can only use one payment type at a time. If you'd like to enable One-Time Payments, please disable Recurring Payments."
        };
    </script>

    <script>
        /* <![CDATA[ */
        var userSettings = {
            "url": "\/demo\/",
            "uid": "1",
            "time": "1703755798",
            "secure": ""
        };
        var _wpUtilSettings = {
            "ajax": {
                "url": "\/demo\/wp-admin\/admin-ajax.php"
            }
        }; /* ]]> */
    </script>
  @verbatim

<script type="text/javascript">wpforms_preset_choices={"countries":{"name":"Countries","choices":["Afghanistan","Albania","Algeria","American Samoa","Andorra","Angola","Anguilla","Antarctica","Antigua and Barbuda","Argentina","Armenia","Aruba","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bermuda","Bhutan","Bolivia (Plurinational State of)","Bonaire, Saint Eustatius and Saba","Bosnia and Herzegovina","Botswana","Bouvet Island","Brazil","British Indian Ocean Territory","Brunei Darussalam","Bulgaria","Burkina Faso","Burundi","Cabo Verde","Cambodia","Cameroon","Canada","Cayman Islands","Central African Republic","Chad","Chile","China","Christmas Island","Cocos (Keeling) Islands","Colombia","Comoros","Congo","Congo (Democratic Republic of the)","Cook Islands","Costa Rica","Croatia","Cuba","Cura\u00e7ao","Cyprus","Czech Republic","C\u00f4te d&#039;Ivoire","Denmark","Djibouti","Dominica","Dominican Republic","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Eswatini (Kingdom of)","Ethiopia","Falkland Islands (Malvinas)","Faroe Islands","Fiji","Finland","France","French Guiana","French Polynesia","French Southern Territories","Gabon","Gambia","Georgia","Germany","Ghana","Gibraltar","Greece","Greenland","Grenada","Guadeloupe","Guam","Guatemala","Guernsey","Guinea","Guinea-Bissau","Guyana","Haiti","Heard Island and McDonald Islands","Honduras","Hong Kong","Hungary","Iceland","India","Indonesia","Iran (Islamic Republic of)","Iraq","Ireland (Republic of)","Isle of Man","Israel","Italy","Jamaica","Japan","Jersey","Jordan","Kazakhstan","Kenya","Kiribati","Korea (Democratic People&#039;s Republic of)","Korea (Republic of)","Kosovo","Kuwait","Kyrgyzstan","Lao People&#039;s Democratic Republic","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macao","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Martinique","Mauritania","Mauritius","Mayotte","Mexico","Micronesia (Federated States of)","Moldova (Republic of)","Monaco","Mongolia","Montenegro","Montserrat","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepal","Netherlands","New Caledonia","New Zealand","Nicaragua","Niger","Nigeria","Niue","Norfolk Island","North Macedonia (Republic of)","Northern Mariana Islands","Norway","Oman","Pakistan","Palau","Palestine (State of)","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Pitcairn","Poland","Portugal","Puerto Rico","Qatar","Romania","Russian Federation","Rwanda","R\u00e9union","Saint Barth\u00e9lemy","Saint Helena, Ascension and Tristan da Cunha","Saint Kitts and Nevis","Saint Lucia","Saint Martin (French part)","Saint Pierre and Miquelon","Saint Vincent and the Grenadines","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia","Seychelles","Sierra Leone","Singapore","Sint Maarten (Dutch part)","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","South Georgia and the South Sandwich Islands","South Sudan","Spain","Sri Lanka","Sudan","Suriname","Svalbard and Jan Mayen","Sweden","Switzerland","Syrian Arab Republic","Taiwan, Republic of China","Tajikistan","Tanzania (United Republic of)","Thailand","Timor-Leste","Togo","Tokelau","Tonga","Trinidad and Tobago","Tunisia","Turkmenistan","Turks and Caicos Islands","Tuvalu","T\u00fcrkiye","Uganda","Ukraine","United Arab Emirates","United Kingdom of Great Britain and Northern Ireland","United States Minor Outlying Islands","United States of America","Uruguay","Uzbekistan","Vanuatu","Vatican City State","Venezuela (Bolivarian Republic of)","Vietnam","Virgin Islands (British)","Virgin Islands (U.S.)","Wallis and Futuna","Western Sahara","Yemen","Zambia","Zimbabwe","\u00c5land Islands"]},"countries_postal":{"name":"Countries Postal Code","choices":["AD","AE","AF","AG","AI","AL","AM","AO","AQ","AR","AS","AT","AU","AW","AX","AZ","BA","BB","BD","BE","BF","BG","BH","BI","BJ","BL","BM","BN","BO","BQ","BR","BS","BT","BV","BW","BY","BZ","CA","CC","CD","CF","CG","CH","CI","CK","CL","CM","CN","CO","CR","CU","CV","CW","CX","CY","CZ","DE","DJ","DK","DM","DO","DZ","EC","EE","EG","EH","ER","ES","ET","FI","FJ","FK","FM","FO","FR","GA","GB","GD","GE","GF","GG","GH","GI","GL","GM","GN","GP","GQ","GR","GS","GT","GU","GW","GY","HK","HM","HN","HR","HT","HU","ID","IE","IL","IM","IN","IO","IQ","IR","IS","IT","JE","JM","JO","JP","KE","KG","KH","KI","KM","KN","KP","KR","KW","KY","KZ","LA","LB","LC","LI","LK","LR","LS","LT","LU","LV","LY","MA","MC","MD","ME","MF","MG","MH","MK","ML","MM","MN","MO","MP","MQ","MR","MS","MT","MU","MV","MW","MX","MY","MZ","NA","NC","NE","NF","NG","NI","NL","NO","NP","NR","NU","NZ","OM","PA","PE","PF","PG","PH","PK","PL","PM","PN","PR","PS","PT","PW","PY","QA","RE","RO","RS","RU","RW","SA","SB","SC","SD","SE","SG","SH","SI","SJ","SK","SL","SM","SN","SO","SR","SS","ST","SV","SX","SY","SZ","TC","TD","TF","TG","TH","TJ","TK","TL","TM","TN","TO","TR","TT","TV","TW","TZ","UA","UG","UM","US","UY","UZ","VA","VC","VE","VG","VI","VN","VU","WF","WS","XK","YE","YT","ZA","ZM","ZW"]},"states":{"name":"States","choices":["Alabama","Alaska","Arizona","Arkansas","California","Colorado","Connecticut","Delaware","District of Columbia","Florida","Georgia","Hawaii","Idaho","Illinois","Indiana","Iowa","Kansas","Kentucky","Louisiana","Maine","Maryland","Massachusetts","Michigan","Minnesota","Mississippi","Missouri","Montana","Nebraska","Nevada","New Hampshire","New Jersey","New Mexico","New York","North Carolina","North Dakota","Ohio","Oklahoma","Oregon","Pennsylvania","Rhode Island","South Carolina","South Dakota","Tennessee","Texas","Utah","Vermont","Virginia","Washington","West Virginia","Wisconsin","Wyoming"]},"states_postal":{"name":"States Postal Code","choices":["AL","AK","AZ","AR","CA","CO","CT","DE","DC","FL","GA","HI","ID","IL","IN","IA","KS","KY","LA","ME","MD","MA","MI","MN","MS","MO","MT","NE","NV","NH","NJ","NM","NY","NC","ND","OH","OK","OR","PA","RI","SC","SD","TN","TX","UT","VT","VA","WA","WV","WI","WY"]},"months":{"name":"Months","choices":["January","February","March","April","May","June","July","August","September","October","November","December"]},"days":{"name":"Days","choices":["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]}}</script>
		<!-- Confirmation block 'message' field template -->
		<script type="text/html" id="tmpl-wpforms-builder-confirmations-message-field">
			<div id="wpforms-panel-field-confirmations-message-{{ data.id }}-wrap" class="wpforms-panel-field wpforms-panel-field-tinymce" style="display: block;">
				<label for="wpforms-panel-field-confirmations-message-{{ data.id }}">Confirmation Message</label>
				<textarea id="wpforms-panel-field-confirmations-message-{{ data.id }}" name="settings[confirmations][{{ data.id }}][message]" rows="3" placeholder="" class="wpforms-panel-field-confirmations-message"></textarea>
				<a href="#" class="toggle-smart-tag-display toggle-unfoldable-cont" data-type="all" data-fields=""><i class="fa fa-tags"></i><span>Show Smart Tags</span></a>
			</div>
		</script>

		<!-- Conditional logic toggle field template -->
		<script  type="text/html" id="tmpl-wpforms-builder-conditional-logic-toggle-field">
			<div id="wpforms-panel-field-settings-{{ data.type }}s-{{ data.id }}-conditional_logic-wrap" class="wpforms-panel-field wpforms-conditionals-enable-toggle wpforms-panel-field-checkbox">
				<span class="wpforms-toggle-control">
					<input type="checkbox" id="wpforms-panel-field-settings-{{ data.type }}s-{{ data.id }}-conditional_logic-checkbox" name="settings[{{ data.type }}s][{{ data.id }}][conditional_logic]" value="1"
						class="wpforms-panel-field-conditional_logic-checkbox"
						data-name="settings[{{ data.type }}s][{{ data.id }}]"
						data-actions="{{ data.actions }}"
						data-action-desc="{{ data.actionDesc }}">
					<label class="wpforms-toggle-control-icon" for="wpforms-panel-field-settings-{{ data.type }}s-{{ data.id }}-conditional_logic-checkbox"></label>
					<label for="wpforms-panel-field-settings-{{ data.type }}s-{{ data.id }}-conditional_logic-checkbox" class="wpforms-toggle-control-label">
						Enable Conditional Logic					</label><i class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered" title="&lt;a href=&quot;https://wpforms.com/docs/how-to-use-conditional-logic-with-wpforms/?utm_campaign=plugin&#038;utm_source=WordPress&#038;utm_medium=Field%20Options&#038;utm_content=Conditional%20Logic%20Documentation&#038;utm_locale=en&quot; target=&quot;_blank&quot; rel=&quot;noopener noreferrer&quot;&gt;How to use Conditional Logic&lt;/a&gt;"></i>
				</span>
			</div>
		</script>

				<script type="text/html" id="tmpl-wpforms-conditional-block">
			<# var containerID = data.fieldName.replace(/]/g, '').replace(/\[/g, '-'); #>
			<div class="wpforms-conditional-groups" id="wpforms-conditional-groups-{{ containerID }}">
				<h4>
					<select name="{{ data.fieldName }}[conditional_type]">
						<# _.each(data.actions, function(key, val) { #>
						<option value="{{ val }}">{{ key }}</option>
						<# }) #>
					</select>
					{{ data.actionDesc }}
				</h4>
				<div class="wpforms-conditional-group" data-reference="{{ data.fieldID }}">
					<table><tbody>
					<tr class="wpforms-conditional-row" data-field-id="{{ data.fieldID }}" data-input-name="{{ data.fieldName }}">
						<td class="field">
							<select name="{{ data.fieldName }}[conditionals][0][0][field]" class="wpforms-conditional-field" data-groupid="0" data-ruleid="0">
								<option value="">{{ wpforms_builder.select_field }}</option>
							</select>
						</td>
						<td class="operator">
							<select name="{{ data.fieldName }}[conditionals][0][0][operator]" class="wpforms-conditional-operator">
								<option value="==">{{ wpforms_builder.operator_is }}</option>
								<option value="!=">{{ wpforms_builder.operator_is_not }}</option>
								<option value="e">{{ wpforms_builder.operator_empty }}</option>
								<option value="!e">{{ wpforms_builder.operator_not_empty }}</option>
								<option value="c">{{ wpforms_builder.operator_contains }}</option>
								<option value="!c">{{ wpforms_builder.operator_not_contains }}</option>
								<option value="^">{{ wpforms_builder.operator_starts }}</option>
								<option value="~">{{ wpforms_builder.operator_ends }}</option>
								<option value=">">{{ wpforms_builder.operator_greater_than }}</option>
								<option value="<">{{ wpforms_builder.operator_less_than }}</option>
							</select>
						</td>
						<td class="value">
							<select name="{{ data.fieldName }}[conditionals][0][0][value]" class="wpforms-conditional-value">
								<option value="">{{ wpforms_builder.select_choice }}</option>
							</select>
						</td>
							<td class="actions">
								<button class="wpforms-conditional-rule-add wpforms-btn wpforms-btn-sm wpforms-btn-blue" title="{{ wpforms_builder.rule_create }}">{{ wpforms_builder.and }}</button><button class="wpforms-conditional-rule-delete" title="{{ wpforms_builder.rule_delete }}"><i class="fa fa-trash" aria-hidden="true"></i></button>
							</td>
						</tr>
					</tbody></table>
					<h5>{{ wpforms_builder.or }}</h5>
				</div>
				<button class="wpforms-conditional-groups-add wpforms-btn wpforms-btn-sm wpforms-btn-blue">{{ wpforms_builder.rule_create_group }}</button>
			</div>
		</script>
				<script type="text/html" id="tmpl-wpforms-settings-anti-spam-keyword-filter-reformat-warning-template">
			<div class="wpforms-alert wpforms-alert-warning wpforms-alert-dismissible wpforms-alert-keyword-filter-reformat">
	<div class="wpforms-alert-message">
		<p>It appears your keyword filter list is comma-separated. Would you like to reformat it?</p>
	</div>

	<div class="wpforms-alert-buttons">
		<button type="button" class="wpforms-btn wpforms-btn-sm wpforms-btn-light-grey wpforms-btn-keyword-filter-reformat">
			Yes, Reformat		</button>
	</div>
</div>
		</script>
				<script type="text/html" id="tmpl-wpforms-content-editor-tools">
			<div id="wp-wpforms-field-{{data.optionId}}-content-editor-tools" class="wp-editor-tools hide-if-no-js">
				<div id="wp-wpforms-field-{{data.optionId}}-content-media-buttons" class="wp-media-buttons">
					<button type="button" id="insert-media-button" class="button insert-media add_media" data-editor="wpforms-field-{{data.optionId}}-content">
						<span class="wp-media-buttons-icon"></span>
						Add Media					</button>
				</div>
				<div class="wp-editor-tabs">
					<button type="button" id="wpforms-field-{{data.optionId}}-content-tmce" class="wp-switch-editor switch-tmce" data-wp-editor-id="wpforms-field-{{data.optionId}}-content">
						Visual					</button>
					<button type="button" id="wpforms-field-{{data.optionId}}-content-html" class="wp-switch-editor switch-html" data-wp-editor-id="wpforms-field-{{data.optionId}}-content">
						Text					</button>
				</div>
			</div>
		</script>
				<script type="text/javascript">
			jQuery(function($){
				$( '#wpforms-add-fields-credit-card' ).remove();
			});
		</script>
				<script type="text/html" id="tmpl-wpforms-layout-field-column-plus-placeholder-template">
			<div class="wpforms-layout-column-placeholder" title="Click to set this column as default. Click again to unset.">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="normal-icon">
					<path d="M18.2 11.71a.62.62 0 0 0-.59-.58h-4.74V6.39a.62.62 0 0 0-.58-.58h-.58a.59.59 0 0 0-.58.58v4.74H6.39a.59.59 0 0 0-.58.58v.58c0 .34.24.58.58.58h4.74v4.74c0 .34.24.58.58.58h.58c.3 0 .58-.24.58-.58v-4.74h4.74c.3 0 .58-.24.58-.58v-.58ZM24 12a12 12 0 1 0-24 0 12 12 0 0 0 24 0Zm-1.55 0a10.44 10.44 0 1 1-20.9 0C1.55 6.29 6.19 1.55 12 1.55A10.5 10.5 0 0 1 22.45 12Z" class="wpforms-plus-path"/>
				</svg>
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="active-icon">
					<path d="M12 24a12 12 0 1 0 0-24 12 12 0 0 0 0 24ZM1.55 12C1.55 6.29 6.19 1.55 12 1.55A10.5 10.5 0 0 1 22.45 12a10.44 10.44 0 1 1-20.9 0ZM6 11.42a.56.56 0 0 0 0 .82l.34.34c.24.24.58.24.82 0l4.02-4.16v9.2c0 .33.24.57.58.57h.48c.3 0 .58-.24.58-.58v-9.2l3.97 4.17c.24.24.58.24.82 0l.34-.34a.56.56 0 0 0 0-.82L12.4 5.85a.56.56 0 0 0-.83 0L6 11.42Z" class="wpforms-plus-path"/>
				</svg>
			</div>		</script>
				<script type="text/html" id="tmpl-wpforms-field-preview-checkbox-radio-payment-multiple">
			<# if ( data.settings.choices_images ) { #>
			<ul class="primary-input wpforms-image-choices wpforms-image-choices-{{ data.settings.choices_images_style }}">
				<# _.each( data.order, function( choiceID, key ) {  #>
				<li class="wpforms-image-choices-item<# if ( 1 === data.settings.choices[choiceID].default ) { print( ' wpforms-selected' ); } #>">
					<label>
						<span class="wpforms-image-choices-image">
							<# if ( ! _.isEmpty( data.settings.choices[choiceID].image ) ) { #>
							<img src="{{ data.settings.choices[choiceID].image }}" alt="{{ data.settings.choices[choiceID].label }}" title="{{ data.settings.choices[choiceID].label }}">
							<# } else { #>
							<img src="{{ wpforms_builder.image_placeholder }}" alt="{{ data.settings.choices[choiceID].label }}" title="{{ data.settings.choices[choiceID].label }}">
							<# } #>
						</span>
						<# if ( 'none' === data.settings.choices_images_style ) { #>
							<br>
							<input type="{{ data.type }}" readonly<# if ( 1 === data.settings.choices[choiceID].default ) { print( ' checked' ); } #>>
						<# } else { #>
							<input class="wpforms-screen-reader-element" type="{{ data.type }}" readonly<# if ( 1 === data.settings.choices[choiceID].default ) { print( ' checked' ); } #>>
						<# } #>
						<span class="wpforms-image-choices-label">
							{{{ WPFormsBuilder.fieldChoiceLabel( data, choiceID ) }}}
						</span>
					</label>
				</li>
				<# }) #>
			</ul>
			<# } else if ( data.settings.choices_icons ) { #>
			<ul class='primary-input wpforms-icon-choices wpforms-icon-choices-{{ data.settings.choices_icons_style }} wpforms-icon-choices-{{ data.settings.choices_icons_size }}' style="--wpforms-icon-choices-color: {{ data.settings.choices_icons_color }};">
				<# _.each( data.order, function( choiceID, key ) { #>
				<li class="wpforms-icon-choices-item<# if ( 1 === data.settings.choices[choiceID].default ) { print( ' wpforms-selected' ); } #>">
					<label>
						<span class="wpforms-icon-choices-icon">
							<i class="ic-fa-{{ data.settings.choices[choiceID].icon_style }} ic-fa-{{ data.settings.choices[choiceID].icon }}"></i>
							<span class="wpforms-icon-choices-icon-bg"></span>
						</span>
						<# if ( 'none' === data.settings.choices_icons_style ) { #>
							<input type='{{ data.type }}' readonly<# if ( 1 === data.settings.choices[choiceID].default ) { print( ' checked' ); } #>>
						<# } else { #>
							<input class='wpforms-screen-reader-element' type='{{ data.type }}' readonly<# if ( 1 === data.settings.choices[choiceID].default ) { print( ' checked' ); } #>>
						<# } #>
						<span class='wpforms-icon-choices-label'>
							{{{ WPFormsBuilder.fieldChoiceLabel( data, choiceID ) }}}
						</span>
					</label>
				</li>
				<# }) #>
			</ul>
			<# } else { #>
			<ul class="primary-input">
				<# _.each( data.order, function( choiceID, key ) {  #>
				<li>
					<input type="{{ data.type }}" readonly<# if ( 1 === data.settings.choices[choiceID].default ) { print( ' checked' ); } #>>
					{{{ WPFormsBuilder.fieldChoiceLabel( data, choiceID ) }}}
				</li>
				<# }) #>
			</ul>
			<# } #>
		</script>
				<script type="text/html" id="tmpl-wpforms-choices-limit-message">
			<div class="wpforms-alert-dynamic wpforms-alert wpforms-alert-warning">
				Showing the first 20 choices.<br> All {{ data.total }} choices will be displayed when viewing the form.			</div>
		</script>
				<script type="text/html" id="tmpl-wpforms-empty-choice-message">
			<div class="wpforms-notice-dynamic-empty wpforms-alert wpforms-alert-warning">
				{{ data.message }}
			</div>
		</script>
        @endverbatim



    <script src="{{ asset('wpforms/assets/custom/load-scripts_002.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/formidable_admin_global.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/admin-metabox-sitenotes.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/list.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/jquery-confirm.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/purify.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/admin-utils.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/templates.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/jquery.tooltipster.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/choices.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/wpforms-choicesjs.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/js/admin-builder.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/form-embed-wizard.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/drag-fields.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/admin-builder-providers.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/jquery.insert-at-caret.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/jquery.minicolors.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/jquery.conditionals.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/utils.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/admin-builder-stripe.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/admin-builder-modern-stripe.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/builder-conditional-logic-core.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/md5.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/custom/internal-information-field.min.js') }}"></script>
    <script src="{{ asset('wpforms/assets/pro/js/admin/builder/layout.js') }}"></script>

@endsection

@section('page-script')
    <script src={{ asset('assets/js/custom/select2.js') }}></script>
    <script src={{ asset('assets/js/custom/flatpickr.js') }}></script>
@endsection

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />

@endsection

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">User / View /</span> Account
    </h4>
    <div class="row">

        <!-- User Sidebar -->
        <div class="col-12 ">
            <!-- User Card -->
            <div class="card mb-4 ">
                <div class="card-body">

                    <div class="row">
                        <div id="wpforms-builder" class="wpforms-admin-page wpforms-revisions-enabled">





                            <form name="wpforms-builder" id="wpforms-builder-form" method="post" data-id="2481"
                                data-revision="">

                                <input type="hidden" name="id" value="2481">
                                <input type="hidden" value="28" name="field_id" id="wpforms-field-id">


                                <!-- Toolbar -->
                                <div class="wpforms-toolbar">

                                    <div class="wpforms-left">
                                        <img src="http://www.horizonadvksa.com/demo/wp-content/plugins/wpforms/assets/images/sullie-alt.png" alt="Sullie the WPForms mascot">
                                    </div>
                
                                    <div class="wpforms-center">
                
                                        
                                            Now editing							<span class="wpforms-center-form-name wpforms-form-name">New Years Party RSVP...</span>
                
                                        
                                    </div>
                
                                    <div class="wpforms-right">
                
                                        <button id="wpforms-help" class="wpforms-btn wpforms-btn-toolbar wpforms-btn-light-grey" title="Help Ctrl+H">
                                                <i class="fa fa-question-circle-o"></i><span>Help</span>
                                        </button>
                
                                        
                                                                            <a href="http://www.horizonadvksa.com/demo?wpforms_form_preview=2474&amp;new_window=1" id="wpforms-preview-btn" class="wpforms-btn wpforms-btn-toolbar wpforms-btn-light-grey" title="Preview Form Ctrl+P" target="_blank" rel="noopener noreferrer">
                                                    <i class="fa fa-eye"></i><span class="text">Preview</span>
                                                </a>
                                            
                                                                            <button id="wpforms-embed" class="wpforms-btn wpforms-btn-toolbar wpforms-btn-light-grey" title="Embed Form Ctrl+B">
                                                        <i class="fa fa-code"></i><span class="text">Embed</span>
                                                </button>
                                            
                                            <button id="wpforms-save" class="wpforms-btn wpforms-btn-toolbar wpforms-btn-orange" title="Save Form Ctrl+S">
                                                    <i class="fa fa-check"></i><i class="wpforms-loading-spinner wpforms-loading-white wpforms-loading-inline wpforms-hidden"></i><span class="text">Save</span>
                                            </button>
                
                                        
                                        <button id="wpforms-exit" title="Exit Ctrl+Q" class="">
                                            <i class="fa fa-times"></i>
                                        </button>
                
                                    </div>
                
                                </div>

                                <!-- Panel toggle buttons. -->


                                <div class="wpforms-panels">

                                    <div class="wpforms-panel wpforms-panel-fields active" id="wpforms-panel-fields">
                                        <div class="wpforms-panel-sidebar-content row">
                                            <div class="wpforms-panel-sidebar-toggle">
                                                <div class="wpforms-panel-sidebar-toggle-vertical-line"></div>
                                                <div class="wpforms-panel-sidebar-toggle-icon"><i
                                                        class="fa fa-angle-left"></i></div>
                                            </div>
                                            <div class="wpforms-panel-sidebar col-lg-3">
                                                <ul class="wpforms-tabs wpforms-clear">

                                                    <li class="wpforms-tab" id="add-fields">
                                                        <a href="#" class="active">
                                                            <i class="fa fa-list-alt"></i>Add Fields </a>
                                                    </li>

                                                    <li class="wpforms-tab" id="field-options">
                                                        <a href="#" class="">
                                                            <i class="fa fa-sliders"></i>Field Options </a>
                                                    </li>

                                                </ul>

                                                <div class="wpforms-add-fields wpforms-tab-content" style="">
                                                    <div class="wpforms-search-fields-wrapper">
                                                        <div class="wpforms-search-fields-input-wrapper">
                                                            <label for="wpforms-search-fields-input"
                                                                class="wpforms-screen-reader-element">Search fields:</label>
                                                            <input type="search" id="wpforms-search-fields-input"
                                                                placeholder="Search fields..." autocomplete="off">
                                                            <i class="fa fa-times wpforms-search-fields-input-close"
                                                                aria-hidden="true"></i>
                                                        </div>

                                                        <div class="wpforms-search-fields-list" style="display: none;">
                                                            <div class="wpforms-add-fields-group">
                                                                <div class="wpforms-add-fields-buttons"></div>
                                                            </div>
                                                        </div>

                                                        <div class="wpforms-search-fields-no-results"
                                                            style="display: none;">
                                                            <p>
                                                                Sorry, we didn't find any fields that match your criteria.
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="wpforms-add-fields-group"><a href="#"
                                                            class="wpforms-add-fields-heading"
                                                            data-group="standard"><span>Standard Fields</span><i
                                                                class="fa fa-angle-down"></i></a>
                                                        <div class="wpforms-add-fields-buttons"><button
                                                                id="wpforms-add-fields-text"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="text" data-utm-content=""><i
                                                                    class="fa fa-text-width"></i> Single Line
                                                                Text</button><button id="wpforms-add-fields-textarea"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="textarea" data-utm-content=""
                                                                data-field-keywords="textarea"><i
                                                                    class="fa fa-paragraph"></i> Paragraph
                                                                Text</button><button id="wpforms-add-fields-select"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="select" data-utm-content=""
                                                                data-field-keywords="choice"><i
                                                                    class="fa fa-caret-square-o-down"></i>
                                                                Dropdown</button><button id="wpforms-add-fields-radio"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="radio" data-utm-content=""
                                                                data-field-keywords="radio"><i
                                                                    class="fa fa-dot-circle-o"></i> Multiple
                                                                Choice</button><button id="wpforms-add-fields-checkbox"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="checkbox" data-utm-content=""
                                                                data-field-keywords="choice"><i
                                                                    class="fa fa-check-square-o"></i>
                                                                Checkboxes</button><button id="wpforms-add-fields-number"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="number" data-utm-content=""><i
                                                                    class="fa fa-hashtag"></i> Numbers</button><button
                                                                id="wpforms-add-fields-name"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="name" data-utm-content=""
                                                                data-field-keywords="user, first, last"><i
                                                                    class="fa fa-user"></i> Name</button><button
                                                                id="wpforms-add-fields-email"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="email" data-utm-content=""
                                                                data-field-keywords="user"><i
                                                                    class="fa fa-envelope-o"></i> Email</button><button
                                                                id="wpforms-add-fields-number-slider"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="number-slider" data-utm-content=""><i
                                                                    class="fa fa-sliders"></i> Number
                                                                Slider</button><button
                                                                id="wpforms-add-fields-captcha_recaptcha"
                                                                class="wpforms-add-fields-button not-draggable"
                                                                data-field-type="captcha_recaptcha"
                                                                data-utm-content="CAPTCHA"
                                                                data-field-keywords="captcha, spam, antispam"><i
                                                                    class="fa fa-question-circle-o"></i> CAPTCHA</button>
                                                        </div>
                                                    </div>
                                                    <div class="wpforms-add-fields-group"><a href="#"
                                                            class="wpforms-add-fields-heading"
                                                            data-group="fancy"><span>Fancy Fields</span><i
                                                                class="fa fa-angle-down"></i></a>
                                                        <div class="wpforms-add-fields-buttons"><button
                                                                id="wpforms-add-fields-phone"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="phone" data-utm-content="Phone"
                                                                data-field-keywords="telephone, mobile, cell"><i
                                                                    class="fa fa-phone"></i> Phone</button><button
                                                                id="wpforms-add-fields-address"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="address" data-utm-content="Address"><i
                                                                    class="fa fa-map-marker"></i> Address</button><button
                                                                id="wpforms-add-fields-date-time"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="date-time"
                                                                data-utm-content="Date / Time"><i
                                                                    class="fa fa-calendar-o"></i> Date /
                                                                Time</button><button id="wpforms-add-fields-url"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="url" data-utm-content="Website / URL"
                                                                data-field-keywords="uri, link, hyperlink"><i
                                                                    class="fa fa-link"></i> Website / URL</button><button
                                                                id="wpforms-add-fields-file-upload"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="file-upload"
                                                                data-utm-content="File Upload"><i
                                                                    class="fa fa-upload"></i> File Upload</button><button
                                                                id="wpforms-add-fields-password"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="password" data-utm-content="Password"
                                                                data-field-keywords="user"><i class="fa fa-lock"></i>
                                                                Password</button><button id="wpforms-add-fields-richtext"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="richtext" data-utm-content="Rich Text"
                                                                data-field-keywords="image, text, table, list, heading, wysiwyg, visual"><i
                                                                    class="fa fa-pencil-square-o"></i> Rich
                                                                Text</button><button id="wpforms-add-fields-layout"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="layout" data-utm-content="Layout"
                                                                data-field-keywords="column, row"><i
                                                                    class="fa fa-columns"></i> Layout</button><button
                                                                id="wpforms-add-fields-pagebreak"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="pagebreak" data-utm-content="Page Break"
                                                                data-field-keywords="progress bar, multi step, multi part"><i
                                                                    class="fa-solid fa-copy fa-fw"></i> Page
                                                                Break</button><button id="wpforms-add-fields-divider"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="divider"
                                                                data-utm-content="Section Divider"
                                                                data-field-keywords="line, hr"><i
                                                                    class="fa fa-arrows-h"></i> Section
                                                                Divider</button><button id="wpforms-add-fields-html"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="html" data-utm-content="HTML"
                                                                data-field-keywords="code"><i class="fa fa-code"></i>
                                                                HTML</button><button id="wpforms-add-fields-content"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="content" data-utm-content="Content"
                                                                data-field-keywords="image, text, table, list, heading, wysiwyg, visual"><i
                                                                    class="fa fa-file-image-o"></i> Content</button><button
                                                                id="wpforms-add-fields-entry-preview"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="entry-preview"
                                                                data-utm-content="Entry Preview"
                                                                data-field-keywords="confirm"><i
                                                                    class="fa fa-file-text-o"></i> Entry
                                                                Preview</button><button id="wpforms-add-fields-rating"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="rating" data-utm-content="Rating"
                                                                data-field-keywords="review, emoji, star"><i
                                                                    class="fa fa-star"></i> Rating</button><button
                                                                id="wpforms-add-fields-hidden"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="hidden"
                                                                data-utm-content="Hidden Field"><i
                                                                    class="fa fa-eye-slash"></i> Hidden Field</button>
                                                        </div>
                                                    </div>
                                                    <div class="wpforms-add-fields-group"><a href="#"
                                                            class="wpforms-add-fields-heading"
                                                            data-group="payment"><span>Payment Fields</span><i
                                                                class="fa fa-angle-down"></i></a>
                                                        <div class="wpforms-add-fields-buttons"><button
                                                                id="wpforms-add-fields-payment-single"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="payment-single" data-utm-content=""
                                                                data-field-keywords="product, store, ecommerce, pay, payment"><i
                                                                    class="fa fa-file-o"></i> Single Item</button><button
                                                                id="wpforms-add-fields-payment-checkbox"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="payment-checkbox" data-utm-content=""
                                                                data-field-keywords="product, store, ecommerce, pay, payment"><i
                                                                    class="fa fa-check-square-o"></i> Checkbox
                                                                Items</button><button
                                                                id="wpforms-add-fields-payment-multiple"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="payment-multiple" data-utm-content=""
                                                                data-field-keywords="product, store, ecommerce, pay, payment"><i
                                                                    class="fa fa-list-ul"></i> Multiple
                                                                Items</button><button
                                                                id="wpforms-add-fields-payment-select"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="payment-select" data-utm-content=""
                                                                data-field-keywords="product, store, ecommerce, pay, payment"><i
                                                                    class="fa fa-caret-square-o-down"></i> Dropdown
                                                                Items</button><button
                                                                id="wpforms-add-fields-stripe-credit-card"
                                                                class="wpforms-add-fields-button warning-modal stripe-keys-required"
                                                                data-field-type="stripe-credit-card" data-utm-content=""
                                                                data-field-keywords="store, ecommerce, credit card, pay, payment, debit card"><i
                                                                    class="fa fa-credit-card"></i> Stripe Credit
                                                                Card</button><button id="wpforms-add-fields-payment-total"
                                                                class="wpforms-add-fields-button ui-draggable ui-draggable-handle"
                                                                data-field-type="payment-total" data-utm-content=""
                                                                data-field-keywords="store, ecommerce, pay, payment, sum"><i
                                                                    class="fa fa-money"></i> Total</button></div>
                                                    </div>
                                                </div>

                                                <div id="wpforms-field-options"
                                                    class="wpforms-field-options wpforms-tab-content"
                                                    style="display: none;">
                                                    <div class="wpforms-field-option wpforms-field-option-pagebreak "
                                                        id="wpforms-field-option-24" data-field-id="24"
                                                        style="display: none;"><input type="hidden"
                                                            name="fields[24][id]" value="24"
                                                            class="wpforms-field-option-hidden-id"><input type="hidden"
                                                            name="fields[24][type]" value="pagebreak"
                                                            class="wpforms-field-option-hidden-type"><input type="hidden"
                                                            class="position" id="wpforms-field-option-24-position"
                                                            name="fields[24][position]" value="top" placeholder="">
                                                        <div class="wpforms-field-option-field-title">Page Break <span>(ID
                                                                #24)</span></div>
                                                        <div class="wpforms-field-option-group wpforms-field-option-group-basic active wpforms-pagebreak-top"
                                                            id="wpforms-field-option-basic-24">
                                                            <a href="#"
                                                                class="wpforms-field-option-group-toggle">General</a>
                                                            <div
                                                                class="wpforms-field-option-group-inner wpforms-pagebreak-top">
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-indicator "
                                                                    id="wpforms-field-option-row-24-indicator"
                                                                    data-field-id="24"><label
                                                                        for="wpforms-field-option-24-indicator">Progress
                                                                        Indicator<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><select
                                                                        class=""
                                                                        id="wpforms-field-option-24-indicator"
                                                                        name="fields[24][indicator]">
                                                                        <option value="progress" selected="selected">
                                                                            Progress Bar</option>
                                                                        <option value="circles">Circles</option>
                                                                        <option value="connector">Connector</option>
                                                                        <option value="none">None</option>
                                                                    </select></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-indicator_color color-picker-row"
                                                                    id="wpforms-field-option-row-24-indicator_color"
                                                                    data-field-id="24"><label
                                                                        for="wpforms-field-option-24-indicator_color">Page
                                                                        Indicator Color<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label>
                                                                    <div
                                                                        class="minicolors minicolors-theme-default minicolors-position-bottom minicolors-position-left">
                                                                        <input type="text"
                                                                            class="wpforms-color-picker minicolors-input"
                                                                            id="wpforms-field-option-24-indicator_color"
                                                                            name="fields[24][indicator_color]"
                                                                            value="#066aab" placeholder=""
                                                                            data-fallback-color="#066aab"
                                                                            size="7"><span
                                                                            class="minicolors-swatch minicolors-sprite minicolors-input-swatch"><span
                                                                                class="minicolors-swatch-color"
                                                                                style="background-color: rgb(6, 106, 171);"></span></span>
                                                                        <div
                                                                            class="minicolors-panel minicolors-slider-hue">
                                                                            <div
                                                                                class="minicolors-slider minicolors-sprite">
                                                                                <div class="minicolors-picker"
                                                                                    style="top: 65.1515px;"></div>
                                                                            </div>
                                                                            <div
                                                                                class="minicolors-opacity-slider minicolors-sprite">
                                                                                <div class="minicolors-picker"></div>
                                                                            </div>
                                                                            <div class="minicolors-grid minicolors-sprite"
                                                                                style="background-color: rgb(0, 153, 255);">
                                                                                <div class="minicolors-grid-inner"></div>
                                                                                <div class="minicolors-picker"
                                                                                    style="top: 49px; left: 145px;">
                                                                                    <div></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-title "
                                                                    id="wpforms-field-option-row-24-title"
                                                                    data-field-id="24"><label
                                                                        for="wpforms-field-option-24-title">Page Title<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-24-title"
                                                                        name="fields[24][title]" value=""
                                                                        placeholder=""></div>
                                                            </div>
                                                        </div>
                                                        <div class="wpforms-field-option-group wpforms-field-option-group-advanced wpforms-pagebreak-top"
                                                            id="wpforms-field-option-advanced-24"><a href="#"
                                                                class="wpforms-field-option-group-toggle">Advanced</a>
                                                            <div class="wpforms-field-option-group-inner">
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-nav_align "
                                                                    id="wpforms-field-option-row-24-nav_align"
                                                                    data-field-id="24"><label
                                                                        for="wpforms-field-option-24-nav_align">Page
                                                                        Navigation Alignment<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><select
                                                                        class=""
                                                                        id="wpforms-field-option-24-nav_align"
                                                                        name="fields[24][nav_align]">
                                                                        <option value="left" selected="selected">Left
                                                                        </option>
                                                                        <option value="right">Right</option>
                                                                        <option value="">Center</option>
                                                                        <option value="split">Split</option>
                                                                    </select></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-scroll_disabled "
                                                                    id="wpforms-field-option-row-24-scroll_disabled"
                                                                    data-field-id="24"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-24-scroll_disabled"
                                                                            name="fields[24][scroll_disabled]"
                                                                            class="" value="1">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-24-scroll_disabled"></label>
                                                                        <label
                                                                            for="wpforms-field-option-24-scroll_disabled"
                                                                            class="wpforms-toggle-control-label">Disable
                                                                            Scroll Animation</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-css "
                                                                    id="wpforms-field-option-row-24-css"
                                                                    data-field-id="24"><label
                                                                        for="wpforms-field-option-24-css">CSS Classes<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-24-css"
                                                                        name="fields[24][css]" value=""
                                                                        placeholder=""></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="wpforms-field-option wpforms-field-option-text "
                                                        id="wpforms-field-option-22" data-field-id="22"
                                                        style="display: none;"><input type="hidden"
                                                            name="fields[22][id]" value="22"
                                                            class="wpforms-field-option-hidden-id"><input type="hidden"
                                                            name="fields[22][type]" value="text"
                                                            class="wpforms-field-option-hidden-type">
                                                        <div class="wpforms-field-option-field-title">Single Line Text
                                                            <span>(ID #22)</span></div>
                                                        <div class="wpforms-field-option-group wpforms-field-option-group-basic active"
                                                            id="wpforms-field-option-basic-22">
                                                            <a href="#"
                                                                class="wpforms-field-option-group-toggle">General</a>
                                                            <div class="wpforms-field-option-group-inner ">
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-label "
                                                                    id="wpforms-field-option-row-22-label"
                                                                    data-field-id="22"><label
                                                                        for="wpforms-field-option-22-label">Label<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-22-label"
                                                                        name="fields[22][label]" value="Single Line Text"
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-description "
                                                                    id="wpforms-field-option-row-22-description"
                                                                    data-field-id="22"><label
                                                                        for="wpforms-field-option-22-description">Description<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label>
                                                                    <textarea class="" id="wpforms-field-option-22-description" name="fields[22][description]" rows="3"></textarea>
                                                                </div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-required "
                                                                    id="wpforms-field-option-row-22-required"
                                                                    data-field-id="22"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-22-required"
                                                                            name="fields[22][required]" class=""
                                                                            value="1">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-22-required"></label>
                                                                        <label for="wpforms-field-option-22-required"
                                                                            class="wpforms-toggle-control-label">Required</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                            </div>
                                                        </div>
                                                        <div class="wpforms-field-option-group wpforms-field-option-group-advanced"
                                                            id="wpforms-field-option-advanced-22"><a href="#"
                                                                class="wpforms-field-option-group-toggle">Advanced</a>
                                                            <div class="wpforms-field-option-group-inner">
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-size "
                                                                    id="wpforms-field-option-row-22-size"
                                                                    data-field-id="22"><label
                                                                        for="wpforms-field-option-22-size">Field Size<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><select
                                                                        class="" id="wpforms-field-option-22-size"
                                                                        name="fields[22][size]">
                                                                        <option value="small">Small</option>
                                                                        <option value="medium" selected="selected">Medium
                                                                        </option>
                                                                        <option value="large">Large</option>
                                                                    </select></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-placeholder "
                                                                    id="wpforms-field-option-row-22-placeholder"
                                                                    data-field-id="22"><label
                                                                        for="wpforms-field-option-22-placeholder">Placeholder
                                                                        Text<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-22-placeholder"
                                                                        name="fields[22][placeholder]" value=""
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-limit_enabled "
                                                                    id="wpforms-field-option-row-22-limit_enabled"
                                                                    data-field-id="22"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-22-limit_enabled"
                                                                            name="fields[22][limit_enabled]"
                                                                            class="" value="1">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-22-limit_enabled"></label>
                                                                        <label for="wpforms-field-option-22-limit_enabled"
                                                                            class="wpforms-toggle-control-label">Limit
                                                                            Length</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-limit_controls wpforms-hide"
                                                                    id="wpforms-field-option-row-22-limit_controls"
                                                                    data-field-id="22"><input type="number"
                                                                        class=""
                                                                        id="wpforms-field-option-22-limit_count"
                                                                        name="fields[22][limit_count]" value="1"
                                                                        placeholder="" min="1" step="1"
                                                                        pattern="[0-9]"><select class=""
                                                                        id="wpforms-field-option-22-limit_mode"
                                                                        name="fields[22][limit_mode]">
                                                                        <option value="characters" selected="selected">
                                                                            Characters</option>
                                                                        <option value="words">Words</option>
                                                                    </select></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-default_value "
                                                                    id="wpforms-field-option-row-22-default_value"
                                                                    data-field-id="22"><label
                                                                        for="wpforms-field-option-22-default_value">Default
                                                                        Value<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i><a
                                                                            href="#"
                                                                            class="toggle-smart-tag-display toggle-unfoldable-cont"
                                                                            data-type="other"><i
                                                                                class="fa fa-tags"></i><span>Show Smart
                                                                                Tags</span></a></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-22-default_value"
                                                                        name="fields[22][default_value]" value=""
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-input_mask "
                                                                    id="wpforms-field-option-row-22-input_mask"
                                                                    data-field-id="22"><label
                                                                        for="wpforms-field-option-22-input_mask">Input
                                                                        Mask<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i><a
                                                                            href="https://wpforms.com/docs/how-to-use-custom-input-masks/?utm_campaign=plugin&amp;utm_source=WordPress&amp;utm_medium=Field%20Options&amp;utm_content=Input%20Mask%20Documentation&amp;utm_locale=en"
                                                                            class="after-label-description"
                                                                            target="_blank" rel="noopener noreferrer">See
                                                                            Examples &amp; Docs</a></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-22-input_mask"
                                                                        name="fields[22][input_mask]" value=""
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-css "
                                                                    id="wpforms-field-option-row-22-css"
                                                                    data-field-id="22"><label
                                                                        for="wpforms-field-option-22-css">CSS Classes<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i><a
                                                                            href="#"
                                                                            class="toggle-layout-selector-display toggle-unfoldable-cont"><i
                                                                                class="fa fa-th-large"></i><span>Show
                                                                                Layouts</span></a></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-22-css"
                                                                        name="fields[22][css]" value=""
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-label_hide "
                                                                    id="wpforms-field-option-row-22-label_hide"
                                                                    data-field-id="22"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-22-label_hide"
                                                                            name="fields[22][label_hide]" class=""
                                                                            value="1">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-22-label_hide"></label>
                                                                        <label for="wpforms-field-option-22-label_hide"
                                                                            class="wpforms-toggle-control-label">Hide
                                                                            Label</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                            </div>
                                                        </div>
                                                        <div class="wpforms-conditional-fields wpforms-field-option-group wpforms-field-option-group-conditionals wpforms-hide"
                                                            id="wpforms-field-option-conditionals-22">

                                                            <a href="#" class="wpforms-field-option-group-toggle">
                                                                Smart Logic </a>

                                                            <div class="wpforms-field-option-group-inner">
                                                                <div class="wpforms-conditional-block wpforms-conditional-block-field"
                                                                    data-type="field">
                                                                    <div class="wpforms-field-option-row wpforms-field-option-row-conditional_logic wpforms-conditionals-enable-toggle"
                                                                        id="wpforms-field-option-row-22-conditional_logic"
                                                                        data-field-id="22"><span
                                                                            class="wpforms-toggle-control ">

                                                                            <input type="checkbox"
                                                                                id="wpforms-field-option-22-conditional_logic"
                                                                                name="fields[22][conditional_logic]"
                                                                                class="" value="1"
                                                                                data-name="fields[22]"
                                                                                data-actions="{&quot;show&quot;:&quot;Show&quot;,&quot;hide&quot;:&quot;Hide&quot;}"
                                                                                data-action-desc="this field if">
                                                                            <label class="wpforms-toggle-control-icon"
                                                                                for="wpforms-field-option-22-conditional_logic"></label>
                                                                            <label
                                                                                for="wpforms-field-option-22-conditional_logic"
                                                                                class="wpforms-toggle-control-label">Enable
                                                                                Conditional Logic</label><i
                                                                                class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                        </span></div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="wpforms-field-option wpforms-field-option-pagebreak "
                                                        id="wpforms-field-option-23" data-field-id="23"
                                                        style="display: none;"><input type="hidden"
                                                            name="fields[23][id]" value="23"
                                                            class="wpforms-field-option-hidden-id"><input type="hidden"
                                                            name="fields[23][type]" value="pagebreak"
                                                            class="wpforms-field-option-hidden-type"><input type="hidden"
                                                            class="position" id="wpforms-field-option-23-position"
                                                            name="fields[23][position]" value="" placeholder="">
                                                        <div class="wpforms-field-option-field-title">Page Break <span>(ID
                                                                #23)</span></div>
                                                        <div class="wpforms-field-option-group wpforms-field-option-group-basic active"
                                                            id="wpforms-field-option-basic-23">
                                                            <a href="#"
                                                                class="wpforms-field-option-group-toggle">General</a>
                                                            <div class="wpforms-field-option-group-inner ">
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-title "
                                                                    id="wpforms-field-option-row-23-title"
                                                                    data-field-id="23"><label
                                                                        for="wpforms-field-option-23-title">Page Title<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-23-title"
                                                                        name="fields[23][title]" value=""
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-next "
                                                                    id="wpforms-field-option-row-23-next"
                                                                    data-field-id="23"><label
                                                                        for="wpforms-field-option-23-next">Next Label<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-23-next"
                                                                        name="fields[23][next]" value="Next"
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-prev_toggle hidden"
                                                                    id="wpforms-field-option-row-23-prev_toggle"
                                                                    data-field-id="23"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-23-prev_toggle"
                                                                            name="fields[23][prev_toggle]" class=""
                                                                            value="1">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-23-prev_toggle"></label>
                                                                        <label for="wpforms-field-option-23-prev_toggle"
                                                                            class="wpforms-toggle-control-label">Display
                                                                            Previous</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-prev wpforms-hidden hidden"
                                                                    id="wpforms-field-option-row-23-prev"
                                                                    data-field-id="23"><label
                                                                        for="wpforms-field-option-23-prev">Previous Label<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-23-prev"
                                                                        name="fields[23][prev]" value=""
                                                                        placeholder=""></div>
                                                            </div>
                                                        </div>
                                                        <div class="wpforms-field-option-group wpforms-field-option-group-advanced"
                                                            id="wpforms-field-option-advanced-23"><a href="#"
                                                                class="wpforms-field-option-group-toggle">Advanced</a>
                                                            <div class="wpforms-field-option-group-inner">
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-css "
                                                                    id="wpforms-field-option-row-23-css"
                                                                    data-field-id="23"><label
                                                                        for="wpforms-field-option-23-css">CSS Classes<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-23-css"
                                                                        name="fields[23][css]" value=""
                                                                        placeholder=""></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="wpforms-field-option wpforms-field-option-text "
                                                        id="wpforms-field-option-21" data-field-id="21"
                                                        style="display: none;"><input type="hidden"
                                                            name="fields[21][id]" value="21"
                                                            class="wpforms-field-option-hidden-id"><input type="hidden"
                                                            name="fields[21][type]" value="text"
                                                            class="wpforms-field-option-hidden-type">
                                                        <div class="wpforms-field-option-field-title">Single Line Text
                                                            <span>(ID #21)</span></div>
                                                        <div class="wpforms-field-option-group wpforms-field-option-group-basic active"
                                                            id="wpforms-field-option-basic-21">
                                                            <a href="#"
                                                                class="wpforms-field-option-group-toggle">General</a>
                                                            <div class="wpforms-field-option-group-inner ">
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-label "
                                                                    id="wpforms-field-option-row-21-label"
                                                                    data-field-id="21"><label
                                                                        for="wpforms-field-option-21-label">Label<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-21-label"
                                                                        name="fields[21][label]" value="Single Line Text"
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-description "
                                                                    id="wpforms-field-option-row-21-description"
                                                                    data-field-id="21"><label
                                                                        for="wpforms-field-option-21-description">Description<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label>
                                                                    <textarea class="" id="wpforms-field-option-21-description" name="fields[21][description]" rows="3"></textarea>
                                                                </div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-required "
                                                                    id="wpforms-field-option-row-21-required"
                                                                    data-field-id="21"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-21-required"
                                                                            name="fields[21][required]" class=""
                                                                            value="1">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-21-required"></label>
                                                                        <label for="wpforms-field-option-21-required"
                                                                            class="wpforms-toggle-control-label">Required</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                            </div>
                                                        </div>
                                                        <div class="wpforms-field-option-group wpforms-field-option-group-advanced"
                                                            id="wpforms-field-option-advanced-21"><a href="#"
                                                                class="wpforms-field-option-group-toggle">Advanced</a>
                                                            <div class="wpforms-field-option-group-inner">
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-size "
                                                                    id="wpforms-field-option-row-21-size"
                                                                    data-field-id="21"><label
                                                                        for="wpforms-field-option-21-size">Field Size<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><select
                                                                        class="" id="wpforms-field-option-21-size"
                                                                        name="fields[21][size]" title="">
                                                                        <option value="small">Small</option>
                                                                        <option value="medium" selected="selected">Medium
                                                                        </option>
                                                                        <option value="large">Large</option>
                                                                    </select><label
                                                                        class="sub-label wpforms-notice-field-size wpforms-hidden"
                                                                        title="When a field is placed inside a column, the field size always equals the column width.">
                                                                        Field size cannot be changed when used in a layout.
                                                                    </label></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-placeholder "
                                                                    id="wpforms-field-option-row-21-placeholder"
                                                                    data-field-id="21"><label
                                                                        for="wpforms-field-option-21-placeholder">Placeholder
                                                                        Text<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-21-placeholder"
                                                                        name="fields[21][placeholder]" value=""
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-limit_enabled "
                                                                    id="wpforms-field-option-row-21-limit_enabled"
                                                                    data-field-id="21"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-21-limit_enabled"
                                                                            name="fields[21][limit_enabled]"
                                                                            class="" value="1">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-21-limit_enabled"></label>
                                                                        <label for="wpforms-field-option-21-limit_enabled"
                                                                            class="wpforms-toggle-control-label">Limit
                                                                            Length</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-limit_controls wpforms-hide"
                                                                    id="wpforms-field-option-row-21-limit_controls"
                                                                    data-field-id="21"><input type="number"
                                                                        class=""
                                                                        id="wpforms-field-option-21-limit_count"
                                                                        name="fields[21][limit_count]" value="1"
                                                                        placeholder="" min="1" step="1"
                                                                        pattern="[0-9]"><select class=""
                                                                        id="wpforms-field-option-21-limit_mode"
                                                                        name="fields[21][limit_mode]">
                                                                        <option value="characters" selected="selected">
                                                                            Characters</option>
                                                                        <option value="words">Words</option>
                                                                    </select></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-default_value "
                                                                    id="wpforms-field-option-row-21-default_value"
                                                                    data-field-id="21"><label
                                                                        for="wpforms-field-option-21-default_value">Default
                                                                        Value<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i><a
                                                                            href="#"
                                                                            class="toggle-smart-tag-display toggle-unfoldable-cont"
                                                                            data-type="other"><i
                                                                                class="fa fa-tags"></i><span>Show Smart
                                                                                Tags</span></a></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-21-default_value"
                                                                        name="fields[21][default_value]" value=""
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-input_mask "
                                                                    id="wpforms-field-option-row-21-input_mask"
                                                                    data-field-id="21"><label
                                                                        for="wpforms-field-option-21-input_mask">Input
                                                                        Mask<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i><a
                                                                            href="https://wpforms.com/docs/how-to-use-custom-input-masks/?utm_campaign=plugin&amp;utm_source=WordPress&amp;utm_medium=Field%20Options&amp;utm_content=Input%20Mask%20Documentation&amp;utm_locale=en"
                                                                            class="after-label-description"
                                                                            target="_blank" rel="noopener noreferrer">See
                                                                            Examples &amp; Docs</a></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-21-input_mask"
                                                                        name="fields[21][input_mask]" value=""
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-css "
                                                                    id="wpforms-field-option-row-21-css"
                                                                    data-field-id="21"><label
                                                                        for="wpforms-field-option-21-css">CSS Classes<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i><a
                                                                            href="#"
                                                                            class="toggle-layout-selector-display toggle-unfoldable-cont"><i
                                                                                class="fa fa-th-large"></i><span>Show
                                                                                Layouts</span></a></label>
                                                                    <div class="layout-selector-display unfoldable-cont">
                                                                        <p class="heading wpforms-hidden-strict">Select
                                                                            your layout</p>
                                                                        <div class="layouts wpforms-hidden-strict">
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="one-half"
                                                                                    data-classes="wpforms-one-half wpforms-first"></span><span
                                                                                    class="one-half"
                                                                                    data-classes="wpforms-one-half"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="one-third"
                                                                                    data-classes="wpforms-one-third wpforms-first"></span><span
                                                                                    class="one-third"
                                                                                    data-classes="wpforms-one-third"></span><span
                                                                                    class="one-third"
                                                                                    data-classes="wpforms-one-third"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth wpforms-first"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="one-third"
                                                                                    data-classes="wpforms-one-third wpforms-first"></span><span
                                                                                    class="two-third"
                                                                                    data-classes="wpforms-two-thirds"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="two-third"
                                                                                    data-classes="wpforms-two-thirds wpforms-first"></span><span
                                                                                    class="one-third"
                                                                                    data-classes="wpforms-one-third"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth wpforms-first"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span><span
                                                                                    class="two-fourth"
                                                                                    data-classes="wpforms-two-fourths"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="two-fourth"
                                                                                    data-classes="wpforms-two-fourths wpforms-first"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth wpforms-first"></span><span
                                                                                    class="two-fourth"
                                                                                    data-classes="wpforms-two-fourths"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span>
                                                                            </div>
                                                                        </div>
                                                                        <div
                                                                            class="wpforms-alert-warning wpforms-alert-layout wpforms-alert wpforms-alert-nomargin">
                                                                            <h4>Layouts Have Moved!</h4>
                                                                            <p>We’ve added a new field to help you build
                                                                                advanced form layouts more easily. Give the
                                                                                Layout Field a try! Layout CSS classes are
                                                                                still supported. <a
                                                                                    href="https://wpforms.com/docs/how-to-use-the-layout-field-in-wpforms/?utm_campaign=plugin&amp;utm_source=WordPress&amp;utm_medium=Field%20Options&amp;utm_content=How%20to%20Use%20the%20Layout%20Field%20Documentation&amp;utm_locale=en"
                                                                                    target="_blank"
                                                                                    rel="noopener noreferrer">Learn
                                                                                    More</a></p>
                                                                        </div>
                                                                    </div><input type="text" class=""
                                                                        id="wpforms-field-option-21-css"
                                                                        name="fields[21][css]" value=""
                                                                        placeholder="">
                                                                </div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-label_hide "
                                                                    id="wpforms-field-option-row-21-label_hide"
                                                                    data-field-id="21"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-21-label_hide"
                                                                            name="fields[21][label_hide]" class=""
                                                                            value="1">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-21-label_hide"></label>
                                                                        <label for="wpforms-field-option-21-label_hide"
                                                                            class="wpforms-toggle-control-label">Hide
                                                                            Label</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                            </div>
                                                        </div>
                                                        <div class="wpforms-conditional-fields wpforms-field-option-group wpforms-field-option-group-conditionals wpforms-hide"
                                                            id="wpforms-field-option-conditionals-21">

                                                            <a href="#" class="wpforms-field-option-group-toggle">
                                                                Smart Logic </a>

                                                            <div class="wpforms-field-option-group-inner">
                                                                <div class="wpforms-conditional-block wpforms-conditional-block-field"
                                                                    data-type="field">
                                                                    <div class="wpforms-field-option-row wpforms-field-option-row-conditional_logic wpforms-conditionals-enable-toggle"
                                                                        id="wpforms-field-option-row-21-conditional_logic"
                                                                        data-field-id="21"><span
                                                                            class="wpforms-toggle-control ">

                                                                            <input type="checkbox"
                                                                                id="wpforms-field-option-21-conditional_logic"
                                                                                name="fields[21][conditional_logic]"
                                                                                class="" value="1"
                                                                                data-name="fields[21]"
                                                                                data-actions="{&quot;show&quot;:&quot;Show&quot;,&quot;hide&quot;:&quot;Hide&quot;}"
                                                                                data-action-desc="this field if">
                                                                            <label class="wpforms-toggle-control-icon"
                                                                                for="wpforms-field-option-21-conditional_logic"></label>
                                                                            <label
                                                                                for="wpforms-field-option-21-conditional_logic"
                                                                                class="wpforms-toggle-control-label">Enable
                                                                                Conditional Logic</label><i
                                                                                class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                        </span></div>

                                                                    <div class="wpforms-conditional-groups"
                                                                        id="wpforms-conditional-groups-fields-21">
                                                                        <h4>
                                                                            <select name="fields[21][conditional_type]">

                                                                                <option value="show">Show</option>

                                                                                <option value="hide">Hide</option>

                                                                            </select>
                                                                            this field if
                                                                        </h4>
                                                                        <div class="wpforms-conditional-group"
                                                                            data-reference="21">
                                                                            <table>
                                                                                <tbody>
                                                                                    <tr class="wpforms-conditional-row"
                                                                                        data-field-id="21"
                                                                                        data-input-name="fields[21]">
                                                                                        <td class="field">
                                                                                            <select
                                                                                                name="fields[21][conditionals][0][0][field]"
                                                                                                class="wpforms-conditional-field"
                                                                                                data-groupid="0"
                                                                                                data-ruleid="0">
                                                                                                <option value="">---
                                                                                                    Select Field ---
                                                                                                </option>
                                                                                                <option value="22">
                                                                                                    Single Line Text
                                                                                                </option>
                                                                                                <option value="1">
                                                                                                    Email</option>
                                                                                                <option value="2">
                                                                                                    Comment or Message
                                                                                                </option>
                                                                                            </select>
                                                                                        </td>
                                                                                        <td class="operator">
                                                                                            <select
                                                                                                name="fields[21][conditionals][0][0][operator]"
                                                                                                class="wpforms-conditional-operator">
                                                                                                <option value="==">is
                                                                                                </option>
                                                                                                <option value="!=">is
                                                                                                    not</option>
                                                                                                <option value="e">
                                                                                                    empty</option>
                                                                                                <option value="!e">not
                                                                                                    empty</option>
                                                                                                <option value="c">
                                                                                                    contains</option>
                                                                                                <option value="!c">does
                                                                                                    not contain</option>
                                                                                                <option value="^">
                                                                                                    starts with</option>
                                                                                                <option value="~">ends
                                                                                                    with</option>
                                                                                                <option value=">">
                                                                                                    greater than</option>
                                                                                                <option value="<">less
                                                                                                    than</option>
                                                                                            </select>
                                                                                        </td>
                                                                                        <td class="value"><input
                                                                                                type="text"
                                                                                                name="fields[21][conditionals][0][0][value]"
                                                                                                class="wpforms-conditional-value">
                                                                                        </td>
                                                                                        <td class="actions">
                                                                                            <button
                                                                                                class="wpforms-conditional-rule-add wpforms-btn wpforms-btn-sm wpforms-btn-blue"
                                                                                                title="Create new rule">And</button><button
                                                                                                class="wpforms-conditional-rule-delete"
                                                                                                title="Delete rule"><i
                                                                                                    class="fa-solid fa-trash"
                                                                                                    aria-hidden="true"></i></button>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                            <h5>or</h5>
                                                                        </div>
                                                                        <button
                                                                            class="wpforms-conditional-groups-add wpforms-btn wpforms-btn-sm wpforms-btn-blue">Add
                                                                            New Group</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="wpforms-field-option wpforms-field-option-name "
                                                        id="wpforms-field-option-0" data-field-id="0"
                                                        style="display: none;"><input type="hidden"
                                                            name="fields[0][id]" value="0"
                                                            class="wpforms-field-option-hidden-id"><input type="hidden"
                                                            name="fields[0][type]" value="name"
                                                            class="wpforms-field-option-hidden-type">
                                                        <div class="wpforms-field-option-field-title">Name <span>(ID
                                                                #0)</span></div>
                                                        <div class="wpforms-field-option-group wpforms-field-option-group-basic active"
                                                            id="wpforms-field-option-basic-0">
                                                            <a href="#"
                                                                class="wpforms-field-option-group-toggle">General</a>
                                                            <div class="wpforms-field-option-group-inner ">
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-label "
                                                                    id="wpforms-field-option-row-0-label"
                                                                    data-field-id="0"><label
                                                                        for="wpforms-field-option-0-label">Label<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-0-label"
                                                                        name="fields[0][label]" value="Name"
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-format "
                                                                    id="wpforms-field-option-row-0-format"
                                                                    data-field-id="0"><label
                                                                        for="wpforms-field-option-0-format">Format<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><select
                                                                        class=""
                                                                        id="wpforms-field-option-0-format"
                                                                        name="fields[0][format]">
                                                                        <option value="simple">Simple</option>
                                                                        <option value="first-last" selected="selected">
                                                                            First Last</option>
                                                                        <option value="first-middle-last">First Middle
                                                                            Last</option>
                                                                    </select></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-description "
                                                                    id="wpforms-field-option-row-0-description"
                                                                    data-field-id="0"><label
                                                                        for="wpforms-field-option-0-description">Description<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label>
                                                                    <textarea class="" id="wpforms-field-option-0-description" name="fields[0][description]" rows="3"></textarea>
                                                                </div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-required "
                                                                    id="wpforms-field-option-row-0-required"
                                                                    data-field-id="0"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-0-required"
                                                                            name="fields[0][required]" class=""
                                                                            value="1" checked="checked">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-0-required"></label>
                                                                        <label for="wpforms-field-option-0-required"
                                                                            class="wpforms-toggle-control-label">Required</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                            </div>
                                                        </div>
                                                        <div class="wpforms-field-option-group wpforms-field-option-group-advanced"
                                                            id="wpforms-field-option-advanced-0"><a href="#"
                                                                class="wpforms-field-option-group-toggle">Advanced</a>
                                                            <div class="wpforms-field-option-group-inner">
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-size "
                                                                    id="wpforms-field-option-row-0-size"
                                                                    data-field-id="0"><label
                                                                        for="wpforms-field-option-0-size">Field Size<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><select
                                                                        class="" id="wpforms-field-option-0-size"
                                                                        name="fields[0][size]">
                                                                        <option value="small">Small</option>
                                                                        <option value="medium" selected="selected">
                                                                            Medium</option>
                                                                        <option value="large">Large</option>
                                                                    </select></div>
                                                                <div class="format-selected-first-last format-selected">
                                                                    <div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-simple"
                                                                        id="wpforms-field-option-row-0-simple"
                                                                        data-subfield="simple" data-field-id="0"><label
                                                                            for="wpforms-field-option-0-simple_placeholder">Name<i
                                                                                class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label>
                                                                        <div
                                                                            class="wpforms-field-options-columns-2 wpforms-field-options-columns">
                                                                            <div
                                                                                class="placeholder wpforms-field-options-column">
                                                                                <input type="text"
                                                                                    class="placeholder"
                                                                                    id="wpforms-field-option-0-simple_placeholder"
                                                                                    name="fields[0][simple_placeholder]"
                                                                                    value=""><label
                                                                                    for="wpforms-field-option-0-simple_placeholder"
                                                                                    class="sub-label">Placeholder</label>
                                                                            </div>
                                                                            <div
                                                                                class="default wpforms-field-options-column">
                                                                                <input type="text" class="default"
                                                                                    id="wpforms-field-option-0-simple_default"
                                                                                    name="fields[0][simple_default]"
                                                                                    value=""><label
                                                                                    for="wpforms-field-option-0-simple_default"
                                                                                    class="sub-label">Default
                                                                                    Value</label></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-first"
                                                                        id="wpforms-field-option-row-0-first"
                                                                        data-subfield="first-name" data-field-id="0">
                                                                        <label
                                                                            for="wpforms-field-option-0-first_placeholder">First
                                                                            Name<i
                                                                                class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label>
                                                                        <div
                                                                            class="wpforms-field-options-columns-2 wpforms-field-options-columns">
                                                                            <div
                                                                                class="placeholder wpforms-field-options-column">
                                                                                <input type="text"
                                                                                    class="placeholder"
                                                                                    id="wpforms-field-option-0-first_placeholder"
                                                                                    name="fields[0][first_placeholder]"
                                                                                    value=""><label
                                                                                    for="wpforms-field-option-0-first_placeholder"
                                                                                    class="sub-label">Placeholder</label>
                                                                            </div>
                                                                            <div
                                                                                class="default wpforms-field-options-column">
                                                                                <input type="text" class="default"
                                                                                    id="wpforms-field-option-0-first_default"
                                                                                    name="fields[0][first_default]"
                                                                                    value=""><label
                                                                                    for="wpforms-field-option-0-first_default"
                                                                                    class="sub-label">Default
                                                                                    Value</label></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-middle"
                                                                        id="wpforms-field-option-row-0-middle"
                                                                        data-subfield="middle-name" data-field-id="0">
                                                                        <label
                                                                            for="wpforms-field-option-0-middle_placeholder">Middle
                                                                            Name<i
                                                                                class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label>
                                                                        <div
                                                                            class="wpforms-field-options-columns-2 wpforms-field-options-columns">
                                                                            <div
                                                                                class="placeholder wpforms-field-options-column">
                                                                                <input type="text"
                                                                                    class="placeholder"
                                                                                    id="wpforms-field-option-0-middle_placeholder"
                                                                                    name="fields[0][middle_placeholder]"
                                                                                    value=""><label
                                                                                    for="wpforms-field-option-0-middle_placeholder"
                                                                                    class="sub-label">Placeholder</label>
                                                                            </div>
                                                                            <div
                                                                                class="default wpforms-field-options-column">
                                                                                <input type="text" class="default"
                                                                                    id="wpforms-field-option-0-middle_default"
                                                                                    name="fields[0][middle_default]"
                                                                                    value=""><label
                                                                                    for="wpforms-field-option-0-middle_default"
                                                                                    class="sub-label">Default
                                                                                    Value</label></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-last"
                                                                        id="wpforms-field-option-row-0-last"
                                                                        data-subfield="last-name" data-field-id="0">
                                                                        <label
                                                                            for="wpforms-field-option-0-last_placeholder">Last
                                                                            Name<i
                                                                                class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label>
                                                                        <div
                                                                            class="wpforms-field-options-columns-2 wpforms-field-options-columns">
                                                                            <div
                                                                                class="placeholder wpforms-field-options-column">
                                                                                <input type="text"
                                                                                    class="placeholder"
                                                                                    id="wpforms-field-option-0-last_placeholder"
                                                                                    name="fields[0][last_placeholder]"
                                                                                    value=""><label
                                                                                    for="wpforms-field-option-0-last_placeholder"
                                                                                    class="sub-label">Placeholder</label>
                                                                            </div>
                                                                            <div
                                                                                class="default wpforms-field-options-column">
                                                                                <input type="text" class="default"
                                                                                    id="wpforms-field-option-0-last_default"
                                                                                    name="fields[0][last_default]"
                                                                                    value=""><label
                                                                                    for="wpforms-field-option-0-last_default"
                                                                                    class="sub-label">Default
                                                                                    Value</label></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-css "
                                                                    id="wpforms-field-option-row-0-css"
                                                                    data-field-id="0"><label
                                                                        for="wpforms-field-option-0-css">CSS Classes<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i><a
                                                                            href="#"
                                                                            class="toggle-layout-selector-display toggle-unfoldable-cont"><i
                                                                                class="fa fa-th-large"></i><span>Show
                                                                                Layouts</span></a></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-0-css"
                                                                        name="fields[0][css]" value=""
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-label_hide "
                                                                    id="wpforms-field-option-row-0-label_hide"
                                                                    data-field-id="0"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-0-label_hide"
                                                                            name="fields[0][label_hide]" class=""
                                                                            value="1">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-0-label_hide"></label>
                                                                        <label for="wpforms-field-option-0-label_hide"
                                                                            class="wpforms-toggle-control-label">Hide
                                                                            Label</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-sublabel_hide "
                                                                    id="wpforms-field-option-row-0-sublabel_hide"
                                                                    data-field-id="0"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-0-sublabel_hide"
                                                                            name="fields[0][sublabel_hide]"
                                                                            class="" value="1">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-0-sublabel_hide"></label>
                                                                        <label for="wpforms-field-option-0-sublabel_hide"
                                                                            class="wpforms-toggle-control-label">Hide
                                                                            Sublabels</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                            </div>
                                                        </div>
                                                        <div class="wpforms-conditional-fields wpforms-field-option-group wpforms-field-option-group-conditionals wpforms-hide"
                                                            id="wpforms-field-option-conditionals-0">

                                                            <a href="#" class="wpforms-field-option-group-toggle">
                                                                Smart Logic </a>

                                                            <div class="wpforms-field-option-group-inner">
                                                                <div class="wpforms-conditional-block wpforms-conditional-block-field"
                                                                    data-type="field">
                                                                    <div class="wpforms-field-option-row wpforms-field-option-row-conditional_logic wpforms-conditionals-enable-toggle"
                                                                        id="wpforms-field-option-row-0-conditional_logic"
                                                                        data-field-id="0"><span
                                                                            class="wpforms-toggle-control ">

                                                                            <input type="checkbox"
                                                                                id="wpforms-field-option-0-conditional_logic"
                                                                                name="fields[0][conditional_logic]"
                                                                                class="" value="1"
                                                                                checked="checked" data-name="fields[0]"
                                                                                data-actions="{&quot;show&quot;:&quot;Show&quot;,&quot;hide&quot;:&quot;Hide&quot;}"
                                                                                data-action-desc="this field if">
                                                                            <label class="wpforms-toggle-control-icon"
                                                                                for="wpforms-field-option-0-conditional_logic"></label>
                                                                            <label
                                                                                for="wpforms-field-option-0-conditional_logic"
                                                                                class="wpforms-toggle-control-label">Enable
                                                                                Conditional Logic</label><i
                                                                                class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                        </span></div>
                                                                    <div class="wpforms-conditional-groups"
                                                                        id="wpforms-conditional-groups-fields-0"
                                                                        style="">
                                                                        <h4><select name="fields[0][conditional_type]">
                                                                                <option value="show"
                                                                                    selected="selected">Show</option>
                                                                                <option value="hide">Hide</option>
                                                                            </select>this field if</h4>
                                                                        <div class="wpforms-conditional-group"
                                                                            data-reference="0">
                                                                            <table>
                                                                                <tbody>
                                                                                    <tr class="wpforms-conditional-row"
                                                                                        data-field-id="0"
                                                                                        data-input-name="fields[0]">
                                                                                        <td class="field"><select
                                                                                                name="fields[0][conditionals][0][0][field]"
                                                                                                class="wpforms-conditional-field"
                                                                                                data-groupid="0"
                                                                                                data-ruleid="0">
                                                                                                <option value="">---
                                                                                                    Select Field ---
                                                                                                </option>
                                                                                                <option value="22">
                                                                                                    Single Line Text
                                                                                                </option>
                                                                                                <option value="21">
                                                                                                    Single Line Text
                                                                                                </option>
                                                                                                <option value="1">
                                                                                                    Email</option>
                                                                                                <option value="2">
                                                                                                    Comment or Message
                                                                                                </option>
                                                                                            </select></td>
                                                                                        <td class="operator"><select
                                                                                                name="fields[0][conditionals][0][0][operator]"
                                                                                                class="wpforms-conditional-operator">
                                                                                                <option value="=="
                                                                                                    selected="selected">is
                                                                                                </option>
                                                                                                <option value="!=">is
                                                                                                    not</option>
                                                                                                <option value="e">
                                                                                                    empty</option>
                                                                                                <option value="!e">not
                                                                                                    empty</option>
                                                                                                <option value="c">
                                                                                                    contains</option>
                                                                                                <option value="!c">
                                                                                                    does not contain
                                                                                                </option>
                                                                                                <option value="^">
                                                                                                    starts with</option>
                                                                                                <option value="~">
                                                                                                    ends with</option>
                                                                                                <option value=">">
                                                                                                    greater than</option>
                                                                                                <option value="<">
                                                                                                    less than</option>
                                                                                            </select></td>
                                                                                        <td class="value"><select
                                                                                                name="fields[0][conditionals][0][0][value]"
                                                                                                class="wpforms-conditional-value"
                                                                                                0="">
                                                                                                <option value="">---
                                                                                                    Select Choice ---
                                                                                                </option>
                                                                                            </select></td>
                                                                                        <td class="actions"><button
                                                                                                class="wpforms-conditional-rule-add wpforms-btn wpforms-btn-sm wpforms-btn-blue"
                                                                                                title="Create new rule">And</button><button
                                                                                                class="wpforms-conditional-rule-delete"
                                                                                                title="Delete rule"><i
                                                                                                    class="fa-solid fa-trash"
                                                                                                    aria-hidden="true"></i></button>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                            <h5>or</h5>
                                                                        </div><button
                                                                            class="wpforms-conditional-groups-add wpforms-btn wpforms-btn-sm wpforms-btn-blue">Add
                                                                            New Group</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="wpforms-field-option wpforms-field-option-email wpforms-confirm-disabled"
                                                        id="wpforms-field-option-1" data-field-id="1"
                                                        style="display: none;"><input type="hidden"
                                                            name="fields[1][id]" value="1"
                                                            class="wpforms-field-option-hidden-id"><input type="hidden"
                                                            name="fields[1][type]" value="email"
                                                            class="wpforms-field-option-hidden-type">
                                                        <div class="wpforms-field-option-field-title">Email <span>(ID
                                                                #1)</span></div>
                                                        <div class="wpforms-field-option-group wpforms-field-option-group-basic"
                                                            id="wpforms-field-option-basic-1">
                                                            <a href="#"
                                                                class="wpforms-field-option-group-toggle">General</a>
                                                            <div class="wpforms-field-option-group-inner ">
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-label "
                                                                    id="wpforms-field-option-row-1-label"
                                                                    data-field-id="1"><label
                                                                        for="wpforms-field-option-1-label">Label<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-1-label"
                                                                        name="fields[1][label]" value="Email"
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-description "
                                                                    id="wpforms-field-option-row-1-description"
                                                                    data-field-id="1"><label
                                                                        for="wpforms-field-option-1-description">Description<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label>
                                                                    <textarea class="" id="wpforms-field-option-1-description" name="fields[1][description]" rows="3"></textarea>
                                                                </div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-required "
                                                                    id="wpforms-field-option-row-1-required"
                                                                    data-field-id="1"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-1-required"
                                                                            name="fields[1][required]" class=""
                                                                            value="1" checked="checked">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-1-required"></label>
                                                                        <label for="wpforms-field-option-1-required"
                                                                            class="wpforms-toggle-control-label">Required</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-confirmation "
                                                                    id="wpforms-field-option-row-1-confirmation"
                                                                    data-field-id="1"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-1-confirmation"
                                                                            name="fields[1][confirmation]"
                                                                            class="" value="1">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-1-confirmation"></label>
                                                                        <label for="wpforms-field-option-1-confirmation"
                                                                            class="wpforms-toggle-control-label">Enable
                                                                            Email Confirmation</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                            </div>
                                                        </div>
                                                        <div class="wpforms-field-option-group wpforms-field-option-group-advanced"
                                                            id="wpforms-field-option-advanced-1"><a href="#"
                                                                class="wpforms-field-option-group-toggle">Advanced</a>
                                                            <div class="wpforms-field-option-group-inner">
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-size "
                                                                    id="wpforms-field-option-row-1-size"
                                                                    data-field-id="1"><label
                                                                        for="wpforms-field-option-1-size">Field Size<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><select
                                                                        class="" id="wpforms-field-option-1-size"
                                                                        name="fields[1][size]" title="">
                                                                        <option value="small">Small</option>
                                                                        <option value="medium" selected="selected">
                                                                            Medium</option>
                                                                        <option value="large">Large</option>
                                                                    </select><label
                                                                        class="sub-label wpforms-notice-field-size wpforms-hidden"
                                                                        title="When a field is placed inside a column, the field size always equals the column width.">
                                                                        Field size cannot be changed when used in a layout.
                                                                    </label></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-placeholder "
                                                                    id="wpforms-field-option-row-1-placeholder"
                                                                    data-field-id="1"><label
                                                                        for="wpforms-field-option-1-placeholder">Placeholder
                                                                        Text<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-1-placeholder"
                                                                        name="fields[1][placeholder]" value=""
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-confirmation_placeholder "
                                                                    id="wpforms-field-option-row-1-confirmation_placeholder"
                                                                    data-field-id="1"><label
                                                                        for="wpforms-field-option-1-confirmation_placeholder">Confirmation
                                                                        Placeholder Text<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-1-confirmation_placeholder"
                                                                        name="fields[1][confirmation_placeholder]"
                                                                        value="" placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-default_value "
                                                                    id="wpforms-field-option-row-1-default_value"
                                                                    data-field-id="1"><label
                                                                        for="wpforms-field-option-1-default_value">Default
                                                                        Value<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i><a
                                                                            href="#"
                                                                            class="toggle-smart-tag-display toggle-unfoldable-cont"
                                                                            data-type="other"><i
                                                                                class="fa fa-tags"></i><span>Show Smart
                                                                                Tags</span></a></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-1-default_value"
                                                                        name="fields[1][default_value]" value=""
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-filter_type "
                                                                    id="wpforms-field-option-row-1-filter_type"
                                                                    data-field-id="1"><label
                                                                        for="wpforms-field-option-1-filter_type">Allowlist
                                                                        / Denylist<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><select
                                                                        class=""
                                                                        id="wpforms-field-option-1-filter_type"
                                                                        name="fields[1][filter_type]">
                                                                        <option value="" selected="selected">None
                                                                        </option>
                                                                        <option value="allowlist">Allowlist</option>
                                                                        <option value="denylist">Denylist</option>
                                                                    </select></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-allowlist "
                                                                    id="wpforms-field-option-row-1-allowlist"
                                                                    data-field-id="1">
                                                                    <textarea class="" id="wpforms-field-option-1-allowlist" name="fields[1][allowlist]" rows="3"></textarea>
                                                                </div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-denylist "
                                                                    id="wpforms-field-option-row-1-denylist"
                                                                    data-field-id="1">
                                                                    <textarea class="" id="wpforms-field-option-1-denylist" name="fields[1][denylist]" rows="3"></textarea>
                                                                </div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-css "
                                                                    id="wpforms-field-option-row-1-css"
                                                                    data-field-id="1"><label
                                                                        for="wpforms-field-option-1-css">CSS Classes<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i><a
                                                                            href="#"
                                                                            class="toggle-layout-selector-display toggle-unfoldable-cont"><i
                                                                                class="fa fa-th-large"></i><span>Show
                                                                                Layouts</span></a></label>
                                                                    <div class="layout-selector-display unfoldable-cont">
                                                                        <p class="heading wpforms-hidden-strict">Select
                                                                            your layout</p>
                                                                        <div class="layouts wpforms-hidden-strict">
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="one-half"
                                                                                    data-classes="wpforms-one-half wpforms-first"></span><span
                                                                                    class="one-half"
                                                                                    data-classes="wpforms-one-half"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="one-third"
                                                                                    data-classes="wpforms-one-third wpforms-first"></span><span
                                                                                    class="one-third"
                                                                                    data-classes="wpforms-one-third"></span><span
                                                                                    class="one-third"
                                                                                    data-classes="wpforms-one-third"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth wpforms-first"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="one-third"
                                                                                    data-classes="wpforms-one-third wpforms-first"></span><span
                                                                                    class="two-third"
                                                                                    data-classes="wpforms-two-thirds"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="two-third"
                                                                                    data-classes="wpforms-two-thirds wpforms-first"></span><span
                                                                                    class="one-third"
                                                                                    data-classes="wpforms-one-third"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth wpforms-first"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span><span
                                                                                    class="two-fourth"
                                                                                    data-classes="wpforms-two-fourths"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="two-fourth"
                                                                                    data-classes="wpforms-two-fourths wpforms-first"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth wpforms-first"></span><span
                                                                                    class="two-fourth"
                                                                                    data-classes="wpforms-two-fourths"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span>
                                                                            </div>
                                                                        </div>
                                                                        <div
                                                                            class="wpforms-alert-warning wpforms-alert-layout wpforms-alert wpforms-alert-nomargin">
                                                                            <h4>Layouts Have Moved!</h4>
                                                                            <p>We’ve added a new field to help you build
                                                                                advanced form layouts more easily. Give the
                                                                                Layout Field a try! Layout CSS classes are
                                                                                still supported. <a
                                                                                    href="https://wpforms.com/docs/how-to-use-the-layout-field-in-wpforms/?utm_campaign=plugin&amp;utm_source=WordPress&amp;utm_medium=Field%20Options&amp;utm_content=How%20to%20Use%20the%20Layout%20Field%20Documentation&amp;utm_locale=en"
                                                                                    target="_blank"
                                                                                    rel="noopener noreferrer">Learn
                                                                                    More</a></p>
                                                                        </div>
                                                                    </div><input type="text" class=""
                                                                        id="wpforms-field-option-1-css"
                                                                        name="fields[1][css]" value=""
                                                                        placeholder="">
                                                                </div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-label_hide "
                                                                    id="wpforms-field-option-row-1-label_hide"
                                                                    data-field-id="1"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-1-label_hide"
                                                                            name="fields[1][label_hide]" class=""
                                                                            value="1">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-1-label_hide"></label>
                                                                        <label for="wpforms-field-option-1-label_hide"
                                                                            class="wpforms-toggle-control-label">Hide
                                                                            Label</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-sublabel_hide "
                                                                    id="wpforms-field-option-row-1-sublabel_hide"
                                                                    data-field-id="1"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-1-sublabel_hide"
                                                                            name="fields[1][sublabel_hide]"
                                                                            class="" value="1">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-1-sublabel_hide"></label>
                                                                        <label for="wpforms-field-option-1-sublabel_hide"
                                                                            class="wpforms-toggle-control-label">Hide
                                                                            Sublabels</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                            </div>
                                                        </div>
                                                        <div class="wpforms-conditional-fields wpforms-field-option-group wpforms-field-option-group-conditionals wpforms-hide active"
                                                            id="wpforms-field-option-conditionals-1">

                                                            <a href="#" class="wpforms-field-option-group-toggle">
                                                                Smart Logic </a>

                                                            <div class="wpforms-field-option-group-inner">
                                                                <div class="wpforms-conditional-block wpforms-conditional-block-field"
                                                                    data-type="field">
                                                                    <div class="wpforms-field-option-row wpforms-field-option-row-conditional_logic wpforms-conditionals-enable-toggle"
                                                                        id="wpforms-field-option-row-1-conditional_logic"
                                                                        data-field-id="1"><span
                                                                            class="wpforms-toggle-control ">

                                                                            <input type="checkbox"
                                                                                id="wpforms-field-option-1-conditional_logic"
                                                                                name="fields[1][conditional_logic]"
                                                                                class="" value="1"
                                                                                checked="checked" data-name="fields[1]"
                                                                                data-actions="{&quot;show&quot;:&quot;Show&quot;,&quot;hide&quot;:&quot;Hide&quot;}"
                                                                                data-action-desc="this field if">
                                                                            <label class="wpforms-toggle-control-icon"
                                                                                for="wpforms-field-option-1-conditional_logic"></label>
                                                                            <label
                                                                                for="wpforms-field-option-1-conditional_logic"
                                                                                class="wpforms-toggle-control-label">Enable
                                                                                Conditional Logic</label><i
                                                                                class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                        </span></div>
                                                                    <div class="wpforms-conditional-groups"
                                                                        id="wpforms-conditional-groups-fields-1"
                                                                        style="">
                                                                        <h4><select name="fields[1][conditional_type]">
                                                                                <option value="show"
                                                                                    selected="selected">Show</option>
                                                                                <option value="hide">Hide</option>
                                                                            </select>this field if</h4>
                                                                        <div class="wpforms-conditional-group"
                                                                            data-reference="1">
                                                                            <table>
                                                                                <tbody>
                                                                                    <tr class="wpforms-conditional-row"
                                                                                        data-field-id="1"
                                                                                        data-input-name="fields[1]">
                                                                                        <td class="field"><select
                                                                                                name="fields[1][conditionals][0][0][field]"
                                                                                                class="wpforms-conditional-field"
                                                                                                data-groupid="0"
                                                                                                data-ruleid="0">
                                                                                                <option value="">---
                                                                                                    Select Field ---
                                                                                                </option>
                                                                                                <option value="22">
                                                                                                    Single Line Text
                                                                                                </option>
                                                                                                <option value="21">
                                                                                                    Single Line Text
                                                                                                </option>
                                                                                                <option value="2">
                                                                                                    Comment or Message
                                                                                                </option>
                                                                                            </select></td>
                                                                                        <td class="operator"><select
                                                                                                name="fields[1][conditionals][0][0][operator]"
                                                                                                class="wpforms-conditional-operator">
                                                                                                <option value="=="
                                                                                                    selected="selected">is
                                                                                                </option>
                                                                                                <option value="!=">is
                                                                                                    not</option>
                                                                                                <option value="e">
                                                                                                    empty</option>
                                                                                                <option value="!e">not
                                                                                                    empty</option>
                                                                                                <option value="c">
                                                                                                    contains</option>
                                                                                                <option value="!c">
                                                                                                    does not contain
                                                                                                </option>
                                                                                                <option value="^">
                                                                                                    starts with</option>
                                                                                                <option value="~">
                                                                                                    ends with</option>
                                                                                                <option value=">">
                                                                                                    greater than</option>
                                                                                                <option value="<">
                                                                                                    less than</option>
                                                                                            </select></td>
                                                                                        <td class="value"><select
                                                                                                name="fields[1][conditionals][0][0][value]"
                                                                                                class="wpforms-conditional-value"
                                                                                                0="">
                                                                                                <option value="">---
                                                                                                    Select Choice ---
                                                                                                </option>
                                                                                            </select></td>
                                                                                        <td class="actions"><button
                                                                                                class="wpforms-conditional-rule-add wpforms-btn wpforms-btn-sm wpforms-btn-blue"
                                                                                                title="Create new rule">And</button><button
                                                                                                class="wpforms-conditional-rule-delete"
                                                                                                title="Delete rule"><i
                                                                                                    class="fa-solid fa-trash"
                                                                                                    aria-hidden="true"></i></button>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                            <h5>or</h5>
                                                                        </div><button
                                                                            class="wpforms-conditional-groups-add wpforms-btn wpforms-btn-sm wpforms-btn-blue">Add
                                                                            New Group</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="wpforms-field-option wpforms-field-option-layout "
                                                        id="wpforms-field-option-26" data-field-id="26"
                                                        style=""><input type="hidden" name="fields[26][id]"
                                                            value="26" class="wpforms-field-option-hidden-id"><input
                                                            type="hidden" name="fields[26][type]" value="layout"
                                                            class="wpforms-field-option-hidden-type"><input
                                                            type="hidden" name="fields[26][columns-json]"
                                                            id="wpforms-field-option-26-columns-json"
                                                            value="[{&quot;width_custom&quot;:&quot;&quot;,&quot;width_preset&quot;:&quot;50&quot;,&quot;fields&quot;:[]},{&quot;width_custom&quot;:&quot;&quot;,&quot;width_preset&quot;:&quot;50&quot;,&quot;fields&quot;:[]}]">
                                                        <div class="wpforms-field-option-field-title">Layout <span>(ID
                                                                #26)</span></div>
                                                        <div class="wpforms-field-option-group wpforms-field-option-group-basic active"
                                                            id="wpforms-field-option-basic-26">
                                                            <a href="#"
                                                                class="wpforms-field-option-group-toggle">General</a>
                                                            <div class="wpforms-field-option-group-inner ">
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-label "
                                                                    id="wpforms-field-option-row-26-label"
                                                                    data-field-id="26"><label
                                                                        for="wpforms-field-option-26-label">Label<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-26-label"
                                                                        name="fields[26][label]" value="Layout"
                                                                        placeholder=""></div><label
                                                                    for="wpforms-field-option-26-preset">Select a Layout<i
                                                                        class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-preset "
                                                                    id="wpforms-field-option-row-26-preset"
                                                                    data-field-id="26"><input type="radio"
                                                                        name="fields[26][preset]"
                                                                        id="wpforms-field-option-26-preset-50-50"
                                                                        value="50-50" checked=""><label
                                                                        for="wpforms-field-option-26-preset-50-50"
                                                                        class="preset-50-50"></label><input
                                                                        type="radio" name="fields[26][preset]"
                                                                        id="wpforms-field-option-26-preset-67-33"
                                                                        value="67-33"><label
                                                                        for="wpforms-field-option-26-preset-67-33"
                                                                        class="preset-67-33"></label><input
                                                                        type="radio" name="fields[26][preset]"
                                                                        id="wpforms-field-option-26-preset-33-67"
                                                                        value="33-67"><label
                                                                        for="wpforms-field-option-26-preset-33-67"
                                                                        class="preset-33-67"></label><input
                                                                        type="radio" name="fields[26][preset]"
                                                                        id="wpforms-field-option-26-preset-33-33-33"
                                                                        value="33-33-33"><label
                                                                        for="wpforms-field-option-26-preset-33-33-33"
                                                                        class="preset-33-33-33"></label><input
                                                                        type="radio" name="fields[26][preset]"
                                                                        id="wpforms-field-option-26-preset-50-25-25"
                                                                        value="50-25-25"><label
                                                                        for="wpforms-field-option-26-preset-50-25-25"
                                                                        class="preset-50-25-25"></label><input
                                                                        type="radio" name="fields[26][preset]"
                                                                        id="wpforms-field-option-26-preset-25-25-50"
                                                                        value="25-25-50"><label
                                                                        for="wpforms-field-option-26-preset-25-25-50"
                                                                        class="preset-25-25-50"></label><input
                                                                        type="radio" name="fields[26][preset]"
                                                                        id="wpforms-field-option-26-preset-25-50-25"
                                                                        value="25-50-25"><label
                                                                        for="wpforms-field-option-26-preset-25-50-25"
                                                                        class="preset-25-50-25"></label><input
                                                                        type="radio" name="fields[26][preset]"
                                                                        id="wpforms-field-option-26-preset-25-25-25-25"
                                                                        value="25-25-25-25"><label
                                                                        for="wpforms-field-option-26-preset-25-25-25-25"
                                                                        class="preset-25-25-25-25"></label></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="wpforms-field-option wpforms-field-option-name "
                                                        id="wpforms-field-option-19" data-field-id="19"
                                                        style="display: none;"><input type="hidden"
                                                            name="fields[19][id]" value="19"
                                                            class="wpforms-field-option-hidden-id"><input type="hidden"
                                                            name="fields[19][type]" value="name"
                                                            class="wpforms-field-option-hidden-type">
                                                        <div class="wpforms-field-option-field-title">Name <span>(ID
                                                                #19)</span></div>
                                                        <div class="wpforms-field-option-group wpforms-field-option-group-basic active"
                                                            id="wpforms-field-option-basic-19">
                                                            <a href="#"
                                                                class="wpforms-field-option-group-toggle">General</a>
                                                            <div class="wpforms-field-option-group-inner ">
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-label "
                                                                    id="wpforms-field-option-row-19-label"
                                                                    data-field-id="19"><label
                                                                        for="wpforms-field-option-19-label">Label<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-19-label"
                                                                        name="fields[19][label]" value="Name99"
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-format "
                                                                    id="wpforms-field-option-row-19-format"
                                                                    data-field-id="19"><label
                                                                        for="wpforms-field-option-19-format">Format<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><select
                                                                        class=""
                                                                        id="wpforms-field-option-19-format"
                                                                        name="fields[19][format]">
                                                                        <option value="simple" selected="selected">
                                                                            Simple</option>
                                                                        <option value="first-last">First Last</option>
                                                                        <option value="first-middle-last">First Middle
                                                                            Last</option>
                                                                    </select></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-description "
                                                                    id="wpforms-field-option-row-19-description"
                                                                    data-field-id="19"><label
                                                                        for="wpforms-field-option-19-description">Description<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label>
                                                                    <textarea class="" id="wpforms-field-option-19-description" name="fields[19][description]" rows="3"></textarea>
                                                                </div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-required "
                                                                    id="wpforms-field-option-row-19-required"
                                                                    data-field-id="19"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-19-required"
                                                                            name="fields[19][required]" class=""
                                                                            value="1" checked="checked">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-19-required"></label>
                                                                        <label for="wpforms-field-option-19-required"
                                                                            class="wpforms-toggle-control-label">Required</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                            </div>
                                                        </div>
                                                        <div class="wpforms-field-option-group wpforms-field-option-group-advanced"
                                                            id="wpforms-field-option-advanced-19"><a href="#"
                                                                class="wpforms-field-option-group-toggle">Advanced</a>
                                                            <div class="wpforms-field-option-group-inner">
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-size "
                                                                    id="wpforms-field-option-row-19-size"
                                                                    data-field-id="19"><label
                                                                        for="wpforms-field-option-19-size">Field Size<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><select
                                                                        class="" id="wpforms-field-option-19-size"
                                                                        name="fields[19][size]">
                                                                        <option value="small">Small</option>
                                                                        <option value="medium" selected="selected">
                                                                            Medium</option>
                                                                        <option value="large">Large</option>
                                                                    </select></div>
                                                                <div class="format-selected-simple format-selected">
                                                                    <div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-simple"
                                                                        id="wpforms-field-option-row-19-simple"
                                                                        data-subfield="simple" data-field-id="19"><label
                                                                            for="wpforms-field-option-19-simple_placeholder">Name<i
                                                                                class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label>
                                                                        <div
                                                                            class="wpforms-field-options-columns-2 wpforms-field-options-columns">
                                                                            <div
                                                                                class="placeholder wpforms-field-options-column">
                                                                                <input type="text"
                                                                                    class="placeholder"
                                                                                    id="wpforms-field-option-19-simple_placeholder"
                                                                                    name="fields[19][simple_placeholder]"
                                                                                    value=""><label
                                                                                    for="wpforms-field-option-19-simple_placeholder"
                                                                                    class="sub-label">Placeholder</label>
                                                                            </div>
                                                                            <div
                                                                                class="default wpforms-field-options-column">
                                                                                <input type="text" class="default"
                                                                                    id="wpforms-field-option-19-simple_default"
                                                                                    name="fields[19][simple_default]"
                                                                                    value=""><label
                                                                                    for="wpforms-field-option-19-simple_default"
                                                                                    class="sub-label">Default
                                                                                    Value</label></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-first"
                                                                        id="wpforms-field-option-row-19-first"
                                                                        data-subfield="first-name" data-field-id="19">
                                                                        <label
                                                                            for="wpforms-field-option-19-first_placeholder">First
                                                                            Name<i
                                                                                class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label>
                                                                        <div
                                                                            class="wpforms-field-options-columns-2 wpforms-field-options-columns">
                                                                            <div
                                                                                class="placeholder wpforms-field-options-column">
                                                                                <input type="text"
                                                                                    class="placeholder"
                                                                                    id="wpforms-field-option-19-first_placeholder"
                                                                                    name="fields[19][first_placeholder]"
                                                                                    value=""><label
                                                                                    for="wpforms-field-option-19-first_placeholder"
                                                                                    class="sub-label">Placeholder</label>
                                                                            </div>
                                                                            <div
                                                                                class="default wpforms-field-options-column">
                                                                                <input type="text" class="default"
                                                                                    id="wpforms-field-option-19-first_default"
                                                                                    name="fields[19][first_default]"
                                                                                    value=""><label
                                                                                    for="wpforms-field-option-19-first_default"
                                                                                    class="sub-label">Default
                                                                                    Value</label></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-middle"
                                                                        id="wpforms-field-option-row-19-middle"
                                                                        data-subfield="middle-name" data-field-id="19">
                                                                        <label
                                                                            for="wpforms-field-option-19-middle_placeholder">Middle
                                                                            Name<i
                                                                                class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label>
                                                                        <div
                                                                            class="wpforms-field-options-columns-2 wpforms-field-options-columns">
                                                                            <div
                                                                                class="placeholder wpforms-field-options-column">
                                                                                <input type="text"
                                                                                    class="placeholder"
                                                                                    id="wpforms-field-option-19-middle_placeholder"
                                                                                    name="fields[19][middle_placeholder]"
                                                                                    value=""><label
                                                                                    for="wpforms-field-option-19-middle_placeholder"
                                                                                    class="sub-label">Placeholder</label>
                                                                            </div>
                                                                            <div
                                                                                class="default wpforms-field-options-column">
                                                                                <input type="text" class="default"
                                                                                    id="wpforms-field-option-19-middle_default"
                                                                                    name="fields[19][middle_default]"
                                                                                    value=""><label
                                                                                    for="wpforms-field-option-19-middle_default"
                                                                                    class="sub-label">Default
                                                                                    Value</label></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-last"
                                                                        id="wpforms-field-option-row-19-last"
                                                                        data-subfield="last-name" data-field-id="19">
                                                                        <label
                                                                            for="wpforms-field-option-19-last_placeholder">Last
                                                                            Name<i
                                                                                class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label>
                                                                        <div
                                                                            class="wpforms-field-options-columns-2 wpforms-field-options-columns">
                                                                            <div
                                                                                class="placeholder wpforms-field-options-column">
                                                                                <input type="text"
                                                                                    class="placeholder"
                                                                                    id="wpforms-field-option-19-last_placeholder"
                                                                                    name="fields[19][last_placeholder]"
                                                                                    value=""><label
                                                                                    for="wpforms-field-option-19-last_placeholder"
                                                                                    class="sub-label">Placeholder</label>
                                                                            </div>
                                                                            <div
                                                                                class="default wpforms-field-options-column">
                                                                                <input type="text" class="default"
                                                                                    id="wpforms-field-option-19-last_default"
                                                                                    name="fields[19][last_default]"
                                                                                    value=""><label
                                                                                    for="wpforms-field-option-19-last_default"
                                                                                    class="sub-label">Default
                                                                                    Value</label></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-css "
                                                                    id="wpforms-field-option-row-19-css"
                                                                    data-field-id="19"><label
                                                                        for="wpforms-field-option-19-css">CSS Classes<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i><a
                                                                            href="#"
                                                                            class="toggle-layout-selector-display toggle-unfoldable-cont"><i
                                                                                class="fa fa-th-large"></i><span>Show
                                                                                Layouts</span></a></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-19-css"
                                                                        name="fields[19][css]" value=""
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-label_hide "
                                                                    id="wpforms-field-option-row-19-label_hide"
                                                                    data-field-id="19"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-19-label_hide"
                                                                            name="fields[19][label_hide]" class=""
                                                                            value="1">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-19-label_hide"></label>
                                                                        <label for="wpforms-field-option-19-label_hide"
                                                                            class="wpforms-toggle-control-label">Hide
                                                                            Label</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-sublabel_hide wpforms-hidden"
                                                                    id="wpforms-field-option-row-19-sublabel_hide"
                                                                    data-field-id="19"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-19-sublabel_hide"
                                                                            name="fields[19][sublabel_hide]"
                                                                            class="" value="1">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-19-sublabel_hide"></label>
                                                                        <label for="wpforms-field-option-19-sublabel_hide"
                                                                            class="wpforms-toggle-control-label">Hide
                                                                            Sublabels</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                            </div>
                                                        </div>
                                                        <div class="wpforms-conditional-fields wpforms-field-option-group wpforms-field-option-group-conditionals wpforms-hide"
                                                            id="wpforms-field-option-conditionals-19">

                                                            <a href="#" class="wpforms-field-option-group-toggle">
                                                                Smart Logic </a>

                                                            <div class="wpforms-field-option-group-inner">
                                                                <div class="wpforms-conditional-block wpforms-conditional-block-field"
                                                                    data-type="field">
                                                                    <div class="wpforms-field-option-row wpforms-field-option-row-conditional_logic wpforms-conditionals-enable-toggle"
                                                                        id="wpforms-field-option-row-19-conditional_logic"
                                                                        data-field-id="19"><span
                                                                            class="wpforms-toggle-control ">

                                                                            <input type="checkbox"
                                                                                id="wpforms-field-option-19-conditional_logic"
                                                                                name="fields[19][conditional_logic]"
                                                                                class="" value="1"
                                                                                checked="checked" data-name="fields[19]"
                                                                                data-actions="{&quot;show&quot;:&quot;Show&quot;,&quot;hide&quot;:&quot;Hide&quot;}"
                                                                                data-action-desc="this field if">
                                                                            <label class="wpforms-toggle-control-icon"
                                                                                for="wpforms-field-option-19-conditional_logic"></label>
                                                                            <label
                                                                                for="wpforms-field-option-19-conditional_logic"
                                                                                class="wpforms-toggle-control-label">Enable
                                                                                Conditional Logic</label><i
                                                                                class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                        </span></div>
                                                                    <div class="wpforms-conditional-groups"
                                                                        id="wpforms-conditional-groups-fields-19"
                                                                        style="">
                                                                        <h4><select name="fields[19][conditional_type]">
                                                                                <option value="show"
                                                                                    selected="selected">Show</option>
                                                                                <option value="hide">Hide</option>
                                                                            </select>this field if</h4>
                                                                        <div class="wpforms-conditional-group"
                                                                            data-reference="19">
                                                                            <table>
                                                                                <tbody>
                                                                                    <tr class="wpforms-conditional-row"
                                                                                        data-field-id="19"
                                                                                        data-input-name="fields[19]">
                                                                                        <td class="field"><select
                                                                                                name="fields[19][conditionals][0][0][field]"
                                                                                                class="wpforms-conditional-field"
                                                                                                data-groupid="0"
                                                                                                data-ruleid="0">
                                                                                                <option value="">---
                                                                                                    Select Field ---
                                                                                                </option>
                                                                                                <option value="22">
                                                                                                    Single Line Text
                                                                                                </option>
                                                                                                <option value="21">
                                                                                                    Single Line Text
                                                                                                </option>
                                                                                                <option value="1">
                                                                                                    Email</option>
                                                                                                <option value="2">
                                                                                                    Comment or Message
                                                                                                </option>
                                                                                            </select></td>
                                                                                        <td class="operator"><select
                                                                                                name="fields[19][conditionals][0][0][operator]"
                                                                                                class="wpforms-conditional-operator">
                                                                                                <option value="=="
                                                                                                    selected="selected">is
                                                                                                </option>
                                                                                                <option value="!=">is
                                                                                                    not</option>
                                                                                                <option value="e">
                                                                                                    empty</option>
                                                                                                <option value="!e">not
                                                                                                    empty</option>
                                                                                                <option value="c">
                                                                                                    contains</option>
                                                                                                <option value="!c">
                                                                                                    does not contain
                                                                                                </option>
                                                                                                <option value="^">
                                                                                                    starts with</option>
                                                                                                <option value="~">
                                                                                                    ends with</option>
                                                                                                <option value=">">
                                                                                                    greater than</option>
                                                                                                <option value="<">
                                                                                                    less than</option>
                                                                                            </select></td>
                                                                                        <td class="value"><select
                                                                                                name="fields[19][conditionals][0][0][value]"
                                                                                                class="wpforms-conditional-value"
                                                                                                0="">
                                                                                                <option value="">---
                                                                                                    Select Choice ---
                                                                                                </option>
                                                                                            </select></td>
                                                                                        <td class="actions"><button
                                                                                                class="wpforms-conditional-rule-add wpforms-btn wpforms-btn-sm wpforms-btn-blue"
                                                                                                title="Create new rule">And</button><button
                                                                                                class="wpforms-conditional-rule-delete"
                                                                                                title="Delete rule"><i
                                                                                                    class="fa-solid fa-trash"
                                                                                                    aria-hidden="true"></i></button>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                            <h5>or</h5>
                                                                        </div><button
                                                                            class="wpforms-conditional-groups-add wpforms-btn wpforms-btn-sm wpforms-btn-blue">Add
                                                                            New Group</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="wpforms-field-option wpforms-field-option-textarea "
                                                        id="wpforms-field-option-2" data-field-id="2"
                                                        style="display: none;"><input type="hidden"
                                                            name="fields[2][id]" value="2"
                                                            class="wpforms-field-option-hidden-id"><input type="hidden"
                                                            name="fields[2][type]" value="textarea"
                                                            class="wpforms-field-option-hidden-type">
                                                        <div class="wpforms-field-option-field-title">Paragraph Text
                                                            <span>(ID #2)</span></div>
                                                        <div class="wpforms-field-option-group wpforms-field-option-group-basic active"
                                                            id="wpforms-field-option-basic-2">
                                                            <a href="#"
                                                                class="wpforms-field-option-group-toggle">General</a>
                                                            <div class="wpforms-field-option-group-inner ">
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-label "
                                                                    id="wpforms-field-option-row-2-label"
                                                                    data-field-id="2"><label
                                                                        for="wpforms-field-option-2-label">Label<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-2-label"
                                                                        name="fields[2][label]"
                                                                        value="Comment or Message" placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-description "
                                                                    id="wpforms-field-option-row-2-description"
                                                                    data-field-id="2"><label
                                                                        for="wpforms-field-option-2-description">Description<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label>
                                                                    <textarea class="" id="wpforms-field-option-2-description" name="fields[2][description]" rows="3"></textarea>
                                                                </div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-required "
                                                                    id="wpforms-field-option-row-2-required"
                                                                    data-field-id="2"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-2-required"
                                                                            name="fields[2][required]" class=""
                                                                            value="1">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-2-required"></label>
                                                                        <label for="wpforms-field-option-2-required"
                                                                            class="wpforms-toggle-control-label">Required</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                            </div>
                                                        </div>
                                                        <div class="wpforms-field-option-group wpforms-field-option-group-advanced"
                                                            id="wpforms-field-option-advanced-2"><a href="#"
                                                                class="wpforms-field-option-group-toggle">Advanced</a>
                                                            <div class="wpforms-field-option-group-inner">
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-size "
                                                                    id="wpforms-field-option-row-2-size"
                                                                    data-field-id="2"><label
                                                                        for="wpforms-field-option-2-size">Field Size<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><select
                                                                        class="" id="wpforms-field-option-2-size"
                                                                        name="fields[2][size]">
                                                                        <option value="small">Small</option>
                                                                        <option value="medium" selected="selected">
                                                                            Medium</option>
                                                                        <option value="large">Large</option>
                                                                    </select></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-placeholder "
                                                                    id="wpforms-field-option-row-2-placeholder"
                                                                    data-field-id="2"><label
                                                                        for="wpforms-field-option-2-placeholder">Placeholder
                                                                        Text<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-2-placeholder"
                                                                        name="fields[2][placeholder]" value=""
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-limit_enabled "
                                                                    id="wpforms-field-option-row-2-limit_enabled"
                                                                    data-field-id="2"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-2-limit_enabled"
                                                                            name="fields[2][limit_enabled]"
                                                                            class="" value="1">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-2-limit_enabled"></label>
                                                                        <label for="wpforms-field-option-2-limit_enabled"
                                                                            class="wpforms-toggle-control-label">Limit
                                                                            Length</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-limit_controls wpforms-hide"
                                                                    id="wpforms-field-option-row-2-limit_controls"
                                                                    data-field-id="2"><input type="number"
                                                                        class=""
                                                                        id="wpforms-field-option-2-limit_count"
                                                                        name="fields[2][limit_count]" value="1"
                                                                        placeholder="" min="1" step="1"
                                                                        pattern="[0-9]"><select class=""
                                                                        id="wpforms-field-option-2-limit_mode"
                                                                        name="fields[2][limit_mode]">
                                                                        <option value="characters" selected="selected">
                                                                            Characters</option>
                                                                        <option value="words">Words</option>
                                                                    </select></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-default_value "
                                                                    id="wpforms-field-option-row-2-default_value"
                                                                    data-field-id="2"><label
                                                                        for="wpforms-field-option-2-default_value">Default
                                                                        Value<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i><a
                                                                            href="#"
                                                                            class="toggle-smart-tag-display toggle-unfoldable-cont"
                                                                            data-type="other"><i
                                                                                class="fa fa-tags"></i><span>Show Smart
                                                                                Tags</span></a></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-2-default_value"
                                                                        name="fields[2][default_value]" value=""
                                                                        placeholder=""></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-css "
                                                                    id="wpforms-field-option-row-2-css"
                                                                    data-field-id="2"><label
                                                                        for="wpforms-field-option-2-css">CSS Classes<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i><a
                                                                            href="#"
                                                                            class="toggle-layout-selector-display toggle-unfoldable-cont"><i
                                                                                class="fa fa-th-large"></i><span>Show
                                                                                Layouts</span></a></label>
                                                                    <div class="layout-selector-display unfoldable-cont">
                                                                        <p class="heading wpforms-hidden-strict">Select
                                                                            your layout</p>
                                                                        <div class="layouts wpforms-hidden-strict">
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="one-half"
                                                                                    data-classes="wpforms-one-half wpforms-first"></span><span
                                                                                    class="one-half"
                                                                                    data-classes="wpforms-one-half"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="one-third"
                                                                                    data-classes="wpforms-one-third wpforms-first"></span><span
                                                                                    class="one-third"
                                                                                    data-classes="wpforms-one-third"></span><span
                                                                                    class="one-third"
                                                                                    data-classes="wpforms-one-third"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth wpforms-first"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="one-third"
                                                                                    data-classes="wpforms-one-third wpforms-first"></span><span
                                                                                    class="two-third"
                                                                                    data-classes="wpforms-two-thirds"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="two-third"
                                                                                    data-classes="wpforms-two-thirds wpforms-first"></span><span
                                                                                    class="one-third"
                                                                                    data-classes="wpforms-one-third"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth wpforms-first"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span><span
                                                                                    class="two-fourth"
                                                                                    data-classes="wpforms-two-fourths"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="two-fourth"
                                                                                    data-classes="wpforms-two-fourths wpforms-first"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span>
                                                                            </div>
                                                                            <div class="layout-selector-display-layout">
                                                                                <span class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth wpforms-first"></span><span
                                                                                    class="two-fourth"
                                                                                    data-classes="wpforms-two-fourths"></span><span
                                                                                    class="one-fourth"
                                                                                    data-classes="wpforms-one-fourth"></span>
                                                                            </div>
                                                                        </div>
                                                                        <div
                                                                            class="wpforms-alert-warning wpforms-alert-layout wpforms-alert wpforms-alert-nomargin">
                                                                            <h4>Layouts Have Moved!</h4>
                                                                            <p>We’ve added a new field to help you build
                                                                                advanced form layouts more easily. Give the
                                                                                Layout Field a try! Layout CSS classes are
                                                                                still supported. <a
                                                                                    href="https://wpforms.com/docs/how-to-use-the-layout-field-in-wpforms/?utm_campaign=plugin&amp;utm_source=WordPress&amp;utm_medium=Field%20Options&amp;utm_content=How%20to%20Use%20the%20Layout%20Field%20Documentation&amp;utm_locale=en"
                                                                                    target="_blank"
                                                                                    rel="noopener noreferrer">Learn
                                                                                    More</a></p>
                                                                        </div>
                                                                    </div><input type="text" class=""
                                                                        id="wpforms-field-option-2-css"
                                                                        name="fields[2][css]" value=""
                                                                        placeholder="">
                                                                </div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-label_hide "
                                                                    id="wpforms-field-option-row-2-label_hide"
                                                                    data-field-id="2"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-2-label_hide"
                                                                            name="fields[2][label_hide]" class=""
                                                                            value="1">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-2-label_hide"></label>
                                                                        <label for="wpforms-field-option-2-label_hide"
                                                                            class="wpforms-toggle-control-label">Hide
                                                                            Label</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                            </div>
                                                        </div>
                                                        <div class="wpforms-conditional-fields wpforms-field-option-group wpforms-field-option-group-conditionals wpforms-hide"
                                                            id="wpforms-field-option-conditionals-2">

                                                            <a href="#" class="wpforms-field-option-group-toggle">
                                                                Smart Logic </a>

                                                            <div class="wpforms-field-option-group-inner">
                                                                <div class="wpforms-conditional-block wpforms-conditional-block-field"
                                                                    data-type="field">
                                                                    <div class="wpforms-field-option-row wpforms-field-option-row-conditional_logic wpforms-conditionals-enable-toggle"
                                                                        id="wpforms-field-option-row-2-conditional_logic"
                                                                        data-field-id="2"><span
                                                                            class="wpforms-toggle-control ">

                                                                            <input type="checkbox"
                                                                                id="wpforms-field-option-2-conditional_logic"
                                                                                name="fields[2][conditional_logic]"
                                                                                class="" value="1"
                                                                                data-name="fields[2]"
                                                                                data-actions="{&quot;show&quot;:&quot;Show&quot;,&quot;hide&quot;:&quot;Hide&quot;}"
                                                                                data-action-desc="this field if">
                                                                            <label class="wpforms-toggle-control-icon"
                                                                                for="wpforms-field-option-2-conditional_logic"></label>
                                                                            <label
                                                                                for="wpforms-field-option-2-conditional_logic"
                                                                                class="wpforms-toggle-control-label">Enable
                                                                                Conditional Logic</label><i
                                                                                class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                        </span></div>

                                                                    <div class="wpforms-conditional-groups"
                                                                        id="wpforms-conditional-groups-fields-2">
                                                                        <h4>
                                                                            <select name="fields[2][conditional_type]">

                                                                                <option value="show">Show</option>

                                                                                <option value="hide">Hide</option>

                                                                            </select>
                                                                            this field if
                                                                        </h4>
                                                                        <div class="wpforms-conditional-group"
                                                                            data-reference="2">
                                                                            <table>
                                                                                <tbody>
                                                                                    <tr class="wpforms-conditional-row"
                                                                                        data-field-id="2"
                                                                                        data-input-name="fields[2]">
                                                                                        <td class="field">
                                                                                            <select
                                                                                                name="fields[2][conditionals][0][0][field]"
                                                                                                class="wpforms-conditional-field"
                                                                                                data-groupid="0"
                                                                                                data-ruleid="0">
                                                                                                <option value="">---
                                                                                                    Select Field ---
                                                                                                </option>
                                                                                                <option value="22">
                                                                                                    Single Line Text
                                                                                                </option>
                                                                                                <option value="21">
                                                                                                    Single Line Text
                                                                                                </option>
                                                                                                <option value="1">
                                                                                                    Email</option>
                                                                                            </select>
                                                                                        </td>
                                                                                        <td class="operator">
                                                                                            <select
                                                                                                name="fields[2][conditionals][0][0][operator]"
                                                                                                class="wpforms-conditional-operator">
                                                                                                <option value="==">is
                                                                                                </option>
                                                                                                <option value="!=">is
                                                                                                    not</option>
                                                                                                <option value="e">
                                                                                                    empty</option>
                                                                                                <option value="!e">not
                                                                                                    empty</option>
                                                                                                <option value="c">
                                                                                                    contains</option>
                                                                                                <option value="!c">
                                                                                                    does not contain
                                                                                                </option>
                                                                                                <option value="^">
                                                                                                    starts with</option>
                                                                                                <option value="~">
                                                                                                    ends with</option>
                                                                                                <option value=">">
                                                                                                    greater than</option>
                                                                                                <option value="<">
                                                                                                    less than</option>
                                                                                            </select>
                                                                                        </td>
                                                                                        <td class="value">
                                                                                            <select
                                                                                                name="fields[2][conditionals][0][0][value]"
                                                                                                class="wpforms-conditional-value">
                                                                                                <option value="">---
                                                                                                    Select Choice ---
                                                                                                </option>
                                                                                            </select>
                                                                                        </td>
                                                                                        <td class="actions">
                                                                                            <button
                                                                                                class="wpforms-conditional-rule-add wpforms-btn wpforms-btn-sm wpforms-btn-blue"
                                                                                                title="Create new rule">And</button><button
                                                                                                class="wpforms-conditional-rule-delete"
                                                                                                title="Delete rule"><i
                                                                                                    class="fa-solid fa-trash"
                                                                                                    aria-hidden="true"></i></button>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                            <h5>or</h5>
                                                                        </div>
                                                                        <button
                                                                            class="wpforms-conditional-groups-add wpforms-btn wpforms-btn-sm wpforms-btn-blue">Add
                                                                            New Group</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="wpforms-field-option wpforms-field-option-pagebreak "
                                                        id="wpforms-field-option-25" data-field-id="25"
                                                        style="display: none;"><input type="hidden"
                                                            name="fields[25][id]" value="25"
                                                            class="wpforms-field-option-hidden-id"><input type="hidden"
                                                            name="fields[25][type]" value="pagebreak"
                                                            class="wpforms-field-option-hidden-type"><input
                                                            type="hidden" class="position"
                                                            id="wpforms-field-option-25-position"
                                                            name="fields[25][position]" value="bottom" placeholder="">
                                                        <div class="wpforms-field-option-field-title">Page Break <span>(ID
                                                                #25)</span></div>
                                                        <div class="wpforms-field-option-group wpforms-field-option-group-basic active wpforms-pagebreak-bottom"
                                                            id="wpforms-field-option-basic-25">
                                                            <a href="#"
                                                                class="wpforms-field-option-group-toggle">General</a>
                                                            <div
                                                                class="wpforms-field-option-group-inner wpforms-pagebreak-bottom">
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-prev_toggle "
                                                                    id="wpforms-field-option-row-25-prev_toggle"
                                                                    data-field-id="25"><span
                                                                        class="wpforms-toggle-control ">

                                                                        <input type="checkbox"
                                                                            id="wpforms-field-option-25-prev_toggle"
                                                                            name="fields[25][prev_toggle]"
                                                                            class="" value="1">
                                                                        <label class="wpforms-toggle-control-icon"
                                                                            for="wpforms-field-option-25-prev_toggle"></label>
                                                                        <label for="wpforms-field-option-25-prev_toggle"
                                                                            class="wpforms-toggle-control-label">Display
                                                                            Previous</label><i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i>
                                                                    </span></div>
                                                                <div class="wpforms-field-option-row wpforms-field-option-row-prev wpforms-hidden"
                                                                    id="wpforms-field-option-row-25-prev"
                                                                    data-field-id="25"><label
                                                                        for="wpforms-field-option-25-prev">Previous
                                                                        Label<i
                                                                            class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered"></i></label><input
                                                                        type="text" class=""
                                                                        id="wpforms-field-option-25-prev"
                                                                        name="fields[25][prev]" value=""
                                                                        placeholder=""></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="wpforms-panel-content-wrap col-lg-9">
                                                <div class="wpforms-panel-content">
                                                    <div class="wpforms-preview-wrap">

                                                        <div class="wpforms-preview">

                                                            <div class="wpforms-title-desc">
                                                                <div class="wpforms-title-desc-inner">
                                                                    <h2 class="wpforms-form-name">
                                                                        Shipping Cost Calculator Form </h2>
                                                                    <span class="wpforms-form-desc">
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <div class="wpforms-no-fields-holder wpforms-hidden">
                                                            </div>

                                                            <div class="wpforms-field-wrap ui-sortable">
                                                                <div class="wpforms-field wpforms-field-pagebreak wpforms-field-stick wpforms-pagebreak-top ui-draggable ui-draggable-handle"
                                                                    id="wpforms-field-24" data-field-id="24"
                                                                    data-field-type="pagebreak" style=""><a
                                                                        href="#" class="wpforms-field-duplicate"
                                                                        title="Duplicate Field"><i
                                                                            class="fa-solid fa-copy fa-fw"
                                                                            aria-hidden="true"></i></a><a href="#"
                                                                        class="wpforms-field-delete"
                                                                        title="Delete Field"><i
                                                                            class="fa-solid fa-trash"></i></a>
                                                                    <div class="wpforms-field-helper">
                                                                        <span class="wpforms-field-helper-edit">Click to
                                                                            Edit</span>
                                                                        <span class="wpforms-field-helper-drag">Drag to
                                                                            Reorder</span>
                                                                        <span class="wpforms-field-helper-hide"
                                                                            title="Hide Helper">
                                                                            <i class="fa fa-times-circle"
                                                                                aria-hidden="true"></i>
                                                                        </span>
                                                                    </div>
                                                                    <div class="wpforms-pagebreak-divider"><span
                                                                            class="pagebreak-label">First Page / Progress
                                                                            Indicator <span
                                                                                class="wpforms-pagebreak-title"></span></span><span
                                                                            class="line"></span></div>
                                                                </div>
                                                                <div class="wpforms-field wpforms-field-text"
                                                                    id="wpforms-field-22" data-field-id="22"
                                                                    data-field-type="text" style=""><a
                                                                        href="#" class="wpforms-field-duplicate"
                                                                        title="Duplicate Field"><i
                                                                            class="fa-solid fa-copy fa-fw"
                                                                            aria-hidden="true"></i></a><a href="#"
                                                                        class="wpforms-field-delete"
                                                                        title="Delete Field"><i
                                                                            class="fa-solid fa-trash"></i></a>
                                                                    <div class="wpforms-field-helper">
                                                                        <span class="wpforms-field-helper-edit">Click to
                                                                            Edit</span>
                                                                        <span class="wpforms-field-helper-drag">Drag to
                                                                            Reorder</span>
                                                                        <span class="wpforms-field-helper-hide"
                                                                            title="Hide Helper">
                                                                            <i class="fa fa-times-circle"
                                                                                aria-hidden="true"></i>
                                                                        </span>
                                                                    </div><label class="label-title "><span
                                                                            class="hidden_text" title="Label Hidden"><i
                                                                                class="fa fa-eye-slash"></i></span><span
                                                                            class="empty_text"
                                                                            title="To ensure your form is accessible, every field should have a descriptive label. If you'd like to hide the label, you can do so by enabling Hide Label in the Advanced Field Options tab."><i
                                                                                class="fa fa-exclamation-triangle"></i></span><span
                                                                            class="text">Single Line Text</span><span
                                                                            class="required">*</span></label><input
                                                                        type="text" placeholder="" value=""
                                                                        class="primary-input" readonly="">
                                                                    <div class="description "></div>
                                                                </div>
                                                                <div class="wpforms-field wpforms-field-pagebreak wpforms-pagebreak-normal"
                                                                    id="wpforms-field-23" data-field-id="23"
                                                                    data-field-type="pagebreak" style=""><a
                                                                        href="#" class="wpforms-field-duplicate"
                                                                        title="Duplicate Field"><i
                                                                            class="fa-solid fa-copy fa-fw"
                                                                            aria-hidden="true"></i></a><a href="#"
                                                                        class="wpforms-field-delete"
                                                                        title="Delete Field"><i
                                                                            class="fa-solid fa-trash"></i></a>
                                                                    <div class="wpforms-field-helper">
                                                                        <span class="wpforms-field-helper-edit">Click to
                                                                            Edit</span>
                                                                        <span class="wpforms-field-helper-drag">Drag to
                                                                            Reorder</span>
                                                                        <span class="wpforms-field-helper-hide"
                                                                            title="Hide Helper">
                                                                            <i class="fa fa-times-circle"
                                                                                aria-hidden="true"></i>
                                                                        </span>
                                                                    </div>
                                                                    <div
                                                                        class="wpforms-pagebreak-buttons wpforms-pagebreak-buttons-left">
                                                                        <button
                                                                            class="wpforms-pagebreak-button wpforms-pagebreak-prev wpforms-hidden">Previous</button><button
                                                                            class="wpforms-pagebreak-button wpforms-pagebreak-next ">Next</button>
                                                                    </div>
                                                                    <div class="wpforms-pagebreak-divider"><span
                                                                            class="pagebreak-label">Page Break <span
                                                                                class="wpforms-pagebreak-title"></span></span><span
                                                                            class="line"></span></div>
                                                                </div>
                                                                <div class="wpforms-field wpforms-field-text"
                                                                    id="wpforms-field-21" data-field-id="21"
                                                                    data-field-type="text" style=""><a
                                                                        href="#" class="wpforms-field-duplicate"
                                                                        title="Duplicate Field"><i
                                                                            class="fa-solid fa-copy fa-fw"
                                                                            aria-hidden="true"></i></a><a href="#"
                                                                        class="wpforms-field-delete"
                                                                        title="Delete Field"><i
                                                                            class="fa-solid fa-trash"></i></a>
                                                                    <div class="wpforms-field-helper">
                                                                        <span class="wpforms-field-helper-edit">Click to
                                                                            Edit</span>
                                                                        <span class="wpforms-field-helper-drag">Drag to
                                                                            Reorder</span>
                                                                        <span class="wpforms-field-helper-hide"
                                                                            title="Hide Helper">
                                                                            <i class="fa fa-times-circle"
                                                                                aria-hidden="true"></i>
                                                                        </span>
                                                                    </div><label class="label-title "><span
                                                                            class="hidden_text" title="Label Hidden"><i
                                                                                class="fa fa-eye-slash"></i></span><span
                                                                            class="empty_text"
                                                                            title="To ensure your form is accessible, every field should have a descriptive label. If you'd like to hide the label, you can do so by enabling Hide Label in the Advanced Field Options tab."><i
                                                                                class="fa fa-exclamation-triangle"></i></span><span
                                                                            class="text">Single Line Text</span><span
                                                                            class="required">*</span></label><input
                                                                        type="text" placeholder="" value=""
                                                                        class="primary-input" readonly="">
                                                                    <div class="description "></div>
                                                                </div>
                                                                <div class="wpforms-field wpforms-field-name size-medium required ui-sortable-handle"
                                                                    id="wpforms-field-0" data-field-id="0"
                                                                    data-field-type="name"><a href="#"
                                                                        class="wpforms-field-duplicate"
                                                                        title="Duplicate Field"><i
                                                                            class="fa-solid fa-copy fa-fw"
                                                                            aria-hidden="true"></i></a><a href="#"
                                                                        class="wpforms-field-delete"
                                                                        title="Delete Field"><i
                                                                            class="fa-solid fa-trash"
                                                                            aria-hidden="true"></i></a>
                                                                    <div class="wpforms-field-helper">
                                                                        <span class="wpforms-field-helper-edit">Click to
                                                                            Edit</span>
                                                                        <span class="wpforms-field-helper-drag">Drag to
                                                                            Reorder</span>
                                                                        <span class="wpforms-field-helper-hide"
                                                                            title="Hide Helper">
                                                                            <i class="fa fa-times-circle"
                                                                                aria-hidden="true"></i>
                                                                        </span>
                                                                    </div><label class="label-title "><span
                                                                            class="hidden_text" title="Label Hidden"><i
                                                                                class="fa fa-eye-slash"></i></span><span
                                                                            class="empty_text"
                                                                            title="To ensure your form is accessible, every field should have a descriptive label. If you'd like to hide the label, you can do so by enabling Hide Label in the Advanced Field Options tab."><i
                                                                                class="fa fa-exclamation-triangle"></i></span><span
                                                                            class="text">Name</span><span
                                                                            class="required">*</span></label>
                                                                    <div
                                                                        class="format-selected-first-last format-selected wpforms-clear">

                                                                        <div class="wpforms-simple">
                                                                            <input type="text" placeholder=""
                                                                                value="" class="primary-input"
                                                                                readonly="">
                                                                        </div>

                                                                        <div class="wpforms-first-name">
                                                                            <input type="text" placeholder=""
                                                                                value="" class="primary-input"
                                                                                readonly="">
                                                                            <label class="wpforms-sub-label">First</label>
                                                                        </div>

                                                                        <div class="wpforms-middle-name">
                                                                            <input type="text" placeholder=""
                                                                                value="" class="primary-input"
                                                                                readonly="">
                                                                            <label
                                                                                class="wpforms-sub-label">Middle</label>
                                                                        </div>

                                                                        <div class="wpforms-last-name">
                                                                            <input type="text" placeholder=""
                                                                                value="" class="primary-input"
                                                                                readonly="">
                                                                            <label class="wpforms-sub-label">Last</label>
                                                                        </div>

                                                                    </div>

                                                                    <div class="description "></div>
                                                                </div>
                                                                <div class="wpforms-field wpforms-field-email size-medium required ui-sortable-handle"
                                                                    id="wpforms-field-1" data-field-id="1"
                                                                    data-field-type="email"><a href="#"
                                                                        class="wpforms-field-duplicate"
                                                                        title="Duplicate Field"><i
                                                                            class="fa-solid fa-copy fa-fw"
                                                                            aria-hidden="true"></i></a><a href="#"
                                                                        class="wpforms-field-delete"
                                                                        title="Delete Field"><i
                                                                            class="fa-solid fa-trash"
                                                                            aria-hidden="true"></i></a>
                                                                    <div class="wpforms-field-helper">
                                                                        <span class="wpforms-field-helper-edit">Click to
                                                                            Edit</span>
                                                                        <span class="wpforms-field-helper-drag">Drag to
                                                                            Reorder</span>
                                                                        <span class="wpforms-field-helper-hide"
                                                                            title="Hide Helper">
                                                                            <i class="fa fa-times-circle"
                                                                                aria-hidden="true"></i>
                                                                        </span>
                                                                    </div><label class="label-title "><span
                                                                            class="hidden_text" title="Label Hidden"><i
                                                                                class="fa fa-eye-slash"></i></span><span
                                                                            class="empty_text"
                                                                            title="To ensure your form is accessible, every field should have a descriptive label. If you'd like to hide the label, you can do so by enabling Hide Label in the Advanced Field Options tab."><i
                                                                                class="fa fa-exclamation-triangle"></i></span><span
                                                                            class="text">Email</span><span
                                                                            class="required">*</span></label>
                                                                    <div class="wpforms-confirm wpforms-confirm-disabled">

                                                                        <div class="wpforms-confirm-primary">
                                                                            <input type="email" placeholder=""
                                                                                value="" class="primary-input"
                                                                                readonly="">
                                                                            <label class="wpforms-sub-label">Email</label>
                                                                        </div>

                                                                        <div class="wpforms-confirm-confirmation">
                                                                            <input type="email" placeholder=""
                                                                                class="secondary-input" readonly="">
                                                                            <label class="wpforms-sub-label">Confirm
                                                                                Email</label>
                                                                        </div>

                                                                    </div>

                                                                    <div class="description "></div>
                                                                </div>
                                                                <div class="wpforms-field wpforms-field-layout label_hide"
                                                                    id="wpforms-field-26" data-field-id="26"
                                                                    data-field-type="layout" style=""><a
                                                                        href="#" class="wpforms-field-duplicate"
                                                                        title="Duplicate Field"><i
                                                                            class="fa-solid fa-copy fa-fw"
                                                                            aria-hidden="true"></i></a><a href="#"
                                                                        class="wpforms-field-delete"
                                                                        title="Delete Field"><i
                                                                            class="fa-solid fa-trash"></i></a>
                                                                    <div class="wpforms-field-helper">
                                                                        <span class="wpforms-field-helper-edit">Click to
                                                                            Edit</span>
                                                                        <span class="wpforms-field-helper-drag">Drag to
                                                                            Reorder</span>
                                                                        <span class="wpforms-field-helper-hide"
                                                                            title="Hide Helper">
                                                                            <i class="fa fa-times-circle"
                                                                                aria-hidden="true"></i>
                                                                        </span>
                                                                    </div><label class="label-title "><span
                                                                            class="hidden_text" title="Label Hidden"><i
                                                                                class="fa fa-eye-slash"></i></span><span
                                                                            class="empty_text"
                                                                            title="To ensure your form is accessible, every field should have a descriptive label. If you'd like to hide the label, you can do so by enabling Hide Label in the Advanced Field Options tab."><i
                                                                                class="fa fa-exclamation-triangle"></i></span><span
                                                                            class="text">Layout</span><span
                                                                            class="required">*</span></label>
                                                                    <div class="wpforms-field-layout-columns">
                                                                        <div
                                                                            class="wpforms-layout-column wpforms-layout-column-50 ui-sortable">
                                                                            <div class="wpforms-layout-column-placeholder"
                                                                                title="Click to set this column as default. Click again to unset.">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    fill="none" viewBox="0 0 24 24"
                                                                                    class="normal-icon">
                                                                                    <path
                                                                                        d="M18.2 11.71a.62.62 0 0 0-.59-.58h-4.74V6.39a.62.62 0 0 0-.58-.58h-.58a.59.59 0 0 0-.58.58v4.74H6.39a.59.59 0 0 0-.58.58v.58c0 .34.24.58.58.58h4.74v4.74c0 .34.24.58.58.58h.58c.3 0 .58-.24.58-.58v-4.74h4.74c.3 0 .58-.24.58-.58v-.58ZM24 12a12 12 0 1 0-24 0 12 12 0 0 0 24 0Zm-1.55 0a10.44 10.44 0 1 1-20.9 0C1.55 6.29 6.19 1.55 12 1.55A10.5 10.5 0 0 1 22.45 12Z"
                                                                                        class="wpforms-plus-path"></path>
                                                                                </svg>
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    fill="none" viewBox="0 0 24 24"
                                                                                    class="active-icon">
                                                                                    <path
                                                                                        d="M12 24a12 12 0 1 0 0-24 12 12 0 0 0 0 24ZM1.55 12C1.55 6.29 6.19 1.55 12 1.55A10.5 10.5 0 0 1 22.45 12a10.44 10.44 0 1 1-20.9 0ZM6 11.42a.56.56 0 0 0 0 .82l.34.34c.24.24.58.24.82 0l4.02-4.16v9.2c0 .33.24.57.58.57h.48c.3 0 .58-.24.58-.58v-9.2l3.97 4.17c.24.24.58.24.82 0l.34-.34a.56.56 0 0 0 0-.82L12.4 5.85a.56.56 0 0 0-.83 0L6 11.42Z"
                                                                                        class="wpforms-plus-path"></path>
                                                                                </svg>
                                                                            </div>
                                                                        </div>
                                                                        <div
                                                                            class="wpforms-layout-column wpforms-layout-column-50 ui-sortable">
                                                                            <div class="wpforms-layout-column-placeholder"
                                                                                title="Click to set this column as default. Click again to unset.">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    fill="none" viewBox="0 0 24 24"
                                                                                    class="normal-icon">
                                                                                    <path
                                                                                        d="M18.2 11.71a.62.62 0 0 0-.59-.58h-4.74V6.39a.62.62 0 0 0-.58-.58h-.58a.59.59 0 0 0-.58.58v4.74H6.39a.59.59 0 0 0-.58.58v.58c0 .34.24.58.58.58h4.74v4.74c0 .34.24.58.58.58h.58c.3 0 .58-.24.58-.58v-4.74h4.74c.3 0 .58-.24.58-.58v-.58ZM24 12a12 12 0 1 0-24 0 12 12 0 0 0 24 0Zm-1.55 0a10.44 10.44 0 1 1-20.9 0C1.55 6.29 6.19 1.55 12 1.55A10.5 10.5 0 0 1 22.45 12Z"
                                                                                        class="wpforms-plus-path"></path>
                                                                                </svg>
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    fill="none" viewBox="0 0 24 24"
                                                                                    class="active-icon">
                                                                                    <path
                                                                                        d="M12 24a12 12 0 1 0 0-24 12 12 0 0 0 0 24ZM1.55 12C1.55 6.29 6.19 1.55 12 1.55A10.5 10.5 0 0 1 22.45 12a10.44 10.44 0 1 1-20.9 0ZM6 11.42a.56.56 0 0 0 0 .82l.34.34c.24.24.58.24.82 0l4.02-4.16v9.2c0 .33.24.57.58.57h.48c.3 0 .58-.24.58-.58v-9.2l3.97 4.17c.24.24.58.24.82 0l.34-.34a.56.56 0 0 0 0-.82L12.4 5.85a.56.56 0 0 0-.83 0L6 11.42Z"
                                                                                        class="wpforms-plus-path"></path>
                                                                                </svg>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="description "></div>
                                                                </div>
                                                                <div class="wpforms-field wpforms-field-name size-medium required ui-sortable-handle"
                                                                    id="wpforms-field-19" data-field-id="19"
                                                                    data-field-type="name"><a href="#"
                                                                        class="wpforms-field-duplicate"
                                                                        title="Duplicate Field"><i
                                                                            class="fa-solid fa-copy fa-fw"
                                                                            aria-hidden="true"></i></a><a href="#"
                                                                        class="wpforms-field-delete"
                                                                        title="Delete Field"><i
                                                                            class="fa-solid fa-trash"
                                                                            aria-hidden="true"></i></a>
                                                                    <div class="wpforms-field-helper">
                                                                        <span class="wpforms-field-helper-edit">Click to
                                                                            Edit</span>
                                                                        <span class="wpforms-field-helper-drag">Drag to
                                                                            Reorder</span>
                                                                        <span class="wpforms-field-helper-hide"
                                                                            title="Hide Helper">
                                                                            <i class="fa fa-times-circle"
                                                                                aria-hidden="true"></i>
                                                                        </span>
                                                                    </div><label class="label-title "><span
                                                                            class="hidden_text" title="Label Hidden"><i
                                                                                class="fa fa-eye-slash"></i></span><span
                                                                            class="empty_text"
                                                                            title="To ensure your form is accessible, every field should have a descriptive label. If you'd like to hide the label, you can do so by enabling Hide Label in the Advanced Field Options tab."><i
                                                                                class="fa fa-exclamation-triangle"></i></span><span
                                                                            class="text">Name99</span><span
                                                                            class="required">*</span></label>
                                                                    <div
                                                                        class="format-selected-simple format-selected wpforms-clear">

                                                                        <div class="wpforms-simple">
                                                                            <input type="text" placeholder=""
                                                                                value="" class="primary-input"
                                                                                readonly="">
                                                                        </div>

                                                                        <div class="wpforms-first-name">
                                                                            <input type="text" placeholder=""
                                                                                value="" class="primary-input"
                                                                                readonly="">
                                                                            <label class="wpforms-sub-label">First</label>
                                                                        </div>

                                                                        <div class="wpforms-middle-name">
                                                                            <input type="text" placeholder=""
                                                                                value="" class="primary-input"
                                                                                readonly="">
                                                                            <label
                                                                                class="wpforms-sub-label">Middle</label>
                                                                        </div>

                                                                        <div class="wpforms-last-name">
                                                                            <input type="text" placeholder=""
                                                                                value="" class="primary-input"
                                                                                readonly="">
                                                                            <label class="wpforms-sub-label">Last</label>
                                                                        </div>

                                                                    </div>

                                                                    <div class="description "></div>
                                                                </div>
                                                                <div class="wpforms-field wpforms-field-textarea size-medium ui-sortable-handle"
                                                                    id="wpforms-field-2" data-field-id="2"
                                                                    data-field-type="textarea"><a href="#"
                                                                        class="wpforms-field-duplicate"
                                                                        title="Duplicate Field"><i
                                                                            class="fa-solid fa-copy fa-fw"
                                                                            aria-hidden="true"></i></a><a href="#"
                                                                        class="wpforms-field-delete"
                                                                        title="Delete Field"><i
                                                                            class="fa-solid fa-trash"
                                                                            aria-hidden="true"></i></a>
                                                                    <div class="wpforms-field-helper">
                                                                        <span class="wpforms-field-helper-edit">Click to
                                                                            Edit</span>
                                                                        <span class="wpforms-field-helper-drag">Drag to
                                                                            Reorder</span>
                                                                        <span class="wpforms-field-helper-hide"
                                                                            title="Hide Helper">
                                                                            <i class="fa fa-times-circle"
                                                                                aria-hidden="true"></i>
                                                                        </span>
                                                                    </div><label class="label-title "><span
                                                                            class="hidden_text" title="Label Hidden"><i
                                                                                class="fa fa-eye-slash"></i></span><span
                                                                            class="empty_text"
                                                                            title="To ensure your form is accessible, every field should have a descriptive label. If you'd like to hide the label, you can do so by enabling Hide Label in the Advanced Field Options tab."><i
                                                                                class="fa fa-exclamation-triangle"></i></span><span
                                                                            class="text">Comment or Message</span><span
                                                                            class="required">*</span></label>
                                                                    <textarea placeholder="" class="primary-input" readonly=""></textarea>
                                                                    <div class="description "></div>
                                                                </div>
                                                                <div class="wpforms-field wpforms-field-pagebreak wpforms-field-stick wpforms-pagebreak-bottom ui-draggable ui-draggable-handle"
                                                                    id="wpforms-field-25" data-field-id="25"
                                                                    data-field-type="pagebreak" style=""><a
                                                                        href="#" class="wpforms-field-duplicate"
                                                                        title="Duplicate Field"><i
                                                                            class="fa-solid fa-copy fa-fw"
                                                                            aria-hidden="true"></i></a><a href="#"
                                                                        class="wpforms-field-delete"
                                                                        title="Delete Field"><i
                                                                            class="fa-solid fa-trash"></i></a>
                                                                    <div class="wpforms-field-helper">
                                                                        <span class="wpforms-field-helper-edit">Click to
                                                                            Edit</span>
                                                                        <span class="wpforms-field-helper-drag">Drag to
                                                                            Reorder</span>
                                                                        <span class="wpforms-field-helper-hide"
                                                                            title="Hide Helper">
                                                                            <i class="fa fa-times-circle"
                                                                                aria-hidden="true"></i>
                                                                        </span>
                                                                    </div>
                                                                    <div
                                                                        class="wpforms-pagebreak-buttons wpforms-pagebreak-buttons-left">
                                                                        <button
                                                                            class="wpforms-pagebreak-button wpforms-pagebreak-prev wpforms-hidden">Previous</button>
                                                                    </div>
                                                                    <div class="wpforms-pagebreak-divider"><span
                                                                            class="line"></span></div>
                                                                </div>
                                                            </div>


                                                            <div class="wpforms-field-recaptcha is-recaptcha"
                                                                style="display: none;">
                                                                <div class="wpforms-field-recaptcha-wrap">
                                                                    <div class="wpforms-field-recaptcha-wrap-l">
                                                                        <svg class="wpforms-field-hcaptcha-icon"
                                                                            fill="none" viewBox="0 0 83 90">
                                                                            <path opacity=".5"
                                                                                d="M60.012 69.998H50.01V80h10.002V69.998z"
                                                                                fill="#0074BF"></path>
                                                                            <path opacity=".7"
                                                                                d="M50.01 69.998H40.008V80H50.01V69.998zM40.008 69.998H30.006V80h10.002V69.998z"
                                                                                fill="#0074BF"></path>
                                                                            <path opacity=".5"
                                                                                d="M30.006 69.998H20.004V80h10.002V69.998z"
                                                                                fill="#0074BF"></path>
                                                                            <path opacity=".7"
                                                                                d="M70.014 60.013H60.014v10.002h10.002V60.012z"
                                                                                fill="#0082BF"></path>
                                                                            <path opacity=".8"
                                                                                d="M60.012 60.013H50.01v10.002h10.002V60.012z"
                                                                                fill="#0082BF"></path>
                                                                            <path
                                                                                d="M50.01 60.013H40.008v10.002H50.01V60.012zM40.008 60.013H30.006v10.002h10.002V60.012z"
                                                                                fill="#0082BF"></path>
                                                                            <path opacity=".8"
                                                                                d="M30.006 60.013H20.004v10.002h10.002V60.012z"
                                                                                fill="#0082BF"></path>
                                                                            <path opacity=".7"
                                                                                d="M20.004 60.013H10.002v10.002h10.002V60.012z"
                                                                                fill="#0082BF"></path>
                                                                            <path opacity=".5"
                                                                                d="M80 50.01H69.998v10.002H80V50.01z"
                                                                                fill="#008FBF"></path>
                                                                            <path opacity=".8"
                                                                                d="M70.014 50.01H60.014v10.002h10.002V50.01z"
                                                                                fill="#008FBF"></path>
                                                                            <path
                                                                                d="M60.012 50.01H50.01v10.002h10.002V50.01zM50.01 50.01H40.008v10.002H50.01V50.01zM40.008 50.01H30.006v10.002h10.002V50.01zM30.006 50.01H20.004v10.002h10.002V50.01z"
                                                                                fill="#008FBF"></path>
                                                                            <path opacity=".8"
                                                                                d="M20.004 50.01H10.002v10.002h10.002V50.01z"
                                                                                fill="#008FBF"></path>
                                                                            <path opacity=".5"
                                                                                d="M10.002 50.01H0v10.002h10.002V50.01z"
                                                                                fill="#008FBF"></path>
                                                                            <path opacity=".7"
                                                                                d="M80 40.008H69.998V50.01H80V40.008z"
                                                                                fill="#009DBF"></path>
                                                                            <path
                                                                                d="M70.014 40.008H60.014V50.01h10.002V40.008zM60.012 40.008H50.01V50.01h10.002V40.008zM50.01 40.008H40.008V50.01H50.01V40.008zM40.008 40.008H30.006V50.01h10.002V40.008zM30.006 40.008H20.004V50.01h10.002V40.008zM20.004 40.008H10.002V50.01h10.002V40.008z"
                                                                                fill="#009DBF"></path>
                                                                            <path opacity=".7"
                                                                                d="M10.002 40.008H0V50.01h10.002V40.008z"
                                                                                fill="#009DBF"></path>
                                                                            <path opacity=".7"
                                                                                d="M80 30.006H69.998v10.002H80V30.006z"
                                                                                fill="#00ABBF"></path>
                                                                            <path
                                                                                d="M70.014 30.006H60.014v10.002h10.002V30.006zM60.012 30.006H50.01v10.002h10.002V30.006zM50.01 30.006H40.008v10.002H50.01V30.006zM40.008 30.006H30.006v10.002h10.002V30.006zM30.006 30.006H20.004v10.002h10.002V30.006zM20.004 30.006H10.002v10.002h10.002V30.006z"
                                                                                fill="#00ABBF"></path>
                                                                            <path opacity=".7"
                                                                                d="M10.002 30.006H0v10.002h10.002V30.006z"
                                                                                fill="#00ABBF"></path>
                                                                            <path opacity=".5"
                                                                                d="M80 20.004H69.998v10.002H80V20.004z"
                                                                                fill="#00B9BF"></path>
                                                                            <path opacity=".8"
                                                                                d="M70.014 20.004H60.014v10.002h10.002V20.004z"
                                                                                fill="#00B9BF"></path>
                                                                            <path
                                                                                d="M60.012 20.004H50.01v10.002h10.002V20.004zM50.01 20.004H40.008v10.002H50.01V20.004zM40.008 20.004H30.006v10.002h10.002V20.004zM30.006 20.004H20.004v10.002h10.002V20.004z"
                                                                                fill="#00B9BF"></path>
                                                                            <path opacity=".8"
                                                                                d="M20.004 20.004H10.002v10.002h10.002V20.004z"
                                                                                fill="#00B9BF"></path>
                                                                            <path opacity=".5"
                                                                                d="M10.002 20.004H0v10.002h10.002V20.004z"
                                                                                fill="#00B9BF"></path>
                                                                            <path opacity=".7"
                                                                                d="M70.014 10.002H60.014v10.002h10.002V10.002z"
                                                                                fill="#00C6BF"></path>
                                                                            <path opacity=".8"
                                                                                d="M60.012 10.002H50.01v10.002h10.002V10.002z"
                                                                                fill="#00C6BF"></path>
                                                                            <path
                                                                                d="M50.01 10.002H40.008v10.002H50.01V10.002zM40.008 10.002H30.006v10.002h10.002V10.002z"
                                                                                fill="#00C6BF"></path>
                                                                            <path opacity=".8"
                                                                                d="M30.006 10.002H20.004v10.002h10.002V10.002z"
                                                                                fill="#00C6BF"></path>
                                                                            <path opacity=".7"
                                                                                d="M20.004 10.002H10.002v10.002h10.002V10.002z"
                                                                                fill="#00C6BF"></path>
                                                                            <path opacity=".5"
                                                                                d="M60.012 0H50.01v10.002h10.002V0z"
                                                                                fill="#00D4BF"></path>
                                                                            <path opacity=".7"
                                                                                d="M50.01 0H40.008v10.002H50.01V0zM40.008 0H30.006v10.002h10.002V0z"
                                                                                fill="#00D4BF"></path>
                                                                            <path opacity=".5"
                                                                                d="M30.006 0H20.004v10.002h10.002V0z"
                                                                                fill="#00D4BF"></path>
                                                                            <path
                                                                                d="M26.34 36.84l2.787-6.237c1.012-1.592.88-3.55-.232-4.66a3.6 3.6 0 00-.481-.399 3.053 3.053 0 00-2.571-.298 4.246 4.246 0 00-2.322 1.791s-3.816 8.907-5.242 12.905c-1.426 3.998-.863 11.346 4.611 16.836 5.806 5.806 14.215 7.132 19.573 3.102.232-.116.431-.25.63-.415l16.521-13.8c.797-.664 1.99-2.024.93-3.583-1.046-1.526-3.003-.481-3.816.033l-9.504 6.917a.421.421 0 01-.597-.05s0-.017-.017-.017c-.249-.298-.282-1.078.1-1.393l14.58-12.374c1.26-1.128 1.426-2.787.414-3.915-.995-1.11-2.57-1.078-3.848.067l-13.12 10.267a.578.578 0 01-.813-.083c0-.016-.017-.016-.017-.033-.265-.298-.365-.78-.066-1.078l14.862-14.414c1.178-1.095 1.244-2.936.15-4.097a2.824 2.824 0 00-2.024-.863 2.905 2.905 0 00-2.09.83L39.544 36.144c-.365.364-1.078 0-1.161-.432a.474.474 0 01.132-.431l11.628-13.237a2.86 2.86 0 00.15-4.047 2.86 2.86 0 00-4.048-.15c-.05.05-.1.084-.133.133L28.447 37.47c-.63.63-1.56.664-2.007.299a.657.657 0 01-.1-.929z"
                                                                                fill="#fff"></path>
                                                                        </svg>
                                                                        <svg class="wpforms-field-recaptcha-icon"
                                                                            viewBox="0 0 28 27.918">
                                                                            <path
                                                                                d="M28 13.943l-.016-.607V2l-3.133 3.134a13.983 13.983 0 00-21.964.394l5.134 5.183a6.766 6.766 0 012.083-2.329A6.171 6.171 0 0114.025 7.1a1.778 1.778 0 01.492.066 6.719 6.719 0 015.17 3.119l-3.625 3.641 11.941.016"
                                                                                fill="#1c3aa9"></path>
                                                                            <path
                                                                                d="M13.943 0l-.607.016H2.018l3.133 3.133a13.969 13.969 0 00.377 21.964l5.183-5.134A6.766 6.766 0 018.382 17.9 6.171 6.171 0 017.1 13.975a1.778 1.778 0 01.066-.492 6.719 6.719 0 013.117-5.167l3.641 3.641L13.943 0"
                                                                                fill="#4285f4"></path>
                                                                            <path
                                                                                d="M0 13.975l.016.607v11.334l3.133-3.133a13.983 13.983 0 0021.964-.394l-5.134-5.183a6.766 6.766 0 01-2.079 2.33 6.171 6.171 0 01-3.92 1.279 1.778 1.778 0 01-.492-.066 6.719 6.719 0 01-5.167-3.117l3.641-3.641c-4.626 0-9.825.016-11.958-.016"
                                                                                fill="#ababab"></path>
                                                                        </svg>
                                                                        <svg class="wpforms-field-turnstile-icon"
                                                                            fill="none" viewBox="0 0 106 106">
                                                                            <g clip-path="url(#a)">
                                                                                <path fill="#F4801F"
                                                                                    d="m72.375 76.265.541-1.877c.643-2.231.405-4.29-.678-5.808-1.011-1.397-2.66-2.216-4.68-2.312l-38.213-.486a.743.743 0 0 1-.683-1.012 1.012 1.012 0 0 1 .885-.678l38.583-.506c4.554-.207 9.532-3.92 11.267-8.454l2.196-5.748a1.354 1.354 0 0 0 .061-.779 25.13 25.13 0 0 0-48.312-2.6 11.307 11.307 0 0 0-17.708 11.849A16.054 16.054 0 0 0 .172 76.28a.744.744 0 0 0 .734.643H71.48a.927.927 0 0 0 .895-.658Z">
                                                                                </path>
                                                                                <path fill="#F9AB41"
                                                                                    d="M85.11 49.82c-.338 0-.692.01-1.063.03a.444.444 0 0 0-.162.035.59.59 0 0 0-.384.405l-1.518 5.191c-.648 2.231-.41 4.29.678 5.808a5.895 5.895 0 0 0 4.675 2.313l8.15.505a.728.728 0 0 1 .577.314.759.759 0 0 1 .086.693 1.012 1.012 0 0 1-.885.678l-8.465.506c-4.599.213-9.552 3.921-11.287 8.45l-.612 1.598a.455.455 0 0 0 .4.617h29.157a.782.782 0 0 0 .779-.592 20.92 20.92 0 0 0-10.822-24.36 20.916 20.916 0 0 0-9.294-2.191h-.01Z">
                                                                                </path>
                                                                            </g>
                                                                            <defs>
                                                                                <clipPath id="a">
                                                                                    <path fill="#fff"
                                                                                        d="M0 0h106v106H0z"></path>
                                                                                </clipPath>
                                                                            </defs>
                                                                        </svg>
                                                                    </div>
                                                                    <div class="wpforms-field-recaptcha-wrap-r">
                                                                        <p class="wpforms-field-hcaptcha-title">hCaptcha
                                                                        </p>
                                                                        <p class="wpforms-field-recaptcha-title">reCAPTCHA
                                                                        </p>
                                                                        <p class="wpforms-field-turnstile-title">Turnstile
                                                                        </p>
                                                                        <p class="wpforms-field-recaptcha-desc">
                                                                            <span
                                                                                class="wpforms-field-recaptcha-desc-txt">Enabled</span><svg
                                                                                class="wpforms-field-recaptcha-desc-icon"
                                                                                xmlns="http://www.w3.org/2000/svg"
                                                                                viewBox="0 0 512 512">
                                                                                <path
                                                                                    d="M512 256c0-37.7-23.7-69.9-57.1-82.4 14.7-32.4 8.8-71.9-17.9-98.6-26.7-26.7-66.2-32.6-98.6-17.9C325.9 23.7 293.7 0 256 0s-69.9 23.7-82.4 57.1c-32.4-14.7-72-8.8-98.6 17.9-26.7 26.7-32.6 66.2-17.9 98.6C23.7 186.1 0 218.3 0 256s23.7 69.9 57.1 82.4c-14.7 32.4-8.8 72 17.9 98.6 26.6 26.6 66.1 32.7 98.6 17.9 12.5 33.3 44.7 57.1 82.4 57.1s69.9-23.7 82.4-57.1c32.6 14.8 72 8.7 98.6-17.9 26.7-26.7 32.6-66.2 17.9-98.6 33.4-12.5 57.1-44.7 57.1-82.4zm-144.8-44.25L236.16 341.74c-4.31 4.28-11.28 4.25-15.55-.06l-75.72-76.33c-4.28-4.31-4.25-11.28.06-15.56l26.03-25.82c4.31-4.28 11.28-4.25 15.56.06l42.15 42.49 97.2-96.42c4.31-4.28 11.28-4.25 15.55.06l25.82 26.03c4.28 4.32 4.26 11.29-.06 15.56z">
                                                                                </path>
                                                                            </svg>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <p class="wpforms-field-submit" style=""><input
                                                                    type="submit" value="Submit"
                                                                    class="wpforms-field-submit-button"></p>
                                                        </div>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>

                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-9"></div>
                        <div class="col-lg-3">
                            <span>Configure<span>
                        </div>
                        <hr>
                        <div class="col-lg-9">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>
                                    <h4>Customize your application form</h4>
                                </span>
                                <div class="d-flex flex-column flex-md-row">
                                    <button type="button"
                                        class="btn btn-default btn-transparent mb-2 mb-md-0 mr-md-2">Default</button>
                                    <button type="button"
                                        class="btn btn-default btn-transparent mb-2 mb-md-0 mr-md-2 btncancle">Default</button>
                                    <button type="button" class="btn btn-success btnsave">Save</button>
                                </div>
                            </div>
                            <div class="custom-form-container">
                                <form class="row g-3">
                                    <div class="col-md-6">
                                        <label for="inputEmail4" class="form-label">Full Name*</label>
                                        <input type="text" placeholder="Your full name" class="form-control"
                                            id="inputEmail4">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="inputPassword4" class="form-label">Email*</label>
                                        <input type="email" placeholder="Your email" class="form-control"
                                            id="inputPassword4">
                                    </div>
                                    <div class="col-6">
                                        <label for="inputAddress" class="form-label">Phone*</label>
                                        <input type="number" class="form-control" id="inputAddress"
                                            placeholder="Your phone number">
                                    </div>
                                    <div class="col-12">
                                        <label for="inputAddress2" class="form-label">Portfolio</label>
                                        <fieldset class="upload_dropZone text-center mb-3 p-4">

                                            <legend class="visually-hidden">Drop Here</legend>

                                            <svg class="upload_svg" width="60" height="60"
                                                aria-hidden="true">
                                                <use href="#icon-imageUpload"></use>
                                            </svg>

                                            <p class="small my-2">Drag &amp; Drop file or browse file
                                                Max size 10 Mb<br><i>or</i></p>

                                            <input id="upload_image_background" data-post-name="image_background"
                                                data-post-url="https://someplace.com/image/uploads/backgrounds/"
                                                class="position-absolute invisible" type="file" multiple
                                                accept="image/jpeg, image/png, image/svg+xml" />

                                            <label class="btn btn-upload mb-3" for="upload_image_background">Choose
                                                file(s)</label>

                                            <div
                                                class="upload_gallery d-flex flex-wrap justify-content-center gap-3 mb-0">
                                            </div>

                                        </fieldset>


                                    </div>
                                    <label for="inputGendar" class="form-label">Gender*</label>

                                    <div class="d-flex ">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio1" value="option1">
                                            <label class="form-check-label" for="inlineRadio1">Male</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio2" value="option2">
                                            <label class="form-check-label" for="inlineRadio2">Female</label>
                                        </div>
                                    </div>

                                    <div class="col-12">


                                        <span type="button" class=" Toggle-Section btn-default btn-transparent"
                                            data-bs-toggle="collapse" data-bs-target="#sectionContent"
                                            aria-expanded="false" aria-controls="sectionContent"
                                            onclick="toggleIcon()">
                                            <i id="icon" class="fa fa-chevron-right"></i> &nbsp; &nbsp;
                                            Home Address
                                        </span>


                                        <div class="row g-3 collapse" id="sectionContent">
                                            <div class="col-12">
                                                <hr>
                                            </div>
                                            <div class="col-md-6">

                                                <label for="inputEmail4" class="form-label">Country*</label>
                                                <input type="text" placeholder="Select country"
                                                    class="form-control" id="inputEmail4">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="inputPassword4" class="form-label">Zip Code*</label>
                                                <input type="text" placeholder="Your ZIP code" class="form-control"
                                                    id="inputPassword4">
                                            </div>
                                            <div class="col-12">
                                                <label for="inputAddress" class="form-label">Street Address*</label>
                                                <input type="text" class="form-control" id="inputAddress"
                                                    placeholder="Your address">
                                            </div>
                                        </div>

                                    </div>


                                </form>
                            </div>
                        </div>


                        <div class="col-lg-3 ms-auto">
                            <div class="nav-align-top mb-4">
                                <ul class="nav nav-tabs nav-justified  nav-fill" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button type="button" class="nav-link active" role="tab"
                                            data-bs-toggle="tab" data-bs-target="#form-tabs-add-fields"
                                            aria-controls="form-tabs-add-fields" aria-selected="false"
                                            tabindex="-1"><i class="tf-icons ti ti-home ti-xs me-1"></i> Add
                                            Fields</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                            data-bs-target="#form-tabs-field-options"
                                            aria-controls="form-tabs-field-options" aria-selected="true"><i
                                                class="tf-icons ti ti-user ti-xs me-1"></i> Field Options</button>
                                    </li>

                                </ul>

                                <div class="tab-content p-0 mt-3">
                                    <div class="tab-pane fade show active" id="form-tabs-add-fields" role="tabpanel">
                                        <hr>
                                        <h5 class="h5"> Drag&Drop</h5>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <button type="button" class="form-control draggable custom-size"
                                                    id="inputEmail4">
                                                    <i class="fas fa-paragraph"></i> Paragraph
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="form-control draggable custom-size"
                                                    id="inputEmail4">
                                                    <i class="fas fa-cogs"></i> Form Field
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="form-control draggable custom-size"
                                                    id="inputEmail4">
                                                    <i class="fas fa-font"></i> Text Area
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="form-control draggable custom-size"
                                                    id="inputEmail4">
                                                    <i class="fas fa-check-circle"></i> Radio
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="form-control draggable custom-size"
                                                    id="inputEmail4">
                                                    <i class="fas fa-check-square"></i> Checkbox
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="form-control draggable custom-size"
                                                    id="inputEmail4">
                                                    <i class="far fa-calendar-alt"></i> Date Picker
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="form-control draggable custom-size"
                                                    id="inputEmail4">
                                                    <i class="fas fa-caret-down"></i> Dropdown
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" style="    font-size: 14.6px; "
                                                    class="form-control draggable " id="inputEmail4">
                                                    <i class="fas fa-paperclip"></i> Attachment
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="form-control draggable custom-size"
                                                    id="inputEmail4">
                                                    <i class="fas fa-link"></i> Hyperlink
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="form-control draggable custom-size"
                                                    id="inputEmail4">
                                                    <i class="fas fa-list"></i> List Item
                                                </button>
                                            </div>
                                        </div>

                                        <hr>
                                        <h5 class="h5"> Suggested Field</h5>

                                        <div class="mb-4 row g-3">
                                            <div class="col-md-6">
                                                <button type="button" class="form-control draggable custom-size"
                                                    id="inputEmail4">
                                                    <i class="fas fa-user"></i> Full Name
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="form-control draggable custom-size"
                                                    id="inputEmail4">
                                                    <i class="fas fa-venus-mars"></i> Gender
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="form-control draggable custom-size"
                                                    id="inputEmail4">
                                                    <i class="fas fa-birthday-cake"></i> DOB
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="form-control draggable custom-size"
                                                    id="inputEmail4">
                                                    <i class="fas fa-phone"></i> Phone
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="form-control draggable custom-size"
                                                    id="inputEmail4">
                                                    <i class="fas fa-envelope"></i> Email
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="form-control draggable custom-size"
                                                    id="inputEmail4">
                                                    <i class="fas fa-city"></i> City
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="form-control draggable custom-size"
                                                    id="inputEmail4">
                                                    <i class="fas fa-map-marker-alt"></i> Address
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="form-control draggable custom-size"
                                                    id="inputEmail4">
                                                    <i class="fas fa-map-pin"></i> ZIP Code
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="form-tabs-field-options" role="tabpanel">

                                        <ul class="nav nav-tabs nav-fill mb-2" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button type="button" class="nav-link" role="tab"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#form-tabs-field-options-general"
                                                    aria-controls="form-tabs-field-options-general"
                                                    aria-selected="false" tabindex="-1"><i
                                                        class="tf-icons ti ti-home ti-xs me-1"></i>General</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button type="button" class="nav-link" role="tab"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#form-tabs-field-options-advanced"
                                                    aria-controls="form-tabs-field-options-advanced"
                                                    aria-selected="false" tabindex="-1"><i
                                                        class="tf-icons ti ti-home ti-xs me-1"></i>Advanced</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button type="button" class="nav-link active" role="tab"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#form-tabs-field-options-smartlogic"
                                                    aria-controls="form-tabs-field-options-smartlogic"
                                                    aria-selected="true"><i
                                                        class="tf-icons ti ti-user ti-xs me-1"></i>Smart Logic</button>
                                            </li>

                                        </ul>

                                        <p>
                                            Icing pastry pudding oat cake. Lemon drops cotton candy caramels cake caramels
                                            sesame snaps powder. Bear
                                            claw
                                            candy topping.
                                        </p>
                                        <p class="mb-0">
                                            Tootsie roll fruitcake cookie. Dessert topping pie. Jujubes wafer carrot cake
                                            jelly. Bonbon jelly-o
                                            jelly-o ice
                                            cream jelly beans candy canes cake bonbon. Cookie jelly beans marshmallow
                                            jujubes sweet.
                                        </p>
                                    </div>
                                    <div class="tab-pane fade active show" id="navs-justified-profile"
                                        role="tabpanel">
                                        <p>
                                            Donut dragée jelly pie halvah. Danish gingerbread bonbon cookie wafer candy oat
                                            cake ice cream. Gummies
                                            halvah
                                            tootsie roll muffin biscuit icing dessert gingerbread. Pastry ice cream
                                            cheesecake fruitcake.
                                        </p>
                                        <p class="mb-0">
                                            Jelly-o jelly beans icing pastry cake cake lemon drops. Muffin muffin pie
                                            tiramisu halvah cotton candy
                                            liquorice caramels.
                                        </p>
                                    </div>
                                    <div class="tab-pane fade" id="navs-justified-messages" role="tabpanel">
                                        <p>
                                            Oat cake chupa chups dragée donut toffee. Sweet cotton candy jelly beans
                                            macaroon gummies cupcake gummi
                                            bears
                                            cake chocolate.
                                        </p>
                                        <p class="mb-0">
                                            Cake chocolate bar cotton candy apple pie tootsie roll ice cream apple pie
                                            brownie cake. Sweet roll icing
                                            sesame snaps caramels danish toffee. Brownie biscuit dessert dessert. Pudding
                                            jelly jelly-o tart brownie
                                            jelly.
                                        </p>
                                    </div>
                                </div>
                            </div>


                            <div class="tab-content w-100">
                                <div class="tab-pane fade" id="form-tabs-account" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <button type="button" class="form-control draggable custom-size"
                                                id="inputEmail4">
                                                Button</button>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" class="form-control draggable custom-size"
                                                id="inputEmail4">
                                                Button</button>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" class="form-control draggable custom-size"
                                                id="inputEmail4">
                                                Button</button>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" class="form-control draggable custom-size"
                                                id="inputEmail4">
                                                Button</button>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" class="form-control draggable custom-size"
                                                id="inputEmail4">
                                                Button</button>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" class="form-control draggable custom-size"
                                                id="inputEmail4">
                                                Button</button>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" class="form-control draggable custom-size"
                                                id="inputEmail4">
                                                Button</button>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" class="form-control draggable custom-size"
                                                id="inputEmail4">
                                                Button</button>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" class="form-control draggable custom-size"
                                                id="inputEmail4">
                                                Button</button>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" class="form-control draggable custom-size"
                                                id="inputEmail4">
                                                Button</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>

                </div>




            </div>

        </div>
    </div>



{{-- 
    <script>
        $(document).ready(function() {
            $(".draggable").draggable();
        });
    </script> --}}
@endsection
