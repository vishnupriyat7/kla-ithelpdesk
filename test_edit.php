<?php
use Illuminate\Support\Facades\Log;
echo "Starting test...\n";
$ticket = \App\Models\ComplaintRegister::where('status', 'Open')->first();
if (!$ticket) {
    echo "No Open tickets found.\n";
    exit;
}
echo "Found ticket {$ticket->ticket_no} with technician {$ticket->technician_id}\n";
$ticket->update(['status' => 'Assigned']);
echo "Updated status to {$ticket->status}\n";
