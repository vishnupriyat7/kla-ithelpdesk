<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Room;
use App\Models\ComplaintRegister;


class ComplaintRegisterController extends Controller
{


    // ✅ Get Floors based on Building
    public function getFloors($locationId)
    {
        $roomFloorIds = Room::where('office_location_id', $locationId)
            ->whereNotNull('floor_id')
            ->distinct()
            ->pluck('floor_id');

        $floors = \App\Models\Floor::whereIn('id', $roomFloorIds)
            ->orderBy('sort_order')
            ->pluck('name');

        return response()->json($floors);
    }

    // ✅ Get Rooms based on Building + Floor
    public function getRooms($locationId, $floor)
    {
        $floorRecord = \App\Models\Floor::where('name', $floor)->first();
        
        if (!$floorRecord) {
            return response()->json([]);
        }

        $rooms = Room::where('office_location_id', $locationId)
            ->where('floor_id', $floorRecord->id)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        return response()->json($rooms);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'nullable',
            'section' => 'required',
            'office_location_id' => 'required',
            'floor' => 'nullable',
            'room_id' => 'nullable',
            'complaint_type' => 'required',
            'description' => 'required',
        ]);

        $ticket = ComplaintRegister::create([
            'employee_id' => $request->employee_id,
            'section' => $request->section,
            'office_location_id' => $request->office_location_id,
            'floor' => $request->floor,
            'room_id' => $request->room_id,
            'complaint_type' => $request->complaint_type,
            'description' => $request->description,
            'user_id' => auth()->id(),
        ]);

        return back()->with([
            'success' => 'Ticket submitted successfully! Your Ticket ID is: ' . $ticket->ticket_no,
            'ticket' => $ticket
        ]);
    }
    public function liveScreen()
    {
        return view('complaintregister.live');
    }
    public function liveData(Request $request)
    {
        $query = ComplaintRegister::with(['technician', 'location', 'room', 'statusHistories.technician'])->latest();

        if (auth()->check() && $request->query('scope') !== 'all') {
            $role = auth()->user()->getRoleName();
            if (in_array($role, ['chm', 'programmer'])) {
                $query->where(function ($q) {
                    $q->where('status', 'Open')
                      ->orWhere('technician_id', auth()->id());
                });
            }
        }

        $tickets = $query->get();

        return response()->json($tickets);
    }

    public function takeTicket(Request $request, ComplaintRegister $ticket)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $role = auth()->user()->getRoleName();
        if (!in_array($role, ['chm', 'programmer', 'admin', 'superadmin', 'hardwareadmin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Only Programmer, HardwareAdmin, and Admin technicians can process tickets.']);
        }

        $ticket->update([
            'technician_id' => auth()->id(),
            'status' => 'Assigned',
        ]);

        return response()->json(['success' => true, 'message' => 'Ticket Assigned to ' . auth()->user()->name]);
    }

    public function resolveTicket(Request $request, ComplaintRegister $ticket)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $role = auth()->user()->getRoleName();
        if (!in_array($role, ['chm', 'programmer', 'admin', 'superadmin', 'hardwareadmin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Only Programmer, HardwareAdmin, and Admin technicians can process tickets.']);
        }

        if ($ticket->technician_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'You are not assigned to this ticket.']);
        }

        $status = $request->input('status', 'Resolved');

        if ($status === 'Unassign') {
            $updateData = [
                'status' => 'Open',
                'technician_id' => null,
                'remarks' => $request->input('remarks'),
            ];
            if ($request->filled('custom_status_date')) {
                $updateData['custom_status_date'] = $request->input('custom_status_date');
            }
            $ticket->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Ticket Unassigned successfully',
                'updated_by' => auth()->user()->name
            ]);
        }

        $vendor_complaint_id = $request->input('vendor_complaint_id');

        if ($status === 'Complaint' && $vendor_complaint_id) {
            $existing = \App\Models\VendorComplaint::where('vendor_complaint_no', $vendor_complaint_id)->first();
            if ($existing && $existing->complaint_ticket_id !== $ticket->id) {
                return response()->json(['success' => false, 'message' => 'The Vendor Complaint ID must be unique. It is already registered to another ticket.']);
            }
        }

        $updateData = [
            'status' => $status,
            'remarks' => $request->input('remarks'),
            'vendor_complaint_id' => $vendor_complaint_id,
        ];
        if ($request->filled('custom_status_date')) {
            $updateData['custom_status_date'] = $request->input('custom_status_date');
        }

        $ticket->update($updateData);

        if ($status === 'Complaint' && $vendor_complaint_id) {
            \App\Models\VendorComplaint::updateOrCreate(
                ['complaint_ticket_id' => $ticket->id],
                [
                    'vendor' => $request->input('vendor_name'),
                    'vendor_complaint_no' => $vendor_complaint_id,
                    'status' => $request->input('vendor_status', 'Unattended'),
                    'complaint_description' => $request->input('vendor_description'),
                    'chm_remark' => $request->input('remarks'),
                    'user_id' => auth()->id(),
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Ticket Status Updated successfully',
            'updated_by' => auth()->user()->name
        ]);
    }
}