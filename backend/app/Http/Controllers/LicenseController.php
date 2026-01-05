<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function validateLicense(Request $request)
    {
        $data = $request->validate([
            'license_key' => ['required', 'string'],
        ]);

        $company = Company::where('license_key', $data['license_key'])->first();

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid license key',
            ], 401);
        }

        if ($company->is_used == 1) {
            return response()->json([
                'success' => false,
                'message' => 'License key already used',
            ], 403);
        }

        // --------------------
        // If expiry_date is NULL -> set 30 days from today (trial) ONCE
        // --------------------
        if (is_null($company->expiry_date)) {
            $company->where("id", $company->id)->update([
                'expiry_date' => now()->addDays(30)->toDateString(), // YYYY-MM-DD
            ]);

            // refresh model value for below checks/response formats
            $company->refresh();
        }

        // --------------------
        // Expiry check
        // --------------------
        if ($company->expiry_date) {
            $expiry = Carbon::parse($company->expiry_date)->startOfDay();
            $today  = now()->startOfDay();

            if ($expiry->lt($today)) {
                return response()->json([
                    'success' => false,
                    'message' => 'License expired',
                    'data' => [
                        'expiry_date' => $expiry->format('d-M-Y'),
                    ],
                ], 403);
            }
        }

        $company->where("id", $company->id)->update(["is_used" => 1]);

        return response()->json([
            'success' => true,
            'message' => 'License activated',
        ], 403);
    }
}
