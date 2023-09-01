<?php

namespace App\Http\Controllers\Admin\EmailTemplate;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\EmailTemplateLang;
use App\Models\Language;
use App\Models\UserEmailTemplate;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailTemplateController extends Controller
{
  /**
   * Update the specified resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @param \App\EmailTemplate $emailTemplate
   *
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id, $lang = 'en')
  {
    $validator = Validator::make(
      $request->all(),
      [
        'from' => 'required',
        'subject' => 'required',
        'content' => 'required',
      ]
    );

    if ($validator->fails()) {
      $messages = $validator->getMessageBag();

      return redirect()->back()->with('error', $messages->first());
    }

    $emailTemplate       = EmailTemplate::where('id', $id)->first();
    $emailTemplate->from = $request->from;
    $emailTemplate->save();

    $emailLangTemplate = EmailTemplateLang::where('parent_id', '=', $id)->where('lang', '=', $request->lang)->first();

    // if record not found then create new record else update it.
    if (empty($emailLangTemplate)) {
      $emailLangTemplate            = new EmailTemplateLang();
      $emailLangTemplate->parent_id = $id;
      $emailLangTemplate->lang      = $request['lang'];
      $emailLangTemplate->subject   = $request['subject'];
      $emailLangTemplate->content   = $request['content'];
      $emailLangTemplate->save();
    } else {
      $emailLangTemplate->subject = $request['subject'];
      $emailLangTemplate->content = $request['content'];
      $emailLangTemplate->save();
    }

    return redirect()->route(
      'admin.manage.email.language',
      [
        $emailTemplate->id,
        $request->lang,
      ]
    )->with('success', __('Email Template successfully updated.'));
  }

  // Used For View Email Template Language Wise
  public function manageEmailLang($id, $lang = 'en')
  {
    $data['languages'] = config('languages');
    $data['LangName'] = config('languages.' . $lang); //Language::where('code', $lang)->first();
    $data['emailTemplate']     = EmailTemplate::first();
    $data['currEmailTempLang'] = EmailTemplateLang::where('parent_id', '=', $id)->where('lang', $lang)->firstOrNew();
    $data['emailTemplate']     = EmailTemplate::where('id', '=', $id)->first();
    $data['EmailTemplates'] = EmailTemplate::all();
    $data['id'] = $id;
    $data['template_lang'] = $lang;

    return view('admin.pages.email-templates.show', $data);
  }

  // Used For Store Email Template Language Wise
  public function storeEmailLang(Request $request, $id)
  {

    $validator = Validator::make(
      $request->all(),
      [
        'subject' => 'required',
        'content' => 'required',
      ]
    );

    if ($validator->fails()) {
      $messages = $validator->getMessageBag();

      return redirect()->back()->with('error', $messages->first());
    }

    $emailLangTemplate = EmailTemplateLang::where('parent_id', '=', $id)->where('lang', '=', $request->lang)->first();

    // if record not found then create new record else update it.
    if (empty($emailLangTemplate)) {
      $emailLangTemplate            = new EmailTemplateLang();
      $emailLangTemplate->parent_id = $id;
      $emailLangTemplate->lang      = $request['lang'];
      $emailLangTemplate->subject   = $request['subject'];
      $emailLangTemplate->content   = $request['content'];
      $emailLangTemplate->save();
    } else {
      $emailLangTemplate->subject = $request['subject'];
      $emailLangTemplate->content = $request['content'];
      $emailLangTemplate->save();
    }

    return redirect()->route(
      'manage.email.language',
      [
        $id,
        $request->lang,
      ]
    )->with('success', __('Email Template Detail successfully updated.'));
  }
}
