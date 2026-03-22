<?php

namespace App\Http\Middleware;

use App\Models\Block;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Allows Admin / Super Admin / Manager / Viewer / Block Manager roles, or a user
 * assigned as block_manager_id on the block being edited.
 */
class EnsureStaffOrAssignedBlockManager
{
    private const STAFF_ROLES = ['Admin', 'Super Admin', 'Manager', 'Viewer', 'Block Manager'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        if ($user->hasAnyRole(self::STAFF_ROLES)) {
            return $next($request);
        }

        $block = $request->route('block');
        if ($block instanceof Block && (int) $block->block_manager_id === (int) $user->id) {
            return $next($request);
        }

        abort(403);
    }
}
