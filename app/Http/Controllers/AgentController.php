<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AgentController extends Controller
{
    /**
     * Display a listing of the agents.
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            $agents = User::where('role', 'agent')
                ->where('merchant_id', auth()->user()->merchant_id)
                ->get();

            // $merchant_name = auth()->user()->merchant()->first()->name;
            // $agents['merchant_name'] = $merchant_name;
            // Log::info($agents);

            return Inertia::render('Agents/Index', [
                'agents' => $agents
            ]);
        } else {
            abort(403);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
