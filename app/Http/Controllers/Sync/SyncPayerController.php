<?php

namespace App\Http\Controllers\Sync;

use App\Http\Controllers\Controller;
use App\Models\Payer;
use Illuminate\Http\Request;

class SyncPayerController extends Controller
{
    public function lookup(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $payer = Payer::where('email', $request->query('email'))->first();

        if (!$payer) {
            return response()->json(['found' => false], 404);
        }

        return response()->json([
            'found'     => true,
            'id'        => $payer->id,
            'firstName' => $payer->firstName,
            'lastName'  => $payer->lastName,
            'email'     => $payer->email,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName'  => 'required|string|max:255',
            'email'     => 'required|email|unique:payers,email',
        ]);

        $payer = Payer::create($validated);

        return response()->json([
            'id'        => $payer->id,
            'firstName' => $payer->firstName,
            'lastName'  => $payer->lastName,
            'email'     => $payer->email,
        ], 201);
    }

    public function update(Request $request, Payer $payer)
    {
        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName'  => 'required|string|max:255',
        ]);

        $payer->update($validated);

        return response()->json([
            'id'        => $payer->id,
            'firstName' => $payer->firstName,
            'lastName'  => $payer->lastName,
            'email'     => $payer->email,
        ]);
    }
}
