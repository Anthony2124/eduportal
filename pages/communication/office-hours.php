<?php
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/communication/office-hours.php';
$user = require_page_access($page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Office Hours - EduPortal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <?php app_shell_styles(); ?>
</head>
<body>

<?php app_sidebar($user, $page); ?>

    <!-- Main Content -->
    <div class="main-content app-main">
<?php app_topbar($user, ['placeholder' => 'Search professor, department, or subject...']); ?>

        <!-- Page Content -->
        <main class="content-area">
            <div class="page-header">
                <h1>Virtual Office Hours</h1>
                <p class="page-subtitle">Book one-on-one consultation time with your professors for academic guidance</p>
            </div>

            <!-- My Upcoming Appointments Card -->
            <div class="card" style="margin-bottom: 30px;">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-calendar-check text-primary"></i>
                        <span>My Upcoming Appointments</span>
                    </div>
                    <span class="badge badge-primary">2 Confirmed</span>
                </div>
                <div class="card-body">
                    <div id="appointments-list" style="display: flex; flex-direction: column; gap: 14px;">
                        <!-- Appointment 1 -->
                        <div class="appointment-item" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: var(--light); border: 1px solid var(--gray-200); border-radius: var(--radius-sm); flex-wrap: wrap; gap: 12px;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div style="width: 44px; height: 44px; border-radius: var(--radius-full); background: rgba(79, 70, 229, 0.12); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                                    <i class="fa-solid fa-video"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: var(--dark); font-size: 0.95rem;">
                                        Prof. Annie Fe Mendez • Data Structures (CS 201)
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--gray-600); margin-top: 2px;">
                                        <i class="fa-regular fa-calendar"></i> Oct 14, 2026 at 2:00 PM • <i class="fa-regular fa-comment"></i> Topic: <strong>Midterm review</strong>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <button class="btn btn-sm btn-primary" onclick="showToast('Connecting to Zoom meeting room with Prof. Mendez...', 'success')">
                                    <i class="fa-solid fa-video"></i> Join Meeting
                                </button>
                                <button class="btn btn-sm btn-outline" onclick="showToast('Appointment rescheduled link sent to your email.', 'info')">
                                    Reschedule
                                </button>
                            </div>
                        </div>

                        <!-- Appointment 2 -->
                        <div class="appointment-item" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: var(--light); border: 1px solid var(--gray-200); border-radius: var(--radius-sm); flex-wrap: wrap; gap: 12px;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div style="width: 44px; height: 44px; border-radius: var(--radius-full); background: rgba(245, 158, 11, 0.12); color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                                    <i class="fa-solid fa-video"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: var(--dark); font-size: 0.95rem;">
                                        Prof. Einjela Picart • Ethics (MATH 202)
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--gray-600); margin-top: 2px;">
                                        <i class="fa-regular fa-calendar"></i> Oct 16, 2026 at 3:00 PM • <i class="fa-regular fa-comment"></i> Topic: <strong>Problem set help</strong>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <span class="badge badge-outline" style="align-self: center;">
                                    <i class="fa-solid fa-clock"></i> Starts in 3 days
                                </span>
                                <button class="btn btn-sm btn-outline" onclick="showToast('Appointment rescheduled link sent to your email.', 'info')">
                                    Reschedule
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Office Hours Section -->
            <div style="margin-bottom: 20px;">
                <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--dark); margin-bottom: 6px;">Available Office Hours</h2>
                <p style="font-size: 0.88rem; color: var(--gray-500); margin-bottom: 20px;">Select a faculty member below to reserve an open consultation slot.</p>

                <div class="grid-3">
                    <!-- Prof 1: Prof. Annie Fe Mendez -->
                    <div class="prof-card">
                        <div>
                            <div class="prof-header">
                                <div class="prof-avatar" style="background: linear-gradient(135deg, #4F46E5, #818CF8);">MS</div>
                                <div>
                                    <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--dark);">Prof. Annie Fe Mendez</h3>
                                    <div style="font-size: 0.82rem; color: var(--primary); font-weight: 600;">CS 201 • Computer Science</div>
                                    <div class="prof-rating">
                                        <i class="fa-solid fa-star"></i> 4.8 / 5.0 (94 reviews)
                                    </div>
                                </div>
                            </div>

                            <div style="background: var(--light); padding: 12px; border-radius: var(--radius-sm); margin-bottom: 16px;">
                                <div class="slot-item">
                                    <i class="fa-regular fa-calendar-days"></i>
                                    <span>Schedule: <strong>MWF 2:00-4:00 PM</strong></span>
                                </div>
                                <div class="slot-item">
                                    <i class="fa-solid fa-circle-check text-success"></i>
                                    <span>Next available: <strong class="text-success">Oct 14, 2:30 PM</strong></span>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-primary btn-block open-book-btn" 
                                data-prof="Prof. Annie Fe Mendez" 
                                data-course="CS 201 (Data Structures)" 
                                data-nextslot="2026-10-14T14:30"
                                data-modal-open="book-appointment-modal"
                                style="width: 100%;">
                            <i class="fa-regular fa-calendar-plus"></i> Book Appointment
                        </button>
                    </div>

                    <!-- Prof 2: Prof. Einjela Picart -->
                    <div class="prof-card">
                        <div>
                            <div class="prof-header">
                                <div class="prof-avatar" style="background: linear-gradient(135deg, #F59E0B, #D97706);">JG</div>
                                <div>
                                    <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--dark);">Prof. Einjela Picart</h3>
                                    <div style="font-size: 0.82rem; color: #d97706; font-weight: 600;">MATH 202 • Mathematics</div>
                                    <div class="prof-rating">
                                        <i class="fa-solid fa-star"></i> 4.9 / 5.0 (112 reviews)
                                    </div>
                                </div>
                            </div>

                            <div style="background: var(--light); padding: 12px; border-radius: var(--radius-sm); margin-bottom: 16px;">
                                <div class="slot-item">
                                    <i class="fa-regular fa-calendar-days"></i>
                                    <span>Schedule: <strong>TTh 1:00-3:00 PM</strong></span>
                                </div>
                                <div class="slot-item">
                                    <i class="fa-solid fa-circle-check text-success"></i>
                                    <span>Next available: <strong class="text-success">Oct 15, 1:00 PM</strong></span>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-primary btn-block open-book-btn" 
                                data-prof="Prof. Einjela Picart" 
                                data-course="MATH 202 (Ethics)" 
                                data-nextslot="2026-10-15T13:00"
                                data-modal-open="book-appointment-modal"
                                style="width: 100%;">
                            <i class="fa-regular fa-calendar-plus"></i> Book Appointment
                        </button>
                    </div>

                    <!-- Prof 3: Prof. Glenn Kennedy Marzo -->
                    <div class="prof-card">
                        <div>
                            <div class="prof-header">
                                <div class="prof-avatar" style="background: linear-gradient(135deg, #06B6D4, #3B82F6);">AR</div>
                                <div>
                                    <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--dark);">Prof. Glenn Kennedy Marzo</h3>
                                    <div style="font-size: 0.82rem; color: var(--secondary); font-weight: 600;">CS 203 • Algorithms & Complexity</div>
                                    <div class="prof-rating">
                                        <i class="fa-solid fa-star"></i> 4.7 / 5.0 (82 reviews)
                                    </div>
                                </div>
                            </div>

                            <div style="background: var(--light); padding: 12px; border-radius: var(--radius-sm); margin-bottom: 16px;">
                                <div class="slot-item">
                                    <i class="fa-regular fa-calendar-days"></i>
                                    <span>Schedule: <strong>MWF 3:00-5:00 PM</strong></span>
                                </div>
                                <div class="slot-item">
                                    <i class="fa-solid fa-circle-check text-success"></i>
                                    <span>Next available: <strong class="text-success">Oct 14, 3:00 PM</strong></span>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-primary btn-block open-book-btn" 
                                data-prof="Prof. Glenn Kennedy Marzo" 
                                data-course="CS 203 (Algorithms)" 
                                data-nextslot="2026-10-14T15:00"
                                data-modal-open="book-appointment-modal"
                                style="width: 100%;">
                            <i class="fa-regular fa-calendar-plus"></i> Book Appointment
                        </button>
                    </div>

                    <!-- Prof 4: Prof. Dale Lyko Abion -->
                    <div class="prof-card">
                        <div>
                            <div class="prof-header">
                                <div class="prof-avatar" style="background: linear-gradient(135deg, #10B981, #059669);">CC</div>
                                <div>
                                    <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--dark);">Prof. Dale Lyko Abion</h3>
                                    <div style="font-size: 0.82rem; color: #059669; font-weight: 600;">DTP 101 • Desktop Publishing</div>
                                    <div class="prof-rating">
                                        <i class="fa-solid fa-star"></i> 4.6 / 5.0 (68 reviews)
                                    </div>
                                </div>
                            </div>

                            <div style="background: var(--light); padding: 12px; border-radius: var(--radius-sm); margin-bottom: 16px;">
                                <div class="slot-item">
                                    <i class="fa-regular fa-calendar-days"></i>
                                    <span>Schedule: <strong>TTh 4:00-5:30 PM</strong></span>
                                </div>
                                <div class="slot-item">
                                    <i class="fa-solid fa-circle-check text-success"></i>
                                    <span>Next available: <strong class="text-success">Oct 15, 4:00 PM</strong></span>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-primary btn-block open-book-btn" 
                                data-prof="Prof. Dale Lyko Abion" 
                                data-course="DTP 101 (Desktop Publishing)" 
                                data-nextslot="2026-10-15T16:00"
                                data-modal-open="book-appointment-modal"
                                style="width: 100%;">
                            <i class="fa-regular fa-calendar-plus"></i> Book Appointment
                        </button>
                    </div>

                    <!-- Prof 5: Prof. Christopher Marmol -->
                    <div class="prof-card">
                        <div>
                            <div class="prof-header">
                                <div class="prof-avatar" style="background: linear-gradient(135deg, #EC4899, #8B5CF6);">LT</div>
                                <div>
                                    <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--dark);">Prof. Christopher Marmol</h3>
                                    <div style="font-size: 0.82rem; color: #8B5CF6; font-weight: 600;">NET 101 • Networking I</div>
                                    <div class="prof-rating">
                                        <i class="fa-solid fa-star"></i> 4.9 / 5.0 (130 reviews)
                                    </div>
                                </div>
                            </div>

                            <div style="background: var(--light); padding: 12px; border-radius: var(--radius-sm); margin-bottom: 16px;">
                                <div class="slot-item">
                                    <i class="fa-regular fa-calendar-days"></i>
                                    <span>Schedule: <strong>MWF 11:00 AM-12:00 PM</strong></span>
                                </div>
                                <div class="slot-item">
                                    <i class="fa-solid fa-circle-check text-success"></i>
                                    <span>Next available: <strong class="text-success">Oct 14, 11:00 AM</strong></span>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-primary btn-block open-book-btn" 
                                data-prof="Prof. Christopher Marmol" 
                                data-course="NET 101 (Networking I)" 
                                data-nextslot="2026-10-14T11:00"
                                data-modal-open="book-appointment-modal"
                                style="width: 100%;">
                            <i class="fa-regular fa-calendar-plus"></i> Book Appointment
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Booking Modal -->
    <div class="modal-overlay" id="book-appointment-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-calendar-plus text-primary"></i> Book Consultation Slot</h3>
                <button class="modal-close-btn" data-modal-close aria-label="Close Modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form id="booking-form" data-toast-success="Office hours appointment booked! Check your university email for the calendar invitation.">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Professor</label>
                        <input type="text" id="modal-prof-name" class="form-control" readonly value="Prof. Annie Fe Mendez">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="booking-date">Select Time Slot</label>
                        <input type="datetime-local" id="booking-date" class="form-control" value="2026-10-14T14:30" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="booking-topic">Topic / Specific Question</label>
                        <textarea id="booking-topic" class="form-textarea" placeholder="Describe the topics, questions, or project items you would like to discuss..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Preferred Mode</label>
                        <div style="display: flex; gap: 16px; margin-top: 6px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.9rem;">
                                <input type="radio" name="preferred-mode" value="video" checked>
                                <span><i class="fa-solid fa-video text-primary"></i> Video Call (Zoom)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.9rem;">
                                <input type="radio" name="preferred-mode" value="chat">
                                <span><i class="fa-solid fa-comments text-secondary"></i> Portal Chat</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-check"></i> Confirm Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../js/main.js?v=9"></script>
    <script>
        // Connect data-prof to modal inputs
        document.querySelectorAll('.open-book-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const prof = btn.getAttribute('data-prof');
                const nextslot = btn.getAttribute('data-nextslot');
                if (prof) {
                    document.getElementById('modal-prof-name').value = prof;
                }
                if (nextslot) {
                    document.getElementById('booking-date').value = nextslot;
                }
            });
        });

        // Add dynamically to appointments list on submission
        const bookingForm = document.getElementById('booking-form');
        if (bookingForm) {
            bookingForm.addEventListener('submit', (e) => {
                const prof = document.getElementById('modal-prof-name').value;
                const topic = document.getElementById('booking-topic').value;
                const slot = document.getElementById('booking-date').value;
                const appList = document.getElementById('appointments-list');

                if (appList && prof && topic) {
                    const formattedDate = new Date(slot).toLocaleString('en-US', {
                        month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit'
                    });
                    const item = document.createElement('div');
                    item.className = 'appointment-item';
                    item.style.cssText = "display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: var(--radius-sm); flex-wrap: wrap; gap: 12px;";
                    item.innerHTML = `
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div style="width: 44px; height: 44px; border-radius: var(--radius-full); background: rgba(16, 185, 129, 0.2); color: #047857; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <div>
                                <div style="font-weight: 700; color: var(--dark); font-size: 0.95rem;">${prof}</div>
                                <div style="font-size: 0.8rem; color: var(--gray-600); margin-top: 2px;">
                                    <i class="fa-regular fa-calendar"></i> ${formattedDate} • <i class="fa-regular fa-comment"></i> Topic: <strong>${topic}</strong>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Confirmed</span>
                        </div>
                    `;
                    appList.insertBefore(item, appList.firstChild);
                }
            });
        }
    </script>
</body>
</html>
