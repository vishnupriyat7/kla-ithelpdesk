<?php

use App\Exports\ComplaintRegisterExport;
use App\Models\ComplaintRegister;
use App\Models\Role;
use App\Models\User;
use Filament\Pages\Page;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['name' => 'superadmin']);
    $this->user = User::factory()->create([
        'role_id' => $this->adminRole->id,
    ]);
});

test('complaint register export handles collections and queries properly', function () {
    $this->actingAs($this->user);

    $ticket = ComplaintRegister::create([
        'ticket_no' => 'TEST-001',
        'section' => 'Accounts',
        'complaint_type' => 'Printer',
        'description' => 'Paper jam issue',
        'status' => 'Resolved',
        'remarks' => 'Fixed roller',
    ]);

    $export = new ComplaintRegisterExport();
    $collection = $export->collection();
    expect($collection->contains('id', $ticket->id))->toBeTrue();

    $headings = $export->headings();
    expect($headings)->toContain('Ticket No', 'Date & Time', 'Section', 'Status');

    $row = $export->map($ticket);
    expect($row[0])->toBe('TEST-001');
    expect($row[2])->toBe('Accounts');
    expect($row[6])->toBe('Printer');
    expect($row[8])->toBe('Resolved');
});

test('excel and csv exports can be generated via maatwebsite excel', function () {
    $this->actingAs($this->user);

    Excel::fake();

    $ticket = ComplaintRegister::create([
        'ticket_no' => 'TEST-002',
        'section' => 'IT Section',
        'complaint_type' => 'Network',
        'description' => 'LAN cable unplugged',
        'status' => 'Open',
    ]);

    Excel::download(new ComplaintRegisterExport(collect([$ticket])), 'test.xlsx');
    Excel::assertDownloaded('test.xlsx');

    Excel::download(new ComplaintRegisterExport(collect([$ticket])), 'test.csv', \Maatwebsite\Excel\Excel::CSV);
    Excel::assertDownloaded('test.csv');
});

test('complaint registers admin page renders successfully with export action', function () {
    $this->actingAs($this->user);
    \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

    $page = new \App\Filament\Resources\ComplaintRegisterResource\Pages\ListComplaintRegisters();
    $table = $page->table(\Filament\Tables\Table::make($page));

    expect($table->getColumns())->not->toBeEmpty();
    expect($table->getBulkActions())->not->toBeEmpty();
    // Test the page-level header actions
    $reflection = new \ReflectionClass($page);
    $method = $reflection->getMethod('getHeaderActions');
    $method->setAccessible(true);
    $headerActions = $method->invoke($page);
    expect($headerActions)->not->toBeEmpty();
});
