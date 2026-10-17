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
    confirmAction('Log out of EduPortal?', "You'll need to sign in again to access your account.", 'Log Out', 'fa-right-from-bracket')
      .then(confirmed => {
        if (confirmed) window.location.href = link.href;
      });
  }, true);
}

/**
 * "Are you sure?" box. Resolves true when the red button is clicked; false for Cancel,
 * the Esc key or a click outside the box.
 */
function confirmAction(title, text, confirmLabel, icon = 'fa-triangle-exclamation') {
  let dialog = document.getElementById('confirm-dialog');
  if (!dialog) {
    const style = document.createElement('style');
    style.textContent = `
      .confirm-dialog { margin: auto; border: none; border-radius: 16px; padding: 28px 26px 22px; width: min(380px, calc(100vw - 32px));
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.06);
        text-align: center; font-family: 'Inter', -apple-system, 'Segoe UI', sans-serif; color: #1E293B; }
      .confirm-dialog::backdrop { background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(3px); }
      .confirm-dialog-icon { width: 56px; height: 56px; margin: 0 auto 14px; border-radius: 50%; display: flex;
        align-items: center; justify-content: center; font-size: 1.4rem; background: rgba(239, 68, 68, 0.12); color: #DC2626; }
      .confirm-dialog h3 { font-size: 1.15rem; font-weight: 700; margin: 0 0 6px; word-break: break-word; }
      .confirm-dialog p { font-size: 0.9rem; color: #64748B; margin: 0 0 22px; line-height: 1.5; }
      .confirm-dialog-actions { display: flex; gap: 10px; }
      .confirm-dialog-actions button { flex: 1; padding: 11px 16px; border-radius: 10px; font-size: 0.92rem;
        font-weight: 600; cursor: pointer; font-family: inherit; transition: background-color 0.15s ease; }
      .confirm-dialog-cancel { background: #FFFFFF; border: 1px solid #CBD5E1; color: #334155; }
      .confirm-dialog-cancel:hover { background: #F1F5F9; }
      .confirm-dialog-confirm { background: #EF4444; border: 1px solid #EF4444; color: #FFFFFF; }
      .confirm-dialog-confirm:hover { background: #DC2626; }
      .confirm-dialog-actions button:focus-visible { outline: 3px solid rgba(79, 70, 229, 0.35); outline-offset: 2px; }
    `;
    document.head.appendChild(style);

    dialog = document.createElement('dialog');
    dialog.id = 'confirm-dialog';
    dialog.className = 'confirm-dialog';
    dialog.setAttribute('aria-labelledby', 'confirm-dialog-title');
    dialog.innerHTML = `
      <div class="confirm-dialog-icon"><i></i></div>
      <h3 id="confirm-dialog-title"></h3>
      <p></p>
      <div class="confirm-dialog-actions">
        <button type="button" class="confirm-dialog-cancel">Cancel</button>
        <button type="button" class="confirm-dialog-confirm"></button>
      </div>
    `;
    document.body.appendChild(dialog);

    dialog.querySelector('.confirm-dialog-cancel').addEventListener('click', () => dialog.close('cancel'));
    dialog.querySelector('.confirm-dialog-confirm').addEventListener('click', () => dialog.close('confirm'));
    // Clicking the dark backdrop (outside the box) also cancels
    dialog.addEventListener('click', (e) => {
      const box = dialog.getBoundingClientRect();
      const outside = e.clientX < box.left || e.clientX > box.right || e.clientY < box.top || e.clientY > box.bottom;
      if (e.target === dialog && outside) dialog.close('cancel');
    });
  }

  dialog.querySelector('.confirm-dialog-icon i').className = `fa-solid ${icon}`;
  dialog.querySelector('h3').textContent = title;
  dialog.querySelector('p').textContent = text;
  dialog.querySelector('.confirm-dialog-confirm').textContent = confirmLabel;
  dialog.returnValue = '';
  dialog.showModal();
  dialog.querySelector('.confirm-dialog-cancel').focus();

  return new Promise(resolve => {
    dialog.addEventListener('close', () => resolve(dialog.returnValue === 'confirm'), { once: true });
  });
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

  // Remember how far the menu is scrolled; the next page opens it at the same place (see app_sidebar())
  if (sidebar) {
    window.addEventListener('pagehide', () => {
      try {
        sessionStorage.setItem('eduportal-sidebar-scroll', String(sidebar.scrollTop));
      } catch (e) { /* storage blocked */ }
    });
  }
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
