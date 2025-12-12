// Admin Page Functionality

let currentUser = null;
let dashboardData = null;

/**
 * Initialize admin dashboard
 */
async function initAdminDashboard() {
    currentUser = await protectPage('admin');
    if (!currentUser) return;

    // Update user name in sidebar
    const nameElement = document.getElementById('admin-name');
    if (nameElement) {
        nameElement.textContent = currentUser.name;
    }

    // Load dashboard data
    await loadDashboardData();
}

/**
 * Load dashboard statistics and recent data
 */
async function loadDashboardData() {
    try {
        showLoading();
        const response = await apiGet('admin/dashboard.php');
        
        if (response.success) {
            dashboardData = response.data;
            displayStatistics(dashboardData.statistics);
            displayRecentResults(dashboardData.recent_results);
        }
    } catch (error) {
        showToast('Error', 'Failed to load dashboard data', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Display statistics cards
 */
function displayStatistics(stats) {
    document.getElementById('total-students').textContent = stats.total_students;
    document.getElementById('total-faculty').textContent = stats.total_faculty;
    document.getElementById('total-courses').textContent = stats.total_courses;
    document.getElementById('total-departments').textContent = stats.total_departments;
}

/**
 * Display recent results table
 */
function displayRecentResults(results) {
    const tbody = document.getElementById('recent-results-tbody');
    if (!tbody) return;

    if (results.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No results found</td></tr>';
        return;
    }

    tbody.innerHTML = results.map((result, index) => `
        <tr style="animation-delay: ${index * 0.05}s">
            <td>${escapeHtml(result.student_id)}</td>
            <td>${escapeHtml(result.student_name)}</td>
            <td>${escapeHtml(result.course_code)} - ${escapeHtml(result.course_name)}</td>
            <td>${result.marks}</td>
            <td><span class="badge badge-${result.grade === 'F' ? 'danger' : 'success'}">${result.grade}</span></td>
            <td>
                ${result.published ? 
                    '<span class="badge badge-success">Published</span>' : 
                    '<span class="badge badge-warning">Draft</span>'}
            </td>
        </tr>
    `).join('');
}

/**
 * Initialize students page
 */
async function initStudentsPage() {
    currentUser = await protectPage('admin');
    if (!currentUser) return;

    const nameElement = document.getElementById('admin-name');
    if (nameElement) {
        nameElement.textContent = currentUser.name;
    }

    await loadStudents();
    setupStudentForm();
}

/**
 * Load all students
 */
async function loadStudents() {
    try {
        showLoading();
        const response = await apiGet('admin/students.php');
        
        if (response.success) {
            displayStudents(response.data);
        }
    } catch (error) {
        showToast('Error', 'Failed to load students', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Display students table
 */
function displayStudents(students) {
    const tbody = document.getElementById('students-tbody');
    if (!tbody) return;

    if (students.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No students found</td></tr>';
        return;
    }

    tbody.innerHTML = students.map((student, index) => `
        <tr style="animation-delay: ${index * 0.05}s">
            <td>${escapeHtml(student.student_id)}</td>
            <td>${escapeHtml(student.name)}</td>
            <td>${escapeHtml(student.email)}</td>
            <td>${escapeHtml(student.department_name || 'N/A')}</td>
            <td>Year ${student.year}</td>
            <td>Semester ${student.semester}</td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="editStudent(${student.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteStudent(${student.id}, '${escapeHtml(student.name)}')">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

/**
 * Setup student form handlers
 */
function setupStudentForm() {
    const form = document.getElementById('student-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        try {
            showLoading();
            const studentId = document.getElementById('student-edit-id').value;
            
            let response;
            if (studentId) {
                data.id = studentId;
                response = await apiPut('admin/students.php', data);
            } else {
                response = await apiPost('admin/students.php', data);
            }
            
            if (response.success) {
                showToast('Success', response.message, 'success');
                hideModal('student-modal');
                form.reset();
                await loadStudents();
            }
        } catch (error) {
            showToast('Error', error.message, 'error');
        } finally {
            hideLoading();
        }
    });
}

/**
 * Edit student
 */
async function editStudent(id) {
    try {
        showLoading();
        const response = await apiGet(`admin/students.php?id=${id}`);
        
        if (response.success) {
            const student = response.data;
            document.getElementById('student-edit-id').value = student.id;
            document.getElementById('student-name').value = student.name;
            document.getElementById('student-email').value = student.email;
            document.getElementById('student-id-field').value = student.student_id;
            document.getElementById('student-department').value = student.department_id || '';
            document.getElementById('student-year').value = student.year;
            document.getElementById('student-semester').value = student.semester;
            
            // Hide password field for edit
            document.getElementById('password-group').style.display = 'none';
            
            showModal('student-modal');
        }
    } catch (error) {
        showToast('Error', 'Failed to load student data', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Delete student
 */
async function deleteStudent(id, name) {
    const confirmed = await confirmAction(`Are you sure you want to delete student "${name}"?`);
    if (!confirmed) return;

    try {
        showLoading();
        const response = await apiDelete('admin/students.php', { id });
        
        if (response.success) {
            showToast('Success', response.message, 'success');
            await loadStudents();
        }
    } catch (error) {
        showToast('Error', error.message, 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Open new student modal
 */
function openNewStudentModal() {
    document.getElementById('student-form').reset();
    document.getElementById('student-edit-id').value = '';
    document.getElementById('password-group').style.display = 'block';
    showModal('student-modal');
}

/**
 * Load departments for dropdowns
 */
async function loadDepartments() {
    try {
        const response = await apiGet('admin/departments.php');
        if (response.success) {
            const departments = response.data;
            const selects = document.querySelectorAll('select[name="department_id"]');
            
            selects.forEach(select => {
                select.innerHTML = '<option value="">Select Department</option>' +
                    departments.map(dept => 
                        `<option value="${dept.id}">${escapeHtml(dept.name)}</option>`
                    ).join('');
            });
        }
    } catch (error) {
        console.error('Failed to load departments:', error);
    }
}
