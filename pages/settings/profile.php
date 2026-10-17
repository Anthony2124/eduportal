<?php
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/settings/profile.php';
$user = require_page_access($page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - EduPortal</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Base Stylesheet -->
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        /* Settings Page-Specific Styles */
        .settings-layout-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 1.5rem;
            align-items: start;
        }

        /* Avatar Uploader */
        .profile-avatar-row {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .large-avatar-wrap {
            position: relative;
            width: 90px;
            height: 90px;
        }

        .large-avatar-img {
            width: 100%;
            height: 100%;
            border-radius: var(--radius-full);
            object-fit: cover;
            border: 3px solid var(--white);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .avatar-edit-overlay {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 30px;
            height: 30px;
            background: var(--primary);
            color: var(--white);
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            cursor: pointer;
            border: 2px solid var(--white);
            box-shadow: var(--shadow-sm);
        }

        .avatar-action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .avatar-help-text {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        /* Form Layout */
        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }

        .form-row-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1.25rem;
        }

        .academic-badge-banner {
            background: #F8FAFC;
            border-radius: var(--radius-md);
            padding: 1rem 1.25rem;
            border: 1px solid var(--gray-200);
            margin-bottom: 1.5rem;
        }

        .academic-pills-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        /* Security Specifics */
        .security-section-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .session-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.875rem 0;
            border-bottom: 1px solid var(--gray-100);
        }
        .session-item:last-child {
            border-bottom: none;
        }

        .session-device-col {
            display: flex;
            align-items: center;
            gap: 0.875rem;
        }

        .session-device-icon {
            width: 38px;
            height: 38px;
            border-radius: var(--radius-md);
            background: var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.125rem;
            color: var(--gray-600);
        }

        /* Login History Table */
        .login-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8125rem;
            margin-top: 0.75rem;
        }

        .login-table th {
            text-align: left;
            padding: 0.625rem 0.75rem;
            background: var(--gray-100);
            color: var(--gray-600);
            font-weight: 600;
            border-bottom: 1px solid var(--gray-200);
        }

        .login-table td {
            padding: 0.625rem 0.75rem;
            border-bottom: 1px solid var(--gray-100);
            color: var(--gray-700);
        }

        /* Radio Card Selection */
        .radio-card-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .radio-card {
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-md);
            padding: 1rem;
            cursor: pointer;
            transition: all var(--transition-fast);
            position: relative;
        }

        .radio-card.active, .radio-card:hover {
            border-color: var(--primary);
            background: rgba(79, 70, 229, 0.03);
        }

        .radio-card-title {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .radio-card-desc {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        /* Range Slider */
        .range-slider-wrap {
            margin: 1.25rem 0;
        }

        .range-ticks {
            display: flex;
            justify-content: space-between;
            margin-top: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--gray-500);
        }

        /* Danger Zone */
        .danger-zone-box {
            border: 1px solid rgba(239, 68, 68, 0.3);
            background: rgba(239, 68, 68, 0.03);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        /* Role Card */
        .role-badge-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid var(--gray-200);
            padding: 1.25rem;
            box-shadow: var(--shadow-sm);
        }

        .role-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--gray-100);
        }

        .role-avatar {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-full);
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .role-title h3 {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--dark);
        }

        .role-permission-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .role-permission-item {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            font-size: 0.75rem;
            color: var(--gray-600);
            line-height: 1.4;
        }

        .role-permission-item i {
            margin-top: 2px;
            font-size: 0.75rem;
        }

        @media (max-width: 1024px) {
            .settings-layout-grid {
                grid-template-columns: 1fr;
            }
            .radio-card-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .form-row-2, .form-row-3 {
                grid-template-columns: 1fr;
            }
            .profile-avatar-row {
                flex-direction: column;
                align-items: flex-start;
            }
            .danger-zone-box {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
    <?php app_shell_styles(); ?>
</head>
<body>
<?php app_sidebar($user, $page); ?>

    <!-- Main Content -->
    <div class="main-content app-main">
<?php app_topbar($user, ['placeholder' => 'Search settings, preferences, security...']); ?>

        <!-- Page Content -->
        <div class="content-area">
            <div class="page-header">
                <h1>Account Settings</h1>
                <p class="page-subtitle">Manage your profile and security preferences</p>
            </div>

            <!-- Two-Column Layout (Settings Content + Role Info Side Card) -->
            <div class="settings-layout-grid tab-container">
                <!-- Left: Main Tabbed Settings Area -->
                <div>
                    <!-- Settings Nav Tabs -->
                    <div class="nav-tabs">
                        <button class="tab-btn active" data-tab="tab-profile">
                            <i class="fa-solid fa-user"></i>
                            <span>Profile</span>
                        </button>
                        <button class="tab-btn" data-tab="tab-security">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>Security</span>
                        </button>
                        <button class="tab-btn" data-tab="tab-notifications">
                            <i class="fa-solid fa-bell"></i>
                            <span>Notifications</span>
                        </button>
                        <button class="tab-btn" data-tab="tab-accessibility">
                            <i class="fa-solid fa-universal-access"></i>
                            <span>Accessibility</span>
                        </button>
                        <button class="tab-btn" data-tab="tab-privacy">
                            <i class="fa-solid fa-lock"></i>
                            <span>Privacy</span>
                        </button>
                    </div>

                    <!-- TAB 1: PROFILE -->
                    <div class="tab-content active" id="tab-profile">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">
                                    <i class="fa-solid fa-address-card" style="color: var(--primary);"></i>
                                    Personal & Academic Information
                                </h2>
                                <span class="badge badge-success">Verified Student</span>
                            </div>

                            <form id="profileForm" onsubmit="saveProfile(event)">
                                <!-- Profile Photo Row -->
                                <div class="profile-avatar-row">
                                    <div class="large-avatar-wrap">
                                        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="" data-user="avatar" class="large-avatar-img" id="avatarPreview">
                                        <label for="avatarInput" class="avatar-edit-overlay" title="Change photo">
                                            <i class="fa-solid fa-camera"></i>
                                        </label>
                                        <input type="file" id="avatarInput" accept="image/*" style="display: none;" onchange="previewAvatar(event)">
                                    </div>
                                    <div class="avatar-action-buttons">
                                        <div style="display: flex; gap: 0.5rem;">
                                            <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('avatarInput').click()">
                                                <i class="fa-solid fa-upload"></i> Upload New Photo
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="removePhoto()">
                                                <i class="fa-solid fa-trash-can"></i> Remove
                                            </button>
                                        </div>
                                        <span class="avatar-help-text">JPG, PNG, or GIF. Max 2MB. Institutional ID format recommended.</span>
                                    </div>
                                </div>

                                <!-- Academic Info Banner -->
                                <div class="academic-badge-banner">
                                    <div style="font-size: 0.8125rem; font-weight: 700; color: var(--dark);">
                                        <i class="fa-solid fa-graduation-cap" style="color: var(--primary);"></i> Institutional Academic Enrollment (Read-Only)
                                    </div>
                                    <div class="academic-pills-row">
                                        <span class="badge badge-primary">Program: BS Computer Science</span>
                                        <span class="badge badge-cyan">Year Level: 3rd Year</span>
                                        <span class="badge badge-warning">Section: CS-3A</span>
                                        <span class="badge badge-success">Status: Regular / Good Standing</span>
                                    </div>
                                </div>

                                <!-- Form Fields -->
                                <div class="form-row-2">
                                    <div class="form-group">
                                        <label class="form-label" for="fullName">Full Name</label>
                                        <input type="text" id="fullName" class="form-control" value="" data-user-value="fullName" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="studentId">Student ID</label>
                                        <input type="text" id="studentId" class="form-control" value="" data-user-value="studentId" readonly title="Institutional ID cannot be edited directly">
                                    </div>
                                </div>

                                <div class="form-row-2">
                                    <div class="form-group">
                                        <label class="form-label" for="email">Linked Gmail</label>
                                        <input type="email" id="email" class="form-control" value="" data-user-value="email" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="phone">Phone Number</label>
                                        <input type="tel" id="phone" class="form-control" value="" placeholder="+63 9XX XXX XXXX" required>
                                    </div>
                                </div>

                                <div class="form-row-2">
                                    <div class="form-group">
                                        <label class="form-label" for="dob">Date of Birth</label>
                                        <input type="date" id="dob" class="form-control" value="">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="address">Residential Address</label>
                                        <input type="text" id="address" class="form-control" value="" placeholder="House no., street, city">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="bio">Student Bio</label>
                                    <textarea id="bio" class="form-control" rows="3" placeholder="Tell your peers and professors a little about your academic interests..."></textarea>
                                </div>

                                <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--gray-200);">
                                    <button type="button" class="btn btn-secondary" onclick="resetProfileForm()">Cancel</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-floppy-disk"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- TAB 2: SECURITY -->
                    <div class="tab-content" id="tab-security">
                        <!-- Change Password Card -->
                        <div class="card" style="margin-bottom: 1.5rem;">
                            <div class="card-header">
                                <h2 class="card-title">
                                    <i class="fa-solid fa-key" style="color: var(--primary);"></i>
                                    Change Password
                                </h2>
                            </div>
                            <form onsubmit="handlePasswordUpdate(event)">
                                <div class="form-group">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" class="form-control" placeholder="••••••••••••" required>
                                </div>
                                <div class="form-row-2">
                                    <div class="form-group">
                                        <label class="form-label">New Password</label>
                                        <input type="password" class="form-control" placeholder="At least 8 characters" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" placeholder="Re-type new password" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-lock"></i> Update Password
                                </button>
                            </form>
                        </div>

                        <!-- Two-Factor Authentication -->
                        <div class="card" style="margin-bottom: 1.5rem;">
                            <div class="card-header">
                                <h2 class="card-title">
                                    <i class="fa-solid fa-shield-halved" style="color: var(--success);"></i>
                                    Two-Factor Authentication (2FA)
                                </h2>
                                <span class="badge badge-success">Status: Enabled ✅</span>
                            </div>

                            <p style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 1rem;">
                                Two-factor authentication adds an extra layer of security to your student account. In addition to your password, you will be prompted for a 6-digit code.
                            </p>

                            <div style="background: var(--light); border: 1px solid var(--gray-200); border-radius: var(--radius-md); padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i class="fa-solid fa-mobile-screen-button" style="font-size: 1.5rem; color: var(--primary);"></i>
                                    <div>
                                        <strong style="font-size: 0.875rem; color: var(--dark);">Method: Authenticator App</strong>
                                        <div style="font-size: 0.75rem; color: var(--gray-500);">Google Authenticator, Duo, or Microsoft Authenticator</div>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button class="btn btn-outline btn-sm" onclick="showToast('Change method dialog opened', 'indigo')">Change Method</button>
                                    <button class="btn btn-secondary btn-sm" style="color: var(--danger);" onclick="toggle2FA()">Disable 2FA</button>
                                </div>
                            </div>

                            <!-- Backup codes section -->
                            <div style="border-top: 1px solid var(--gray-100); padding-top: 1rem; display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <strong style="font-size: 0.875rem; color: var(--dark);">Backup Recovery Codes</strong>
                                    <p style="font-size: 0.75rem; color: var(--gray-500);">7 of 10 single-use codes remaining. Use these if you lose device access.</p>
                                </div>
                                <button class="btn btn-outline btn-sm" onclick="showToast('Backup codes revealed: [REDACTED FOR SECURITY]', 'indigo')">
                                    <i class="fa-solid fa-eye"></i> View Backup Codes
                                </button>
                            </div>
                        </div>

                        <!-- Active Sessions -->
                        <div class="card" style="margin-bottom: 1.5rem;">
                            <div class="card-header">
                                <h2 class="card-title">
                                    <i class="fa-solid fa-laptop" style="color: var(--primary);"></i>
                                    Active Sessions
                                </h2>
                                <button class="btn btn-secondary btn-sm" onclick="showToast('All other sessions revoked', 'success')">Revoke All Others</button>
                            </div>

                            <div class="session-list">
                                <!-- Session 1: Current -->
                                <div class="session-item">
                                    <div class="session-device-col">
                                        <div class="session-device-icon" style="color: var(--primary);">
                                            <i class="fa-solid fa-desktop"></i>
                                        </div>
                                        <div>
                                            <strong style="font-size: 0.875rem; color: var(--dark);">Chrome on Windows 11</strong>
                                            <div style="font-size: 0.75rem; color: var(--gray-500);">Manila, PH · IP 192.168.1.45</div>
                                        </div>
                                    </div>
                                    <span class="badge badge-success">Current Session</span>
                                </div>

                                <!-- Session 2 -->
                                <div class="session-item">
                                    <div class="session-device-col">
                                        <div class="session-device-icon">
                                            <i class="fa-solid fa-mobile-screen"></i>
                                        </div>
                                        <div>
                                            <strong style="font-size: 0.875rem; color: var(--dark);">Safari on iPhone 15</strong>
                                            <div style="font-size: 0.75rem; color: var(--gray-500);">Manila, PH · Last active 2h ago</div>
                                        </div>
                                    </div>
                                    <button class="btn btn-secondary btn-sm" style="color: var(--danger);" onclick="revokeSession(this)">Revoke</button>
                                </div>

                                <!-- Session 3 -->
                                <div class="session-item">
                                    <div class="session-device-col">
                                        <div class="session-device-icon">
                                            <i class="fa-brands fa-firefox"></i>
                                        </div>
                                        <div>
                                            <strong style="font-size: 0.875rem; color: var(--dark);">Firefox on macOS</strong>
                                            <div style="font-size: 0.75rem; color: var(--gray-500);">Quezon City, PH · Last active 4 days ago</div>
                                        </div>
                                    </div>
                                    <button class="btn btn-secondary btn-sm" style="color: var(--danger);" onclick="revokeSession(this)">Revoke</button>
                                </div>
                            </div>
                        </div>

                        <!-- Login History Table -->
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">
                                    <i class="fa-solid fa-clock-rotate-left" style="color: var(--gray-600);"></i>
                                    Login History (Last 5 Logins)
                                </h2>
                            </div>
                            <div style="overflow-x: auto;">
                                <table class="login-table">
                                    <thead>
                                        <tr>
                                            <th>Date & Time</th>
                                            <th>IP Address</th>
                                            <th>Device / Browser</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Oct 13, 2026 08:30 AM</td>
                                            <td>192.168.1.45</td>
                                            <td>Chrome / Windows</td>
                                            <td>Manila, PH</td>
                                            <td><span class="badge badge-success">Success</span></td>
                                        </tr>
                                        <tr>
                                            <td>Oct 12, 2026 02:15 PM</td>
                                            <td>192.168.1.45</td>
                                            <td>Safari / iOS</td>
                                            <td>Manila, PH</td>
                                            <td><span class="badge badge-success">Success</span></td>
                                        </tr>
                                        <tr>
                                            <td>Oct 11, 2026 09:12 AM</td>
                                            <td>112.198.34.12</td>
                                            <td>Chrome / Windows</td>
                                            <td>Manila, PH</td>
                                            <td><span class="badge badge-success">Success</span></td>
                                        </tr>
                                        <tr>
                                            <td>Oct 10, 2026 07:45 PM</td>
                                            <td>112.198.34.12</td>
                                            <td>Safari / iOS</td>
                                            <td>Manila, PH</td>
                                            <td><span class="badge badge-success">Success</span></td>
                                        </tr>
                                        <tr>
                                            <td>Oct 09, 2026 11:20 AM</td>
                                            <td>180.190.22.88</td>
                                            <td>Firefox / macOS</td>
                                            <td>Quezon City, PH</td>
                                            <td><span class="badge badge-success">Success</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: NOTIFICATIONS -->
                    <div class="tab-content" id="tab-notifications">
                        <div class="card" style="margin-bottom: 1.5rem;">
                            <div class="card-header">
                                <h2 class="card-title">
                                    <i class="fa-regular fa-envelope" style="color: var(--primary);"></i>
                                    Email Notifications
                                </h2>
                            </div>

                            <label class="switch-label">
                                <div class="switch-info">
                                    <span class="switch-title">Assignments & Deadlines</span>
                                    <span class="switch-desc">Receive email alerts 48h and 24h prior to task deadlines</span>
                                </div>
                                <div class="switch">
                                    <input type="checkbox" checked onchange="handleToggleChange('Email: Assignments', this.checked)">
                                    <span class="slider"></span>
                                </div>
                            </label>

                            <label class="switch-label">
                                <div class="switch-info">
                                    <span class="switch-title">Grades & Feedback</span>
                                    <span class="switch-desc">Immediate notifications when an exam or coursework is graded</span>
                                </div>
                                <div class="switch">
                                    <input type="checkbox" checked onchange="handleToggleChange('Email: Grades', this.checked)">
                                    <span class="slider"></span>
                                </div>
                            </label>

                            <label class="switch-label">
                                <div class="switch-info">
                                    <span class="switch-title">Campus & Course Announcements</span>
                                    <span class="switch-desc">Official departmental broadcasts and university bulletins</span>
                                </div>
                                <div class="switch">
                                    <input type="checkbox" checked onchange="handleToggleChange('Email: Announcements', this.checked)">
                                    <span class="slider"></span>
                                </div>
                            </label>

                            <label class="switch-label">
                                <div class="switch-info">
                                    <span class="switch-title">Direct Messages</span>
                                    <span class="switch-desc">Notifications when professors or study group members message you</span>
                                </div>
                                <div class="switch">
                                    <input type="checkbox" onchange="handleToggleChange('Email: Messages', this.checked)">
                                    <span class="slider"></span>
                                </div>
                            </label>
                        </div>

                        <!-- Push Notifications -->
                        <div class="card" style="margin-bottom: 1.5rem;">
                            <div class="card-header">
                                <h2 class="card-title">
                                    <i class="fa-solid fa-mobile-screen" style="color: var(--secondary);"></i>
                                    Push Notifications (Mobile & Web App)
                                </h2>
                            </div>

                            <label class="switch-label">
                                <div class="switch-info">
                                    <span class="switch-title">Assignments & Quizzes</span>
                                    <span class="switch-desc">Pop-up reminders when new assignments or quizzes are posted</span>
                                </div>
                                <div class="switch">
                                    <input type="checkbox" checked onchange="handleToggleChange('Push: Assignments', this.checked)">
                                    <span class="slider"></span>
                                </div>
                            </label>

                            <label class="switch-label">
                                <div class="switch-info">
                                    <span class="switch-title">Grades Released</span>
                                    <span class="switch-desc">Instant phone banner when scores become visible</span>
                                </div>
                                <div class="switch">
                                    <input type="checkbox" checked onchange="handleToggleChange('Push: Grades', this.checked)">
                                    <span class="slider"></span>
                                </div>
                            </label>

                            <label class="switch-label">
                                <div class="switch-info">
                                    <span class="switch-title">Campus Announcements</span>
                                    <span class="switch-desc">Emergency bulletins and campus notices</span>
                                </div>
                                <div class="switch">
                                    <input type="checkbox" checked onchange="handleToggleChange('Push: Announcements', this.checked)">
                                    <span class="slider"></span>
                                </div>
                            </label>

                            <label class="switch-label">
                                <div class="switch-info">
                                    <span class="switch-title">Direct Messages</span>
                                    <span class="switch-desc">Instant real-time chat push alerts</span>
                                </div>
                                <div class="switch">
                                    <input type="checkbox" checked onchange="handleToggleChange('Push: Messages', this.checked)">
                                    <span class="slider"></span>
                                </div>
                            </label>
                        </div>

                        <!-- SMS & Quiet Hours -->
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">
                                    <i class="fa-solid fa-comment-sms" style="color: var(--accent);"></i>
                                    SMS Alerts & Schedule
                                </h2>
                            </div>

                            <label class="switch-label">
                                <div class="switch-info">
                                    <span class="switch-title">SMS Notifications (Urgent Only)</span>
                                    <span class="switch-desc">Urgent typhoons/weather cancellations, room changes, and security alerts</span>
                                </div>
                                <div class="switch">
                                    <input type="checkbox" checked onchange="handleToggleChange('SMS: Urgent', this.checked)">
                                    <span class="slider"></span>
                                </div>
                            </label>

                            <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--gray-100);">
                                <label class="switch-label" style="border-bottom: none; padding-bottom: 0.5rem;">
                                    <div class="switch-info">
                                        <span class="switch-title">Quiet Hours (Do Not Disturb)</span>
                                        <span class="switch-desc">Mute all non-critical notifications between scheduled night hours</span>
                                    </div>
                                    <div class="switch">
                                        <input type="checkbox" checked onchange="handleToggleChange('Quiet Hours', this.checked)">
                                        <span class="slider"></span>
                                    </div>
                                </label>

                                <div class="form-row-2" style="margin-top: 0.5rem; max-width: 400px;">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label class="form-label" style="font-size: 0.75rem;">Start Time</label>
                                        <input type="time" class="form-control" value="22:00">
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label class="form-label" style="font-size: 0.75rem;">End Time</label>
                                        <input type="time" class="form-control" value="07:00">
                                    </div>
                                </div>
                                <span style="font-size: 0.6875rem; color: var(--gray-400); margin-top: 0.25rem; display: block;">Default: 10:00 PM – 7:00 AM (PHT)</span>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: ACCESSIBILITY -->
                    <div class="tab-content" id="tab-accessibility">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">
                                    <i class="fa-solid fa-universal-access" style="color: var(--primary);"></i>
                                    Accessibility Preferences
                                </h2>
                                <button class="btn btn-outline btn-sm" onclick="resetAccessibility()">
                                    <i class="fa-solid fa-rotate-left"></i> Reset to Defaults
                                </button>
                            </div>

                            <!-- Font Size Slider -->
                            <div class="range-slider-wrap">
                                <label class="form-label" style="display: flex; justify-content: space-between;">
                                    <span>Interface Font Size</span>
                                    <span id="fontSizeValLabel" style="color: var(--primary); font-weight: 700;">Normal (16px)</span>
                                </label>
                                <input type="range" id="fontSizeSlider" min="1" max="4" value="2" step="1" style="width: 100%; accent-color: var(--primary);" oninput="updateFontSize(this.value)">
                                <div class="range-ticks">
                                    <span>Small (14px)</span>
                                    <span>Normal (16px)</span>
                                    <span>Large (18px)</span>
                                    <span>Extra Large (20px)</span>
                                </div>
                                <div id="fontPreviewSample" style="margin-top: 0.75rem; padding: 0.75rem 1rem; background: var(--light); border-radius: var(--radius-md); font-size: 16px; border: 1px dashed var(--gray-300);">
                                    Sample: The quick brown fox jumps over the lazy dog. EduPortal University.
                                </div>
                            </div>

                            <!-- Accessibility Toggles -->
                            <label class="switch-label">
                                <div class="switch-info">
                                    <span class="switch-title">High Contrast Mode</span>
                                    <span class="switch-desc">Enhances text borders and increases foreground/background contrast</span>
                                </div>
                                <div class="switch">
                                    <input type="checkbox" id="highContrastToggle" onchange="toggleHighContrast(this.checked)">
                                    <span class="slider"></span>
                                </div>
                            </label>

                            <label class="switch-label">
                                <div class="switch-info">
                                    <span class="switch-title">Screen Reader Optimized</span>
                                    <span class="switch-desc">Adds explicit ARIA announcements and simplified navigation flow</span>
                                </div>
                                <div class="switch">
                                    <input type="checkbox" onchange="showToast('Screen reader optimizations active', 'indigo')">
                                    <span class="slider"></span>
                                </div>
                            </label>

                            <label class="switch-label">
                                <div class="switch-info">
                                    <span class="switch-title">Reduce Motion</span>
                                    <span class="switch-desc">Minimizes smooth animations, glowing effects, and transitions</span>
                                </div>
                                <div class="switch">
                                    <input type="checkbox" onchange="showToast('Motion reduction enabled', 'indigo')">
                                    <span class="slider"></span>
                                </div>
                            </label>

                            <label class="switch-label">
                                <div class="switch-info">
                                    <span class="switch-title">Dyslexia-Friendly Font</span>
                                    <span class="switch-desc">Applies OpenDyslexic typeface for easier reading</span>
                                </div>
                                <div class="switch">
                                    <input type="checkbox" onchange="toggleDyslexicFont(this.checked)">
                                    <span class="slider"></span>
                                </div>
                            </label>

                            <!-- Color Blind Mode Dropdown -->
                            <div class="form-group" style="margin-top: 1.25rem;">
                                <label class="form-label">Color Blind Mode</label>
                                <select class="form-control" onchange="showToast(`Color filter applied: ${this.value}`, 'indigo')">
                                    <option value="None">None (Default)</option>
                                    <option value="Protanopia">Protanopia (Red-blind)</option>
                                    <option value="Deuteranopia">Deuteranopia (Green-blind)</option>
                                    <option value="Tritanopia">Tritanopia (Blue-blind)</option>
                                </select>
                            </div>

                            <label class="switch-label">
                                <div class="switch-info">
                                    <span class="switch-title">Keyboard Navigation Focus Ring</span>
                                    <span class="switch-desc">Adds high-visibility outlines for Tab key traversal</span>
                                </div>
                                <div class="switch">
                                    <input type="checkbox" checked onchange="showToast('High-visibility focus outlines active', 'indigo')">
                                    <span class="slider"></span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- TAB 5: PRIVACY -->
                    <div class="tab-content" id="tab-privacy">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">
                                    <i class="fa-solid fa-user-shield" style="color: var(--primary);"></i>
                                    Profile Visibility & Data Controls
                                </h2>
                            </div>

                            <label class="form-label" style="margin-bottom: 0.75rem;">Who can see your student profile and achievements?</label>
                            <div class="radio-card-grid">
                                <div class="radio-card active" onclick="selectVisibility(this, 'Public')">
                                    <div class="radio-card-title">
                                        <span>Public</span>
                                        <i class="fa-solid fa-globe" style="color: var(--primary);"></i>
                                    </div>
                                    <div class="radio-card-desc">Visible to university directory and registered peers</div>
                                </div>

                                <div class="radio-card" onclick="selectVisibility(this, 'Students Only')">
                                    <div class="radio-card-title">
                                        <span>Students Only</span>
                                        <i class="fa-solid fa-users" style="color: var(--secondary);"></i>
                                    </div>
                                    <div class="radio-card-desc">Only visible to classmates enrolled in your sections</div>
                                </div>

                                <div class="radio-card" onclick="selectVisibility(this, 'Private')">
                                    <div class="radio-card-title">
                                        <span>Private</span>
                                        <i class="fa-solid fa-lock" style="color: var(--gray-500);"></i>
                                    </div>
                                    <div class="radio-card-desc">Hidden from peer directories, visible only to faculty</div>
                                </div>
                            </div>

                            <label class="switch-label">
                                <div class="switch-info">
                                    <span class="switch-title">Show Online Status</span>
                                    <span class="switch-desc">Allows study group members to see when you are active on the portal</span>
                                </div>
                                <div class="switch">
                                    <input type="checkbox" checked onchange="handleToggleChange('Online Status', this.checked)">
                                    <span class="slider"></span>
                                </div>
                            </label>

                            <label class="switch-label">
                                <div class="switch-info">
                                    <span class="switch-title">Show on Public Class Leaderboard</span>
                                    <span class="switch-desc">Display your alias and earned XP rank in gamification leaderboards</span>
                                </div>
                                <div class="switch">
                                    <input type="checkbox" checked onchange="handleToggleChange('Leaderboard Visibility', this.checked)">
                                    <span class="slider"></span>
                                </div>
                            </label>

                            <!-- Data Export -->
                            <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--gray-200); display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <strong style="font-size: 0.875rem; color: var(--dark);">Data Portability & Export</strong>
                                    <p style="font-size: 0.75rem; color: var(--gray-500);">Download a full archive of your submitted coursework, grades, and forum activity.</p>
                                </div>
                                <button class="btn btn-outline btn-sm" onclick="downloadUserData()">
                                    <i class="fa-solid fa-download"></i> Download My Data
                                </button>
                            </div>

                            <!-- Account Deletion Danger Zone -->
                            <div class="danger-zone-box">
                                <div>
                                    <strong style="font-size: 0.875rem; color: var(--danger); display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Danger Zone: Account Deletion
                                    </strong>
                                    <p style="font-size: 0.75rem; color: var(--gray-600); margin-top: 0.25rem;">
                                        Permanently delete portal access. Active degree programs require university registrar clearance before complete deletion.
                                    </p>
                                </div>
                                <button class="btn btn-danger btn-sm" onclick="confirmDeleteAccount()">
                                    Delete Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Role-Based Access Info Card -->
                <div class="role-badge-card">
                    <div class="role-header">
                        <div class="role-avatar">
                            <i class="fa-solid fa-user-graduate"></i>
                        </div>
                        <div class="role-title">
                            <h3>Current Role: Student</h3>
                            <span class="badge badge-primary">Undergraduate Degree</span>
                        </div>
                    </div>

                    <h4 style="font-size: 0.8125rem; font-weight: 700; color: var(--dark); margin-bottom: 0.625rem;">
                        Permissions Summary:
                    </h4>

                    <div class="role-permission-list">
                        <div class="role-permission-item">
                            <i class="fa-solid fa-circle-check" style="color: var(--success);"></i>
                            <span>Course enrollment and syllabus viewing</span>
                        </div>
                        <div class="role-permission-item">
                            <i class="fa-solid fa-circle-check" style="color: var(--success);"></i>
                            <span>Assignment submission and grade tracking</span>
                        </div>
                        <div class="role-permission-item">
                            <i class="fa-solid fa-circle-check" style="color: var(--success);"></i>
                            <span>Forum participation & study group chats</span>
                        </div>
                        <div class="role-permission-item">
                            <i class="fa-solid fa-circle-check" style="color: var(--success);"></i>
                            <span>Full access to AI Study Assistant & Analytics</span>
                        </div>
                        <div class="role-permission-item">
                            <i class="fa-solid fa-circle-xmark" style="color: var(--gray-400);"></i>
                            <span>Course catalog curriculum modification (Faculty)</span>
                        </div>
                        <div class="role-permission-item">
                            <i class="fa-solid fa-circle-xmark" style="color: var(--gray-400);"></i>
                            <span>Official transcript sign-off (Registrar)</span>
                        </div>
                    </div>

                    <div style="background: var(--light); border: 1px solid var(--gray-200); border-radius: var(--radius-md); padding: 0.875rem; font-size: 0.75rem; color: var(--gray-600); margin-top: 1rem;">
                        <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i>
                        <span>Need a role elevation (e.g. Teaching Assistant) or major change?</span>
                        <div style="margin-top: 0.5rem;">
                            <a href="mailto:registrar@eduportal.edu.ph" class="btn btn-outline btn-sm" style="width: 100%; font-size: 0.6875rem;">
                                Contact Registrar Office
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../../js/main.js?v=9"></script>
    <script>
        function saveProfile(e) {
            e.preventDefault();
            const name = document.getElementById('fullName').value;
            showToast(`Profile updated successfully for ${name}!`, 'success');
        }

        function resetProfileForm() {
            showToast('Changes discarded', 'indigo');
        }

        function previewAvatar(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    document.getElementById('avatarPreview').src = evt.target.result;
                    showToast('Profile photo preview updated. Click "Save Changes" to apply.', 'success');
                };
                reader.readAsDataURL(file);
            }
        }

        function removePhoto() {
            if (confirm('Remove profile photo and revert to initial avatar?')) {
                document.getElementById('avatarPreview').src = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80';
                showToast('Profile photo reset to default placeholder', 'indigo');
            }
        }

        function handlePasswordUpdate(e) {
            e.preventDefault();
            showToast('Password successfully changed! Please use your new password next time you login.', 'success');
            e.target.reset();
        }

        function toggle2FA() {
            if (confirm('Are you sure you want to disable Two-Factor Authentication? Your account security will be reduced.')) {
                showToast('2FA has been disabled. We strongly recommend enabling it again.', 'danger');
            }
        }

        function revokeSession(btn) {
            const item = btn.closest('.session-item');
            if (confirm('Revoke this active session? The device will be logged out immediately.')) {
                item.style.opacity = '0.4';
                item.style.pointerEvents = 'none';
                btn.textContent = 'Revoked';
                showToast('Session revoked', 'success');
            }
        }

        function handleToggleChange(setting, isChecked) {
            showToast(`${setting} is now ${isChecked ? 'Enabled' : 'Disabled'}`, isChecked ? 'success' : 'indigo');
        }

        function selectVisibility(el, type) {
            document.querySelectorAll('.radio-card').forEach(c => c.classList.remove('active'));
            el.classList.add('active');
            showToast(`Visibility set to: ${type}`, 'success');
        }

        function updateFontSize(val) {
            const sizes = ['Small (14px)', 'Normal (16px)', 'Large (18px)', 'Extra Large (20px)'];
            const pxs = ['14px', '16px', '18px', '20px'];
            document.getElementById('fontSizeValLabel').textContent = sizes[val - 1];
            document.getElementById('fontPreviewSample').style.fontSize = pxs[val - 1];
            showToast(`Interface font scale updated to ${sizes[val - 1]}`, 'indigo');
        }

        function toggleHighContrast(checked) {
            if (checked) {
                document.body.style.filter = 'contrast(125%)';
                showToast('High Contrast mode enabled', 'success');
            } else {
                document.body.style.filter = '';
                showToast('High Contrast mode disabled', 'indigo');
            }
        }

        function toggleDyslexicFont(checked) {
            if (checked) {
                showToast('Dyslexia font applied to current session', 'success');
            } else {
                showToast('Default Inter font restored', 'indigo');
            }
        }

        function resetAccessibility() {
            document.getElementById('fontSizeSlider').value = 2;
            updateFontSize(2);
            document.getElementById('highContrastToggle').checked = false;
            toggleHighContrast(false);
            showToast('Accessibility settings reset to default', 'indigo');
        }

        function downloadUserData() {
            showToast('Preparing your student data archive (JSON + PDF). Download starting shortly...', 'success');
        }

        function confirmDeleteAccount() {
            const promptVal = prompt('WARNING: Account deletion is permanent! Type "DELETE" to confirm:');
            if (promptVal === 'DELETE') {
                showToast('Account deletion request submitted to Registrar for mandatory academic clearance.', 'danger');
            } else {
                showToast('Deletion canceled', 'indigo');
            }
        }
    </script>
</body>
</html>
