<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportMembersController extends Controller
{
    /**
     * Export members to CSV with optional search, status filter and sorting.
     */
    public function __invoke(Request $request): StreamedResponse
    {
        $query = Member::query();

        $search = $request->string('q')->trim()->toString();
        if ($search !== '') {
            $searchLower = strtolower($search);
            $query->where(function ($q) use ($searchLower, $search) {
                $q->whereRaw('LOWER(first_name) LIKE ?', ['%' . $searchLower . '%'])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . $searchLower . '%'])
                    ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $searchLower . '%'])
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $statusFilter = $request->string('status')->toString();
        if (in_array($statusFilter, ['active', 'inactive'], true)) {
            $query->where('status', $statusFilter);
        }

        $sortBy = $request->string('sort')->toString();
        $sortDir = $request->string('dir')->toString() === 'asc' ? 'asc' : 'desc';
        $orderDir = $sortDir === 'asc' ? 'asc' : 'desc';

        match ($sortBy) {
            'name' => $query->orderBy('last_name', $orderDir)->orderBy('first_name', $orderDir),
            'expiry' => $query->orderBy('subscription_expiry', $orderDir === 'asc' ? 'asc' : 'desc'),
            default => $query->orderBy('created_at', $orderDir),
        };

        $members = $query->get();

        $headers = ['Name', 'Email', 'Phone', 'Status', 'Registration Date', 'Subscription Expiry'];
        $rows = $members->map(fn (Member $m) => [
            $m->full_name,
            $m->email,
            $m->phone,
            $m->status ?? 'active',
            $m->created_at->format('Y-m-d'),
            $m->subscription_expiry ? $m->subscription_expiry->format('Y-m-d') : '',
        ]);

        $csv = implode("\n", [implode(',', $headers), ...$rows->map(fn ($row) => implode(',', $row))]);

        return response()->streamDownload(
            fn () => print($csv),
            'members-' . now()->format('Y-m-d') . '.csv',
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }
}
