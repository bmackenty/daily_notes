<?php
function human_timing($timestamp) {
    $time = time() - $timestamp;
    
    // Handle future dates
    if ($time < 0) {
        $time = abs($time);
        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day'
        );
        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return 'in ' . $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
        }
        return 'today';
    }
    
    // Handle past dates
    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day'
    );
    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '') . ' ago';
    }
    return 'today';
}
?>

<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/courses">Courses</a></li>
                    <li class="breadcrumb-item"><a href="/courses/<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($section['name']) ?> Daily Notes</li>
                </ol>
            </nav>

            <!-- Quick Links -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-link-45deg text-primary me-2"></i>
                        <h3 class="h5 mb-0">Quick Links</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-3 justify-content-center">
                        <?php if (!empty($course['github_link'])): ?>
                            <a href="<?= htmlspecialchars($course['github_link']) ?>" class="btn btn-outline-secondary" target="_blank">
                                <i class="bi bi-github"></i> GitHub
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($course['lms_link'])): ?>
                            <a href="<?= htmlspecialchars($course['lms_link']) ?>" class="btn btn-outline-primary" target="_blank">
                                <i class="bi bi-mortarboard"></i> LMS
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($course['help_link'])): ?>
                            <a href="<?= htmlspecialchars($course['help_link']) ?>" class="btn btn-outline-info" target="_blank">
                                <i class="bi bi-question-circle"></i> Get Help
                            </a>
                        <?php endif; ?>
                        
                        <a href="/syllabus/<?= $course['id'] ?>" class="btn btn-outline-success">
                            <i class="bi bi-file-text"></i> Syllabus
                        </a>
                        

                        
                        <?php if (!empty($course['library_link'])): ?>
                            <a href="<?= htmlspecialchars($course['library_link']) ?>" class="btn btn-outline-danger" target="_blank">
                                <i class="bi bi-book"></i> Class Library
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-search text-primary me-2"></i>
                            <h3 class="h5 mb-0">Search Notes</h3>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" data-bs-placement="left" title="Use the calendar picker, natural language input, or quick preset buttons to find notes by date">
                            <i class="bi bi-info-circle me-1"></i>
                            How to use
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Date Search Section -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="dateSearch" class="form-label">
                                <i class="bi bi-calendar3 text-primary me-2"></i>
                                Search by Date
                            </label>
                            <div class="input-group">
                                <input type="date" id="dateSearch" class="form-control" 
                                       value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
                                <button type="button" id="searchByDate" class="btn btn-outline-primary">
                                    <i class="bi bi-search"></i> Find Note
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="naturalDateSearch" class="form-label">
                                <i class="bi bi-chat-quote text-primary me-2"></i>
                                Natural Language Search
                            </label>
                            <div class="input-group">
                                <input type="text" id="naturalDateSearch" class="form-control" 
                                       placeholder="e.g., '2 days ago', 'last week', '3 weeks ago'">
                                <button type="button" id="searchByNaturalDate" class="btn btn-outline-primary">
                                    <i class="bi bi-search"></i> Find Note
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Date Presets -->
                    <div class="mb-3">
                        <label class="form-label text-muted small">Quick Presets:</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary date-preset" data-days="1">Yesterday</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary date-preset" data-days="2">2 days ago</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary date-preset" data-days="7">Last week</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary date-preset" data-days="14">2 weeks ago</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary date-preset" data-days="30">Last month</button>
                        </div>
                    </div>

                    <hr class="my-3">
                    
                    <!-- Text Search Section -->
                    <form action="/courses/<?= $course['id'] ?>/sections/<?= $section['id'] ?>/search" method="GET" class="d-flex gap-2">
                        <input type="text" name="q" class="form-control" placeholder="Search notes by content..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Search Content
                        </button>
                    </form>
                </div>
            </div>

  

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="h5 mb-0">
                            <i class="bi bi-journal-text text-primary me-2"></i>
                            Daily Notes for <?= htmlspecialchars($section['name']) ?>
                            <span class="badge bg-secondary ms-2">
                                <?= count($notes) ?> note<?= count($notes) !== 1 ? 's' : '' ?>
                            </span>
                        </h3>
                    </div>
                </div>
                <?php if (!empty($notes)): ?>
                    <div class="card-body mb-4">
                        <h4 class="h6 text-muted mb-3">
                            Most Recent Note 
                            <span class="text-muted">
                                (<?= human_timing(strtotime($notes[0]['date'])) ?>)
                            </span>
                        </h4>
                        <div class="most-recent-note">
                            <h5><?= date('l, F j, Y', strtotime($notes[0]['date'])) ?></h5>
                            <div class="note-content">
                                <?= $notes[0]['content'] ?>
                            </div>
                            <div class="mt-3">
                                <a href="/courses/<?= $course['id'] ?>/sections/<?= $section['id'] ?>/notes/<?= $notes[0]['id'] ?>" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> View Full Note
                                </a>
                                <?php if ($isAdmin): ?>
                                    <a href="/admin/notes/<?= $notes[0]['id'] ?>/edit" 
                                       class="btn btn-outline-warning btn-sm ms-2">
                                        <i class="bi bi-pencil"></i> Edit This Note
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                </div> <!-- close card -->

                <div class="card mb-4">
                    <div class="card-body">
                        <?php if (empty($notes)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                No daily notes have been added for this section yet.
                                <div class="mt-2">
                                    <small class="text-muted">
                                        Notes will appear here once they are created by your instructor.
                                    </small>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($notes as $index => $note): ?>
                                    <?php 
                                    $isCurrentNote = $index === 0;
                                    $noteClass = $isCurrentNote ? 'list-group-item-action' : 'list-group-item-action past-note';
                                    ?>
                                    <a href="/courses/<?= $course['id'] ?>/sections/<?= $section['id'] ?>/notes/<?= $note['id'] ?>" 
                                       class="list-group-item <?= $noteClass ?>">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center flex-grow-1">
                                                <?php if ($isCurrentNote): ?>
                                                    <i class="bi bi-journal-text text-primary me-2"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-journal-text text-muted me-2"></i>
                                                <?php endif; ?>
                                                <small class="text-muted me-2">
                                                    <?= date('l, F j, Y', strtotime($note['date'])) ?>
                                                    <span class="ms-2">(<?= human_timing(strtotime($note['date'])) ?>)</span>
                                                </small>
                                                <span class="<?= $isCurrentNote ? 'fw-bold' : 'text-muted' ?>">
                                                    <?= htmlspecialchars($note['title']) ?>
                                                </span>
                                            </div>
                                            <?php if ($isAdmin && isset($note['academic_year_id'])): ?>
                                                <?php 
                                                $noteYear = $academicYearModel->get($note['academic_year_id']);
                                                $isCurrentYear = $activeYear && $note['academic_year_id'] == $activeYear['id'];
                                                ?>
                                                <span class="badge <?= $isCurrentYear ? 'bg-success' : 'bg-warning' ?>">
                                                    <?= $noteYear ? htmlspecialchars($noteYear['name']) : 'Unknown Year' ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.past-note {
    opacity: 0.7;
    background-color: #f8f9fa;
    border-left: 3px solid #dee2e6;
    transition: all 0.2s ease;
}

.past-note:hover {
    opacity: 0.9;
    background-color: #e9ecef;
    border-left-color: #adb5bd;
}

.past-note .text-muted {
    color: #6c757d !important;
}

.past-note i.bi-journal-text {
    opacity: 0.6;
}

/* Date Search Styles */
.date-preset {
    transition: all 0.2s ease;
    border-radius: 20px;
    font-size: 0.875rem;
}

.date-preset:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.date-preset:active {
    transform: translateY(0);
}

/* Highlighted note styles */
.list-group-item.border-primary {
    background-color: rgba(13, 110, 253, 0.05);
    border-left: 5px solid #0d6efd !important;
    animation: highlightPulse 0.5s ease-in-out;
}

@keyframes highlightPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

/* Date search alert positioning */
.date-search-alert {
    margin-bottom: 1rem;
    border-radius: 8px;
}

/* Instruction alert styling */
.alert-info.bg-light {
    border-left: 4px solid #0dcaf0;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
}

.alert-info.bg-light .alert-heading {
    color: #0c5460;
    font-weight: 600;
}

.alert-info.bg-light .bi-lightbulb {
    font-size: 1.2rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .date-preset {
        font-size: 0.8rem;
        padding: 0.375rem 0.75rem;
    }
    
    .col-md-6 {
        margin-bottom: 1rem;
    }
    
    .alert-info.bg-light {
        padding: 1rem;
    }
    
    .alert-info.bg-light ul {
        padding-left: 1.2rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
    
    // Date Search Functionality
    const dateSearch = document.getElementById('dateSearch');
    const naturalDateSearch = document.getElementById('naturalDateSearch');
    const searchByDateBtn = document.getElementById('searchByDate');
    const searchByNaturalDateBtn = document.getElementById('searchByNaturalDate');
    const datePresets = document.querySelectorAll('.date-preset');
    
    // Search by specific date
    searchByDateBtn.addEventListener('click', function() {
        const selectedDate = dateSearch.value;
        if (selectedDate) {
            searchNotesByDate(selectedDate);
        }
    });
    
    // Search by natural language
    searchByNaturalDateBtn.addEventListener('click', function() {
        const naturalInput = naturalDateSearch.value.trim();
        if (naturalInput) {
            const targetDate = parseNaturalLanguage(naturalInput);
            if (targetDate) {
                searchNotesByDate(targetDate);
            } else {
                showAlert('Could not understand the date format. Try phrases like "2 days ago", "last week", or "3 weeks ago".', 'warning');
            }
        }
    });
    
    // Quick preset buttons
    datePresets.forEach(preset => {
        preset.addEventListener('click', function() {
            const days = parseInt(this.dataset.days);
            const targetDate = new Date();
            targetDate.setDate(targetDate.getDate() - days);
            const dateString = targetDate.toISOString().split('T')[0];
            searchNotesByDate(dateString);
        });
    });
    
    // Parse natural language to date
    function parseNaturalLanguage(input) {
        const lowerInput = input.toLowerCase();
        const today = new Date();
        
        // Handle "ago" patterns
        const agoMatch = lowerInput.match(/(\d+)\s*(day|week|month)s?\s*ago/);
        if (agoMatch) {
            const amount = parseInt(agoMatch[1]);
            const unit = agoMatch[2];
            const targetDate = new Date(today);
            
            if (unit === 'day') {
                targetDate.setDate(today.getDate() - amount);
            } else if (unit === 'week') {
                targetDate.setDate(today.getDate() - (amount * 7));
            } else if (unit === 'month') {
                targetDate.setMonth(today.getMonth() - amount);
            }
            
            return targetDate.toISOString().split('T')[0];
        }
        
        // Handle "last" patterns
        if (lowerInput.includes('last week')) {
            const targetDate = new Date(today);
            targetDate.setDate(today.getDate() - 7);
            return targetDate.toISOString().split('T')[0];
        }
        
        if (lowerInput.includes('last month')) {
            const targetDate = new Date(today);
            targetDate.setMonth(today.getMonth() - 1);
            return targetDate.toISOString().split('T')[0];
        }
        
        if (lowerInput.includes('yesterday')) {
            const targetDate = new Date(today);
            targetDate.setDate(today.getDate() - 1);
            return targetDate.toISOString().split('T')[0];
        }
        
        return null;
    }
    
    // Search notes by date
    function searchNotesByDate(targetDate) {
        
        // Use server-side search for better accuracy
        const courseId = <?= $course['id'] ?>;
        const sectionId = <?= $section['id'] ?>;
        
        const searchUrl = `/courses/${courseId}/sections/${sectionId}/notes/date-search?date=${targetDate}`;
        
        fetch(searchUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Highlight the found note
                    highlightFoundNote(data.note);
                    showAlert(data.message, 'success');
                } else {
                    showAlert(data.message, 'info');
                }
            })
            .catch(error => {
                console.error('Error searching for note:', error);
                // Fallback to client-side search
                clientSideDateSearch(targetDate);
            });
    }
    
    // Client-side fallback search
    function clientSideDateSearch(targetDate) {
        const notes = document.querySelectorAll('.list-group-item');
        let foundNote = null;
        
        notes.forEach(note => {
            const noteText = note.textContent;
            const dateMatch = noteText.match(/(\w+,\s+\w+\s+\d+,\s+\d{4})/);
            if (dateMatch) {
                const noteDate = new Date(dateMatch[1]);
                const targetDateObj = new Date(targetDate);
                
                if (noteDate.toDateString() === targetDateObj.toDateString()) {
                    foundNote = note;
                }
            }
        });
        
        if (foundNote) {
            // Highlight the found note
            notes.forEach(note => note.classList.remove('border-primary', 'border-3'));
            foundNote.classList.add('border-primary', 'border-3');
            
            // Scroll to the note
            foundNote.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Show success message
            showAlert(`Found note for ${targetDate} (client-side search)!`, 'success');
            
            // Remove highlight after 3 seconds
            setTimeout(() => {
                foundNote.classList.remove('border-primary', 'border-3');
            }, 3000);
        } else {
            showAlert(`No note found for ${targetDate} (client-side search).`, 'info');
        }
    }
    
    // Highlight the found note
    function highlightFoundNote(noteData) {
        const notes = document.querySelectorAll('.list-group-item');
        
        // Remove existing highlights
        notes.forEach(note => note.classList.remove('border-primary', 'border-3'));
        
        // Find and highlight the matching note
        notes.forEach(note => {
            const noteText = note.textContent;
            if (noteText.includes(noteData.formatted_date)) {
                note.classList.add('border-primary', 'border-3');
                
                // Scroll to the note
                note.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Remove highlight after 3 seconds
                setTimeout(() => {
                    note.classList.remove('border-primary', 'border-3');
                }, 3000);
                
                return;
            }
        });
    }
    
    // Show alert message
    function showAlert(message, type = 'info') {
        // Remove existing alerts
        const existingAlert = document.querySelector('.date-search-alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} date-search-alert alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="bi bi-info-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insert alert above the search card
        const searchCard = document.querySelector('.card.mb-4');
        searchCard.parentNode.insertBefore(alertDiv, searchCard.nextSibling);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    // Enter key support for natural language search
    naturalDateSearch.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchByNaturalDateBtn.click();
        }
    });
    
    // Enter key support for date search
    dateSearch.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchByDateBtn.click();
        }
    });
});
</script>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?> 