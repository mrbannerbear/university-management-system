// Student Page Functionality

let currentUser = null;

/**
 * Initialize student dashboard
 */
async function initStudentDashboard() {
    currentUser = await protectPage('student');
    if (!currentUser) return;

    const nameElement = document.getElementById('student-name');
    if (nameElement) {
        nameElement.textContent = currentUser.name;
    }

    await loadStudentDashboard();
}

/**
 * Load student dashboard data
 */
async function loadStudentDashboard() {
    try {
        showLoading();
        const response = await apiGet('student/dashboard.php');
        
        if (response.success) {
            displayStudentInfo(response.data.student_info);
            displayStudentStatistics(response.data.statistics);
        }
    } catch (error) {
        showToast('Error', 'Failed to load dashboard data', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Display student information
 */
function displayStudentInfo(info) {
    document.getElementById('student-id-display').textContent = info.student_id;
    document.getElementById('student-department').textContent = info.department_name || 'N/A';
    document.getElementById('student-year').textContent = `Year ${info.year}`;
    document.getElementById('student-semester').textContent = `Semester ${info.semester}`;
}

/**
 * Display student statistics
 */
function displayStudentStatistics(stats) {
    document.getElementById('current-gpa').textContent = stats.current_gpa.toFixed(2);
    document.getElementById('cgpa').textContent = stats.cgpa.toFixed(2);
    document.getElementById('total-courses').textContent = stats.total_courses;
    document.getElementById('published-results').textContent = stats.published_results;
}

/**
 * Initialize student profile page
 */
async function initStudentProfile() {
    currentUser = await protectPage('student');
    if (!currentUser) return;

    const nameElement = document.getElementById('student-name');
    if (nameElement) {
        nameElement.textContent = currentUser.name;
    }

    await loadStudentProfile();
}

/**
 * Load student profile
 */
async function loadStudentProfile() {
    try {
        showLoading();
        const response = await apiGet('student/profile.php');
        
        if (response.success) {
            displayProfile(response.data);
        }
    } catch (error) {
        showToast('Error', 'Failed to load profile', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Display student profile
 */
function displayProfile(student) {
    document.getElementById('profile-name').textContent = student.name;
    document.getElementById('profile-email').textContent = student.email;
    document.getElementById('profile-student-id').textContent = student.student_id;
    document.getElementById('profile-department').textContent = student.department_name || 'N/A';
    document.getElementById('profile-year').textContent = student.year;
    document.getElementById('profile-semester').textContent = student.semester;
}

/**
 * Initialize student results page
 */
async function initStudentResults() {
    currentUser = await protectPage('student');
    if (!currentUser) return;

    const nameElement = document.getElementById('student-name');
    if (nameElement) {
        nameElement.textContent = currentUser.name;
    }

    await loadStudentResults();
}

/**
 * Load student results
 */
async function loadStudentResults() {
    try {
        showLoading();
        const response = await apiGet('student/results.php');
        
        if (response.success) {
            displayResults(response.data);
        }
    } catch (error) {
        showToast('Error', 'Failed to load results', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Display results by semester
 */
function displayResults(resultsBySemester) {
    const container = document.getElementById('results-container');
    if (!container) return;

    if (Object.keys(resultsBySemester).length === 0) {
        container.innerHTML = '<div class="card"><div class="card-body text-center">No results published yet</div></div>';
        return;
    }

    container.innerHTML = Object.keys(resultsBySemester).sort().map(semester => {
        const results = resultsBySemester[semester];
        return `
            <div class="card animate-slideInUp">
                <div class="card-header">
                    <h3><i class="fas fa-graduation-cap"></i> Semester ${semester}</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Credit</th>
                                    <th>Marks</th>
                                    <th>Grade</th>
                                    <th>Grade Point</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${results.map(result => `
                                    <tr>
                                        <td>${escapeHtml(result.course_code)}</td>
                                        <td>${escapeHtml(result.course_name)}</td>
                                        <td>${result.credit}</td>
                                        <td>${result.marks}</td>
                                        <td><span class="badge badge-${result.grade === 'F' ? 'danger' : 'success'}">${result.grade}</span></td>
                                        <td>${result.grade_point}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}
