<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('workspaces.{id}', function ($user, $id) {
    // Only allow access if the user's agency_id matches the workspace's agency_id
    // Or if they are a super admin. For simplicity, we assume they have access to their agency's workspaces.
    $workspace = \App\Models\Workspace::find($id);
    return $workspace && $user->agency_id === $workspace->agency_id;
});

Broadcast::channel('analytics.{id}', function ($user, $id) {
    return $user->agency_id == $id;
});
