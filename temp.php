<?php 
$duplicates = DB::select("
    SELECT MAX(id) as id
    FROM complaint_status_histories
    GROUP BY helpdesk_ticket_id, status, remarks, technician_id, created_at
    HAVING COUNT(*) > 1
");
foreach ($duplicates as $dup) {
    DB::table("complaint_status_histories")->where("id", $dup->id)->delete();
}
echo "Deleted " . count($duplicates) . " duplicates.\n";

