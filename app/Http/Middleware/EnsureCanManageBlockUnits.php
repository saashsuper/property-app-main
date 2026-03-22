<?php

namespace App\Http\Middleware;

use App\Models\Block;
use App\Models\BlockUnit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Allows standard staff roles, the Block Manager role, or a user assigned as
 * block_manager_id for the block relevant to the request (unit CRUD, upload, listing by block).
 */
class EnsureCanManageBlockUnits
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

        // Read-only unit list for dropdowns (issues, work orders, visits, etc.)
        if ($request->routeIs('block-units.by-block') && $user->can('block-units.view')) {
            return $next($request);
        }

        $blockId = $this->resolveBlockId($request);
        if ($blockId === null) {
            abort(403);
        }

        $allowed = Block::where('id', $blockId)
            ->where('block_manager_id', $user->id)
            ->exists();

        if (! $allowed) {
            abort(403);
        }

        return $next($request);
    }

    private function resolveBlockId(Request $request): ?int
    {
        $route = $request->route();

        $blockUnit = $route?->parameter('block_unit');
        if ($blockUnit instanceof BlockUnit) {
            return (int) $blockUnit->block_id;
        }

        $blockIdParam = $route?->parameter('block_id');
        if ($blockIdParam !== null && $blockIdParam !== '') {
            return (int) $blockIdParam;
        }

        $blockIdFromPath = $route?->parameter('blockId');
        if ($blockIdFromPath !== null && $blockIdFromPath !== '') {
            return (int) $blockIdFromPath;
        }

        $inputBlockId = $request->input('block_id');
        if ($inputBlockId !== null && $inputBlockId !== '') {
            return (int) $inputBlockId;
        }

        return null;
    }
}
