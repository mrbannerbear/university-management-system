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
    const studentIdEl = document.getElementById('student-id-display');
    const deptEl = document.getElementById('student-department');
    const yearEl = document.getElementById('student-year');
    const semesterEl = document.getElementById('student-semester');
    
    if (studentIdEl) studentIdEl.textContent = info. student_id;
    if (deptEl) deptEl.textContent = info.department_name || 'N/A';
    if (yearEl) yearEl.textContent = `Year ${info.year}`;
    if (semesterEl) semesterEl.textContent = `Semester ${info.semester}`;
}

/**
 * Display student statistics
 */
function displayStudentStatistics(stats) {
    const gpaEl = document.getElementById('current-gpa');
    const cgpaEl = document.getElementById('cgpa');
    const coursesEl = document.getElementById('total-courses');
    const resultsEl = document.getElementById('published-results');
    
    if (gpaEl) gpaEl.textContent = stats.current_gpa.toFixed(2);
    if (cgpaEl) cgpaEl.textContent = stats.cgpa.toFixed(2);
    if (coursesEl) coursesEl.textContent = stats.total_courses;
    if (resultsEl) resultsEl.textContent = stats.published_results;
}

/**
 * Initialize profile page (alias for HTML compatibility)
 */
async function initProfilePage() {
    await initStudentProfile();
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
            console.log(response.data);
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
    const nameEl = document.getElementById('profile-name');
    const emailEl = document.getElementById('profile-email');
    const idEl = document.getElementById('profile-student-id');
    const deptEl = document.getElementById('profile-department');
    const yearEl = document.getElementById('profile-year');
    const semesterEl = document.getElementById('profile-semester');
    const enrollmentDateEl = document.getElementById('profile-enrollment-date');
    
    if (nameEl) nameEl.textContent = student.name;
    if (emailEl) emailEl.textContent = student.email;
    if (idEl) idEl.textContent = student.student_id;
    if (deptEl) deptEl.textContent = student. department_name || 'N/A';
    if (yearEl) yearEl.textContent = student.year;
    if (semesterEl) semesterEl.textContent = student.semester;
    if (enrollmentDateEl) enrollmentDateEl.textContent = student.created_at;
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
    const container = document. getElementById('results-container');
    if (!container) return;

    if (Object.keys(resultsBySemester).length === 0) {
        container.innerHTML = '<div class="card"><div class="card-body text-center">No results published yet</div></div>';
        return;
    }

    container.innerHTML = Object. keys(resultsBySemester).sort().map(semester => {
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
                                        <td>${result. marks}</td>
                                        <td><span class="badge badge-${result.grade === 'F' ? 'danger' :  'success'}">${result. grade}</span></td>
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

/**
 * Initialize print result page
 */
async function initPrintResultPage() {
    currentUser = await protectPage('student');
    if (!currentUser) return;

    const nameElement = document.getElementById('student-name');
    if (nameElement) {
        nameElement.textContent = currentUser.name;
    }

    await loadPrintResultData();
}

/**
 * Load data for print result page
 */
async function loadPrintResultData() {
    try {
        showLoading();
        
        // Load profile info
        const profileResponse = await apiGet('student/profile.php');
        if (profileResponse.success) {
            displayPrintProfileInfo(profileResponse.data);
        }
        
        // Load results
        const resultsResponse = await apiGet('student/results.php');
        if (resultsResponse.success) {
            displayPrintResults(resultsResponse.data);
        }
        
        // Load GPA data
        const gpaResponse = await apiGet('student/gpa. php');
        if (gpaResponse.success) {
            displayPrintGPA(gpaResponse.data);
        }
    } catch (error) {
        showToast('Error', 'Failed to load result data', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Display profile info for print
 */
function displayPrintProfileInfo(student) {
    const nameEl = document.getElementById('print-student-name');
    const idEl = document.getElementById('print-student-id');
    const deptEl = document.getElementById('print-department');
    const yearEl = document.getElementById('print-year');
    const semesterEl = document. getElementById('print-semester');
    
    if (nameEl) nameEl.textContent = student.name;
    if (idEl) idEl.textContent = student. student_id;
    if (deptEl) deptEl.textContent = student.department_name || 'N/A';
    if (yearEl) yearEl.textContent = student.year;
    if (semesterEl) semesterEl.textContent = student.semester;
}

/**
 * Display results for print
 */
function displayPrintResults(resultsBySemester) {
    const container = document.getElementById('print-results-container');
    if (!container) return;

    if (Object.keys(resultsBySemester).length === 0) {
        container.innerHTML = '<p class="text-center">No results published yet</p>';
        return;
    }

    container. innerHTML = Object.keys(resultsBySemester).sort().map(semester => {
        const results = resultsBySemester[semester];
        const totalCredits = results.reduce((sum, r) => sum + parseFloat(r.credit), 0);
        const totalPoints = results.reduce((sum, r) => sum + (parseFloat(r.credit) * parseFloat(r.grade_point)), 0);
        const semesterGPA = totalCredits > 0 ? (totalPoints / totalCredits).toFixed(2) : '0.00';
        
        return `
            <div class="print-semester-section">
                <h4>Semester ${semester}</h4>
                <table class="print-table">
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
                                <td>${result. credit}</td>
                                <td>${result.marks}</td>
                                <td>${result.grade}</td>
                                <td>${result.grade_point}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4"></td>
                            <td><strong>GPA: </strong></td>
                            <td><strong>${semesterGPA}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        `;
    }).join('');
}

/**
 * Display GPA summary for print
 */
function displayPrintGPA(gpaData) {
    const cgpaEl = document.getElementById('print-cgpa');
    if (cgpaEl && gpaData.cgpa !== undefined) {
        cgpaEl.textContent = parseFloat(gpaData.cgpa).toFixed(2);
    }
}

/**
 * Print the result
 */
function printResult() {
    window.print();
}