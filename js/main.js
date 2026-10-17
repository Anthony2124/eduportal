/**
 * EduPortal - Main JavaScript (loaded on every signed-in page)
 * Logout confirmation, mobile sidebar, account menu, dialogs and toast messages.
 * Who is signed in and which pages they can open is handled by the server (includes/app_shell.php).
 */

// Site root, worked out from this script's own URL so it is right for root pages and for pages/*/*.php
const SITE_ROOT = new URL('..', (document.currentScript || document.querySelector('script[src*="js/main.js"]')).src);

document.addEventListener('DOMContentLoaded', () => {
  initLogoutConfirm();
  initSidebar();
  initDropdowns();
  initModals();
});

// A URL as a path from the site root, e.g. "pages/academics/grades.php"
function sitePath(url) {
  return new URL(url, location.href).pathname.slice(SITE_ROOT.pathname.length);
}

// Logout Confirmation: every "Logout" / "Log Out" link points to logout.php, so ask first
function initLogoutConfirm() {
  // Capture phase, so the question appears even inside menus that handle their own clicks
  document.addEventListener('click', (e) => {
    const link = e.target.closest('a[href]');
    if (!link || sitePath(link.href) !== 'logout.php') return;
    e.preventDefault();
    e.stopPropagation();
    confirmLogout(link.href);
  }, true);
}

function confirmLogout(logoutUrl) {
  let dialog = document.getElementById('logout-dialog');
  if (!dialog) {
    const style = document.createElement('style');
    style.textContent = `
      .logout-dialog { margin: auto; border: none; border-radius: 16px; padding: 28px 26px 22px; width: min(380px, calc(100vw - 32px));
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.06);
        text-align: center; font-family: 'Inter', -apple-system, 'Segoe UI', sans-serif; color: #1E293B; }
      .logout-dialog::backdrop { background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(3px); }
      .logout-dialog-icon { width: 56px; height: 56px; margin: 0 auto 14px; border-radius: 50%; display: flex;
        align-items: center; justify-content: center; font-size: 1.4rem; background: rgba(239, 68, 68, 0.12); color: #DC2626; }
      .logout-dialog h3 { font-size: 1.15rem; font-weight: 700; margin: 0 0 6px; }
      .logout-dialog p { font-size: 0.9rem; color: #64748B; margin: 0 0 22px; line-height: 1.5; }
      .logout-dialog-actions { display: flex; gap: 10px; }
      .logout-dialog-actions button { flex: 1; padding: 11px 16px; border-radius: 10px; font-size: 0.92rem;
        font-weight: 600; cursor: pointer; font-family: inherit; transition: background-color 0.15s ease; }
      .logout-dialog-cancel { background: #FFFFFF; border: 1px solid #CBD5E1; color: #334155; }
      .logout-dialog-cancel:hover { background: #F1F5F9; }
      .logout-dialog-confirm { background: #EF4444; border: 1px solid #EF4444; color: #FFFFFF; }
      .logout-dialog-confirm:hover { background: #DC2626; }
      .logout-dialog-actions button:focus-visible { outline: 3px solid rgba(79, 70, 229, 0.35); outline-offset: 2px; }
    `;
    document.head.appendChild(style);

    dialog = document.createElement('dialog');
    dialog.id = 'logout-dialog';
    dialog.className = 'logout-dialog';
    dialog.setAttribute('aria-labelledby', 'logout-dialog-title');
    dialog.innerHTML = `
      <div class="logout-dialog-icon"><i class="fa-solid fa-right-from-bracket"></i></div>
      <h3 id="logout-dialog-title">Log out of EduPortal?</h3>
      <p>You'll need to sign in again to access your account.</p>
      <div class="logout-dialog-actions">
        <button type="button" class="logout-dialog-cancel">Cancel</button>
        <button type="button" class="logout-dialog-confirm">Log Out</button>
      </div>
    `;
    document.body.appendChild(dialog);

    dialog.querySelector('.logout-dialog-cancel').addEventListener('click', () => dialog.close());
    dialog.querySelector('.logout-dialog-confirm').addEventListener('click', () => {
      window.location.href = dialog.dataset.logoutUrl;
    });
    // Clicking the dark backdrop (outside the box) also cancels
    dialog.addEventListener('click', (e) => {
      const box = dialog.getBoundingClientRect();
      const outside = e.clientX < box.left || e.clientX > box.right || e.clientY < box.top || e.clientY > box.bottom;
      if (e.target === dialog && outside) dialog.close();
    });
  }

  dialog.dataset.logoutUrl = logoutUrl;
  dialog.showModal();
  dialog.querySelector('.logout-dialog-cancel').focus();
}

// Sidebar open/close on phones (the menu button only shows on small screens)
function initSidebar() {
  const sidebar = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebar-toggle');
  const sidebarClose = document.getElementById('sidebar-close');
  const sidebarOverlay = document.getElementById('sidebar-overlay');

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('mobile-open');
      if (sidebarOverlay) sidebarOverlay.classList.toggle('active');
    });
  }

  const closeSidebar = () => {
    if (sidebar) sidebar.classList.remove('mobile-open');
    if (sidebarOverlay) sidebarOverlay.classList.remove('active');
  };

  if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
  if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);
}

// Dropdown menus (.dropdown-container + .dropdown-trigger), e.g. the account menu in the top bar
function initDropdowns() {
  const dropdownContainers = document.querySelectorAll('.dropdown-container');

  dropdownContainers.forEach(container => {
    const trigger = container.querySelector('.dropdown-trigger');
    if (!trigger) return;

    trigger.addEventListener('click', (e) => {
      e.stopPropagation();
      // Close other open dropdowns
      dropdownContainers.forEach(other => {
        if (other !== container) other.classList.remove('open');
      });
      container.classList.toggle('open');
    });
  });

  if (dropdownContainers.length > 0) {
    document.addEventListener('click', () => {
      dropdownContainers.forEach(container => container.classList.remove('open'));
    });
  }
}

// Dialogs (.modal-overlay): [data-modal-open="id"] opens one, [data-modal-close] or a click outside closes it
function initModals() {
  document.querySelectorAll('[data-modal-open]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const modal = document.getElementById(btn.getAttribute('data-modal-open'));
      if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
      }
    });
  });

  document.querySelectorAll('[data-modal-close]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const modal = btn.closest('.modal-overlay');
      if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  });

  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  });
}

// Toast Notifications (bottom-right messages)
function showToast(message, type = 'info') {
  let toastContainer = document.querySelector('.toast-container');
  if (!toastContainer) {
    toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container';
    document.body.appendChild(toastContainer);
  }

  const toast = document.createElement('div');
  toast.className = `toast ${type}`;

  let icon = 'fa-info-circle';
  if (type === 'success') icon = 'fa-check-circle';
  if (type === 'warning') icon = 'fa-exclamation-triangle';
  if (type === 'danger') icon = 'fa-times-circle';

  // The message is shown as plain text (it can contain names typed in by users)
  toast.innerHTML = `<i class="fa-solid ${icon}"></i> <span>${escapeHtml(String(message))}</span>`;
  toastContainer.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(100%)';
    toast.style.transition = 'all 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  }, 4000);
}

function escapeHtml(str) {
  return str.replace(/[&<>'"]/g,
    tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
  );
}
