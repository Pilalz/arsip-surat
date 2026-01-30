<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\RSLApp;
use Illuminate\Auth\Access\HandlesAuthorization;

class RSLAppPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RSLApp');
    }

    public function view(AuthUser $authUser, RSLApp $rSLApp): bool
    {
        return $authUser->can('View:RSLApp');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RSLApp');
    }

    public function update(AuthUser $authUser, RSLApp $rSLApp): bool
    {
        return $authUser->can('Update:RSLApp');
    }

    public function delete(AuthUser $authUser, RSLApp $rSLApp): bool
    {
        return $authUser->can('Delete:RSLApp');
    }

    public function restore(AuthUser $authUser, RSLApp $rSLApp): bool
    {
        return $authUser->can('Restore:RSLApp');
    }

    public function forceDelete(AuthUser $authUser, RSLApp $rSLApp): bool
    {
        return $authUser->can('ForceDelete:RSLApp');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RSLApp');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RSLApp');
    }

    public function replicate(AuthUser $authUser, RSLApp $rSLApp): bool
    {
        return $authUser->can('Replicate:RSLApp');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RSLApp');
    }

}