// Authentication Logic

/**
 * Check if user is logged in
 */
async function checkSession() {
    try {
        const response = await apiGet('auth/check-session.php');
        return response.data;
    } catch (error) {
        return { logged_in: false };
    }
}

/**
 * Login user
 */
async function login(email, password) {
    try {
        showLoading();
        const response = await apiPost('auth/login.php', { email, password });
        
        if (response.success) {
            const user = response.data;
            // Redirect based on role
            window.location.href = `../${user.role}/dashboard.html`;
        }
        return response;
    } catch (error) {
        hideLoading();
        showToast('Error', error.message || 'Login failed', 'error');
        throw error;
    }
}

/**
 * Logout user
 */
async function logout() {
    try {
        showLoading();
        await apiPost('auth/logout.php');
        window.location.href = '../index.html';
    } catch (error) {
        hideLoading();
        showToast('Error', 'Logout failed', 'error');
    }
}

/**
 * Protect page - redirect if not logged in
 */
async function protectPage(requiredRole = null) {
    const session = await checkSession();
    
    if (!session.logged_in) {
        window.location.href = '../index.html';
        return null;
    }
    
    if (requiredRole && session.role !== requiredRole) {
        window.location.href = '../index.html';
        return null;
    }
    
    return session;
}

/**
 * Initialize auth for login page
 */
function initLoginPage() {
    // Check if already logged in
    checkSession().then(session => {
        if (session.logged_in) {
            window.location.href = `${session.role}/dashboard.html`;
        }
    });

    // Setup login form
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            try {
                await login(email, password);
            } catch (error) {
                // Error already handled in login function
            }
        });
    }
}

/**
 * Setup logout button
 */
function setupLogoutButton() {
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            await logout();
        });
    }
}
