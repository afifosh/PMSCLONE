<?php

use App\Models\Admin;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('projects.{id}', function (Admin $user, $id) {
  if($user->isSuperAdmin()) return true;
  return ProjectMember::where('project_id', $id)->where('admin_id', $user->id)->exists();
});
