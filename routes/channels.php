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

Broadcast::channel('App.Models.Admin.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('projects.{id}', function (Admin $user, $id) {
  if($user->isSuperAdmin()) return true;
  return ProjectMember::where('project_id', $id)->where('admin_id', $user->id)->exists();
});

/**
 * Presence channel for contracts updates
 * events broadcasted: contract-updated
 * Right Now Contract is not associated with any admin, so we are allowing all admins to join this channel
 */
Broadcast::channel('contracts.{id}', function (Admin $user, $id) {
  return ['id' => $user->id, 'name' => $user->name, 'avatar' => $user->avatar];
});


/**
 * Presence channel for stage updates
 * events broadcasted: [whisper: editing-model]
 * Right Now Stage is not associated with any admin, so we are allowing all admins to join this channel
 */
Broadcast::channel('contract-stages.{id}', function (Admin $user, $id) {
  return ['id' => $user->id, 'name' => $user->name, 'avatar' => $user->avatar];
});

/**
 * Presence channel for contractPhase updates
 * events broadcasted: [whisper: editing-model]
 * Right Now ContractPhase is not associated with any admin, so we are allowing all admins to join this channel
 */
Broadcast::channel('contract-phases.{id}', function (Admin $user, $id) {
  return ['id' => $user->id, 'name' => $user->name, 'avatar' => $user->avatar];
});
