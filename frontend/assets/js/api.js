// API Helper Functions
const API_BASE = '../api';

/**
 * Make an API request
 * @param {string} endpoint - The API endpoint (e.g., 'auth/login')
 * @param {string} method - HTTP method (GET, POST, PUT, DELETE)
 * @param {object} data - Request body data (for POST, PUT)
 * @returns {Promise<object>} - Response data
 */
async function apiRequest(endpoint, method = 'GET', data = null) {
    try {
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include' // Include cookies for session
        };

        if (data && (method === 'POST' || method === 'PUT')) {
            options.body = JSON.stringify(data);
        }

        const response = await fetch(`${API_BASE}/${endpoint}`, options);
        const result = await response.json();

        // Handle non-2xx responses
        if (!response.ok) {
            throw new Error(result.error || 'Request failed');
        }

        return result;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

/**
 * GET request
 */
async function apiGet(endpoint) {
    return apiRequest(endpoint, 'GET');
}

/**
 * POST request
 */
async function apiPost(endpoint, data) {
    return apiRequest(endpoint, 'POST', data);
}

/**
 * PUT request
 */
async function apiPut(endpoint, data) {
    return apiRequest(endpoint, 'PUT', data);
}

/**
 * DELETE request
 */
async function apiDelete(endpoint, data) {
    return apiRequest(endpoint, 'DELETE', data);
}

/**
 * Upload file
 */
async function apiUpload(endpoint, formData) {
    try {
        const options = {
            method: 'POST',
            body: formData,
            credentials: 'include'
        };

        const response = await fetch(`${API_BASE}/${endpoint}`, options);
        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.error || 'Upload failed');
        }

        return result;
    } catch (error) {
        console.error('Upload Error:', error);
        throw error;
    }
}
