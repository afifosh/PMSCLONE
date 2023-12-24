<?php

namespace App\Http\Controllers\Admin\Applications\Form;

use App\DataTables\Admin\Applications\Form\FormsDataTable;
// use App\Facades\UtilityFacades;
use App\Mail\FormSubmitEmail;
use App\Mail\Thanksmail;
use App\Models\AssignFormsRoles;
use App\Models\AssignFormsUsers;
use App\Models\Form;
use App\Models\FormComments;
use App\Models\FormCommentsReply;
use App\Models\FormIntegrationSetting;
use App\Models\formRule;
use App\Models\FormTemplate;
use App\Models\FormValue;
use App\Models\NotificationsSetting;
use App\Models\User;
use App\Models\UserForm;
use App\Notifications\CreateForm;
use App\Notifications\NewSurveyDetails;
use App\Rules\CommaSeparatedEmails;
use Carbon\Carbon;
use Exception;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Stripe\Charge;
use Stripe\Stripe as StripeStripe;
use Illuminate\Support\Facades\Mail;
use Spatie\MailTemplates\Models\MailTemplate;
use App\Http\Controllers\Controller;

class FormController extends Controller
{
    public function index(FormsDataTable $dataTable)
    {
        if (auth()->user()->can('read form')) {
            if (auth()->user()->forms_grid_view == 1) {
                return redirect()->route('admin.applications.settings.grid.form.view', 'view');
            }
            return $dataTable->render('admin.pages.applications.form.index');
            // view('admin.pages.applications.form.index');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function addForm()
    {
        $formTemplates = FormTemplate::where('json', '!=', null)->where('status', 1)->get();
        return view('admin.pages.applications.form.add', compact('formTemplates'));
    }

    public function create()
    {
        if (auth()->user()->can('create form')) {
            // $users = User::where('id', '!=', 1)->pluck('name', 'id');
            // $roles = Role::where('name', '!=', 'Super Admin')
            //     ->orwhere('name', Auth::user()->type)
            //     ->pluck('name', 'id');
            return view('admin.pages.applications.form.create');
        } else {
            return response()->json(['failed' => __('Permission denied.')], 401);
        }
    }

    public function useFormtemplate($id)
    {
        $formtemplate = FormTemplate::find($id);
        $form = Form::create([
            'title'     => $formtemplate->title,
            'json'      => $formtemplate->json,
        ]);
        return redirect()->route('admin.applications.settings.forms.edit', $form->id)->with('success', __('Form created successfully.'));
    }


    public function store(Request $request)
    {
        if (auth()->user()->can('create form')) {
            request()->validate([
                'title'     => 'required|max:191',
                'form_logo' => 'required|mimes:png,jpg,svg,jpeg'
            ]);

            if (isset($request->set_end_date) && $request->set_end_date == 'on') {
                request()->validate([
                    'set_end_date' => 'required',
                    'set_end_date_time' => 'required'
                ]);
            }
            $ccemails = implode(',', $request->ccemail);
            $bccemails = implode(',', $request->bccemail);
            if ($ccemails) {
                request()->validate([
                    'ccemail' => ['nullable', new CommaSeparatedEmails],
                ]);
            }
            if ($bccemails) {
                request()->validate([
                    'bccemail' => ['nullable', new CommaSeparatedEmails],
                ]);
            }
            request()->validate([
                'email' => ['nullable', new CommaSeparatedEmails],
            ]);

            $filename = '';
            if (request()->file('form_logo')) {
                $allowedfileExtension = ['jpeg', 'jpg', 'png'];
                $file = $request->file('form_logo');
                $extension = $file->getClientOriginalExtension();
                $check = in_array($extension, $allowedfileExtension);
                if ($check) {
                    $filename = $file->store('form-logo');
                } else {
                    return redirect()->route('admin.applications.settings.forms.index')->with('failed', __('File type not valid.'));
                }
            }
            if (isset($request->email) and !empty($request->email)) {
                $emails = implode(',', $request->email);
            }
            if (isset($request->ccemail) and !empty($request->ccemail)) {
                $ccemails = implode(',', $request->ccemail);
            }
            if (isset($request->bccemail) and !empty($request->bccemail)) {
                $bccemails = implode(',', $request->bccemail);
            }
            if (isset($request->set_end_date) && $request->set_end_date == 1) {
                $setEndDate = 1;
            } else {
                $setEndDate = 0;
            }
            if (isset($request->set_end_date_time)) {
                $setEndDateTime = Carbon::parse($request->set_end_date_time)->toDateTimeString();
            } else {
                $setEndDateTime = null;
            }

            $form = new Form();
            $form->title                = $request->title;
            $form->logo                 = $filename;
            $form->description          = $request->form_description;
            $form->email                = $emails;
            $form->bccemail             = $bccemails;
            $form->ccemail              = $bccemails;
            $form->allow_comments       = ($request->allow_comments == 'on') ? '1' : '0';
            $form->allow_share_section  = ($request->allow_share_section == 'on') ? '1' : '0';
            $form->json                 = '';
            $form->success_msg          = $request->success_msg;
            $form->thanks_msg           = $request->thanks_msg;
            $form->set_end_date         = $setEndDate;
            $form->set_end_date_time    = $setEndDateTime;
            $form->created_by           = Auth::user()->id;
            $form->assign_type          = $request->assign_type;
            $form->save();
            // if ($request->assign_type == 'role') {
            //     $form->assignRole($request->roles);
            // }
            // if ($request->assign_type == 'user') {
            //     $form->assignUser($request->users);
            // }
            // $form->assignFormRoles($request->roles);

            // $userSchema = User::where('type', '=', 'Admin')->first();
            // $notify = NotificationsSetting::where('title', 'From Create')->first();
            // if (isset($notify)) {
            //     if ($notify->notify == 1) {
            //         if (UtilityFacades::getsettings('email_setting_enable') == 'on') {
            //             if (isset($notify)) {
            //                 if ($notify &&  $notify->notify == '1') {
            //                     $userSchema->notify(new CreateForm($form));
            //                 }
            //             }
            //         }
            //     }
            // }
            return $this->sendRes(__('Form created successfully.'), ['event' => 'redirect', 'url' => route('admin.applications.settings.forms.index')]);
        } else {
            return $this->sendError(__('Permission denied.'));
        }
    }

    public function edit($id)
    {
        $usr                = auth()->user();
        $userRole          = $usr->roles->first()->id;
        $formallowededit    = UserForm::where('role_id', $userRole)->where('form_id', $id)->count();
        if (auth()->user()->can('edit form') && $usr->isSuperAdmin()) {
            $form           = Form::find($id);
            $next           = Form::where('id', '>', $form->id)->first();
            $previous       = Form::where('id', '<', $form->id)->orderBy('id', 'desc')->first();
            $roles          = Role::where('name', '!=', 'Super Admin')->pluck('name', 'id');
            $formRole       = $form->assignedroles->pluck('id')->toArray();
            $getFormRole      = Role::pluck('name', 'id');
            $formUser       =  $form->assignedusers->pluck('id')->toArray();
            $GetformUser    = [];// User::where('id', '!=', 1)->pluck('name', 'id');

            return view('admin.pages.applications.form.edit', compact('form', 'roles', 'GetformUser', 'formUser', 'formRole', 'getFormRole', 'next', 'previous'));
        } else {
            if (auth()->user()->can('edit form') && $formallowededit > 0) {
                $form       = Form::find($id);
                $next       = Form::where('id', '>', $form->id)->first();
                $previous   = Form::where('id', '<', $form->id)->orderBy('id', 'desc')->first();
                $roles      = Role::pluck('name', 'id');
                $formRole   = $form->assignedroles->pluck('id')->toArray();
                $getFormRole  = Role::pluck('name', 'id');
                $formUser   =  $form->assignedusers->pluck('id')->toArray();
                $GetformUser  = [];// User::where('id', '!=', 1)->pluck('name', 'id');

                return view('admin.pages.applications.form.edit', compact('form', 'getFormRole', 'GetformUser', 'formUser', 'formRole', 'next', 'previous'));
            } else {
                return redirect()->back()->with('failed', __('Permission denied.'));
            }
        }
    }

    public function update(Request $request, Form $form)
    {
        if (auth()->user()->can('edit form')) {
            request()->validate([
                'title'       => 'required|max:191',
            ]);

            $ccemails = implode(',', $request->ccemail);
            $bccemails = implode(',', $request->bccemail);
            if ($ccemails) {
                $request->validate([
                    'ccemail' => ['nullable', new CommaSeparatedEmails],
                ]);
            }
            if ($bccemails) {
                $request->validate([
                    'bccemail' => ['nullable', new CommaSeparatedEmails],
                ]);
            }
            request()->validate([
                'email' => ['nullable', new CommaSeparatedEmails],
            ]);

            $filename = $form->logo;
            $emails = $form->logo;
            if (request()->file('form_logo')) {
                $allowedfileExtension   = ['jpeg', 'jpg', 'png'];
                $file                   = $request->file('form_logo');
                $extension              = $file->getClientOriginalExtension();
                $check                  = in_array($extension, $allowedfileExtension);
                if ($check) {
                    $filename = $file->store('form-logo');
                    $form->logo                 = $filename;
                } else {
                    return redirect()->route('admin.applications.settings.forms.index')->with('failed', __('File type not valid.'));
                }
            }
            if (isset($request->email) and !empty($request->email)) {
                $emails = implode(',', $request->email);
            }
            if (isset($request->ccemail) and !empty($request->ccemail)) {
                $ccemails = implode(',', $request->ccemail);
            }
            if (isset($request->bccemail) and !empty($request->bccemail)) {
                $bccemails = implode(',', $request->bccemail);
            }

            if ($request->set_end_date == 'on') {
                $setEndDate = 1;
            } else {
                $setEndDate = 0;
            }
            if (isset($request->set_end_date_time)) {
                $setEndDateTime = Carbon::parse($request->set_end_date_time)->toDateTimeString();;
            } else {
                $setEndDateTime = null;
            }

            $form->title                = $request->title;
            $form->success_msg          = $request->success_msg;
            $form->thanks_msg           = $request->thanks_msg;
            $form->description          = $request->form_description;
            $form->email                = $emails;
            $form->ccemail              = $ccemails;
            $form->bccemail             = $bccemails;
            $form->allow_comments       = ($request->allow_comments == 'on') ? '1' : '0';
            $form->allow_share_section  = ($request->allow_share_section == 'on') ? '1' : '0';
            $form->set_end_date         = $setEndDate;
            $form->set_end_date_time    = $setEndDateTime;
            $form->created_by           = Auth::user()->id;
            $form->assign_type          = $request->assign_type;
            $form->save();
            if ($request->assign_type == 'role') {
                $id = $form->id;
                AssignFormsUsers::where('form_id', $id)->delete();
                $form->assignRole($request->roles);
            }
            if ($request->assign_type == 'user') {
                $id = $form->id;
                AssignFormsRoles::where('form_id', $id)->delete();
                $form->assignUser($request->users);
            }
            if ($request->assign_type == 'public') {
                $id = $form->id;
                AssignFormsRoles::where('form_id', $id)->delete();
                AssignFormsUsers::where('form_id', $id)->delete();
            }
            $form->assignFormRoles($request->roles);
            return redirect()->route('admin.applications.settings.forms.index')->with('success', __('Form updated successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function destroy(Form $form)
    {
        if (auth()->user()->can('delete form')) {
            $id             = $form->id;
            $comments       = FormComments::where('form_id', $id)->get();
            $commentsReply = FormCommentsReply::where('form_id', $id)->get();
            AssignFormsRoles::where('form_id', $id)->delete();
            AssignFormsUsers::where('form_id', $id)->delete();
            foreach ($comments as $allcomments) {
                $commentsids = $allcomments->id;
                $commentsall = FormComments::find($commentsids);
                if ($commentsall) {
                    $commentsall->delete();
                }
            }
            foreach ($commentsReply as $commentsReplyAll) {
                $commentsReplyIds = $commentsReplyAll->id;
                $reply =  FormCommentsReply::find($commentsReplyIds);
                if ($reply) {
                    $reply->delete();
                }
            }
            $form->delete();

            return $this->sendRes(__('Form deleted successfully'), ['event' => 'table_reload', 'table_id' => 'forms-table']);
        } else {
            return $this->sendError(__('Permission denied.'));
        }
    }

    public function gridView($slug = '')
    {
        $usr                  = auth()->user();
        $usr->forms_grid_view = ($slug) ? 1 : 0;
        $usr->save();
        if ($usr->forms_grid_view == 0) {
            return redirect()->route('admin.applications.settings.forms.index');
        }

        $roleId    = $usr->roles->first()->id;
        $userId    = $usr->id;
        if ($usr->type == 'Admin') {
            $forms = Form::all();
        } else {
            $forms = Form::where(function ($query) use ($roleId, $userId) {
                $query->whereIn('id', function ($query1) use ($roleId) {
                    $query1->select('form_id')->from('assign_forms_roles')->where('role_id', $roleId);
                })->OrWhereIn('id', function ($query1) use ($userId) {
                    $query1->select('form_id')->from('assign_forms_users')->where('userId', $userId);
                });
            })->get();
        }

        return view('admin.pages.applications.form.grid-view', compact('forms'));
    }

    public function design($id)
    {
        if (auth()->user()->can('design form')) {
            $form = Form::find($id);
            if ($form) {
                return view('admin.pages.applications.form.design', compact('form'));
            } else {
                return redirect()->back()->with('failed', __('Form not found.'));
            }
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }


    public function designUpdate(Request $request, $id)
    {
        if (auth()->user()->can('design form')) {
            $form           = Form::find($id);
            if ($form) {
                $form->json = $request->json;
                $fieldName = json_decode($request->json);
                $arr        = [];
                foreach ($fieldName[0] as $k => $fields) {
                    if ($fields->type == "header" || $fields->type == "paragraph") {
                        $arr[$k] = $fields->type;
                    } else {
                        $arr[$k] = $fields->name;
                    }
                }
                $form->save();
                return redirect()->route('admin.applications.settings.forms.index')->with('success', __('Form updated successfully.'));
            } else {
                return redirect()->back()->with('failed', __('Form not found.'));
            }
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function fill($id)
    {
        if (auth()->user()->can('fill form')) {
            $form       = Form::find($id);
            if ($form) {
                $array = $form->getFormArray();
                return view('admin.pages.applications.form.fill', compact('form', 'array'));
            } else {
                return redirect()->back()->with('failed', __('Form not found'));
            }
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function publicFill($id)
    {
        $hashids    = new Hashids('', 20);
        $id         = $hashids->decodeHex($id);
        if ($id) {
            $form       = Form::find($id);
            $todayDate = Carbon::now()->toDateTimeString();
            if ($form) {
                if ($form->set_end_date != '0') {
                    if ($form->set_end_date_time && $form->set_end_date_time < $todayDate) {
                        abort('404');
                    }
                }
                $array = $form->getFormArray();
                return view('admin.pages.applications.form.public-fill', compact('form', 'array'));
            } else {
                return redirect()->back()->with('failed', __('Form not found.'));
            }
        } else {
            abort(404);
        }
    }

    public function qrCode($id)
    {
        $hashids  = new Hashids('', 20);
        $id       = $hashids->decodeHex($id);
        $form     = Form::find($id);
        $view     =  view('admin.pages.applications.form.public-fill-qr', compact('form'));
        return ['html' => $view->render()];
    }

    public function fillStore(Request $request, $id)
    {
        $form = Form::find($id);
        // if (false && UtilityFacades::getsettings('captcha_enable') == 'on') {
        //     if (UtilityFacades::getsettings('captcha') == 'hcaptcha') {
        //         if (empty($_POST['h-captcha-response'])) {
        //             if (isset($request->ajax)) {
        //                 return response()->json(['is_success' => false, 'message' => __('Please check hcaptcha.')]);
        //             } else {
        //                 return back()->with('failed', __('Please check hcaptcha.'));
        //             }
        //         }
        //     }
        //     if (UtilityFacades::getsettings('captcha') == 'recaptcha') {
        //         if (empty($_POST['g-recaptcha-response'])) {
        //             if (isset($request->ajax)) {
        //                 return response()->json(['is_success' => false, 'message' => __('Please check recaptcha.')]);
        //             } else {
        //                 return back()->with('failed', __('Please check recaptcha.'));
        //             }
        //         }
        //     }
        // }
        if ($form) {
            $clientEmails = [];
            if ($request->form_value_id) {
                $formValue = FormValue::find($request->form_value_id);
                $array = json_decode($formValue->json);
            } else {
                $array = $form->getFormArray();
            }
            foreach ($array as  &$rows) {
                foreach ($rows as &$row) {
                    if ($row->type == 'checkbox-group') {
                        foreach ($row->values as &$checkboxvalue) {
                            if (is_array($request->{$row->name}) && in_array($checkboxvalue->value, $request->{$row->name})) {
                                $checkboxvalue->selected = 1;
                            } else {
                                if (isset($checkboxvalue->selected)) {
                                    unset($checkboxvalue->selected);
                                }
                            }
                        }
                    } elseif ($row->type == 'file') {
                        if ($row->subtype == "fineuploader") {
                            $fileSize = number_format($row->max_file_size_mb / 1073742848, 2);
                            $fileLimit = $row->max_file_size_mb / 1024;
                            if ($fileSize < $fileLimit) {
                                $values = [];
                                $value = explode(',', $request->input($row->name));
                                foreach ($value as $file) {
                                    $values[] = $file;
                                }
                                $row->value = $values;
                            } else {
                                return response()->json(['is_success' => false, 'message' => __("Please upload maximum $row->max_file_size_mb MB file size.")], 200);
                            }
                        } else {
                            if ($row->file_extention == 'pdf') {
                                $allowedFileExtension = ['pdf', 'pdfa', 'fdf', 'xdp', 'xfa', 'pdx', 'pdp', 'pdfxml', 'pdxox'];
                            } else if ($row->file_extention == 'image') {
                                $allowedFileExtension = ['jpeg', 'jpg', 'png'];
                            } else if ($row->file_extention == 'excel') {
                                $allowedFileExtension = ['xlsx', 'csv', 'xlsm', 'xltx', 'xlsb', 'xltm', 'xlw'];
                            }
                            $requiredextention = implode(',', $allowedFileExtension);
                            $fileSize = number_format($row->max_file_size_mb / 1073742848, 2);
                            $fileLimit = $row->max_file_size_mb / 1024;
                            if ($fileSize < $fileLimit) {
                                if ($row->multiple) {
                                    if ($request->hasFile($row->name)) {
                                        $values = [];
                                        $files = $request->file($row->name);
                                        foreach ($files as $file) {
                                            $extension = $file->getClientOriginalExtension();
                                            $check = in_array($extension, $allowedFileExtension);
                                            if ($check) {
                                                if ($extension == 'csv') {
                                                    $name = \Str::random(40) . '.' . $extension;
                                                    $file->move(storage_path() . '/app/form-values/' . $form->id, $name);
                                                    $values[] = 'form-values/' . $form->id . '/' . $name;
                                                } else {
                                                    $path = Storage::path("form-values/$form->id");
                                                    $fileName = $file->store('form-values/' . $form->id);
                                                    if (!file_exists($path)) {
                                                        mkdir($path, 0777, true);
                                                        chmod($path, 0777);
                                                    }
                                                    if (!file_exists(Storage::path($fileName))) {
                                                        mkdir(Storage::path($fileName), 0777, true);
                                                        chmod(Storage::path($fileName), 0777);
                                                    }
                                                    $values[] = $fileName;
                                                }
                                            } else {
                                                if (isset($request->ajax)) {
                                                    return response()->json(['is_success' => false, 'message' => __("Invalid file type, Please upload $requiredextention files")], 200);
                                                } else {
                                                    return redirect()->back()->with('failed', __("Invalid file type, please upload $requiredextention files."));
                                                }
                                            }
                                        }
                                        $row->value = $values;
                                    }
                                } else {
                                    if ($request->hasFile($row->name)) {
                                        $values = '';
                                        $file = $request->file($row->name);
                                        $extension = $file->getClientOriginalExtension();
                                        $check = in_array($extension, $allowedFileExtension);
                                        if ($check) {
                                            if ($extension == 'csv') {
                                                $name = \Str::random(40) . '.' . $extension;
                                                $file->move(storage_path() . '/app/form-values/' . $form->id, $name);
                                                $values = 'form-values/' . $form->id . '/' . $name;
                                                chmod("$values", 0777);
                                            } else {
                                                $path = Storage::path("form-values/$form->id");
                                                $fileName = $file->store('form-values/' . $form->id);
                                                if (!file_exists($path)) {
                                                    mkdir($path, 0777, true);
                                                    chmod($path, 0777);
                                                }
                                                if (!file_exists(Storage::path($fileName))) {
                                                    mkdir(Storage::path($fileName), 0777, true);
                                                    chmod(Storage::path($fileName), 0777);
                                                }
                                                $values = $fileName;
                                            }
                                        } else {
                                            if (isset($request->ajax)) {
                                                return response()->json(['is_success' => false, 'message' => __("Invalid file type, Please upload $requiredextention files")], 200);
                                            } else {
                                                return redirect()->back()->with('failed', __("Invalid file type, please upload $requiredextention files."));
                                            }
                                        }
                                        $row->value = $values;
                                    }
                                }
                            } else {
                                return response()->json(['is_success' => false, 'message' => __("Please upload maximum $row->max_file_size_mb MB file size.")], 200);
                            }
                        }
                    } elseif ($row->type == 'radio-group') {
                        foreach ($row->values as &$radiovalue) {
                            if ($radiovalue->value == $request->{$row->name}) {
                                $radiovalue->selected = 1;
                            } else {
                                if (isset($radiovalue->selected)) {
                                    unset($radiovalue->selected);
                                }
                            }
                        }
                    } elseif ($row->type == 'autocomplete') {
                        if (isset($row->multiple)) {
                            foreach ($row->values as &$autocompletevalue) {
                                if (is_array($request->{$row->name}) && in_array($autocompletevalue->value, $request->{$row->name})) {
                                    $autocompletevalue->selected = 1;
                                } else {
                                    if (isset($autocompletevalue->selected)) {
                                        unset($autocompletevalue->selected);
                                    }
                                }
                            }
                        } else {
                            foreach ($row->values as &$autocompletevalue) {
                                if ($autocompletevalue->value == $request->autocomplete) {
                                    $autocompletevalue->selected = 1;
                                } else {
                                    if (isset($autocompletevalue->selected)) {
                                        unset($autocompletevalue->selected);
                                    }
                                    $row->value = $request->autocomplete;
                                }
                            }
                        }
                    } elseif ($row->type == 'select') {
                        if ($row->multiple) {
                            foreach ($row->values as &$selectvalue) {
                                if (is_array($request->{$row->name}) && in_array($selectvalue->value, $request->{$row->name})) {
                                    $selectvalue->selected = 1;
                                } else {
                                    if (isset($selectvalue->selected)) {
                                        unset($selectvalue->selected);
                                    }
                                }
                            }
                        } else {
                            foreach ($row->values as &$selectvalue) {
                                if ($selectvalue->value == $request->{$row->name}) {
                                    $selectvalue->selected = 1;
                                } else {
                                    if (isset($selectvalue->selected)) {
                                        unset($selectvalue->selected);
                                    }
                                }
                            }
                        }
                    } elseif ($row->type == 'date') {
                        $row->value = $request->{$row->name};
                    } elseif ($row->type == 'number') {
                        $row->value = $request->{$row->name};
                    } elseif ($row->type == 'textarea') {
                        $row->value = $request->{$row->name};
                    } elseif ($row->type == 'text') {
                        $clientEmail = '';
                        if ($row->subtype == 'email') {
                            if (isset($row->is_client_email) && $row->is_client_email) {

                                $clientEmails[] = $request->{$row->name};
                            }
                        }
                        $row->value = $request->{$row->name};
                    } elseif ($row->type == 'starRating') {
                        $row->value = $request->{$row->name};
                    } elseif ($row->type == 'SignaturePad') {
                        if (property_exists($row, 'value')) {
                            $filepath = $row->value;
                            if ($request->{$row->name} == null) {
                                $url = $row->value;
                            } else {
                                if (file_exists(Storage::path($request->{$row->name}))) {
                                    $url = $request->{$row->name};
                                    $path = $url;
                                    $imgUrl = Storage::path($url);
                                    $filePath = $imgUrl;
                                } else {
                                    $imgUrl = $request->{$row->name};
                                    $path = "form-values/$form->id/" . rand(1, 1000) . '.png';
                                    $filePath = Storage::path($path);
                                }
                                $imageContent = file_get_contents($imgUrl);
                                $file = file_put_contents($filePath, $imageContent);
                            }
                            $row->value = $path;
                        } else {
                            if ($request->{$row->name} != null) {
                                if (!file_exists(Storage::path("form-values/$form->id"))) {
                                    mkdir(Storage::path("form-values/$form->id"), 0777, true);
                                    chmod(Storage::path("form-values/$form->id"), 0777);
                                }
                                $filepath     = "form-values/$form->id/" . rand(1, 1000) . '.png';
                                $url          = $request->{$row->name};
                                $imageContent = file_get_contents($url);
                                $filePath     = Storage::path($filepath);
                                $file         = file_put_contents($filePath, $imageContent);
                                $row->value   = $filepath;
                            }
                        }
                    } elseif ($row->type == 'location') {
                        $row->value = $request->location;
                    } elseif ($row->type == 'video') {
                        $validator = \Validator::make($request->all(),  [
                            'media' => 'required',
                        ]);
                        if ($validator->fails()) {
                            $messages = $validator->errors();
                        }

                        $row->value = $request->media;
                    } elseif ($row->type == 'selfie') {
                        $file = '';
                        $img = $request->image;
                        $folderPath = "form_selfie/";

                        $imageParts = explode(";base64,", $img);

                        if ($imageParts[0]) {

                            $imageBase64 = base64_decode($imageParts[1]);
                            $fileName = uniqid() . '.png';

                            $file = $folderPath . $fileName;
                            Storage::put($file, $imageBase64);
                        }
                        $row->value  = $file;
                    }

                }
            }

            if ($request->form_value_id) {
                $formValue->json = json_encode($array);
                $formValue->save();
            } else {
                if (auth()->user()) {
                    $userId = auth()->user()->id;
                } else {
                    $userId = NULL;
                }
                $data = [];
                $data['status'] = 'free';
                $data['form_id'] = $form->id;
                $data['user_id'] = $userId;
                $data['json']    = json_encode($array);
                $formValue      = FormValue::create($data);
            }
            $formValueArray = json_decode($formValue->json);
            $emails = explode(',', $form->email);
            $ccemails = explode(',', $form->ccemail);
            $bccemails = explode(',', $form->bccemail);
            // if (false && UtilityFacades::getsettings('email_setting_enable') == 'on') {
            //     if ($form->ccemail && $form->bccemail) {
            //         try {
            //             Mail::to($form->email)
            //                 ->cc($form->ccemail)
            //                 ->bcc($form->bccemail)
            //                 ->send(new FormSubmitEmail($formValue, $formValueArray));
            //         } catch (\Exception $e) {
            //         }
            //     } else if ($form->ccemail) {
            //         try {
            //             Mail::to($emails)
            //                 ->cc($ccemails)
            //                 ->send(new FormSubmitEmail($formValue, $formValueArray));
            //         } catch (\Exception $e) {
            //         }
            //     } else if ($form->bccemail) {
            //         try {
            //             Mail::to($emails)
            //                 ->bcc($bccemails)
            //                 ->send(new FormSubmitEmail($formValue, $formValueArray));
            //         } catch (\Exception $e) {
            //         }
            //     } else {
            //         try {
            //             Mail::to($emails)->send(new FormSubmitEmail($formValue, $formValueArray));
            //         } catch (\Exception $e) {
            //         }
            //     }
            //     foreach ($clientEmails as $clientEmail) {
            //         try {
            //             Mail::to($clientEmail)->send(new Thanksmail($formValue));
            //         } catch (\Exception $e) {
            //         }
            //     }
            // }

            // $user = User::where('type', 'Admin')->first();
            // $notificationsSetting = NotificationsSetting::where('title', 'new survey details')->first();
            // if (isset($notificationsSetting)) {
            //     if ($notificationsSetting->notify == '1') {
            //         $user->notify(new NewSurveyDetails($form));
            //     } elseif ($notificationsSetting->email_notification == '1') {
            //         if (UtilityFacades::getsettings('email_setting_enable') == 'on') {
            //             if (MailTemplate::where('mailable', FormSubmitEmail::class)->first()) {
            //                 try {
            //                     Mail::to($formValue->email)->send(new FormSubmitEmail($formValue, $formValueArray));
            //                 } catch (\Exception $e) {
            //                 }
            //             }
            //         }
            //     }
            // }
            if ($form->payment_type != 'coingate' && $form->payment_type != 'mercado') {
                $successMsg = strip_tags($form->success_msg);
            }
            if ($request->form_value_id) {
                $successMsg = strip_tags($form->success_msg);
            }

            // Form::integrationFormData($form, $formValue);

            if (isset($request->ajax)) {
                return response()->json(['is_success' => true, 'message' => $successMsg, 'redirect' => route('admin.applications.settings.edit.form.values', $formValue->id)], 200);
            } else {
                return redirect()->back()->with('success', $successMsg);
            }
        } else {
            if (isset($request->ajax)) {
                return response()->json(['is_success' => false, 'message' => __('Form not found')], 200);
            } else {
                return redirect()->back()->with('failed', __('Form not found.'));
            }
        }
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $fileName           = $request->upload->store('editor');
            $CKEditorFuncNum    = $request->input('CKEditorFuncNum');
            $url                = Storage::url($fileName);
            $msg                = 'Image uploaded successfully';
            $response           = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
            @header('Content-type: text/html; charset=utf-8');
            echo $response;
        }
    }

    public function duplicate(Request $request)
    {
        if (auth()->user()->can('duplicate form')) {
            $form = Form::find($request->form_id);
            if ($form) {
                Form::create([
                    'title'           => $form->title . ' (copy)',
                    'logo'            => $form->logo,
                    'email'           => $form->email,
                    'success_msg'     => $form->success_msg,
                    'thanks_msg'      => $form->thanks_msg,
                    'json'            => $form->json,
                    'payment_status'  => $form->payment_status,
                    'amount'          => $form->amount,
                    'currency_symbol' => $form->currency_symbol,
                    'currency_name'   => $form->currency_name,
                    'payment_type'    => $form->payment_type,
                    'created_by'      => Auth::user()->id,
                    'is_active'       => $form->is_active,
                    'assign_type'     => $form->assign_type,
                ]);
                return redirect()->back()->with('success', __('Form duplicate successfully.'));
            } else {
                return redirect()->back()->with('errors', __('Form not found.'));
            }
        } else {
            return redirect()->back()->with('errors', __('Permission denied.'));
        }
    }

    public function ckupload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $originName         = $request->file('upload')->getClientOriginalName();
            $fileName           = pathinfo($originName, PATHINFO_FILENAME);
            $extension          = $request->file('upload')->getClientOriginalExtension();
            $fileName           = $fileName . '_' . time() . '.' . $extension;
            $request->file('upload')->move(public_path('images'), $fileName);
            $CKEditorFuncNum    = $request->input('CKEditorFuncNum');
            $url                = asset('images/' . $fileName);
            $msg                = __('Image uploaded successfully');
            $response           = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
            @header('Content-type: text/html; charset=utf-8');
            echo $response;
        }
    }

    public function dropzone(Request $request, $id)
    {
        $allowedfileExtension   = [];
        $values                 = '';
        if ($request->file_extention == 'pdf') {
            $allowedfileExtension = ['pdf', 'pdfa', 'fdf', 'xdp', 'xfa', 'pdx', 'pdp', 'pdfxml', 'pdxox'];
        } else if ($request->file_extention == 'image') {
            $allowedfileExtension = ['jpeg', 'jpg', 'png'];
        } else if ($request->file_extention == 'excel') {
            $allowedfileExtension = ['xlsx', 'csv', 'xlsm', 'xltx', 'xlsb', 'xltm', 'xlw'];
        }
        if ($request->hasFile('file')) {
            $file         = $request->file('file');
            $extension    = $file->getClientOriginalExtension();
            if (in_array($extension, $allowedfileExtension)) {
                $filename = $file->store('form-values/' . $id);
                $values   = $filename;
            } else {
                return response()->json(['errors' => 'Only ' . implode(',', $allowedfileExtension) . ' file allowed']);
            }
            return response()->json(['success' => 'File uploded successfully.', 'filename' => $values]);
        } else {
            return response()->json(['errors' => 'File not found.']);
        }
    }

    public function formStatus(Request $request, $id)
    {
        $form   = Form::find($id);
        $input  = ($request->value == "true") ? 1 : 0;
        if ($form) {
            $form->is_active = $input;
            $form->save();
        }
        return response()->json(['is_success' => true, 'message' => __('Form status changed successfully.')]);
    }

    public function formTheme($id)
    {
        $form = Form::find($id);
        return view('admin.pages.applications.form.themes.theme', compact('form'));
    }

    public function formThemeEdit(Request $request, $slug, $id)
    {
        $form = Form::find($id);
        return view('admin.pages.applications.form.themes.index', compact('slug', 'form'));
    }

    public function themeChange(Request $request, $id)
    {
        $form = Form::find($id);
        $form->theme = $request->theme;
        $form->save();
        return redirect()->route('admin.applications.settings.forms.index')->with('success', __('Theme successfully changed.'));
    }

    public function formThemeUpdate(Request $request, $id)
    {
        $validator = \Validator::make($request->all(), [
            'background_image' => 'image|mimes:png,jpg,jpeg',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors();
            return response()->json(['errors' => $messages->first()]);
        }
        $form = Form::find($id);
        $form->theme = $request->theme;
        $form->theme_color = $request->color;
        if ($request->hasFile('background_image')) {
            $themeBackgroundImage = 'form-background.' . $request->background_image->getClientOriginalExtension();
            $themeBackgroundImagePath = 'form-themes/theme3/' . $form->id;
            $backgroundImage = $request->file('background_image')->storeAs(
                $themeBackgroundImagePath,
                $themeBackgroundImage
            );
            $form->theme_background_image = $backgroundImage;
        }
        $form->save();
        return redirect()->route('admin.applications.settings.forms.index')->with('success', __('Form theme selected succesfully.'));
    }

    public function formRules(Request $request,  $id)
    {
        if (auth()->user()->can('read form rule')) {
            $formRules      = form::find($id);
            $jsonData       = json_decode($formRules->json);
            $rules          = formRule::where('form_id', $id)->get();
            return view('admin.pages.applications.form.conditional-rules.rules', compact('formRules', 'jsonData', 'rules'));
        } else {
            return redirect()->back()->with('errors', __('permission Denied'));
        }
    }

    public function storeRule(Request $request)
    {
        if (auth()->user()->can('create form rule')) {

            request()->validate([
                'rule_name'                 => 'required|max:50',
                'condition_type'            => 'nullable',
                'rules.*.if_field_name'     => 'required',
                'rules.*.if_rule_type'      => 'required',
                'rules.*.if_rule_value'     => 'required',
                'rules2.*.else_rule_type'   => 'required',
                'rules2.*.else_field_name'  => 'required',
            ]);

            $conditioal = Form::find($request->form_id);
            $conditioal->conditional_rule = ($request->conditional_rule	 == '1' ? '1'  : '0');
            $conditioal->save();

            $ifJson     = json_encode($request->rules);
            $thenJson   = json_encode($request->rules2);

            $formRule              = new formRule();
            $formRule->form_id     = $request->form_id;
            $formRule->rule_name   = $request->rule_name;
            $formRule->if_json     = $ifJson;
            $formRule->then_json   = $thenJson;
            $formRule->condition   = ($request->condition_type) ?  $request->condition_type : 'or';
            $formRule->save();

            return redirect()->route('admin.applications.settings.form.rules', $request->form_id)->with('success', __('Rule set successfully'));
        } else {
            return redirect()->back()->with('errors', __('permission Denied'));
        }
    }

    public function editRule($id)
    {
        if (auth()->user()->can('edit form rule')) {
            $rule           = formRule::where('id', $id)->first();
            $form           = form::find($rule->form_id);

            $jsonDataIf     = json_decode($rule->if_json);
            $jsonDataThen   = json_decode($rule->then_json);
            $jsonData       = json_decode($form->json);

            return view('admin.pages.applications.form.conditional-rules.edit', compact('form', 'rule', 'jsonDataIf', 'jsonDataThen', 'jsonData'));
        } else {
            return redirect()->back()->with('errors', __('permission Denied'));
        }
    }

    public function ruleUpdate($id, Request $request)
    {
        if (auth()->user()->can('edit-form-rule')) {
            request()->validate([
                'rule_name'                 => 'required|max:50',
                'condition_type'            => 'nullable',
                'rules.*.if_field_name'     => 'required',
                'rules.*.if_rule_type'      => 'required',
                'rules.*.if_rule_value'     => 'required',
                'rules2.*.else_rule_type'   => 'required',
                'rules2.*.else_field_name'  => 'required',
            ]);

            $conditioal = Form::find($request->form_id);
            $conditioal->conditional_rule = ($request->conditional_rule	 == 'on' ? '1'  : '0');
            $conditioal->save();

            $newRules       = $request->rules;
            $existingRules  = formRule::find($id)->if_json;
            $existingRules  = json_decode($existingRules, true);

            $countNewRules = count($newRules);
            $countExistingRules = count($existingRules);

            $lastPosition   = count($newRules) - 1;
            $lastRule       = $newRules[$lastPosition];

            if ($countExistingRules < $countNewRules) {
                foreach ($newRules as $newRule) {
                    $newFieldName = $lastRule['if_field_name'];
                    foreach ($existingRules as $existingRule) {
                        $existingFieldName = $existingRule['if_field_name'];

                        if ($newFieldName === $existingFieldName) {
                            return redirect()->back()->with('errors', 'This name Rule already exists.');
                        }

                    }
                }
            }

            $ifJson = json_encode($request->rules);
            $thenJson = json_encode($request->rules2);

            $ruleUpdate                 = formRule::find($id);
            $ruleUpdate->rule_name      = $request->rule_name;
            $ruleUpdate->if_json        = $ifJson;
            $ruleUpdate->then_json      = $thenJson;
            $ruleUpdate->condition      = ($request->condition_type) ?  $request->condition_type : 'or';
            $ruleUpdate->save();

            return redirect()->route('admin.applications.settings.form.rules', $request->form_id)->with('success', __('Rule set successfully'));
        } else {
            return redirect()->back()->with('errors', __('permission Denied'));
        }
    }

    public function ruleDelete($id)
    {
        if (auth()->user()->can('delete form rule')) {
            $ruleDelete  = formRule::find($id);
            $ruleDelete->delete();

            return back()->with('success', __('Rule Deleted Succesfully'));
        } else {
            return redirect()->back()->with('errors', __('permission Denied'));
        }
    }

    public function getField(Request $request)
    {
        $form = Form::find($request->id);
        $formData = json_decode($form->json, true);
        $fieldName = $request->input('fieldname');

        $matchingField = null;
        foreach ($formData as $section) {
            foreach ($section as $field) {
                if (isset($field['name']) && $field['name'] === $fieldName) {
                    $matchingField = $field;
                    break 2;
                }
            }
        }

        return response(['matchingField' => $matchingField]);
    }
}
