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
        nameElement. textContent = currentUser.name;
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
        
        if (response. success) {
            displayFacultyStatistics(response.data. statistics);
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
            <td>${escapeHtml(result. student_name)}</td>
            <td>${escapeHtml(result.course_code)}</td>
            <td>${result.marks}</td>
            <td><span class="badge badge-${result.grade === 'F' ? 'danger' :  'success'}">${result. grade}</span></td>
        </tr>
    `).join('');
}

/**
 * Initialize my courses page (alias for HTML compatibility)
 */
async function initMyCoursesPage() {
    await initMyCourses();
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
            const totalEl = document.getElementById('total-assigned-courses');
            if (totalEl) {
                totalEl. textContent = response.data.length;
            }
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
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No courses assigned</td></tr>';
        return;
    }

    tbody. innerHTML = courses.map((course, index) => `
        <tr style="animation-delay: ${index * 0.05}s">
            <td>${escapeHtml(course.course_code)}</td>
            <td>${escapeHtml(course.course_name)}</td>
            <td>${escapeHtml(course.department_name || 'N/A')}</td>
            <td>${course.credit}</td>
            <td>Semester ${course.semester}</td>
            <td>${course.enrolled_students || 0}</td>
        </tr>
    `).join('');
}

/**
 * Initialize enter marks page
 */
async function initEnterMarksPage() {
    currentUser = await protectPage('faculty');
    if (!currentUser) return;

    await loadCoursesForMarks();
    
    const courseSelect = document.getElementById('course-select');
    if (courseSelect) {
        courseSelect.addEventListener('change', async (e) => {
            const courseId = e.target.value;
            if (courseId) {
                await loadStudentsForMarks(courseId);
                document.getElementById('students-section').style.display = 'block';
            } else {
                document.getElementById('students-section').style.display = 'none';
            }
        });
    }
}

/**
 * Load courses for marks entry dropdown
 */
async function loadCoursesForMarks() {
    try {
        showLoading();
        const response = await apiGet('faculty/my-courses.php');
        
        if (response.success) {
            const select = document.getElementById('course-select');
            if (select) {
                select.innerHTML = '<option value="">Select a course...</option>' +
                    response.data.map(course => 
                        `<option value="${course.id}">${escapeHtml(course.course_code)} - ${escapeHtml(course.course_name)}</option>`
                    ).join('');
            }
        }
    } catch (error) {
        showToast('Error', 'Failed to load courses', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Load students for a specific course
 */
async function loadStudentsForMarks(courseId) {
    try {
        showLoading();
        const response = await apiGet(`faculty/enter-marks.php?course_id=${courseId}`);
        
        if (response.success) {
            displayStudentsForMarks(response.data, courseId);
        }
    } catch (error) {
        showToast('Error', 'Failed to load students', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Display students for marks entry
 */
/**
 * Display students for marks entry
 */
function displayStudentsForMarks(students, courseId) {
    const tbody = document.getElementById('marks-tbody');
    if (!tbody) return;

    if (students.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No students enrolled in this course</td></tr>';
        return;
    }

    tbody.innerHTML = students.map((student, index) => `
        <tr style="animation-delay: ${index * 0.05}s" data-student-id="${student. id}" data-course-id="${courseId}">
            <td>${escapeHtml(student.student_id || '')}</td>
            <td>${escapeHtml(student.name || student.student_name || '')}</td>
            <td>
                <input type="number" class="form-control marks-input" 
                    value="${student.marks !== null && student.marks !== undefined ? student. marks : ''}" 
                    min="0" max="100" 
                    data-student-id="${student.id}"
                    onchange="updateGradePreview(this)">
            </td>
            <td class="grade-cell">${student.grade || '-'}</td>
            <td class="grade-point-cell">${student.grade_point !== null && student.grade_point !== undefined ? student.grade_point :  '-'}</td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="saveSingleMark('${student.id}', ${courseId})">
                    <i class="fas fa-save"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

/**
 * Update grade preview when marks change
 */
function updateGradePreview(input) {
    const marks = parseInt(input.value);
    const row = input.closest('tr');
    const gradeCell = row.querySelector('.grade-cell');
    const gradePointCell = row.querySelector('.grade-point-cell');
    
    if (isNaN(marks) || marks < 0 || marks > 100) {
        gradeCell. textContent = '-';
        gradePointCell. textContent = '-';
        return;
    }
    
    const { grade, gradePoint } = calculateGrade(marks);
    gradeCell.textContent = grade;
    gradePointCell.textContent = gradePoint. toFixed(2);
}

/**
 * Calculate grade from marks
 */
function calculateGrade(marks) {
    if (marks >= 90) return { grade: 'A+', gradePoint: 4.00 };
    if (marks >= 85) return { grade: 'A', gradePoint: 3.75 };
    if (marks >= 80) return { grade: 'A-', gradePoint: 3.50 };
    if (marks >= 75) return { grade: 'B+', gradePoint: 3.25 };
    if (marks >= 70) return { grade: 'B', gradePoint:  3.00 };
    if (marks >= 65) return { grade: 'B-', gradePoint: 2.75 };
    if (marks >= 60) return { grade: 'C+', gradePoint:  2.50 };
    if (marks >= 55) return { grade: 'C', gradePoint: 2.25 };
    if (marks >= 50) return { grade: 'C-', gradePoint: 2.00 };
    if (marks >= 45) return { grade: 'D', gradePoint: 1.50 };
    return { grade: 'F', gradePoint: 0.00 };
}

/**
 * Save single student mark
 */
async function saveSingleMark(studentId, courseId) {
    const input = document.querySelector(`input[data-student-id="${studentId}"]`);
    const marks = parseInt(input.value);
    
    if (isNaN(marks) || marks < 0 || marks > 100) {
        showToast('Error', 'Please enter valid marks (0-100)', 'error');
        return;
    }
    
    try {
        showLoading();
        const response = await apiPost('faculty/enter-marks.php', {
            student_id: studentId,
            course_id: courseId,
            marks: marks
        });
        
        if (response.success) {
            showToast('Success', 'Marks saved successfully', 'success');
            await loadStudentsForMarks(courseId);
        }
    } catch (error) {
        showToast('Error', error.message || 'Failed to save marks', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Save all marks
 */
/**
 * Save all marks
 */
async function saveAllMarks() {
    const rows = document. querySelectorAll('#marks-tbody tr[data-student-id]');
    const courseId = document.getElementById('course-select').value;
    
    if (! courseId) {
        showToast('Error', 'Please select a course', 'error');
        return;
    }
    
    const marksToSave = [];
    let hasError = false;
    
    rows.forEach(row => {
        const studentId = row.dataset.studentId;
        const input = row.querySelector('.marks-input');
        const marks = input. value;
        
        if (marks !== '') {
            const marksNum = parseInt(marks);
            if (isNaN(marksNum) || marksNum < 0 || marksNum > 100) {
                hasError = true;
                return;
            }
            marksToSave.push({ student_id: studentId, marks: marksNum });
        }
    });
    
    if (hasError) {
        showToast('Error', 'Please enter valid marks (0-100) for all students', 'error');
        return;
    }
    
    if (marksToSave.length === 0) {
        showToast('Error', 'No marks to save', 'error');
        return;
    }
    
    try {
        showLoading();
        
        // Save marks one by one since API doesn't support bulk
        let savedCount = 0;
        let errorCount = 0;
        
        for (const item of marksToSave) {
            try {
                await apiPost('faculty/enter-marks.php', {
                    student_id: item.student_id,
                    course_id: courseId,
                    marks: item.marks
                });
                savedCount++;
            } catch (err) {
                errorCount++;
                console.error(`Failed to save marks for student ${item. student_id}:`, err);
            }
        }
        
        if (errorCount === 0) {
            showToast('Success', `All ${savedCount} marks saved successfully`, 'success');
        } else {
            showToast('Warning', `${savedCount} saved, ${errorCount} failed`, 'warning');
        }
        
        await loadStudentsForMarks(courseId);
    } catch (error) {
        showToast('Error', error.message || 'Failed to save marks', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Initialize view results page
 */
async function initViewResultsPage() {
    currentUser = await protectPage('faculty');
    if (!currentUser) return;

    await loadCoursesForFilter();
    await loadAllResults();
    
    const courseFilter = document.getElementById('course-filter');
    if (courseFilter) {
        courseFilter.addEventListener('change', () => filterResults());
    }
    
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', () => filterResults());
    }
}

let allResults = [];

/**
 * Load courses for filter dropdown
 */
async function loadCoursesForFilter() {
    try {
        const response = await apiGet('faculty/my-courses.php');
        
        if (response.success) {
            const select = document.getElementById('course-filter');
            if (select) {
                select. innerHTML = '<option value="">All Courses</option>' +
                    response.data. map(course => 
                        `<option value="${course.id}">${escapeHtml(course.course_code)} - ${escapeHtml(course.course_name)}</option>`
                    ).join('');
            }
        }
    } catch (error) {
        console.error('Failed to load courses for filter:', error);
    }
}

/**
 * Load all results
 */
async function loadAllResults() {
    try {
        showLoading();
        const response = await apiGet('faculty/view-results.php');
        
        if (response.success) {
            allResults = response.data;
            displayResults(allResults);
        }
    } catch (error) {
        showToast('Error', 'Failed to load results', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Filter results based on course and search
 */
function filterResults() {
    const courseId = document.getElementById('course-filter').value;
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    
    let filtered = allResults;
    
    if (courseId) {
        filtered = filtered.filter(r => r.course_id == courseId);
    }
    
    if (searchTerm) {
        filtered = filtered.filter(r => 
            r. student_name.toLowerCase().includes(searchTerm) ||
            r.student_id.toLowerCase().includes(searchTerm)
        );
    }
    
    displayResults(filtered);
}

/**
 * Display results
 */
function displayResults(results) {
    const tbody = document.getElementById('results-tbody');
    if (!tbody) return;

    if (results.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No results found</td></tr>';
        return;
    }

    tbody.innerHTML = results.map((result, index) => `
        <tr style="animation-delay:  ${index * 0.05}s">
            <td>${escapeHtml(result.student_id)}</td>
            <td>${escapeHtml(result.student_name)}</td>
            <td>${escapeHtml(result.course_code)} - ${escapeHtml(result.course_name)}</td>
            <td>${result.marks}</td>
            <td><span class="badge badge-${result.grade === 'F' ?  'danger' : 'success'}">${result.grade}</span></td>
            <td>${result.grade_point}</td>
            <td><span class="badge badge-${result. grade === 'F' ? 'danger' : 'success'}">${result.grade === 'F' ? 'Failed' : 'Passed'}</span></td>
        </tr>
    `).join('');
}