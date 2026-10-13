@if(session('success'))
@php
    $ticket = session('ticket');
    $waText = "";
    if($ticket) {
        $buildingName = $ticket->location ? $ticket->location->location : ''; 
        $roomName = $ticket->room ? $ticket->room->name : '';
        $locationStr = trim(implode(' / ', array_filter([$buildingName, $ticket->floor, $roomName])));

        $waMessage = "*New Ticket No. [" . $ticket->ticket_no . "]*\n";
        $waMessage .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $waMessage .= "*Section:* " . $ticket->section . "\n";
        $waMessage .= "*Location / Room:* " . $locationStr . "\n";
        $waMessage .= "*Complaint Type:* " . $ticket->complaint_type . "\n";
        $waMessage .= "*Description:* _" . $ticket->description . "_";
        $waText = urlencode($waMessage);
    }
@endphp
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var successModalEl = document.getElementById('successModal');
        if (successModalEl) {
            var successModal = new bootstrap.Modal(successModalEl);
            successModal.show();
        }
    });
</script>
@endif

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                </div>
                <h3 class="fw-bold mb-3">Complaint Submission Successful!</h3>
                <p class="text-muted fs-5 mb-3">
                    {{ session('success') }}
                </p>
                <p class="mb-4 text-secondary">
                    You can monitor the progress of your complaint in real-time by selecting <strong>Complaint
                        Status</strong> from the IT Complaint Register menu.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2 fw-bold" data-bs-dismiss="modal">
                        Close
                    </button>
                    @if(session('ticket'))
                    <a href="https://api.whatsapp.com/send?text={{ $waText }}" target="_blank" class="btn btn-success px-4 py-2 fw-bold" style="background-color: #25D366; border-color: #25D366;" onclick="setTimeout(function(){ bootstrap.Modal.getInstance(document.getElementById('successModal')).hide(); }, 500);">
                        <i class="bi bi-whatsapp me-2"></i> Notify IT Cell Group
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- IT Complaint Register Modal -->
<div class="modal fade" id="complaintregisterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-tools"></i> IT Complaint Register
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('complaintregister.store') }}" method="POST" id="helpdeskForm" novalidate>

                @csrf

                <div class="modal-body">
                    <!-- Form Content -->
                    <div id="helpdeskFormContent">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Section <span class="text-danger">*</span></label>
                                <select class="form-select" name="section" id="sectionSelect" style="width: 100%">
                                    <option value="">Select Section..</option>
                                </select>
                                <div class="invalid-feedback d-none" id="err-section">Please select a section.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Employee</label>
                                <select class="form-select" name="employee_id" id="employeeSelect" style="width: 100%">
                                    <option value="">Select Employee..</option>
                                </select>
                                <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;"><i class="bi bi-info-circle"></i> Not listed? You can type your name manually.</small>
                                <div class="invalid-feedback d-none" id="err-employee">Please select an employee.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Building <span class="text-danger">*</span></label>
                                <select class="form-select" id="buildingSelect" name="office_location_id">
                                    <option value="">Select Building</option>
                                    @foreach ($locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->location }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback d-none" id="err-building">Please select a building.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Floor</label>
                                <select class="form-select" id="floorSelect" name="floor">
                                    <option value="">Select Floor</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Room</label>
                                <select class="form-select" id="roomSelect" name="room_id">
                                    <option value="">Select Room</option>
                                </select>
                                <div class="invalid-feedback d-none" id="err-room">Please select a room.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Complaint Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="complaint_type">
                                    <option value="">Select Type</option>
                                    @foreach (\App\Models\ComplaintType::orderBy('name')->get() as $type)
                                        <option value="{{ $type->name }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback d-none" id="err-complaint">Please select a complaint type.
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Issue Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="description" id="helpdeskDescription"
                                    rows="3"></textarea>
                                <div class="invalid-feedback d-none" id="err-description">Please describe the issue.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Content (Hidden by default) -->
                    <div id="helpdeskPreviewContent" class="d-none">
                        <div class="alert alert-info py-2">
                            <i class="bi bi-info-circle me-2"></i> Please review your ticket details before final
                            submission.
                        </div>
                        <table class="table table-bordered bg-light">
                            <tr>
                                <th width="35%" class="bg-light">Section</th>
                                <td id="p-section"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Employee</th>
                                <td id="p-employee"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Location</th>
                                <td id="p-location"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Complaint Type</th>
                                <td id="p-type"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Description</th>
                                <td id="p-description"></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary d-none" id="btnBack">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>
                    <button type="button" class="btn btn-primary" id="btnPreview">
                        Next: Preview <i class="bi bi-eye"></i>
                    </button>
                    <button type="submit" class="btn btn-success d-none" id="btnConfirm">
                        Confirm & Submit <i class="bi bi-check-circle"></i>
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    $(function () {
        // Initialize Select2 when the modal is shown
        $('#complaintregisterModal').on('shown.bs.modal', function () {
            $('#sectionSelect').select2({
                placeholder: "🔍 Search Section...",
                allowClear: true,
                tags: true,
                dropdownParent: $('#complaintregisterModal'),
                theme: 'bootstrap-5',
                width: '100%',
            });

            $('#employeeSelect').select2({
                placeholder: "🔍 Search or Type Name...",
                allowClear: true,
                tags: true,
                dropdownParent: $('#complaintregisterModal'),
                theme: 'bootstrap-5',
                width: '100%',
            });

            $('#roomSelect').select2({
                placeholder: "🔍 Search Room...",
                allowClear: true,
                dropdownParent: $('#complaintregisterModal'),
                theme: 'bootstrap-5',
                width: '100%',
            });

            $('#floorSelect').select2({
                placeholder: "Select Floor",
                dropdownParent: $('#complaintregisterModal'),
                theme: 'bootstrap-5',
                width: '100%',
            });
        });

        let allEmployees = [];

        // Load employees
        fetch("/employees/list")
            .then(res => res.json())
            .then(data => {
                allEmployees = data;
                let sectionSelect = $('#sectionSelect');
                let sections = [...new Set(data.filter(emp => emp.section).map(emp => emp.section))];
                
                // Add custom sections
                let customSections = [
                    "Minister's Room",
                    "Residence of Deputy Speaker",
                    "Residence of Secretary",
                    "Residence of Speaker"
                ];
                
                customSections.forEach(cs => {
                    if (!sections.includes(cs)) {
                        sections.push(cs);
                    }
                });
                
                sections.sort();
                
                console.log("Loaded " + sections.length + " sections including customs.");

                // Clear existing options except placeholder
                sectionSelect.empty();
                sectionSelect.append(new Option("Select Section..", "", false, false));

                sections.forEach(section => {
                    sectionSelect.append(new Option(section, section, false, false));
                });
                sectionSelect.trigger('change');
            })
            .catch(err => console.error('Failed to load employee list:', err));

        // Section → Employees
        $('#sectionSelect').on('change', function () {
            let section = $(this).val();
            let employeeSelect = $('#employeeSelect');
            employeeSelect.html('<option value="">Select Employee..</option>');

            if (section) {
                let filteredEmployees = allEmployees.filter(emp => emp.section === section);
                filteredEmployees.forEach(emp => {
                    let option = new Option(
                        emp.name + " (" + emp.pen + ")",
                        emp.pen,
                        false,
                        false
                    );
                    employeeSelect.append(option);
                });
            }
            employeeSelect.trigger('change');
        });

        // Building → Floors
        $('#buildingSelect').on('change', function () {
            let locationId = $(this).val();
            $('#floorSelect').html('<option>Loading...</option>').trigger('change');
            $('#roomSelect').html('<option>Select Room</option>').trigger('change');

            if (locationId) {
                $.get('/get-floors/' + locationId, function (floors) {
                    let options = '<option value=""></option>';
                    floors.forEach(floor => {
                        options += `<option value="${floor}">${floor}</option>`;
                    });
                    $('#floorSelect').html(options).trigger('change');
                });
            }
        });

        // Floor → Rooms
        $('#floorSelect').on('change', function () {
            let locationId = $('#buildingSelect').val();
            let floor = $(this).val();
            $('#roomSelect').html('<option>Loading...</option>').trigger('change');

            if (locationId && floor) {
                $.get(`/get-rooms/${locationId}/${floor}`, function (rooms) {
                    let options = '<option value=""></option>';
                    rooms.forEach(room => {
                        options += `<option value="${room.id}">${room.name}</option>`;
                    });
                    $('#roomSelect').html(options).trigger('change');
                });
            }
        });

        // Multi-step form logic
        $('#btnPreview').on('click', function () {
            if (validateForm()) {
                populatePreview();
                $('#helpdeskFormContent').addClass('d-none');
                $('#helpdeskPreviewContent').removeClass('d-none');
                $('#btnPreview').addClass('d-none');
                $('#btnBack, #btnConfirm').removeClass('d-none');
            }
        });

        $('#btnBack').on('click', function () {
            $('#helpdeskPreviewContent').addClass('d-none');
            $('#helpdeskFormContent').removeClass('d-none');
            $('#btnBack, #btnConfirm').addClass('d-none');
            $('#btnPreview').removeClass('d-none');
        });

        function populatePreview() {
            $('#p-section').text($('#sectionSelect').val());
            $('#p-employee').text($('#employeeSelect').find(':selected').text() || 'Not Specified');

            let building = $('#buildingSelect').find(':selected').text();
            let floor = $('#floorSelect').val();
            let room = $('#roomSelect').find(':selected').text() || 'Not Specified';
            $('#p-location').text(`${building}, Floor: ${floor}, Room: ${room}`);

            $('#p-type').text($('select[name="complaint_type"]').val());
            $('#p-description').text($('#helpdeskDescription').val());
        }

        function validateForm() {
            let valid = true;
            function showError(errId, selectId) {
                $('#' + errId).removeClass('d-none');
                if (selectId) {
                    $('#' + selectId).next('.select2-container').find('.select2-selection').addClass('border border-danger');
                }
            }
            function clearError(errId, selectId) {
                $('#' + errId).addClass('d-none');
                if (selectId) {
                    $('#' + selectId).next('.select2-container').find('.select2-selection').removeClass('border border-danger');
                }
            }

            if (!$('#sectionSelect').val()) { showError('err-section', 'sectionSelect'); valid = false; } else { clearError('err-section', 'sectionSelect'); }
            if (!$('#buildingSelect').val()) { showError('err-building', 'buildingSelect'); valid = false; } else { clearError('err-building', 'buildingSelect'); }
            
            // Floor is now optional - no validation check here
            // Room is now optional - no validation check here

            if (!$('select[name="complaint_type"]').val()) {
                $('#err-complaint').removeClass('d-none');
                $('select[name="complaint_type"]').addClass('is-invalid');
                valid = false;
            } else {
                $('#err-complaint').addClass('d-none');
                $('select[name="complaint_type"]').removeClass('is-invalid');
            }

            if (!$('#helpdeskDescription').val().trim()) {
                $('#err-description').removeClass('d-none');
                $('#helpdeskDescription').addClass('is-invalid');
                valid = false;
            } else {
                $('#err-description').addClass('d-none');
                $('#helpdeskDescription').removeClass('is-invalid');
            }
            return valid;
        }

        $('#helpdeskForm').on('submit', function (e) {
            if ($('#helpdeskPreviewContent').hasClass('d-none')) {
                e.preventDefault();
                $('#btnPreview').trigger('click');
            }
        });

        // Clear errors on change
        $('#sectionSelect, #buildingSelect, #floorSelect, #roomSelect').on('change', function () {
            $(this).removeClass('is-invalid');
            let id = $(this).attr('id');
            if (id === 'sectionSelect') $('#err-section').addClass('d-none');
            if (id === 'buildingSelect') $('#err-building').addClass('d-none');
            if (id === 'roomSelect') $('#err-room').addClass('d-none');
            $(this).next('.select2-container').find('.select2-selection').removeClass('border border-danger');
        });

        $('select[name="complaint_type"]').on('change', function () {
            $(this).removeClass('is-invalid');
            $('#err-complaint').addClass('d-none');
        });

        $('#helpdeskDescription').on('input', function () {
            $(this).removeClass('is-invalid');
            $('#err-description').addClass('d-none');
        });
    });
</script>
