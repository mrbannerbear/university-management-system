// Component Loader

/**
 * Load sidebar component based on role
 */
async function loadSidebar(role) {
    const sidebarContainer = document.getElementById('sidebar-container');
    if (!sidebarContainer) return;
    
    try {
        const response = await fetch(`../components/sidebar-${role}.html`);
        const html = await response.text();
        sidebarContainer.innerHTML = html;
        
        // Highlight active menu item
        highlightActiveMenuItem();
        
        // Setup mobile menu toggle after sidebar is loaded
        setupMobileMenuToggle();
    } catch (error) {
        console.error('Error loading sidebar:', error);
    }
}

/**
 * Load header component
 */
async function loadHeader(userName, userRole) {
    const headerContainer = document.getElementById('header-container');
    if (!headerContainer) return;
    
    try {
        const response = await fetch('../components/header.html');
        let html = await response.text();
        
        // Replace placeholders
        html = html.replace('{{userName}}', userName || 'User');
        html = html.replace('{{userRole}}', userRole || 'Guest');
        
        headerContainer.innerHTML = html;
        
        // Setup logout button after header is loaded
        setupLogoutButton();
    } catch (error) {
        console.error('Error loading header:', error);
    }
}

/**
 * Highlight active menu item in sidebar
 */
function highlightActiveMenuItem() {
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.sidebar-menu a');
    
    menuItems.forEach(item => {
        const href = item.getAttribute('href');
        if (currentPath.endsWith(href)) {
            item.classList.add('active');
        }
    });
}

/**
 * Setup mobile menu toggle
 */
function setupMobileMenuToggle() {
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            if (overlay) {
                overlay.classList.toggle('active');
            }
        });
        
        // Close sidebar when clicking overlay
        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        }
    }
}

/**
 * Load page component
 */
async function loadPageComponent(containerId, componentPath) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    try {
        const response = await fetch(componentPath);
        const html = await response.text();
        container.innerHTML = html;
    } catch (error) {
        console.error('Error loading component:', error);
        container.innerHTML = '<div class="alert alert-error">Failed to load component</div>';
    }
}

/**
 * Initialize page layout with sidebar and header
 */
async function initializeLayout(user) {
    if (!user) return;
    
    // Load sidebar
    await loadSidebar(user.role);
    
    // Load header if container exists
    const headerContainer = document.getElementById('header-container');
    if (headerContainer) {
        await loadHeader(user.name, user.role);
    }
    
    // Update welcome message if element exists
    const welcomeElement = document.getElementById('welcome-user');
    if (welcomeElement) {
        welcomeElement.textContent = user.name;
    }
}

/**
 * Create modal dynamically
 */
function createModal(id, title, content, footer = '') {
    const modalHTML = `
        <div id="${id}" class="modal-overlay">
            <div class="modal">
                <div class="modal-header">
                    <h3 class="modal-title">${title}</h3>
                    <button class="modal-close" onclick="hideModal('${id}')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    ${content}
                </div>
                ${footer ? `<div class="modal-footer">${footer}</div>` : ''}
            </div>
        </div>
    `;
    
    // Remove existing modal with same ID
    const existingModal = document.getElementById(id);
    if (existingModal) {
        existingModal.remove();
    }
    
    // Append to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    return id;
}

/**
 * Create confirmation dialog
 */
function createConfirmDialog(title, message, onConfirm, onCancel) {
    const modalId = 'confirm-dialog';
    const content = `<p>${message}</p>`;
    const footer = `
        <button class="btn btn-outline" onclick="hideModal('${modalId}'); ${onCancel ? onCancel.name + '()' : ''}">
            Cancel
        </button>
        <button class="btn btn-danger" onclick="hideModal('${modalId}'); ${onConfirm.name}()">
            Confirm
        </button>
    `;
    
    createModal(modalId, title, content, footer);
    showModal(modalId);
}

/**
 * Show/hide page sections
 */
function showSection(sectionId) {
    // Hide all sections
    document.querySelectorAll('[data-section]').forEach(section => {
        section.style.display = 'none';
    });
    
    // Show selected section
    const section = document.querySelector(`[data-section="${sectionId}"]`);
    if (section) {
        section.style.display = 'block';
    }
}
