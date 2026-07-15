<style>
    /* WhatsApp UI Styles */
    .whatsapp-container {
        display: flex;
        flex-direction: row-reverse;
        height: calc(100vh - 75px);
        /* Adjust based on navbar height */
        background-color: #f0f2f5;
        border: 1px solid #d1d7db;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
    }

    .chat-sidebar {
        width: 35%;
        background-color: #ffffff;
        border-left: 1px solid #d1d7db;
        display: flex;
        flex-direction: column;
    }

    .chat-header {
        background-color: #f0f2f5;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #d1d7db;
    }

    .chat-search {
        padding: 8px 12px;
        background-color: #ffffff;
        border-bottom: 1px solid #d1d7db;
    }

    .chat-search input,
    .chat-search select {
        width: 100%;
        border-radius: 8px;
        border: none;
        background-color: #f0f2f5;
        padding: 8px 12px;
        outline: none;
        margin-bottom: 8px;
    }

    .chat-tabs {
        display: flex;
        background-color: #ffffff;
        border-bottom: 1px solid #d1d7db;
    }

    .chat-tab {
        flex: 1;
        text-align: center;
        padding: 12px 0;
        cursor: pointer;
        color: #54656f;
        font-weight: 500;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
    }

    .chat-tab.active {
        color: #00a884;
        border-bottom: 3px solid #00a884;
    }

    .chat-list {
        flex: 1;
        overflow-y: auto;
        background-color: #ffffff;
    }

    .chat-item {
        display: flex;
        padding: 12px 16px;
        border-bottom: 1px solid #f2f2f2;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .chat-item:hover {
        background-color: #f5f6f6;
    }

    .chat-item.active {
        background-color: #f0f2f5;
    }

    .chat-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background-color: #dfe5e7;
        margin-right: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #fff;
    }

    .chat-info {
        flex: 1;
        overflow: hidden;
    }

    .chat-title {
        display: flex;
        justify-content: space-between;
        margin-bottom: 4px;
    }

    .chat-name {
        font-weight: 500;
        color: #111b21;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .chat-time {
        font-size: 12px;
        color: #667781;
    }

    .chat-message {
        font-size: 13px;
        color: #667781;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .chat-main {
        width: 65%;
        display: flex;
        flex-direction: column;
        background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
        background-color: #efeae2;
    }

    .chat-main-header {
        background-color: #f0f2f5;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #d1d7db;
    }

    .chat-main-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px 5%;
        display: flex;
        flex-direction: column;
    }

    .message-bubble {
        background-color: #ffffff;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 12px;
        max-width: 80%;
        box-shadow: 0 1px 0.5px rgba(11, 20, 26, .13);
        align-self: flex-start;
    }

    .message-info {
        font-size: 11px;
        color: #667781;
        text-align: right;
        margin-top: 4px;
    }

    .ticket-details table {
        width: 100%;
        font-size: 14px;
    }

    .ticket-details td {
        padding: 4px 0;
    }

    .ticket-details .lbl {
        color: #667781;
        width: 120px;
    }

    .action-footer {
        background-color: #f0f2f5;
        padding: 12px 16px;
        text-align: center;
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #667781;
        text-align: center;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .whatsapp-container {
            height: calc(100vh - 60px);
            margin: 0;
            border-radius: 0;
            border: none;
            overflow: hidden;
        }

        .chat-sidebar {
            width: 100%;
            border-right: none;
            display: flex;
        }

        .show-detail .chat-sidebar {
            display: none;
        }

        .chat-main {
            width: 100%;
            height: 100%;
            display: none;
            flex-direction: column;
            background-color: #efeae2;
        }

        .show-detail .chat-main {
            display: flex;
        }

        .btn-toggle-sidebar {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            padding: 8px;
            margin-right: 8px;
            background: none;
            border: none;
            color: #54656f;
            font-size: 24px;
            cursor: pointer;
        }

        .chat-main-header {
            padding: 10px 12px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .message-bubble {
            max-width: 95%;
        }

        .chat-main-body {
            padding: 15px 10px;
        }
    }

    .chat-tabs-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .chat-tabs-header h3 {
        margin: 0;
        font-weight: 700;
        color: #111b21;
    }

    .chat-tabs-header .chat-tabs {
        border-bottom: none;
        margin-bottom: 0;
        width: auto;
        background: #f0f2f5;
        padding: 4px;
        border-radius: 8px;
        display: flex;
        gap: 4px;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .chat-tabs-header .chat-tab {
        border-radius: 6px;
        padding: 6px 10px;
        margin-left: 0;
        background: transparent;
        border-bottom: none !important;
        font-size: 14px;
        color: #54656f;
        white-space: nowrap;
        transition: all 0.2s ease;
    }
    
    .chat-tabs-header .chat-tab:hover {
        color: #111b21;
    }

    .chat-tabs-header .chat-tab.active {
        background: #ffffff;
        color: #008069;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        font-weight: 600;
    }
    .chat-tabs-header .chat-tab.active .badge {
        background-color: #e6f2f0 !important;
        color: #008069 !important;
    }
    .chat-tabs-header .chat-tab .badge {
        background-color: #d1d7db !important;
        color: #54656f !important;
        border-radius: 12px;
        padding: 4px 8px;
        font-weight: 600;
    }

    @media (min-width: 769px) {
        .whatsapp-container {
            display: flex;
            flex-direction: row-reverse;
            height: calc(100vh - 75px);
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
    }

    @media (max-width: 768px) {
        .chat-tabs-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        .chat-tabs-header .chat-tabs {
            width: 100%;
            overflow-x: auto;
            white-space: nowrap;
            justify-content: flex-start;
        }
    }
</style>

<div class="container-fluid px-0 mt-0 h-100">
    <div class="chat-tabs-header px-2 px-md-0">
        <h3 style="font-size: 1.25rem;">IT Complaint Register Live</h3>
        <div class="chat-tabs">
            <div class="chat-tab active" data-tab="Open" onclick="setTab('Open')">Open <span id="count-Open"
                    class="badge bg-secondary ms-1">0</span></div>
            <div class="chat-tab" data-tab="Assigned" onclick="setTab('Assigned')">Assigned <span
                    id="count-Assigned" class="badge bg-secondary ms-1">0</span></div>
            <div class="chat-tab" data-tab="Pending" onclick="setTab('Pending')">Pending <span
                    id="count-Pending" class="badge bg-secondary ms-1">0</span></div>
            <div class="chat-tab" data-tab="Complaint" onclick="setTab('Complaint')">Complaint <span
                    id="count-Complaint" class="badge bg-secondary ms-1">0</span></div>
            <div class="chat-tab" data-tab="Resolved" onclick="setTab('Resolved')">Resolved <span
                    id="count-Resolved" class="badge bg-secondary ms-1">0</span></div>
        </div>
    </div>

    <div class="whatsapp-container" id="whatsappContainer">

        <!-- Sidebar: Ticket List (Now on the right visually due to row-reverse) -->
        <div class="chat-sidebar">

            <div class="chat-search">
                <div class="row g-2">
                    <div class="col-6">
                        <input type="text" id="filterSearch" placeholder="Search ticket no..." oninput="renderTickets()"
                            class="mb-0">
                    </div>
                    <div class="col-6">
                        <input type="text" id="filterSection" placeholder="Filter Section..." oninput="renderTickets()"
                            class="mb-0">
                    </div>
                </div>
            </div>

            <div class="chat-list" id="ticketList">
                <div class="p-3 text-center text-muted">Loading tickets...</div>
            </div>
        </div>

        <!-- Right Main Panel: Ticket Details -->
        <div class="chat-main" id="ticketDetailPane">
            <div class="empty-state">
                <i class="bi bi-tools" style="font-size: 4rem; color: #aebac1; margin-bottom: 20px;"></i>
                <h4>IT Complaint Register</h4>
                <p>Select a ticket from the left to view details.</p>
            </div>
        </div>

    </div>
</div>

<script>
    let allTickets = [];
    let currentTab = '{{ request()->query('status', 'Open') }}';
    let selectedTicketId = null;
    const currentUserId = {{ auth() -> id() ?? 'null' }};
    const currentUserName = @json(auth()->user()->name ?? 'IT HelpDesk Ticket');
    const canTakeTicket = {{ auth() -> check() && in_array(auth() -> user() -> getRoleName(), ['chm', 'programmer', 'admin', 'superadmin', 'hardwareadmin']) ? 'true' : 'false' }};

    let employeeMap = {};

    function loadEmployees() {
        return fetch('/employees/list')
            .then(res => res.json())
            .then(data => {
                data.forEach(emp => {
                    if (emp.attendanceId) employeeMap[emp.attendanceId] = emp.name;
                    if (emp.pen) employeeMap[emp.pen] = emp.name;
                });
            })
            .catch(err => console.error('Error loading employees:', err));
    }

    function loadTickets() {
        fetch('/complaintregister/live-data')
            .then(res => res.json())
            .then(data => {
                allTickets = data;
                updateCounts();
                
                // Update active tab UI without clearing selection
                document.querySelectorAll('.chat-tab').forEach(el => {
                    el.classList.remove('active');
                    if (el.getAttribute('data-tab') === currentTab) {
                        el.classList.add('active');
                    }
                });
                renderTickets();

                // If a ticket is currently selected, re-render its details
                if (selectedTicketId) {
                    const updatedTicket = allTickets.find(t => t.id === selectedTicketId);
                    if (updatedTicket) {
                        renderTicketDetails(updatedTicket);
                    } else {
                        selectedTicketId = null;
                        clearDetailPane();
                    }
                }
            })
            .catch(err => console.error('Error fetching tickets:', err));
    }

    function updateCounts() {
        const counts = {
            Open: allTickets.filter(t => t.status === 'Open').length,
            Assigned: allTickets.filter(t => t.status === 'Assigned').length,
            Pending: allTickets.filter(t => t.status === 'Pending').length,
            Complaint: allTickets.filter(t => t.status === 'Complaint').length,
            Resolved: allTickets.filter(t => ['Resolved', 'Closed'].includes(t.status)).length
        };

        document.getElementById('count-Open').innerText = counts.Open;
        document.getElementById('count-Assigned').innerText = counts.Assigned;
        document.getElementById('count-Pending').innerText = counts.Pending;
        document.getElementById('count-Complaint').innerText = counts.Complaint;
        document.getElementById('count-Resolved').innerText = counts.Resolved;
    }

    function getEmployeeName(id) {
        if (!id) return 'Not Provided';
        return employeeMap[id] || id; // Return name if in map, otherwise return the ID itself (covers manually typed names)
    }

    function setTab(tabName) {
        currentTab = tabName;
        selectedTicketId = null;
        clearDetailPane();
        showSidebar(); // Ensure sidebar is shown on mobile when switching tabs
        document.querySelectorAll('.chat-tab').forEach(el => {
            el.classList.remove('active');
            if (el.getAttribute('data-tab') === tabName) {
                el.classList.add('active');
            }
        });
        renderTickets();
    }

    function clearDetailPane() {
        document.getElementById('ticketDetailPane').innerHTML = `
            <div class="empty-state">
                <i class="bi bi-tools" style="font-size: 4rem; color: #aebac1; margin-bottom: 20px;"></i>
                <h4>IT Complaint Register</h4>
                <p>Select a ticket from the left to view details.</p>
            </div>
        `;
        showSidebar();
    }

    function showSidebar() {
        document.getElementById('whatsappContainer').classList.remove('show-detail');
    }

    function showDetail() {
        document.getElementById('whatsappContainer').classList.add('show-detail');
    }

    // Format date aesthetically
    function formatTicketDate(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        const now = new Date();

        const isToday = d.getDate() === now.getDate() &&
            d.getMonth() === now.getMonth() &&
            d.getFullYear() === now.getFullYear();

        const yesterday = new Date();
        yesterday.setDate(now.getDate() - 1);
        const isYesterday = d.getDate() === yesterday.getDate() &&
            d.getMonth() === yesterday.getMonth() &&
            d.getFullYear() === yesterday.getFullYear();

        const timeStr = d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0');

        if (isToday) return `Today ${timeStr}`;
        if (isYesterday) return `Yesterday ${timeStr}`;

        const day = d.getDate().toString().padStart(2, '0');
        const month = (d.getMonth() + 1).toString().padStart(2, '0');
        const year = d.getFullYear().toString().slice(-2);
        return `${day}/${month}/${year} ${timeStr}`;
    }

    function renderTickets() {
        const search = document.getElementById('filterSearch').value.toLowerCase();
        const section = document.getElementById('filterSection').value.toLowerCase();

        let filtered = allTickets.filter(t => {
            // Check status array (Done tab can include Resolved and Closed or others)
            let statusMatch = false;
            if (currentTab === 'Open') statusMatch = t.status === 'Open';
            if (currentTab === 'Assigned') statusMatch = t.status === 'Assigned';
            if (currentTab === 'Pending') statusMatch = t.status === 'Pending';
            if (currentTab === 'Complaint') statusMatch = t.status === 'Complaint';
            if (currentTab === 'Resolved') statusMatch = ['Resolved', 'Closed'].includes(t.status);
            if (currentTab === 'All') statusMatch = true;

            let searchMatch = String(t.ticket_no || '').toLowerCase().includes(search) ||
                String(t.employee_id || '').toLowerCase().includes(search);

            let sectionMatch = String(t.section || '').toLowerCase().includes(section);

            return statusMatch && searchMatch && sectionMatch;
        });

        let html = '';
        if (filtered.length === 0) {
            html = `<div class="p-4 text-center text-muted">No tickets found in ${currentTab}.</div>`;
        }

        filtered.forEach(t => {
            try {
                let badgeColor = {
                    'Open': '#f64a4a',
                    'Assigned': '#fbc02d',
                    'In Progress': '#1976d2',
                    'InProgress': '#1976d2',
                    'Pending': '#ff9800',
                    'Complaint': '#e91e63',
                    'Resolved': '#388e3c'
                }[t.status] || '#667781';

                // First letter for avatar
                let sectionStr = String(t.section || 'T');
                let letter = sectionStr.length > 0 ? sectionStr.charAt(0).toUpperCase() : 'T';

                let isActive = selectedTicketId === t.id ? 'active' : '';

                let timeStr = formatTicketDate(t.created_at);

                let techDisplay = t.technician ? `<div class="chat-message mt-1" style="font-size: 11px; color: #008069; font-weight: 500;"><i class="bi bi-person-gear"></i> ${t.technician.name}</div>` : '';

                html += `
                    <div class="chat-item ${isActive}" onclick="selectTicket(${t.id})">
                        <div class="chat-avatar" style="background-color: ${badgeColor}">${letter}</div>
                        <div class="chat-info">
                            <div class="chat-title">
                                <span class="chat-name">${t.ticket_no || t.id}</span>
                                <span class="chat-time">${timeStr}</span>
                            </div>
                            <div class="chat-message">
                                ${t.section || 'N/A'} • ${t.complaint_type || ''}
                            </div>
                            <div class="chat-message mt-1" style="font-size: 11px; opacity: 0.8;">
                                <i class="bi bi-geo-alt"></i> ${(t.location ? t.location.location : t.office_location_id) || '-'} / ${(t.room ? t.room.name : t.room_id) || '-'}
                            </div>
                            ${techDisplay}
                        </div>
                    </div>
                `;
            } catch (err) {
                console.error("Error rendering ticket:", t, err);
            }
        });

        document.getElementById('ticketList').innerHTML = html;
    }

    function selectTicket(id) {
        selectedTicketId = id;
        renderTickets(); // Re-render to show active state on left side

        const t = allTickets.find(t => t.id === id);
        if (!t) return;
        renderTicketDetails(t);
        showDetail(); // Show detail pane on mobile
    }

    function renderTicketDetails(t) {
        let techName = t.technician ? t.technician.name : 'Unassigned';

        let actionButtons = '';
        if (t.status === 'Open' && canTakeTicket) {
            actionButtons = `
                <button class="btn btn-success btn-sm ms-auto" onclick="takeTicket(${t.id})">
                    🙋 Take Ticket
                </button>
            `;
        } else if (['Assigned', 'Pending', 'Complaint', 'Resolved'].includes(t.status) && String(t.technician_id) === String(currentUserId)) {
            actionButtons = `
                <button class="btn btn-primary btn-sm ms-auto" onclick="openStatusModal(${t.id})">
                    🔄 Update Status
                </button>
            `;
        }
        
        if (canTakeTicket) {
            let displayStatus = t.status || '-';
            if (t.status === 'Complaint' && t.vendor_complaint_id) {
                displayStatus = `Complaint (No.${t.vendor_complaint_id})`;
            }

            let waMessage = `*${currentUserName}*\n\n`;
            waMessage += `*Ticket No:* ${t.ticket_no || t.id}`;
            
            let empName = getEmployeeName(t.employee_id);
            if (empName && empName !== '-' && empName !== 'Not Provided') {
                waMessage += `\n*Requested By:* ${empName}`;
            }
            if (t.section && t.section !== '-') {
                waMessage += `\n*Section:* ${t.section}`;
            }
            
            let locParts = [];
            let mainLoc = (t.location ? t.location.location : t.office_location_id);
            if (mainLoc && mainLoc !== '-') locParts.push(mainLoc);
            if (t.floor && t.floor !== '-') locParts.push(t.floor);
            let roomName = (t.room ? t.room.name : t.room_id);
            if (roomName && roomName !== '-') locParts.push(roomName);
            
            let locStr = locParts.join(' / ');
            if (locStr) {
                waMessage += `\n*Location:* ${locStr}`;
            }
            
            if (t.complaint_type && t.complaint_type !== '-') {
                waMessage += `\n*Type:* ${t.complaint_type}`;
            }
            if (t.description && t.description !== '-') {
                waMessage += `\n*Problem:* ${t.description}`;
            }
            let statusEmoji = '';
            if (t.status === 'Resolved') statusEmoji = '\u{2705}';
            else if (t.status === 'Pending') statusEmoji = '\u{23F3}';
            else if (t.status === 'Complaint') statusEmoji = '\u{26A0}';
            else if (t.status === 'Assigned') statusEmoji = '\u{1F7E1}';
            else if (t.status === 'Open') statusEmoji = '\u{1F534}';
            
            let finalStatusStr = `${displayStatus} ${statusEmoji}`.trim();
            waMessage += `\n\n*Status:* ${finalStatusStr}`;

            let waUrl = 'https://wa.me/?text=' + encodeURIComponent(waMessage);

            actionButtons = `
                <a href="${waUrl}" target="_blank" class="btn btn-sm me-2" style="background-color: #25D366; color: white; border: none;" title="Push to WhatsApp">
                    <i class="bi bi-whatsapp"></i>
                </a>
            ` + actionButtons;
        }

        let headerHtml = `
            <div class="chat-main-header w-100 d-flex flex-column" style="background-color: #f0f2f5; padding: 15px 20px; border-bottom: 1px solid #d1d7db;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 w-100">
                    <div class="d-flex align-items-center">
                        <button class="btn-toggle-sidebar me-2 d-md-none" onclick="showSidebar()" style="background:none; border:none; color:#54656f;">
                            <i class="bi bi-arrow-left fs-4"></i>
                        </button>
                        <div class="chat-avatar me-2 me-md-3" style="background-color: #008069; width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                            <i class="bi bi-ticket-detailed fs-5"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold text-dark d-flex flex-wrap align-items-center gap-1">
                                ${t.ticket_no} 
                                <span class="badge" style="background-color: #e6f2f0; color: #008069; font-size: 11px; font-weight:600; padding: 4px 6px;">${t.status}</span>
                            </h5>
                            <div class="text-muted" style="font-size: 12px;">
                                <i class="bi bi-clock me-1"></i> ${new Date(t.created_at).toLocaleString('en-US', {day:'numeric', month:'short', year:'numeric', hour:'numeric', minute:'2-digit'})}
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 mt-md-0 ms-auto">
                        ${actionButtons}
                    </div>
                </div>
            </div>
        `;

        let assignmentStatusStr = `<span class="badge" style="background-color: #fffbeb; color: #b45309; border: 1px solid #fef3c7;"><i class="bi bi-person-dash me-1"></i> Unassigned</span>`;
        if (t.technician) {
            let actionVerb = 'Assigned to';
            if (t.status === 'Pending') actionVerb = 'Pending with';
            if (t.status === 'Complaint') actionVerb = 'Complaint handled by';
            if (t.status === 'Resolved' || t.status === 'Closed') actionVerb = 'Resolved by';
            
            assignmentStatusStr = `<span class="badge" style="background-color: #f0fdfa; color: #0f766e; border: 1px solid #ccfbf1;"><i class="bi bi-person-check-fill me-1"></i> ${actionVerb} ${t.technician.name}</span>`;
        }

        let bodyHtml = `
            <div class="chat-main-body">
                <div class="bg-white p-3 rounded shadow-sm border mb-3" style="border-color: #e9edef !important; max-width: 95%;">
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-4">
                            <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Requested By</small>
                            <span class="text-dark fw-medium" style="font-size: 14px;">${getEmployeeName(t.employee_id)}</span>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Section</small>
                            <span class="text-dark fw-medium" style="font-size: 14px;">${t.section || '-'}</span>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Location / Room</small>
                            <span class="text-dark fw-medium" style="font-size: 14px;">${(t.location ? t.location.location : t.office_location_id) || '-'} / ${t.floor || '-'} / ${(t.room ? t.room.name : t.room_id) || '-'}</span>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Complaint Type</small>
                            <span class="text-dark fw-medium" style="font-size: 14px;">${t.complaint_type || '-'}</span>
                        </div>
                        <div class="col-md-12 col-lg-8">
                            <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Description</small>
                            <span class="text-dark" style="font-size: 14px;">${t.description || '-'}</span>
                        </div>
                        <div class="col-12 mt-3 pt-3 border-top">
                            <div class="d-flex align-items-center">
                                <small class="text-muted text-uppercase fw-bold me-3" style="font-size: 10px; letter-spacing: 0.5px;">Assignment Status</small>
                                ${assignmentStatusStr}
                            </div>
                        </div>
                    </div>
                </div>

                ${(t.status_histories || []).map(h => `
                <div class="message-bubble mt-3 w-100" style="background-color: #d1f4cc; border: 1px solid #c1e4bc;">
                    <div class="d-flex justify-content-between">
                        <strong>Status Update: ${h.status}</strong>
                        <small class="text-muted">${formatTicketDate(h.created_at)}</small>
                    </div>
                    <p class="mb-0 mt-1">Technician: <b>${h.technician ? h.technician.name : 'Unassigned'}</b></p>
                    ${h.remarks ? `<p class="mb-0 mt-1"><b>Remarks:</b> <br/> ${h.remarks}</p>` : ''}
                </div>
                `).join('')}
            </div>
        `;

        let footerHtml = '';
        if (t.status === 'Open' && canTakeTicket) {
            footerHtml = `
                <div class="action-footer">
                    <button class="btn btn-success px-4" onclick="takeTicket(${t.id})">
                        🙋 Take Ticket
                    </button>
                </div>
            `;
        } else if (['Assigned', 'Pending', 'Complaint', 'Resolved'].includes(t.status) && String(t.technician_id) === String(currentUserId)) {
            footerHtml = `
                    <div class="action-footer">
                        <button class="btn btn-primary px-4" onclick="openStatusModal(${t.id})">
                            🔄 Update Status
                        </button>
                    </div>
                `;
        }

        document.getElementById('ticketDetailPane').innerHTML = headerHtml + bodyHtml + footerHtml;
    }

    function takeTicket(id) {
        if (!confirm("Are you sure you want to take this ticket?")) return;

        fetch(`/complaintregister/take-ticket/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const t = allTickets.find(t => t.id === id);
                    if (t) {
                        let assignedName = data.message.replace('Ticket Assigned to ', '');
                        
                        let locationParts = [];
                        if(t.location && t.location.location) locationParts.push(t.location.location);
                        if(t.floor) locationParts.push(t.floor);
                        if(t.room && t.room.name) locationParts.push(t.room.name);
                        let locationStr = locationParts.join(' / ');

                        let waMessage = `*Ticket Taken by ${assignedName}*\n`;
                        waMessage += `*Ticket No:*   [${t.ticket_no}]\n`;
                        waMessage += `*Section:*     ${t.section || 'N/A'}\n`;
                        if (locationStr) waMessage += `*Location / Room:* ${locationStr}\n`;
                        waMessage += `*Complaint Type:* ${t.complaint_type || 'N/A'}\n`;
                        if (t.description) waMessage += `*Description:* _${t.description}_`;
                        
                        document.getElementById('btnAssignWa').href = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(waMessage);
                        document.getElementById('assignSuccessMessage').innerText = data.message;
                        
                        var modal = new bootstrap.Modal(document.getElementById('assignSuccessModal'));
                        modal.show();
                    } else {
                        alert(data.message);
                    }
                    
                    // Refresh data after assignment
                    loadTickets();
                } else {
                    alert(data.message || "Failed to assign ticket.");
                }
            })
            .catch(err => {
                console.error(err);
                alert("Error occurring while assigning ticket.");
            });
    }

    function openStatusModal(id) {
        const t = allTickets.find(t => t.id === id);
        if (!t) return;

        document.getElementById('modalTicketId').value = t.id;
        document.getElementById('modalStatus').value = ['Resolved', 'Pending', 'Complaint', 'Assigned'].includes(t.status) ? t.status : 'Assigned';
        document.getElementById('modalRemarks').value = t.remarks || '';
        document.getElementById('modalVendorComplaintId').value = t.vendor_complaint_id || '';

        toggleComplaintLink();

        var statusModal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
        statusModal.show();
    }

    function toggleComplaintLink() {
        const status = document.getElementById('modalStatus').value;
        const linkDiv = document.getElementById('complaintLinkDiv');
        if (status === 'Complaint') {
            linkDiv.style.display = 'block';
        } else {
            linkDiv.style.display = 'none';
        }
    }

    function submitStatusUpdate() {
        const id = document.getElementById('modalTicketId').value;
        const status = document.getElementById('modalStatus').value;
        const remarks = document.getElementById('modalRemarks').value;
        const vendor_complaint_id = document.getElementById('modalVendorComplaintId').value;
        const vendor_name = document.getElementById('modalVendorName').value;
        const vendor_status = document.getElementById('modalVendorStatus').value;
        const vendor_description = document.getElementById('modalVendorDescription').value;

        if (status === 'Pending' && remarks.trim() === '') {
            alert('Please enter Remarks to explain why this ticket is Pending.');
            return;
        }

        fetch(`/complaintregister/resolve-ticket/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
            },
            body: JSON.stringify({
                status: status,
                remarks: remarks,
                vendor_complaint_id: vendor_complaint_id,
                vendor_name: vendor_name,
                vendor_status: vendor_status,
                vendor_description: vendor_description
            })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    var statusModalEl = document.getElementById('statusUpdateModal');
                    var modal = bootstrap.Modal.getInstance(statusModalEl);
                    modal.hide();
                    
                    const t = allTickets.find(ticket => ticket.id == id);
                    if (t) {
                        let waMessage = `*${data.updated_by}*\n`;
                        waMessage += `*Ticket No:*   [${t.ticket_no}]\n`;
                        if (vendor_complaint_id) {
                            waMessage += `*Vendor:*      ${vendor_name}\n`;
                            waMessage += `*Vendor ID:*   ${vendor_complaint_id}\n`;
                        }
                        if (remarks) {
                            waMessage += `*Remarks:* _${remarks}_\n`;
                        }
                        
                        let statusEmoji = '';
                        if (status === 'Resolved') statusEmoji = '\u{2705}';
                        else if (status === 'Pending') statusEmoji = '\u{23F3}';
                        else if (status === 'Complaint') statusEmoji = '\u{26A0}';
                        else if (status === 'Assigned') statusEmoji = '\u{1F7E1}';
                        else if (status === 'Open') statusEmoji = '\u{1F534}';
                        
                        let finalStatusStr = `${status} ${statusEmoji}`.trim();
                        waMessage += `\n\n*Status:*      ${finalStatusStr}`;
                        
                        document.getElementById('btnUpdateWa').href = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(waMessage);
                        document.getElementById('updateSuccessMessage').innerText = data.message;
                        
                        var updateModal = new bootstrap.Modal(document.getElementById('updateSuccessModal'));
                        updateModal.show();
                    } else {
                        alert(data.message);
                    }
                    
                    loadTickets();
                } else {
                    alert(data.message || "Failed to update status.");
                }
            })
            .catch(err => {
                console.error(err);
                alert("Error occurring while updating status.");
            });
    }

    function resolveTicket(id) {
        // Keep for legacy if needed or remove
    }

    // Interval to refresh automatically
    setInterval(loadTickets, 5000);
    // Initial fetch
    loadEmployees(); // Load employees in parallel
    loadTickets();   // Load tickets immediately
</script>
<!-- Assign Success Modal -->
<div class="modal fade" id="assignSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-5">
                <i class="bi bi-person-check-fill text-success mb-3" style="font-size: 4rem;"></i>
                <h4 class="fw-bold mb-2">Ticket Assigned Successfully!</h4>
                <p class="text-muted mb-4" id="assignSuccessMessage"></p>
                
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2 fw-bold" data-bs-dismiss="modal">Close</button>
                    <a href="#" target="_blank" id="btnAssignWa" class="btn btn-success px-4 py-2 fw-bold" style="background-color: #25D366; border-color: #25D366;" onclick="setTimeout(function(){ bootstrap.Modal.getInstance(document.getElementById('assignSuccessModal')).hide(); }, 500);">
                        <i class="bi bi-whatsapp me-2"></i> Notify IT Cell Group
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Success Modal -->
<div class="modal fade" id="updateSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-5">
                <i class="bi bi-arrow-repeat text-primary mb-3" style="font-size: 4rem;"></i>
                <h4 class="fw-bold mb-2">Status Updated Successfully!</h4>
                <p class="text-muted mb-4" id="updateSuccessMessage"></p>
                
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2 fw-bold" data-bs-dismiss="modal">Close</button>
                    <a href="#" target="_blank" id="btnUpdateWa" class="btn btn-success px-4 py-2 fw-bold" style="background-color: #25D366; border-color: #25D366;" onclick="setTimeout(function(){ bootstrap.Modal.getInstance(document.getElementById('updateSuccessModal')).hide(); }, 500);">
                        <i class="bi bi-whatsapp me-2"></i> Notify IT Cell Group
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-labelledby="statusUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusUpdateModalLabel">Update Ticket Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalTicketId">
                <div class="mb-3">
                    <label for="modalStatus" class="form-label">Select Status</label>
                    <select class="form-select" id="modalStatus" onchange="toggleComplaintLink()">
                        <option value="Assigned">Assigned</option>
                        <option value="Pending">Pending</option>
                        <option value="Complaint">Complaint</option>
                        <option value="Resolved">Resolved</option>
                    </select>
                </div>
                <div class="mb-3" id="complaintLinkDiv" style="display: none; background: #fff5f5; padding: 10px; border-radius: 6px; border: 1px solid #ffcccc;">
                    <label class="form-label text-danger fw-bold"><i class="bi bi-exclamation-triangle"></i> Register Vendor Complaint</label>
                    <div class="mb-2">
                        <a href="https://pmdamc.ihrd.ac.in" target="_blank" class="btn btn-outline-danger btn-sm mb-1">
                            <i class="bi bi-box-arrow-up-right"></i> pmdamc.ihrd.ac.in
                        </a>
                        <small class="text-muted d-block">Please register the complaint on the vendor site before updating the status.</small>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label" style="font-size: 12px;">Vendor</label>
                            <select class="form-select form-select-sm" id="modalVendorName">
                                <option value="IHRD">IHRD</option>
                                <option value="Lipi">Lipi</option>
                                <option value="Aser">Aser</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size: 12px;">Vendor Ticket ID</label>
                            <input type="text" class="form-control form-control-sm" id="modalVendorComplaintId" placeholder="e.g. 10100">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 12px;">Vendor Status</label>
                        <select class="form-select form-select-sm" id="modalVendorStatus">
                            <option value="Pending Spare">Pending Spare</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 12px;">Vendor Complaint Description</label>
                        <textarea class="form-control form-control-sm" id="modalVendorDescription" rows="2" placeholder="Hardware issue description..."></textarea>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="modalRemarks" class="form-label">Remarks</label>
                    <textarea class="form-control" id="modalRemarks" rows="3" placeholder="Enter remarks if any..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitStatusUpdate()">Update Status</button>
            </div>
        </div>
    </div>
</div>
