<?php

namespace App\Http\Controllers;

use App\Models\AuthorizedAbsenceDay;
use Illuminate\Http\Request;

class AuthorizedAbsenceDayController extends Controller
{
    // Update the number of authorized absence days
    public function update(Request $request)
    {
        $request->validate([
            'number_of_days' => 'required|integer|min:0',
        ]);

        $authorizedDays = AuthorizedAbsenceDay::first();

        if ($authorizedDays) {
            $authorizedDays->update(['number_of_days' => $request->number_of_days]);
        } else {
            AuthorizedAbsenceDay::create(['number_of_days' => $request->number_of_days]);
        }

        return response()->json(['message' => 'Authorized absence days updated successfully']);
    }
}
