<?php

namespace App\Http\Controllers\Admin\Applications\Form;

use App\DataTables\Admin\Applications\Form\FormValuesDataTable;
use App\Exports\FormValuesExport;
use App\Facades\UtilityFacades;
use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormValue;
use App\Models\UserForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;

class FormValueController extends Controller
{
    public function index(FormValuesDataTable $dataTable)
    {
        if (auth()->user()->can('read submitted form')) {
            $forms = Form::all();
            return $dataTable->render('admin.pages.applications.form-value.index', compact('forms'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function showSubmitedForms($form_id, FormValuesDataTable $dataTable)
    {
        if (auth()->user()->can('read submitted form')) {
            $forms          = Form::all();
            $chartData      = UtilityFacades::dataChart($form_id);
            $formsDetails  = Form::find($form_id);
            return $dataTable->with('form_id', $form_id)->render('admin.pages.applications.form-value.view-submited-form', compact('forms', 'chartData', 'formsDetails'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id)
    {
        if (auth()->user()->can('read submitted form')) {
            try {
                $formValue = FormValue::find($id);
                $array      = json_decode($formValue->json);
            } catch (\Throwable $th) {
                return redirect()->back()->with('errors', $th->getMessage());
            }
            return view('admin.pages.applications.form-value.view', compact('formValue', 'array'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        $usr             = auth()->user();
        $userRole       = $usr->roles->first()->id;
        $formValue      = FormValue::find($id);
        $formallowededit = UserForm::where('role_id', $userRole)->where('form_id', $formValue->form_id)->count();
        if (auth()->user()->can('edit submitted form') && auth()->user()->isSuperAdmin()) {
            $array  = json_decode($formValue->json);
            $form   = $formValue->Form;
            $frm    = Form::find($formValue->form_id);
            return view('admin.pages.applications.form.fill', compact('form', 'formValue', 'array'));
        } else {
            if (auth()->user()->can('edit submitted form') && $formallowededit > 0) {
                $formValue = FormValue::find($id);
                $array      = json_decode($formValue->json);
                $form       = $formValue->Form;
                $frm        = Form::find($formValue->form_id);
                return view('admin.pages.applications.form.fill', compact('form', 'formValue', 'array'));
            } else {
                return redirect()->back()->with('failed', __('Permission denied.'));
            }
        }
    }

    public function destroy($id)
    {
        if (auth()->user()->can('delete submitted form')) {
            FormValue::find($id)->delete();
            return redirect()->back()->with('success',  __('Form successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function downloadPdf($id)
    {
        $formValue = FormValue::where('id', $id)->first();
        if ($formValue) {
            $formValue->createPDF();
        } else {
            $formValue = FormValue::where('id', '=', $id)->first();
            if (!$formValue) {
                $id         = Crypt::decryptString($id);
                $formValue = FormValue::find($id);
            }
            if ($formValue) {
                $formValue->createPDF();
            } else {
                return redirect()->route('admin.dashboard')->with('error', __('File is not exist.'));
            }
        }
    }

    public function export(Request $request)
    {
        $form = Form::find($request->form_id);
        return Excel::download(new FormValuesExport($request), $form->title . '.csv');
    }

    public function downloadCsv2($id)
    {
        $formValue = FormValue::where('id', '=', $id)->first();
        if (!$formValue) {
            $id         = Crypt::decryptString($id);
            $formValue = FormValue::find($id);
        }
        if ($formValue) {
            $formValue->createCSV2();
            return response()->download(storage_path('app/public/csv/Survey_' . $formValue->id . '.xlsx'))->deleteFileAfterSend(true);
        } else {
            return redirect()->route('admin.dashboard')->with('error', __('File is not exist.'));
        }
    }

    public function exportXlsx(Request $request)
    {
        if($request->select_date != ''){

            $form           = Form::find($request->form_id);
            $dateRange  = $request->select_date;
            list($startDate, $endDate) = array_map('trim', explode('to', $dateRange));
        }
        else{
            $form  = Form::find($request->form_id);
            $startDate = '';
            $endDate = '';
        }
        return Excel::download(new FormValuesExport($request , $startDate , $endDate), $form->title . '.xlsx');
    }

    public function getGraphData(Request $request, $id)
    {
        $form       = Form::find($id);
        $chartData  = UtilityFacades::dataChart($id);
        return view('admin.pages.applications.form.form-value.chart', compact('chartData', 'id', 'form'));
    }

    public function VideoStore(Request $request)
    {
        $file      = $request->file('media');
        $extension = $file->getClientOriginalExtension();
        $filename  = $file->store('form_video');
        $values    = $filename;
        return response()->json(['success' => 'Video uploded successfully.', 'filename' => $values]);
    }


    public function SelfieDownload($id)
    {

        $data           = FormValue::find($id);
        $json           = $data->json;
        $jsonData       = json_decode($json, true);
        $selfieValue    = null;
        foreach ($jsonData[0] as $field) {
            if ($field['type'] === 'selfie') {
                $selfieValue = $field['value'];
                break;
            } elseif ($field['type'] === 'video') {
                $selfieValue = $field['value'];
                break;
            }
        }
        if ($selfieValue === null) {
            return redirect()->back()->with('errors', __('Image Value Not Found'));
        }
        $filePath = storage_path('app/' . $selfieValue);
        return response()->download($filePath);
    }
}
