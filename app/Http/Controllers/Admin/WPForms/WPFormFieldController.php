<?php


namespace App\Http\Controllers\Admin\WPForms;

use App\Models\WPForm;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller; // Correct import for base Controller
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Admin\WPForms\Fields\WPFormsFieldFactory;


class WPFormFieldController extends Controller
{


	/**
	 * Create a new field in the admin AJAX editor.
	 *
	 * @since 1.0.0
	 */
  public function addField(Request $request)
  {


    //Validate and process the request data
    $validated = $request->validate([
        'action' => 'required|string',
        'id' => 'required|integer',
       // 'defaults' => 'sometimes|array',
        'type'     => 'required|string',
        'nonce' => 'required|string'
    ]);

		// Grab field data.

    // Use Laravel's array_get helper to provide a default empty array if 'defaults' is not set
    $field_args  = $request->input('defaults', []);
    // 'type' input is already validated as a string, so direct assignment is safe
    $field_type  = $validated['type'];

		// Use the WPForm model to get the next field ID
    //$field_id          = WPForm::nextFieldId($formId);
    $field_id = mt_rand(1, 90000);

    $field_type = $request->input('type');
    $form_id = $request->input('id');
    $field_defaults = $request->input('defaults', []);

    try {
        $field_object = WPFormsFieldFactory::create($field_type, $field_id, $field_defaults);
        // Additional logic for the field
        $field             = [
          'id'          => $field_id,
          'type'        => $field_type,
          'label'       => $field_object->name,
          'description' => '',
        ];


        $field_required = "";
		// Field types that default to required.
		if ( ! empty( $field_required ) ) {
			$field_required    = 'required';
			$field['required'] = '1';
		}

    $field_required    = "";
		$field_class       = "";
		$field_helper_hide = true;
    // return 		$field_object->field_options( $field );
    // return 		$field_object->field_preview( $field );
		// Build Preview.

		ob_start();
		$field_object->field_preview( $field );
    
		$prev    = ob_get_clean();
		$preview = sprintf(
			'<div class="wpforms-field wpforms-field-%1$s %2$s %3$s" id="wpforms-field-%4$d" data-field-id="%4$d" data-field-type="%5$s">',
			esc_attr( $field_type ),
			esc_attr( $field_required ),
			esc_attr( $field_class ),
			absint( $field['id'] ),
			esc_attr( $field_type )
		);




		// if ( apply_filters( 'wpforms_field_new_display_duplicate_button', true, $field ) ) {
		// 	$preview .= sprintf( '<a href="#" class="wpforms-field-duplicate" title="%s"><i class="fa fa-files-o" aria-hidden="true"></i></a>', esc_attr__( 'Duplicate Field', 'wpforms-lite' ) );
		// }

		$preview .= sprintf( '<a href="#" class="wpforms-field-delete" title="%s"><i class="fa fa-trash"></i></a>', esc_attr__( 'Delete Field', 'wpforms-lite' ) );

		if ( ! $field_helper_hide ) {
			$preview .= sprintf(
				'<div class="wpforms-field-helper">
					<span class="wpforms-field-helper-edit">%s</span>
					<span class="wpforms-field-helper-drag">%s</span>
					<span class="wpforms-field-helper-hide" title="%s">
						<i class="fa fa-times-circle" aria-hidden="true"></i>
					</span>
				</div>',
				esc_html__( 'Click to Edit', 'wpforms-lite' ),
				esc_html__( 'Drag to Reorder', 'wpforms-lite' ),
				esc_html__( 'Hide Helper', 'wpforms-lite' )
			);
		}

		$preview .= $prev;
		$preview .= '</div>';

		// Build Options.
		$class   = ""; // apply_filters( 'wpforms_builder_field_option_class', '', $field );
		$options = sprintf(
			'<div class="wpforms-field-option wpforms-field-option-%1$s %2$s" id="wpforms-field-option-%3$d" data-field-id="%3$d">',
			sanitize_html_class( $field['type'] ),
			wpforms_sanitize_classes( $class ),
			absint( $field['id'] )
		);

		$options .= sprintf(
			'<input type="hidden" name="fields[%1$d][id]" value="%1$d" class="wpforms-field-option-hidden-id">',
			absint( $field['id'] )
		);
		$options .= sprintf(
			'<input type="hidden" name="fields[%d][type]" value="%s" class="wpforms-field-option-hidden-type">',
			absint( $field['id'] ),
			esc_attr( $field['type'] )
		);

		ob_start();
		$field_object->field_options( $field );
		$options .= ob_get_clean();
		$options .= '</div>';

		// Prepare to return compiled results.

    
    $html = [
      'success' => true,
      'data' => [
        'form_id' => absint( $form_id ),
        'field'   => $field,
        'preview' => $preview,
        'options' => $options,
        ],
      ];

    return response()->json(
          $html
    );
























        // ... work with the $field instance ...
    } catch (\Exception $e) {
        // ... handle exception ...
        return response()->json(
         $e
      );
    }
        

		$field             = wp_parse_args( $field_args, $field );
		$field             = apply_filters( 'wpforms_field_new_default', $field );
		$field_required    = apply_filters( 'wpforms_field_new_required', '', $field );
		$field_class       = apply_filters( 'wpforms_field_new_class', '', $field );
		$field_helper_hide = ! empty( $_COOKIE['wpforms_field_helper_hide'] );


	}

  public function createField(Request $request)
  {


      // Validate and process the request data
      // $validated = $request->validate([
      //     'action' => 'required|string',
      //     'id' => 'required|integer',
      //     'type' => 'required|string',
      //     'defaults' => 'boolean',
      //     'nonce' => 'required|string'
      // ]);

      // Your logic to handle the creation of the new field
      // ...
      $html = [
        'success' => true,
        'data' => [
          'form_id' => 2481,
          'field' => [
            'id' => 31,
            'type' => 'address',
            'label' => 'Address',
            'description' => '',
            'name' => '',
          ],
          'preview' => '<div class="wpforms-field wpforms-field-address  " id="wpforms-field-31" data-field-id="31" data-field-type="address"><a href="#" class="wpforms-field-duplicate" title="Duplicate Field"><i class="fa fa-files-o" aria-hidden="true"></i></a><a href="#" class="wpforms-field-delete" title="Delete Field"><i class="fa fa-trash"></i></a><label class="label-title "><span class="hidden_text" title="Label Hidden"><i class="fa fa-eye-slash"></i></span><span class="empty_text" title="To ensure your form is accessible, every field should have a descriptive label. If you&#039;d like to hide the label, you can do so by enabling Hide Label in the Advanced Field Options tab."><i class="fa fa-exclamation-triangle"></i></span><span class="text">Address</span><span class="required">*</span></label><div class="wpforms-address-scheme wpforms-address-scheme-us "><div class="wpforms-field-row wpforms-address-1">
                <input type="text" placeholder="" value="" readonly>
                <label class="wpforms-sub-label">Address Line 1</label>
              </div><div class="wpforms-field-row wpforms-address-2 ">
                <input type="text" placeholder="" value="" readonly>
                <label class="wpforms-sub-label">Address Line 2</label>
              </div><div class="wpforms-field-row"><div class="wpforms-city wpforms-one-half ">
                <input type="text" placeholder="" value="" readonly>
                <label class="wpforms-sub-label">City</label>
              </div><div class="wpforms-state wpforms-one-half last"><select readonly> <option class="placeholder" selected>--- Select State ---</option> </select><label class="wpforms-sub-label">State</label></div></div><div class="wpforms-field-row"><div class="wpforms-postal wpforms-one-half ">
                <input type="text" placeholder="" value="" readonly>
                <label class="wpforms-sub-label">Zip Code</label>
              </div><div class="wpforms-country wpforms-one-half last "><label class="wpforms-sub-label"></label></div></div></div><div class="wpforms-address-scheme wpforms-address-scheme-international wpforms-hide"><div class="wpforms-field-row wpforms-address-1">
                <input type="text" placeholder="" value="" readonly>
                <label class="wpforms-sub-label">Address Line 1</label>
              </div><div class="wpforms-field-row wpforms-address-2 ">
                <input type="text" placeholder="" value="" readonly>
                <label class="wpforms-sub-label">Address Line 2</label>
              </div><div class="wpforms-field-row"><div class="wpforms-city wpforms-one-half ">
                <input type="text" placeholder="" value="" readonly>
                <label class="wpforms-sub-label">City</label>
              </div><div class="wpforms-state wpforms-one-half last"><input type="text" placeholder="" value="" readonly><label class="wpforms-sub-label">State / Province / Region</label></div></div><div class="wpforms-field-row"><div class="wpforms-postal wpforms-one-half ">
                <input type="text" placeholder="" value="" readonly>
                <label class="wpforms-sub-label">Postal Code</label>
              </div><div class="wpforms-country wpforms-one-half last "><select readonly><option class="placeholder" selected>--- Select Country ---</option></select><label class="wpforms-sub-label">Country</label></div></div></div><div class="description "></div></div>',
          'options' => '<div class="wpforms-field-option wpforms-field-option-address " id="wpforms-field-option-31" data-field-id="31"><input type="hidden" name="fields[31][id]" value="31" class="wpforms-field-option-hidden-id"><input type="hidden" name="fields[31][type]" value="address" class="wpforms-field-option-hidden-type"><div class="wpforms-field-option-field-title">Address <span>(ID #31)</span></div>
                  <div class="wpforms-field-option-group wpforms-field-option-group-basic active" id="wpforms-field-option-basic-31">
                    <a href="#" class="wpforms-field-option-group-toggle">General</a>
                    <div class="wpforms-field-option-group-inner ">
                  <div class="wpforms-field-option-row wpforms-field-option-row-label " id="wpforms-field-option-row-31-label" data-field-id="31" ><label for="wpforms-field-option-31-label">Label<i class="fa fa-question-circle-o wpforms-help-tooltip" title="Enter text for the form field label. Field labels are recommended and can be hidden in the Advanced Settings."></i></label><input type="text" class="" id="wpforms-field-option-31-label" name="fields[31][label]" value="Address" placeholder="" ></div><div class="wpforms-field-option-row wpforms-field-option-row-scheme " id="wpforms-field-option-row-31-scheme" data-field-id="31" ><label for="wpforms-field-option-31-scheme">Scheme<i class="fa fa-question-circle-o wpforms-help-tooltip" title="Select scheme format for the address field."></i></label><select class="" id="wpforms-field-option-31-scheme" name="fields[31][scheme]" ><option value="us"  selected=\'selected\'>US</option><option value="international" >International</option></select></div><div class="wpforms-field-option-row wpforms-field-option-row-description " id="wpforms-field-option-row-31-description" data-field-id="31" ><label for="wpforms-field-option-31-description">Description<i class="fa fa-question-circle-o wpforms-help-tooltip" title="Enter text for the form field description."></i></label><textarea class="" id="wpforms-field-option-31-description" name="fields[31][description]" rows="3" ></textarea></div><div class="wpforms-field-option-row wpforms-field-option-row-required " id="wpforms-field-option-row-31-required" data-field-id="31" ><span class="wpforms-toggle-control " >
            
            <input type="checkbox" id="wpforms-field-option-31-required" name="fields[31][required]" class="" value="1"  >
            <label class="wpforms-toggle-control-icon" for="wpforms-field-option-31-required"></label>
            <label for="wpforms-field-option-31-required" class="wpforms-toggle-control-label">Required</label><i class="fa fa-question-circle-o wpforms-help-tooltip" title="Check this option to mark the field required. A form will not submit unless all required fields are provided."></i>
          </span></div></div></div><div class="wpforms-field-option-group wpforms-field-option-group-advanced" id="wpforms-field-option-advanced-31" ><a href="#" class="wpforms-field-option-group-toggle">Advanced</a><div class="wpforms-field-option-group-inner"><div class="wpforms-field-option-row wpforms-field-option-row-size " id="wpforms-field-option-row-31-size" data-field-id="31" ><label for="wpforms-field-option-31-size">Field Size<i class="fa fa-question-circle-o wpforms-help-tooltip" title="Select the default form field size."></i></label><select class="" id="wpforms-field-option-31-size" name="fields[31][size]" ><option value="small" >Small</option><option value="medium"  selected=\'selected\'>Medium</option><option value="large" >Large</option></select></div><div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-address1"
              id="wpforms-field-option-row-31-address1"
              data-subfield="address-1"
              data-field-id="31"><label for="wpforms-field-option-31-address1_placeholder">Address Line 1</label><div class="wpforms-field-options-columns-2 wpforms-field-options-columns"><div class="placeholder wpforms-field-options-column"><input type="text" class="placeholder" id="wpforms-field-option-31-address1_placeholder" name="fields[31][address1_placeholder]" value=""><label for="wpforms-field-option-31-address1_placeholder" class="sub-label">Placeholder</label></div><div class="default wpforms-field-options-column"><input type="text" class="default" id="wpforms-field-option-31-address1_default" name="fields[31][address1_default]" value=""><label for="wpforms-field-option-31-address1_default" class="sub-label">Default Value</label></div></div></div><div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-address2"
              id="wpforms-field-option-row-31-address2"
              data-subfield="address-2"
              data-field-id="31"><div class="wpforms-field-header"><label for="wpforms-field-option-31-address2_placeholder">Address Line 2</label><span class="wpforms-toggle-control wpforms-field-option-in-label-right"  title="Turn On if you want to hide this sub field.">
            <label for="wpforms-field-option-31-address2_hide" class="wpforms-toggle-control-label">Hide</label>
            <input type="checkbox" id="wpforms-field-option-31-address2_hide" name="fields[31][address2_hide]" class="wpforms-subfield-hide" value="1"  >
            <label class="wpforms-toggle-control-icon" for="wpforms-field-option-31-address2_hide"></label>
            
          </span></div><div class="wpforms-field-options-columns-2 wpforms-field-options-columns"><div class="placeholder wpforms-field-options-column"><input type="text" class="placeholder" id="wpforms-field-option-31-address2_placeholder" name="fields[31][address2_placeholder]" value=""><label for="wpforms-field-option-31-address2_placeholder" class="sub-label">Placeholder</label></div><div class="default wpforms-field-options-column"><input type="text" class="default" id="wpforms-field-option-31-address2_default" name="fields[31][address2_default]" value=""><label for="wpforms-field-option-31-address2_default" class="sub-label">Default Value</label></div></div></div><div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-city"
              id="wpforms-field-option-row-31-city"
              data-subfield="city"
              data-field-id="31"><label for="wpforms-field-option-31-city_placeholder">City</label><div class="wpforms-field-options-columns-2 wpforms-field-options-columns"><div class="placeholder wpforms-field-options-column"><input type="text" class="placeholder" id="wpforms-field-option-31-city_placeholder" name="fields[31][city_placeholder]" value=""><label for="wpforms-field-option-31-city_placeholder" class="sub-label">Placeholder</label></div><div class="default wpforms-field-options-column"><input type="text" class="default" id="wpforms-field-option-31-city_default" name="fields[31][city_default]" value=""><label for="wpforms-field-option-31-city_default" class="sub-label">Default Value</label></div></div></div><div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-state"
              id="wpforms-field-option-row-31-state"
              data-subfield="state"
              data-field-id="31"><label for="wpforms-field-option-31-state_placeholder">State / Province / Region</label><div class="wpforms-field-options-columns-2 wpforms-field-options-columns"><div class="placeholder wpforms-field-options-column"><input type="text" class="placeholder" id="wpforms-field-option-31-state_placeholder" name="fields[31][state_placeholder]" value=""><label for="wpforms-field-option-31-state_placeholder" class="sub-label">Placeholder</label></div><div class="default wpforms-field-options-column"><select class="default" id="wpforms-field-option-31-state_default" name="fields[31][state_default]" data-scheme="us"><option value="">--- Select State ---</option><option value="AL">Alabama</option><option value="AK">Alaska</option><option value="AZ">Arizona</option><option value="AR">Arkansas</option><option value="CA">California</option><option value="CO">Colorado</option><option value="CT">Connecticut</option><option value="DE">Delaware</option><option value="DC">District of Columbia</option><option value="FL">Florida</option><option value="GA">Georgia</option><option value="HI">Hawaii</option><option value="ID">Idaho</option><option value="IL">Illinois</option><option value="IN">Indiana</option><option value="IA">Iowa</option><option value="KS">Kansas</option><option value="KY">Kentucky</option><option value="LA">Louisiana</option><option value="ME">Maine</option><option value="MD">Maryland</option><option value="MA">Massachusetts</option><option value="MI">Michigan</option><option value="MN">Minnesota</option><option value="MS">Mississippi</option><option value="MO">Missouri</option><option value="MT">Montana</option><option value="NE">Nebraska</option><option value="NV">Nevada</option><option value="NH">New Hampshire</option><option value="NJ">New Jersey</option><option value="NM">New Mexico</option><option value="NY">New York</option><option value="NC">North Carolina</option><option value="ND">North Dakota</option><option value="OH">Ohio</option><option value="OK">Oklahoma</option><option value="OR">Oregon</option><option value="PA">Pennsylvania</option><option value="RI">Rhode Island</option><option value="SC">South Carolina</option><option value="SD">South Dakota</option><option value="TN">Tennessee</option><option value="TX">Texas</option><option value="UT">Utah</option><option value="VT">Vermont</option><option value="VA">Virginia</option><option value="WA">Washington</option><option value="WV">West Virginia</option><option value="WI">Wisconsin</option><option value="WY">Wyoming</option></select><input type="text" class="default wpforms-hidden-strict" id="" name="" value="" data-scheme="international"><label for="wpforms-field-option-31-state_default" class="sub-label">Default Value</label></div></div></div><div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-postal "
              id="wpforms-field-option-row-31-postal"
              data-subfield="postal"
              data-field-id="31"><div class="wpforms-field-header"><label for="wpforms-field-option-31-postal_placeholder">ZIP / Postal</label><span class="wpforms-toggle-control wpforms-field-option-in-label-right"  title="Turn On if you want to hide this sub field.">
            <label for="wpforms-field-option-31-postal_hide" class="wpforms-toggle-control-label">Hide</label>
            <input type="checkbox" id="wpforms-field-option-31-postal_hide" name="fields[31][postal_hide]" class="wpforms-subfield-hide" value="1"  >
            <label class="wpforms-toggle-control-icon" for="wpforms-field-option-31-postal_hide"></label>
            
          </span></div><div class="wpforms-field-options-columns-2 wpforms-field-options-columns"><div class="placeholder wpforms-field-options-column"><input type="text" class="placeholder" id="wpforms-field-option-31-postal_placeholder" name="fields[31][postal_placeholder]" value=""><label for="wpforms-field-option-31-postal_placeholder" class="sub-label">Placeholder</label></div><div class="default wpforms-field-options-column"><input type="text" class="default" id="wpforms-field-option-31-postal_default" name="fields[31][postal_default]" value=""><label for="wpforms-field-option-31-postal_default" class="sub-label">Default Value</label></div></div></div><div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-country wpforms-hidden"
              id="wpforms-field-option-row-31-country"
              data-subfield="country"
              data-field-id="31"><div class="wpforms-field-header"><label for="wpforms-field-option-31-country_placeholder">Country</label><span class="wpforms-toggle-control wpforms-field-option-in-label-right"  title="Turn On if you want to hide this sub field.">
            <label for="wpforms-field-option-31-country_hide" class="wpforms-toggle-control-label">Hide</label>
            <input type="checkbox" id="wpforms-field-option-31-country_hide" name="fields[31][country_hide]" class="wpforms-subfield-hide" value="1"  >
            <label class="wpforms-toggle-control-icon" for="wpforms-field-option-31-country_hide"></label>
            
          </span></div><div class="wpforms-field-options-columns-2 wpforms-field-options-columns"><div class="placeholder wpforms-field-options-column"><input type="text" class="placeholder" id="wpforms-field-option-31-country_placeholder" name="fields[31][country_placeholder]" value=""><label for="wpforms-field-option-31-country_placeholder" class="sub-label">Placeholder</label></div><div class="default wpforms-field-options-column"><input type="text" class="default" id="wpforms-field-option-31-country_default" name="fields[31][country_default]" value="" data-scheme="us"><select class="default wpforms-hidden-strict" id="" name="" data-scheme="international"><option value="">--- Select Country ---</option><option value="AF">Afghanistan</option><option value="AX">Åland Islands</option><option value="AL">Albania</option><option value="DZ">Algeria</option><option value="AS">American Samoa</option><option value="AD">Andorra</option><option value="AO">Angola</option><option value="AI">Anguilla</option><option value="AQ">Antarctica</option><option value="AG">Antigua and Barbuda</option><option value="AR">Argentina</option><option value="AM">Armenia</option><option value="AW">Aruba</option><option value="AU">Australia</option><option value="AT">Austria</option><option value="AZ">Azerbaijan</option><option value="BS">Bahamas</option><option value="BH">Bahrain</option><option value="BD">Bangladesh</option><option value="BB">Barbados</option><option value="BY">Belarus</option><option value="BE">Belgium</option><option value="BZ">Belize</option><option value="BJ">Benin</option><option value="BM">Bermuda</option><option value="BT">Bhutan</option><option value="BO">Bolivia (Plurinational State of)</option><option value="BQ">Bonaire, Saint Eustatius and Saba</option><option value="BA">Bosnia and Herzegovina</option><option value="BW">Botswana</option><option value="BV">Bouvet Island</option><option value="BR">Brazil</option><option value="IO">British Indian Ocean Territory</option><option value="BN">Brunei Darussalam</option><option value="BG">Bulgaria</option><option value="BF">Burkina Faso</option><option value="BI">Burundi</option><option value="CV">Cabo Verde</option><option value="KH">Cambodia</option><option value="CM">Cameroon</option><option value="CA">Canada</option><option value="KY">Cayman Islands</option><option value="CF">Central African Republic</option><option value="TD">Chad</option><option value="CL">Chile</option><option value="CN">China</option><option value="CX">Christmas Island</option><option value="CC">Cocos (Keeling) Islands</option><option value="CO">Colombia</option><option value="KM">Comoros</option><option value="CG">Congo</option><option value="CD">Congo (Democratic Republic of the)</option><option value="CK">Cook Islands</option><option value="CR">Costa Rica</option><option value="CI">Côte d&#039;Ivoire</option><option value="HR">Croatia</option><option value="CU">Cuba</option><option value="CW">Curaçao</option><option value="CY">Cyprus</option><option value="CZ">Czech Republic</option><option value="DK">Denmark</option><option value="DJ">Djibouti</option><option value="DM">Dominica</option><option value="DO">Dominican Republic</option><option value="EC">Ecuador</option><option value="EG">Egypt</option><option value="SV">El Salvador</option><option value="GQ">Equatorial Guinea</option><option value="ER">Eritrea</option><option value="EE">Estonia</option><option value="ET">Ethiopia</option><option value="FK">Falkland Islands (Malvinas)</option><option value="FO">Faroe Islands</option><option value="FJ">Fiji</option><option value="FI">Finland</option><option value="FR">France</option><option value="GF">French Guiana</option><option value="PF">French Polynesia</option><option value="TF">French Southern Territories</option><option value="GA">Gabon</option><option value="GM">Gambia</option><option value="GE">Georgia</option><option value="DE">Germany</option><option value="GH">Ghana</option><option value="GI">Gibraltar</option><option value="GR">Greece</option><option value="GL">Greenland</option><option value="GD">Grenada</option><option value="GP">Guadeloupe</option><option value="GU">Guam</option><option value="GT">Guatemala</option><option value="GG">Guernsey</option><option value="GN">Guinea</option><option value="GW">Guinea-Bissau</option><option value="GY">Guyana</option><option value="HT">Haiti</option><option value="HM">Heard Island and McDonald Islands</option><option value="HN">Honduras</option><option value="HK">Hong Kong</option><option value="HU">Hungary</option><option value="IS">Iceland</option><option value="IN">India</option><option value="ID">Indonesia</option><option value="IR">Iran (Islamic Republic of)</option><option value="IQ">Iraq</option><option value="IE">Ireland (Republic of)</option><option value="IM">Isle of Man</option><option value="IL">Israel</option><option value="IT">Italy</option><option value="JM">Jamaica</option><option value="JP">Japan</option><option value="JE">Jersey</option><option value="JO">Jordan</option><option value="KZ">Kazakhstan</option><option value="KE">Kenya</option><option value="KI">Kiribati</option><option value="KP">Korea (Democratic People&#039;s Republic of)</option><option value="KR">Korea (Republic of)</option><option value="XK">Kosovo</option><option value="KW">Kuwait</option><option value="KG">Kyrgyzstan</option><option value="LA">Lao People&#039;s Democratic Republic</option><option value="LV">Latvia</option><option value="LB">Lebanon</option><option value="LS">Lesotho</option><option value="LR">Liberia</option><option value="LY">Libya</option><option value="LI">Liechtenstein</option><option value="LT">Lithuania</option><option value="LU">Luxembourg</option><option value="MO">Macao</option><option value="MK">North Macedonia (Republic of)</option><option value="MG">Madagascar</option><option value="MW">Malawi</option><option value="MY">Malaysia</option><option value="MV">Maldives</option><option value="ML">Mali</option><option value="MT">Malta</option><option value="MH">Marshall Islands</option><option value="MQ">Martinique</option><option value="MR">Mauritania</option><option value="MU">Mauritius</option><option value="YT">Mayotte</option><option value="MX">Mexico</option><option value="FM">Micronesia (Federated States of)</option><option value="MD">Moldova (Republic of)</option><option value="MC">Monaco</option><option value="MN">Mongolia</option><option value="ME">Montenegro</option><option value="MS">Montserrat</option><option value="MA">Morocco</option><option value="MZ">Mozambique</option><option value="MM">Myanmar</option><option value="NA">Namibia</option><option value="NR">Nauru</option><option value="NP">Nepal</option><option value="NL">Netherlands</option><option value="NC">New Caledonia</option><option value="NZ">New Zealand</option><option value="NI">Nicaragua</option><option value="NE">Niger</option><option value="NG">Nigeria</option><option value="NU">Niue</option><option value="NF">Norfolk Island</option><option value="MP">Northern Mariana Islands</option><option value="NO">Norway</option><option value="OM">Oman</option><option value="PK">Pakistan</option><option value="PW">Palau</option><option value="PS">Palestine (State of)</option><option value="PA">Panama</option><option value="PG">Papua New Guinea</option><option value="PY">Paraguay</option><option value="PE">Peru</option><option value="PH">Philippines</option><option value="PN">Pitcairn</option><option value="PL">Poland</option><option value="PT">Portugal</option><option value="PR">Puerto Rico</option><option value="QA">Qatar</option><option value="RE">Réunion</option><option value="RO">Romania</option><option value="RU">Russian Federation</option><option value="RW">Rwanda</option><option value="BL">Saint Barthélemy</option><option value="SH">Saint Helena, Ascension and Tristan da Cunha</option><option value="KN">Saint Kitts and Nevis</option><option value="LC">Saint Lucia</option><option value="MF">Saint Martin (French part)</option><option value="PM">Saint Pierre and Miquelon</option><option value="VC">Saint Vincent and the Grenadines</option><option value="WS">Samoa</option><option value="SM">San Marino</option><option value="ST">Sao Tome and Principe</option><option value="SA">Saudi Arabia</option><option value="SN">Senegal</option><option value="RS">Serbia</option><option value="SC">Seychelles</option><option value="SL">Sierra Leone</option><option value="SG">Singapore</option><option value="SX">Sint Maarten (Dutch part)</option><option value="SK">Slovakia</option><option value="SI">Slovenia</option><option value="SB">Solomon Islands</option><option value="SO">Somalia</option><option value="ZA">South Africa</option><option value="GS">South Georgia and the South Sandwich Islands</option><option value="SS">South Sudan</option><option value="ES">Spain</option><option value="LK">Sri Lanka</option><option value="SD">Sudan</option><option value="SR">Suriname</option><option value="SJ">Svalbard and Jan Mayen</option><option value="SZ">Eswatini (Kingdom of)</option><option value="SE">Sweden</option><option value="CH">Switzerland</option><option value="SY">Syrian Arab Republic</option><option value="TW">Taiwan, Republic of China</option><option value="TJ">Tajikistan</option><option value="TZ">Tanzania (United Republic of)</option><option value="TH">Thailand</option><option value="TL">Timor-Leste</option><option value="TG">Togo</option><option value="TK">Tokelau</option><option value="TO">Tonga</option><option value="TT">Trinidad and Tobago</option><option value="TN">Tunisia</option><option value="TR">Türkiye</option><option value="TM">Turkmenistan</option><option value="TC">Turks and Caicos Islands</option><option value="TV">Tuvalu</option><option value="UG">Uganda</option><option value="UA">Ukraine</option><option value="AE">United Arab Emirates</option><option value="GB">United Kingdom of Great Britain and Northern Ireland</option><option value="US">United States of America</option><option value="UM">United States Minor Outlying Islands</option><option value="UY">Uruguay</option><option value="UZ">Uzbekistan</option><option value="VU">Vanuatu</option><option value="VA">Vatican City State</option><option value="VE">Venezuela (Bolivarian Republic of)</option><option value="VN">Vietnam</option><option value="VG">Virgin Islands (British)</option><option value="VI">Virgin Islands (U.S.)</option><option value="WF">Wallis and Futuna</option><option value="EH">Western Sahara</option><option value="YE">Yemen</option><option value="ZM">Zambia</option><option value="ZW">Zimbabwe</option></select><label for="wpforms-field-option-31-country_default" class="sub-label">Default Value</label></div></div></div><div class="wpforms-field-option-row wpforms-field-option-row-css " id="wpforms-field-option-row-31-css" data-field-id="31" ><label for="wpforms-field-option-31-css">CSS Classes<i class="fa fa-question-circle-o wpforms-help-tooltip" title="Enter CSS class names for the form field container. Class names should be separated with spaces."></i><a href="#" class="toggle-layout-selector-display toggle-unfoldable-cont"><i class="fa fa-th-large"></i><span>Show Layouts</span></a></label><input type="text" class="" id="wpforms-field-option-31-css" name="fields[31][css]" value="" placeholder="" ></div><div class="wpforms-field-option-row wpforms-field-option-row-label_hide " id="wpforms-field-option-row-31-label_hide" data-field-id="31" ><span class="wpforms-toggle-control " >
            
            <input type="checkbox" id="wpforms-field-option-31-label_hide" name="fields[31][label_hide]" class="" value="1"  >
            <label class="wpforms-toggle-control-icon" for="wpforms-field-option-31-label_hide"></label>
            <label for="wpforms-field-option-31-label_hide" class="wpforms-toggle-control-label">Hide Label</label><i class="fa fa-question-circle-o wpforms-help-tooltip" title="Check this option to hide the form field label."></i>
          </span></div><div class="wpforms-field-option-row wpforms-field-option-row-sublabel_hide " id="wpforms-field-option-row-31-sublabel_hide" data-field-id="31" ><span class="wpforms-toggle-control " >
            
            <input type="checkbox" id="wpforms-field-option-31-sublabel_hide" name="fields[31][sublabel_hide]" class="" value="1"  >
            <label class="wpforms-toggle-control-icon" for="wpforms-field-option-31-sublabel_hide"></label>
            <label for="wpforms-field-option-31-sublabel_hide" class="wpforms-toggle-control-label">Hide Sublabels</label><i class="fa fa-question-circle-o wpforms-help-tooltip" title="Check this option to hide the form field sublabel."></i>
          </span></div></div></div>
          <div class="wpforms-conditional-fields wpforms-field-option-group wpforms-field-option-group-conditionals wpforms-hide"
            id="wpforms-field-option-conditionals-31">
      
            <a href="#" class="wpforms-field-option-group-toggle">
              Smart Logic			</a>
      
            <div class="wpforms-field-option-group-inner">
              <div class="wpforms-conditional-block wpforms-conditional-block-field" data-type="field"><div class="wpforms-field-option-row wpforms-field-option-row-conditional_logic wpforms-conditionals-enable-toggle" id="wpforms-field-option-row-31-conditional_logic" data-field-id="31" ><span class="wpforms-toggle-control " >
            
            <input type="checkbox" id="wpforms-field-option-31-conditional_logic" name="fields[31][conditional_logic]" class="" value="1"   data-name=\'fields[31]\' data-actions=\'{"show":"Show","hide":"Hide"}\' data-action-desc=\'this field if\'>
            <label class="wpforms-toggle-control-icon" for="wpforms-field-option-31-conditional_logic"></label>
            <label for="wpforms-field-option-31-conditional_logic" class="wpforms-toggle-control-label">Enable Conditional Logic</label><i class="fa fa-question-circle-o wpforms-help-tooltip" title="ttt" "href=""></i>
          </span></div></div>			</div>
      
          </div>
          </div>',
        ],
      ];
      
      // For demonstration, return a JSON response
      return response()->json(
          $html
      );
  }
}