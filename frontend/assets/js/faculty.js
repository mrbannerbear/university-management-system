// Faculty Page Functionality

let currentUser = null;

/**
 * Initialize faculty dashboard
 */
async function initFacultyDashboard() {
    currentUser = await protectPage('faculty');
    if (!currentUser) return;

    const nameElement = document.getElementById('faculty-name');
    if (nameElement) {
        nameElement.textContent = currentUser.name;
    }

    await loadFacultyDashboard();
}

/**
 * Load faculty dashboard data
 */
async function loadFacultyDashboard() {
    try {
        showLoading();
        const response = await apiGet('faculty/dashboard.php');
        
        if (response.success) {
            displayFacultyStatistics(response.data.statistics);
            displayFacultyRecentResults(response.data.recent_results);
        }
    } catch (error) {
        showToast('Error', 'Failed to load dashboard data', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Display faculty statistics
 */
function displayFacultyStatistics(stats) {
    document.getElementById('assigned-courses').textContent = stats.assigned_courses;
    document.getElementById('total-students').textContent = stats.total_students;
    document.getElementById('results-entered').textContent = stats.results_entered;
}

/**
 * Display recent results
 */
function displayFacultyRecentResults(results) {
    const tbody = document.getElementById('recent-results-tbody');
    if (!tbody) return;

    if (results.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No results found</td></tr>';
        return;
    }

    tbody.innerHTML = results.map((result, index) => `
        <tr style="animation-delay: ${index * 0.05}s">
            <td>${escapeHtml(result.student_id)}</td>
            <td>${escapeHtml(result.student_name)}</td>
            <td>${escapeHtml(result.course_code)}</td>
            <td>${result.marks}</td>
            <td><span class="badge badge-${result.grade === 'F' ? 'danger' : 'success'}">${result.grade}</span></td>
        </tr>
    `).join('');
}

/**
 * Initialize my courses page
 */
async function initMyCourses() {
    currentUser = await protectPage('faculty');
    if (!currentUser) return;

    const nameElement = document.getElementById('faculty-name');
    if (nameElement) {
        nameElement.textContent = currentUser.name;
    }

    await loadMyCourses();
}

/**
 * Load faculty courses
 */
async function loadMyCourses() {
    try {
        showLoading();
        const response = await apiGet('faculty/my-courses.php');
        
        if (response.success) {
            displayCourses(response.data);
        }
    } catch (error) {
        showToast('Error', 'Failed to load courses', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Display courses
 */
function displayCourses(courses) {
    const tbody = document.getElementById('courses-tbody');
    if (!tbody) return;

    if (courses.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No courses assigned</td></tr>';
        return;
    }

    tbody.innerHTML = courses.map((course, index) => `
        <tr style="animation-delay: ${index * 0.05}s">
            <td>${escapeHtml(course.course_code)}</td>
            <td>${escapeHtml(course.course_name)}</td>
            <td>${course.credit}</td>
            <td>Semester ${course.semester}</td>
            <td>${escapeHtml(course.department_name || 'N/A')}</td>
        </tr>
    `).join('');
}
