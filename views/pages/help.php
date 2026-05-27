<?php
require_once __DIR__ . '/../../init/session.php';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-5 mb-5" style="max-width: 800px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0"><i class="fas fa-question-circle"></i> Help & Documentation</h2>
        </div>
        <div class="card-body">
            
            <h4 class="mt-4"><i class="fas fa-file-signature"></i> 1. Creating a Checklist</h4>
            <p>Start by creating a <strong>Checklist</strong>. This is the foundation of your event. Enter basic details such as the programme name, date, department, guest details, and select the items required for the event. Only Coordinators can create a new Checklist.</p>

            <hr>

            <h4 class="mt-4"><i class="fas fa-envelope-open-text"></i> 2. Generating Documents</h4>
            <p>Once your checklist is saved, you can navigate to the Dashboard to access the Event documents. From the checklist dashboard card, you can generate:</p>
            <ul>
                <li><strong>Notice:</strong> Automatically generated based on your checklist details. You can further customize it with AI Magic Fill.</li>
                <li><strong>Invitation Letter:</strong> Send formal invitations to your guests.</li>
                <li><strong>Appreciation Letter:</strong> Generate letters to thank your guests for their attendance.</li>
                <li><strong>Event Report:</strong> The comprehensive final report containing event details, photos, and signatures.</li>
            </ul>

            <hr>

            <h4 class="mt-4"><i class="fas fa-magic"></i> 3. AI Magic Fill</h4>
            <p>Throughout the document editors (Notice, Invitation, Appreciation, Event Report), you'll see a <strong>✨ Magic Auto-Fill</strong> button. Click this to automatically generate professional, context-aware content based on your checklist details! This saves time and ensures a formal tone.</p>

            <hr>

            <h4 class="mt-4"><i class="fas fa-download"></i> 4. Exporting to PDF </h4>
            <p>When you are finished editing an Event Report, you can view the final formatted document. From the view page, you have the option to <strong>Download Word</strong> or <strong>Download PDF</strong>. This ensures you have offline copies ready for printing and signatures.</p>

            <hr>

            <h4 class="mt-4"><i class="fas fa-user-shield"></i> 5. Roles & Approvals</h4>
            <ul>
                <li><strong>Principal:</strong> Manages overall system settings, Departments, and HODs.</li>
                <li><strong>HOD (Head of Department):</strong> Manages Event schedules and assigns Coordinators for their respective departments.</li>
                <li><strong>Coordinator:</strong> The primary user responsible for creating checklists and event documentation.</li>
            </ul>

            <div class="alert alert-info mt-4">
                <strong>Need more help?</strong> Contact the system administrator at <a href="mailto:foundation@shalaka.org">foundation@shalaka.org</a>.
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
