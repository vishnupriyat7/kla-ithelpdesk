<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Http;
use App\Models\RetiredStaff;

class EmployeeController extends Controller
{
    // for IT Complaint Register employee list
    public function list()
    {
        try {
            $response = Http::get(env('EMPLOYEE_API_URL'));
            
            if ($response->successful()) {
                return response()->json($response->json())
                    ->header('Cache-Control', 'no-store');
            }

            return response()->json([])
                ->header('Cache-Control', 'no-store');
        } catch (\Exception $e) {
            return response()->json([])
                ->header('Cache-Control', 'no-store');
        }
    }
}
