<?php require_once __DIR__ . '/../../views/layouts/header.php'; ?>
<?php require_once __DIR__ . '/../../views/includes/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="row">
        <?php include __DIR__ . '/../includes/quick_doc_action.php'; ?>
        
        <div class="container mt-4">
            <div class="header mt-4">
                <h2 class="text-center">Letter of Appreciation</h2>
            </div>

                <div class="card-body p-4">

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errors as $e): ?>
                                    <li><?= htmlspecialchars($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <style>
                        /* Force all form elements to be clickable */
                        .form-control, .form-check-input, textarea, input[type="text"], input[type="email"], input[type="date"], input[type="time"], input[type="file"] {
                            cursor: text !important;
                            pointer-events: auto !important;
                            z-index: 10 !important;
                            position: relative !important;
                        }
                        
                        .form-check-input {
                            cursor: pointer !important;
                            pointer-events: auto !important;
                            z-index: 10 !important;
                        }
                        
                        .form-check-label {
                            cursor: pointer !important;
                            pointer-events: auto !important;
                            user-select: none;
                        }
                        
                        /* Make buttons clickable */
                        .btn {
                            cursor: pointer !important;
                            pointer-events: auto !important;
                            z-index: 10 !important;
                        }
                        
                        /* Remove any blocking overlays */
                        .container, .card, .card-body, .table, .form-group, .mb-3, .mb-2, .mt-2, .mt-3, .mt-4, .mt-5 {
                            pointer-events: auto !important;
                            z-index: 1 !important;
                        }
                        
                        /* Ensure the entire form is clickable */
                        form {
                            pointer-events: auto !important;
                        }
                    </style>

                    <form method="POST" action="<?= Url::to("/documents/appreciation/$checklist_id") . ($page > 1 ? '?page=' . $page : '') ?>" id="appreciationForm" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= CsrfMiddleware::getToken() ?>">
                        <input type="hidden" name="checklist_id" value="<?= htmlspecialchars($checklist_id) ?>">

                        <!-- Date -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date) ?>" required>
                        </div>

                        <!-- Recipient (display + hidden) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">To</label>
                            <div class="border p-3 bg-light rounded">
                                <strong><?= htmlspecialchars($guestName) ?></strong><br>
                                <?= htmlspecialchars($companyDesignation) ?><br>
                                <?= htmlspecialchars($companyName) ?>
                            </div>
                            <input type="hidden" name="recipient" value="<?= htmlspecialchars($guestName . ' - ' . $companyName . ' - ' . $companyDesignation) ?>">
                        </div>

                        <!-- Subject -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control" value="<?= htmlspecialchars($subject) ?>" required>
                        </div>

                        <!-- Respected -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Respected <span class="text-danger">*</span></label>
                            <input type="text" name="respected" class="form-control" value="<?= htmlspecialchars($respected) ?>" required>
                        </div>

                        <!-- Body -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold m-0">Body <span class="text-danger">*</span></label>
                                <button type="button" class="btn btn-warning btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#aiAppreciationModal">
                                    <i class="bi bi-magic"></i> Auto-Fill with AI
                                </button>
                            </div>
                            <textarea name="body" rows="10" class="form-control ckeditor-field" required><?= htmlspecialchars($body ?? '') ?></textarea>
                            
                        </div>

                        <!-- Readonly Info -->
                        <div class="row g-3 mb-4">
                            <?php 
                            // Check if HOD name should be shown (only if it's not the default 'N/A' from multiple departments)
                            $show_hod = (!empty($hod_name) && $hod_name !== 'N/A');
                            ?>
                            
                            <?php if ($show_hod): ?>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">HOD Name</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($hod_name) ?>" readonly>
                                </div>
                            <?php endif; ?>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Coordinator</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($coordinator_name) ?>" readonly>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-3">
                            <?php if ($existingAppreciation): ?>
                                <button type="submit" class="btn btn-warning btn-lg px-5">Update Appreciation</button>
                                <a href="<?= Url::to("/documents/view/appreciation/$checklist_id") . ($page > 1 ? '?page=' . $page : '') ?>" class="btn btn-info btn-lg px-5">
                                    View Appreciation
                                </a>
                            <?php else: ?>
                                <button type="submit" class="btn btn-primary btn-lg px-5">Save Appreciation</button>
                            <?php endif; ?>
                        </div>
                       
                    </form>

                    <!-- Pagination -->
                    <?php if ($totalGuests > 1): ?>
                        <div class="mt-5 text-center">
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <?php for ($i = 1; $i <= $totalGuests; $i++): ?>
                                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                            <a class="page-link" href="<?= Url::to("/documents/appreciation/$checklist_id?page=$i") ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AI Appreciation Modal (outside container to avoid z-index issues) -->
<div class="modal fade" id="aiAppreciationModal" tabindex="-1" aria-labelledby="aiAppreciationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="aiAppreciationModalLabel"><i class="bi bi-robot"></i> AI Appreciation Letter Generator</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Provide a few details below. The AI will generate a formal subject line and letter body.</p>

                <div class="mb-3">
                    <label class="form-label fw-bold">Guest Name</label>
                    <input type="text" class="form-control" id="aiGuestName" value="<?= htmlspecialchars($guestName ?? '') ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Designation</label>
                    <input type="text" class="form-control" id="aiDesignation" value="<?= htmlspecialchars($companyDesignation ?? '') ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Company / Organization</label>
                    <input type="text" class="form-control" id="aiCompany" value="<?= htmlspecialchars($companyName ?? '') ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Event Name</label>
                    <input type="text" class="form-control" id="aiEventName" value="<?= htmlspecialchars($programme_name ?? '') ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Event Date</label>
                    <input type="text" class="form-control" id="aiEventDate" value="<?= htmlspecialchars($date ?? '') ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Guest's Contribution (what they did at the event)</label>
                    <input type="text" class="form-control" id="aiContribution" placeholder="e.g., Delivered keynote on AI trends, Conducted hands-on workshop">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">College Name</label>
                    <input type="text" class="form-control" id="aiCollegeName" value="KSE" placeholder="e.g., KSE College">
                </div>

                <div id="aiAppreciationLoading" class="text-center d-none my-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 fw-bold text-primary">AI is generating the letter... Please wait.</p>
                </div>
                <div id="aiAppreciationError" class="alert alert-danger d-none mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary fw-bold" id="generateAppreciationBtn" onclick="generateAppreciationLetter()">Generate Letter</button>
            </div>
        </div>
    </div>
</div>

<script>
async function generateAppreciationLetter() {
    const btn = document.getElementById('generateAppreciationBtn');
    const loading = document.getElementById('aiAppreciationLoading');
    const errorAlert = document.getElementById('aiAppreciationError');

    const data = {
        guestName: document.getElementById('aiGuestName').value,
        designation: document.getElementById('aiDesignation').value,
        company: document.getElementById('aiCompany').value,
        eventName: document.getElementById('aiEventName').value,
        eventDate: document.getElementById('aiEventDate').value,
        contribution: document.getElementById('aiContribution').value,
        collegeName: document.getElementById('aiCollegeName').value
    };

    btn.disabled = true;
    loading.classList.remove('d-none');
    errorAlert.classList.add('d-none');

    try {
        const response = await fetch('<?= Url::to("/api/generate-appreciation") ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.error || 'Failed to generate letter');
        }

        // Fill Subject field
        if (result.subject) {
            const subjectInput = document.querySelector('input[name="subject"]');
            if (subjectInput) subjectInput.value = result.subject;
        }

        // Fill Body (CKEditor or textarea)
        if (result.body) {
            if (window.editors && window.editors['body']) {
                window.editors['body'].setData(result.body);
            } else {
                const textarea = document.querySelector('textarea[name="body"]');
                if (textarea) textarea.value = result.body;
            }
        }

        // Close modal
        const modalEl = document.getElementById('aiAppreciationModal');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();

    } catch (error) {
        errorAlert.textContent = error.message;
        errorAlert.classList.remove('d-none');
    } finally {
        btn.disabled = false;
        loading.classList.add('d-none');
    }
}
</script>

<?php require_once __DIR__ . '/../../views/includes/footer.php'; ?>
