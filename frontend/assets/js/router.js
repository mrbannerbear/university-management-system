// Simple Client-Side Router

/**
 * Navigate to a page
 */
function navigate(path) {
    window.location.href = path;
}

/**
 * Redirect to login page
 */
function redirectToLogin() {
    navigate('../index.html');
}

/**
 * Redirect to dashboard based on role
 */
function redirectToDashboard(role) {
    navigate(`../${role}/dashboard.html`);
}

/**
 * Go back to previous page
 */
function goBack() {
    window.history.back();
}

/**
 * Reload current page
 */
function reload() {
    window.location.reload();
}

/**
 * Get URL parameters
 */
function getUrlParams() {
    const params = new URLSearchParams(window.location.search);
    const result = {};
    for (const [key, value] of params) {
        result[key] = value;
    }
    return result;
}

/**
 * Get single URL parameter
 */
function getUrlParam(name) {
    const params = new URLSearchParams(window.location.search);
    return params.get(name);
}

/**
 * Update URL parameter without reload
 */
function setUrlParam(name, value) {
    const url = new URL(window.location);
    url.searchParams.set(name, value);
    window.history.pushState({}, '', url);
}

/**
 * Remove URL parameter without reload
 */
function removeUrlParam(name) {
    const url = new URL(window.location);
    url.searchParams.delete(name);
    window.history.pushState({}, '', url);
}
