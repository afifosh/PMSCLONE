<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class OnlyOfficeRequest extends FormRequest
{




    public static $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/pdf',
        'application/msword',
        'application/vnd.ms-excel',
        // add more valid MIME types as needed
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        $allowedMimeTypes = [
            'git',
            'image/png',
            'image/gif',
            'application/pdf',
            'application/msword',
            'application/vnd.ms-excel',
            // add more valid MIME types as needed
        ];


        return [
            'secret' => 'required|string|max:255',
            'doc_server_url' => 'required|url',
            'doc_server_api_url' => 'required|url',
          //  'supported_filesss' =>  'required|string|validate_allowed_mime_types',
        //   'supported_files' => [
        //     'required',
        //     function ($attribute, $value, $fail) use ($allowedMimeTypes) {
        //         $mimetypes = explode("\n", $value);
        //         $invalidMimetypes = [];
        //         foreach ($mimetypes as $mimetype) {
        //             $mimetype = trim($mimetype);
        //             if (!in_array($mimetype, $allowedMimeTypes)) {
        //                 $invalidMimetypes[] = $mimetype;
        //             }
        //         }
        //         if (count($invalidMimetypes) > 0) {
        //             $invalidMimetypesString = implode(' ', $invalidMimetypes);
        //             $fail("The following MIME type(s) are not allowed: $invalidMimetypesString.");
        //         }
        //     },
        //    // Rule::in($allowedMimeTypes),
        // ],
            'allowed_file_size' => [
                'required',
                'numeric',
                'between:1,200',
            ],

        ];
    }




    /**
     * Validate that each MIME type in the given array is allowed.
     *
     * @param string $attribute
     * @param array $values
     * @return bool
     */
    public function validatesssAllowedMimeTypesInput(string $attribute, $value, $parameters, $validator)
    {
        // Split the input value into an array of MIME types
        $allowedMimeTypes = explode("\n", $value);

        // Trim any whitespace from each MIME type
        $allowedMimeTypes = array_map('trim', $allowedMimeTypes);

        // Remove any empty MIME types
        $allowedMimeTypes = array_filter($allowedMimeTypes);

        // Validate each MIME type to ensure it's in the correct format
        foreach ($allowedMimeTypes as $mimeType) {
            if (!preg_match('/^[a-z]+\/[a-z0-9\-\.\+]+$/', $mimeType)) {
                return false;
            }
        }

        // If all MIME types are valid, set them as the allowed MIME types in the validator
        $validator->setAllowedMimeTypes($allowedMimeTypes);

        return true;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'allowed_file_size.size' => 'The allowed file size may not be greater than :size MB.',
       // 'supported_files' => "The following MIME type(s) are not allowed: :invalid_mimetypes.",
    //    'supported_files.required' => 'The supported files field is required.',
    //     'supported_files.invalid_mimetypes' => 'The following MIME type(s) are not allowed: :invalid_mimetypes.',
    'supported_files.required' => 'The supported files field is required.',
         //   'supported_files.in' => 'The supported files field contains invalid MIME type(s).',
            'supported_files.not_allowed' => 'Theee following MIME type(s) are not allowed: :invalid_mimetypes.',

];

    }

}


