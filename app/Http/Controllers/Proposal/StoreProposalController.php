<?php

namespace App\Http\Controllers\Proposal;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreProposalController extends Controller
{
    /**
     * Store a new proposal from the public "Scrie-ne" form.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:500'],
            'message' => ['required', 'string', 'max:10000'],
        ]);

        Proposal::create($validated);

        return response()->json(['success' => true]);
    }
}
