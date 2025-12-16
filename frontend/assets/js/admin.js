// Admin Page Functionality
let currentUser = null;

// ===== STUDENTS =====
async function initStudentsPage() {
    currentUser = await protectPage('admin');
    if (!currentUser) return;
    await loadDepartmentsDropdown();
    await loadStudents();
}

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

function displayStudents(students) {
    const tbody = document.getElementById('students-tbody');
    if (!tbody) return;
    if (students.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No students found</td></tr>';
        return;
    }
    tbody.innerHTML = students.map((student, index) => `
        <tr>
            <td>${escapeHtml(student.student_id)}</td>
            <td>${escapeHtml(student.name)}</td>
            <td>${escapeHtml(student.email)}</td>
            <td>${escapeHtml(student.department_name || 'N/A')}</td>
            <td>Semester ${student.semester}</td>
            <td>${student.created_at || 'N/A'}</td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="editStudent(${student.id})"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-danger" onclick="deleteStudent(${student.id})"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
    `).join('');
}

function showAddStudentModal() {
    const form = document.getElementById('student-form');
    if (form) form.reset();
    const editId = document.getElementById('student-id');
    if (editId) editId.value = '';
    document.getElementById('modal-title').textContent = 'Add Student';
    showModal('student-modal');
}

async function saveStudent() {
    const id = document.getElementById('student-id').value;
    const data = {
        name: document.getElementById('student-name').value,
        email: document.getElementById('student-email').value,
        password: document.getElementById('student-password').value,
        student_id: document.getElementById('student-student-id').value,
        department_id: document.getElementById('student-department').value,
        semester: document.getElementById('student-semester').value
    };
    try {
        showLoading();
        let response;
        if (id) {
            data.id = id;
            response = await apiPut('admin/students.php', data);
        } else {
            response = await apiPost('admin/students.php', data);
        }
        if (response.success) {
            showToast('Success', response.message, 'success');
            hideModal('student-modal');
            await loadStudents();
        }
    } catch (error) {
        showToast('Error', error.message, 'error');
    } finally {
        hideLoading();
    }
}

async function editStudent(id) {
    try {
        showLoading();
        const response = await apiGet('admin/students.php?id=' + id);
        if (response.success) {
            const student = response.data;
            document.getElementById('student-id').value = student.id;
            document.getElementById('student-name').value = student.name;
            document.getElementById('student-email').value = student.email;
            document.getElementById('student-student-id').value = student.student_id;
            document.getElementById('student-department').value = student.department_id || '';
            document.getElementById('student-semester').value = student.semester;
            document.getElementById('modal-title').textContent = 'Edit Student';
            showModal('student-modal');
        }
    } catch (error) {
        showToast('Error', 'Failed to load student', 'error');
    } finally {
        hideLoading();
    }
}

async function deleteStudent(id) {
    if (!confirm('Are you sure you want to delete this student?')) return;
    try {
        showLoading();
        const response = await apiDelete('admin/students.php', { id: id });
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

// ===== FACULTY =====
async function initFacultyPage() {
    currentUser = await protectPage('admin');
    if (!currentUser) return;
    await loadDepartmentsDropdown();
    await loadFacultyList();
}

async function loadFacultyList() {
    try {
        showLoading();
        const response = await apiGet('admin/faculty.php');
        if (response.success) {
            displayFacultyList(response.data);
        }
    } catch (error) {
        showToast('Error', 'Failed to load faculty', 'error');
    } finally {
        hideLoading();
    }
}

function displayFacultyList(facultyList) {
    const tbody = document.getElementById('faculty-tbody');
    if (!tbody) return;
    if (facultyList.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No faculty found</td></tr>';
        return;
    }
    tbody.innerHTML = facultyList.map((faculty) => `
        <tr>
            <td>${escapeHtml(faculty.faculty_id || '')}</td>
            <td>${escapeHtml(faculty.name)}</td>
            <td>${escapeHtml(faculty.email)}</td>
            <td>${escapeHtml(faculty.department_name || 'N/A')}</td>
            <td>${escapeHtml(faculty.qualification || 'N/A')}</td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="editFaculty(${faculty.id})"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-danger" onclick="deleteFaculty(${faculty.id})"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
    `).join('');
}

function showAddFacultyModal() {
    const form = document.getElementById('faculty-form');
    if (form) form.reset();
    const editId = document.getElementById('faculty-id');
    if (editId) editId.value = '';
    document.getElementById('modal-title').textContent = 'Add Faculty';
    showModal('faculty-modal');
}

async function saveFaculty() {
    const id = document.getElementById('faculty-id').value;
    const data = {
        name: document.getElementById('faculty-name').value,
        email: document.getElementById('faculty-email').value,
        password: document.getElementById('faculty-password').value,
        faculty_id: document.getElementById('faculty-faculty-id').value,
        department_id: document.getElementById('faculty-department').value,
        qualification: document.getElementById('faculty-qualification').value
    };
    try {
        showLoading();
        let response;
        if (id) {
            data.id = id;
            response = await apiPut('admin/faculty.php', data);
        } else {
            response = await apiPost('admin/faculty.php', data);
        }
        if (response.success) {
            showToast('Success', response.message, 'success');
            hideModal('faculty-modal');
            await loadFacultyList();
        }
    } catch (error) {
        showToast('Error', error.message, 'error');
    } finally {
        hideLoading();
    }
}

async function editFaculty(id) {
    try {
        showLoading();
        const response = await apiGet('admin/faculty.php?id=' + id);
        if (response.success) {
            const faculty = response.data;
            document.getElementById('faculty-id').value = faculty.id;
            document.getElementById('faculty-name').value = faculty.name;
            document.getElementById('faculty-email').value = faculty.email;
            document.getElementById('faculty-faculty-id').value = faculty.faculty_id || '';
            document.getElementById('faculty-department').value = faculty.department_id || '';
            document.getElementById('faculty-qualification').value = faculty.qualification || '';
            document.getElementById('modal-title').textContent = 'Edit Faculty';
            showModal('faculty-modal');
        }
    } catch (error) {
        showToast('Error', 'Failed to load faculty', 'error');
    } finally {
        hideLoading();
    }
}

async function deleteFaculty(id) {
    if (!confirm('Are you sure you want to delete this faculty?')) return;
    try {
        showLoading();
        const response = await apiDelete('admin/faculty.php', { id: id });
        if (response.success) {
            showToast('Success', response.message, 'success');
            await loadFacultyList();
        }
    } catch (error) {
        showToast('Error', error.message, 'error');
    } finally {
        hideLoading();
    }
}

// ===== COURSES =====
async function initCoursesPage() {
    currentUser = await protectPage('admin');
    if (!currentUser) return;
    await loadDepartmentsDropdown();
    await loadCourses();
}

async function loadCourses() {
    try {
        showLoading();
        const response = await apiGet('admin/courses.php');
        if (response.success) {
            displayCourses(response.data);
        }
    } catch (error) {
        showToast('Error', 'Failed to load courses', 'error');
    } finally {
        hideLoading();
    }
}

function displayCourses(courses) {
    const tbody = document.getElementById('courses-tbody');
    if (!tbody) return;
    if (courses.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No courses found</td></tr>';
        return;
    }
    tbody.innerHTML = courses.map((course) => `
        <tr>
            <td>${escapeHtml(course.course_code)}</td>
            <td>${escapeHtml(course.name)}</td>
            <td>${escapeHtml(course.department_name || 'N/A')}</td>
            <td>${course.credit}</td>
            <td>Semester ${course.semester}</td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="editCourse(${course.id})"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-danger" onclick="deleteCourse(${course.id})"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
    `).join('');
}

function showAddCourseModal() {
    const form = document.getElementById('course-form');
    if (form) form.reset();
    const editId = document.getElementById('course-id');
    if (editId) editId.value = '';
    document.getElementById('modal-title').textContent = 'Add Course';
    showModal('course-modal');
}

async function saveCourse() {
    const id = document.getElementById('course-id').value;
    const data = {
        course_code: document.getElementById('course-code').value,
        name: document.getElementById('course-name').value,
        department_id: document.getElementById('course-department').value,
        credit: document.getElementById('course-credit').value,
        semester: document.getElementById('course-semester').value
    };
    try {
        showLoading();
        let response;
        if (id) {
            data.id = id;
            response = await apiPut('admin/courses.php', data);
        } else {
            response = await apiPost('admin/courses.php', data);
        }
        if (response.success) {
            showToast('Success', response.message, 'success');
            hideModal('course-modal');
            await loadCourses();
        }
    } catch (error) {
        showToast('Error', error.message, 'error');
    } finally {
        hideLoading();
    }
}

async function editCourse(id) {
    try {
        showLoading();
        const response = await apiGet('admin/courses.php?id=' + id);
        if (response.success) {
            const course = response.data;
            document.getElementById('course-id').value = course.id;
            document.getElementById('course-code').value = course.course_code;
            document.getElementById('course-name').value = course.name;
            document.getElementById('course-department').value = course.department_id || '';
            document.getElementById('course-credit').value = course.credit;
            document.getElementById('course-semester').value = course.semester;
            document.getElementById('modal-title').textContent = 'Edit Course';
            showModal('course-modal');
        }
    } catch (error) {
        showToast('Error', 'Failed to load course', 'error');
    } finally {
        hideLoading();
    }
}

async function deleteCourse(id) {
    if (!confirm('Are you sure you want to delete this course?')) return;
    try {
        showLoading();
        const response = await apiDelete('admin/courses.php', { id: id });
        if (response.success) {
            showToast('Success', response.message, 'success');
            await loadCourses();
        }
    } catch (error) {
        showToast('Error', error.message, 'error');
    } finally {
        hideLoading();
    }
}

// ===== DEPARTMENTS =====
async function initDepartmentsPage() {
    currentUser = await protectPage('admin');
    if (!currentUser) return;
    await loadDepartmentsList();
}

async function loadDepartmentsList() {
    try {
        showLoading();
        const response = await apiGet('admin/departments.php');
        if (response.success) {
            displayDepartmentsList(response.data);
        }
    } catch (error) {
        showToast('Error', 'Failed to load departments', 'error');
    } finally {
        hideLoading();
    }
}

function displayDepartmentsList(departments) {
    const tbody = document.getElementById('departments-tbody');
    if (!tbody) return;
    if (departments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">No departments found</td></tr>';
        return;
    }
    tbody.innerHTML = departments.map((dept) => `
        <tr>
            <td>${escapeHtml(dept.code || dept.id)}</td>
            <td>${escapeHtml(dept.name)}</td>
            <td>${escapeHtml(dept.description || 'N/A')}</td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="editDepartment(${dept.id})"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-danger" onclick="deleteDepartment(${dept.id})"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
    `).join('');
}

function showAddDepartmentModal() {
    const form = document.getElementById('department-form');
    if (form) form.reset();
    const editId = document.getElementById('department-id');
    if (editId) editId.value = '';
    document.getElementById('modal-title').textContent = 'Add Department';
    showModal('department-modal');
}

async function saveDepartment() {
    const id = document.getElementById('department-id').value;
    const data = {
        code: document.getElementById('department-code').value,
        name: document.getElementById('department-name').value,
        description: document.getElementById('department-description').value
    };
    try {
        showLoading();
        let response;
        if (id) {
            data.id = id;
            response = await apiPut('admin/departments.php', data);
        } else {
            response = await apiPost('admin/departments.php', data);
        }
        if (response.success) {
            showToast('Success', response.message, 'success');
            hideModal('department-modal');
            await loadDepartmentsList();
        }
    } catch (error) {
        showToast('Error', error.message, 'error');
    } finally {
        hideLoading();
    }
}

async function editDepartment(id) {
    try {
        showLoading();
        const response = await apiGet('admin/departments.php?id=' + id);
        if (response.success) {
            const dept = response.data;
            document.getElementById('department-id').value = dept.id;
            document.getElementById('department-code').value = dept.code || '';
            document.getElementById('department-name').value = dept.name;
            document.getElementById('department-description').value = dept.description || '';
            document.getElementById('modal-title').textContent = 'Edit Department';
            showModal('department-modal');
        }
    } catch (error) {
        showToast('Error', 'Failed to load department', 'error');
    } finally {
        hideLoading();
    }
}

async function deleteDepartment(id) {
    if (!confirm('Are you sure you want to delete this department?')) return;
    try {
        showLoading();
        const response = await apiDelete('admin/departments.php', { id: id });
        if (response.success) {
            showToast('Success', response.message, 'success');
            await loadDepartmentsList();
        }
    } catch (error) {
        showToast('Error', error.message, 'error');
    } finally {
        hideLoading();
    }
}

// ===== ASSIGN COURSES =====
async function initAssignCoursesPage() {
    currentUser = await protectPage('admin');
    if (!currentUser) return;
    await loadFacultyDropdown();
    await loadCoursesDropdown();
    await loadAssignments();
}

async function loadFacultyDropdown() {
    try {
        const response = await apiGet('admin/faculty.php');
        if (response.success) {
            const select = document.getElementById('assign-faculty');
            if (select) {
                select.innerHTML = '<option value="">Select Faculty</option>' +
                    response.data.map(f => '<option value="' + f.id + '">' + escapeHtml(f.name) + '</option>').join('');
            }
        }
    } catch (error) {
        console.error('Failed to load faculty:', error);
    }
}

async function loadCoursesDropdown() {
    try {
        const response = await apiGet('admin/courses.php');
        if (response.success) {
            const select = document.getElementById('assign-course');
            if (select) {
                select.innerHTML = '<option value="">Select Course</option>' +
                    response.data.map(c => '<option value="' + c.id + '">' + escapeHtml(c.course_code) + ' - ' + escapeHtml(c.name) + '</option>').join('');
            }
        }
    } catch (error) {
        console.error('Failed to load courses:', error);
    }
}

async function loadAssignments() {
    try {
        showLoading();
        const response = await apiGet('admin/assign-courses.php');
        if (response.success) {
            displayAssignments(response.data);
        }
    } catch (error) {
        showToast('Error', 'Failed to load assignments', 'error');
    } finally {
        hideLoading();
    }
}

function displayAssignments(assignments) {
    const tbody = document.getElementById('assignments-tbody');
    if (!tbody) return;
    if (assignments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No assignments found</td></tr>';
        return;
    }
    tbody.innerHTML = assignments.map((a) => `
        <tr>
            <td>${escapeHtml(a.faculty_name)}</td>
            <td>${escapeHtml(a.course_code)}</td>
            <td>${escapeHtml(a.course_name)}</td>
            <td>${escapeHtml(a.department_name || 'N/A')}</td>
            <td>
                <button class="btn btn-sm btn-danger" onclick="deleteAssignment(${a.id})"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
    `).join('');
}

function showAssignCourseModal() {
    const form = document.getElementById('assign-course-form');
    if (form) form.reset();
    showModal('assign-modal');
}

async function saveAssignment() {
    const facultyId = document.getElementById('assign-faculty').value;
    const courseId = document.getElementById('assign-course').value;
    if (!facultyId || !courseId) {
        showToast('Error', 'Please select both faculty and course', 'error');
        return;
    }
    try {
        showLoading();
        const response = await apiPost('admin/assign-courses.php', {
            faculty_id: facultyId,
            course_id: courseId
        });
        if (response.success) {
            showToast('Success', response.message, 'success');
            hideModal('assign-modal');
            await loadAssignments();
        }
    } catch (error) {
        showToast('Error', error.message, 'error');
    } finally {
        hideLoading();
    }
}

async function deleteAssignment(id) {
    if (!confirm('Are you sure you want to remove this assignment?')) return;
    try {
        showLoading();
        const response = await apiDelete('admin/assign-courses.php', { id: id });
        if (response.success) {
            showToast('Success', response.message, 'success');
            await loadAssignments();
        }
    } catch (error) {
        showToast('Error', error.message, 'error');
    } finally {
        hideLoading();
    }
}

// ===== RESULTS =====
async function initResultsPage() {
    currentUser = await protectPage('admin');
    if (!currentUser) return;
    await loadAllResults();
}

async function loadAllResults() {
    try {
        showLoading();
        const response = await apiGet('admin/results.php');
        if (response.success) {
            displayAllResults(response.data);
        }
    } catch (error) {
        showToast('Error', 'Failed to load results', 'error');
    } finally {
        hideLoading();
    }
}

function displayAllResults(results) {
    const tbody = document.getElementById('results-tbody');
    if (!tbody) return;
    if (results.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No results found</td></tr>';
        return;
    }
    tbody.innerHTML = results.map((r) => `
        <tr>
            <td>${escapeHtml(r.student_id)}</td>
            <td>${escapeHtml(r.student_name)}</td>
            <td>${escapeHtml(r.course_code)}</td>
            <td>${escapeHtml(r.course_name)}</td>
            <td>${r.marks}</td>
            <td><span class="badge badge-${r.grade === 'F' ? 'danger' : 'success'}">${r.grade}</span></td>
            <td>${r.published ? '<span class="badge badge-success">Published</span>' : '<span class="badge badge-warning">Draft</span>'}</td>
            <td>
                ${!r.published ? 
                    '<button class="btn btn-sm btn-success" onclick="publishResult(' + r.id + ')"><i class="fas fa-check"></i></button>' :
                    '<button class="btn btn-sm btn-warning" onclick="unpublishResult(' + r.id + ')"><i class="fas fa-times"></i></button>'
                }
            </td>
        </tr>
    `).join('');
}

async function publishResult(id) {
    try {
        showLoading();
        const response = await apiPut('admin/results.php', { id: id, published: true });
        if (response.success) {
            showToast('Success', 'Result published', 'success');
            await loadAllResults();
        }
    } catch (error) {
        showToast('Error', error.message, 'error');
    } finally {
        hideLoading();
    }
}

async function unpublishResult(id) {
    try {
        showLoading();
        const response = await apiPut('admin/results.php', { id: id, published: false });
        if (response.success) {
            showToast('Success', 'Result unpublished', 'success');
            await loadAllResults();
        }
    } catch (error) {
        showToast('Error', error.message, 'error');
    } finally {
        hideLoading();
    }
}

// ===== DASHBOARD =====
async function initAdminDashboard() {
    currentUser = await protectPage('admin');
    if (!currentUser) return;
    await loadDashboardData();
}

async function loadDashboardData() {
    try {
        showLoading();
        const response = await apiGet('admin/dashboard.php');
        if (response.success) {
            displayStatistics(response.data.statistics);
            if (response.data.recent_results) {
                displayRecentResults(response.data.recent_results);
            }
        }
    } catch (error) {
        showToast('Error', 'Failed to load dashboard', 'error');
    } finally {
        hideLoading();
    }
}

function displayStatistics(stats) {
    const el1 = document.getElementById('total-students');
    const el2 = document.getElementById('total-faculty');
    const el3 = document.getElementById('total-courses');
    const el4 = document.getElementById('total-departments');
    if (el1) el1.textContent = stats.total_students;
    if (el2) el2.textContent = stats.total_faculty;
    if (el3) el3.textContent = stats.total_courses;
    if (el4) el4.textContent = stats.total_departments;
}

function displayRecentResults(results) {
    const tbody = document.getElementById('recent-results-tbody');
    if (!tbody) return;
    if (results.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No results</td></tr>';
        return;
    }
    tbody.innerHTML = results.map((r) => `
        <tr>
            <td>${escapeHtml(r.student_id)}</td>
            <td>${escapeHtml(r.student_name)}</td>
            <td>${escapeHtml(r.course_code)} - ${escapeHtml(r.course_name)}</td>
            <td>${r.marks}</td>
            <td><span class="badge badge-${r.grade === 'F' ? 'danger' : 'success'}">${r.grade}</span></td>
            <td>${r.published ? '<span class="badge badge-success">Published</span>' : '<span class="badge badge-warning">Draft</span>'}</td>
        </tr>
    `).join('');
}

// ===== UTILITIES =====
async function loadDepartmentsDropdown() {
    try {
        const response = await apiGet('admin/departments.php');
        if (response.success) {
            const selects = document.querySelectorAll('#student-department, #faculty-department, #course-department');
            selects.forEach(select => {
                if (select) {
                    select.innerHTML = '<option value="">Select Department</option>' +
                        response.data.map(d => '<option value="' + d.id + '">' + escapeHtml(d.name) + '</option>').join('');
                }
            });
        }
    } catch (error) {
        console.error('Failed to load departments:', error);
    }
}