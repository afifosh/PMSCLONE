<?php

namespace App\Http\Controllers\Admin\PersonalNote;

use App\Http\Controllers\Controller;
use App\Models\NoteTag;
use App\Models\PersonalNote;
use Beta\Microsoft\Graph\Model\Person;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PersonalNoteController extends Controller
{
  public function index()
  {
    $notes = auth()->user()->personalNotes()->with('tag')->get();
    $tags = $this->myAndPublicTags()->get();

    return view('admin.pages.personal-notes.index', compact('notes', 'tags'));
  }

  public function create()
  {
    $private_note = new PersonalNote();
    $tags = $this->myAndPublicTags()->pluck('name', 'id');

    return $this->sendRes('success', ['view_data' => view('admin.pages.personal-notes.create', compact('tags', 'private_note'))->render()]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'required|string|max:65000',
      'tag' => 'required|exists:note_tags,id',
    ]);

    if(NoteTag::where('id', $request->tag)->where(function($q){
      $q->where('user_type', Admin::class)
        ->where('user_id', auth()->user()->id);
    })->orWhere(function($q){
      $q->where('user_type', null)
        ->where('user_id', null);
    })->doesntExist()){
      throw ValidationException::withMessages(['tag' => 'Invalid tag.']);
    }

    auth()->user()->personalNotes()->create($request->only(['title', 'description']) + ['note_tag_id' => $request->tag]);

    return $this->sendRes(__('Note Created Successfully'), ['event' => 'page_reload', 'close' => 'globalModal']);
  }

  public function edit(PersonalNote $private_note)
  {
    abort_if($private_note->user_type != Admin::class && $private_note->user_id != auth()->user()->id, 403);

    $tags = $this->myAndPublicTags()->pluck('name', 'id');

    return $this->sendRes('success', ['view_data' => view('admin.pages.personal-notes.create', compact('tags', 'private_note'))->render()]);
  }

  public function update(Request $request, PersonalNote $private_note)
  {
    abort_if($private_note->user_type != Admin::class && $private_note->user_id != auth()->user()->id, 403);

    $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'required|string|max:65000',
      'tag' => 'required|exists:note_tags,id',
    ]);

    if(NoteTag::where('id', $request->tag)->where(function($q){
      $q->where('user_type', Admin::class)
        ->where('user_id', auth()->user()->id);
    })->orWhere(function($q){
      $q->where('user_type', null)
        ->where('user_id', null);
    })->doesntExist()){
      throw ValidationException::withMessages(['tag' => 'Invalid tag.']);
    }

    $private_note->update($request->only(['title', 'description']) + ['note_tag_id' => $request->tag]);

    return $this->sendRes(__('Note Updated Successfully'), ['event' => 'page_reload', 'close' => 'globalModal']);
  }

  public function destroy(PersonalNote $private_note)
  {
    abort_if($private_note->user_type != Admin::class && $private_note->user_id != auth()->user()->id, 403);

    $private_note->delete();

    return $this->sendRes(__('Note Deleted Successfully'), ['event' => 'page_reload']);
  }

  public function myAndPublicTags()
  {
    return NoteTag::where(function($q){
      $q->where('user_type', Admin::class)
        ->where('user_id', auth()->user()->id);
    })->orWhere(function($q){
      $q->where('user_type', null)
        ->where('user_id', null);
    });
  }
}
