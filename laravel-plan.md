# Faculty Portfolio System (Laravel + MySQL)

### Goals

- Accounts for Faculty, Program Chair, Admin
- Faculty profile management
- Portfolio per subject, per course, per AY/term
- Submission → Approval by Program Chair
- Include checklist items (syllabus, IMs, quizzes/exams, rubrics, grades, attendance, etc.)
- Read-only Auditor/Admin views, export and audit logs

### Roles & Permissions

- Faculty: manage profile, create portfolios, upload artifacts, submit for approval
- Program Chair: review/approve/reject with remarks, request changes
- Admin: manage users, courses, subjects, view/export, assign chairs
- Auditor: view-only, export

### Key Entities (DB Schema)

- users(id, name, email, password, role, course_id?, profile_json, …)
- courses(id, code, name) – seed with DCvET, DCET, DEET, DECET, DICT, DMET, DOMT, DRET
- subjects(id, course_id, code, title, year_level, term)
- portfolios(id, user_id, subject_id, academic_year, term, status[draft|submitted|approved|rejected], submitted_at, approved_at)
- portfolio_items(id, portfolio_id, type[faculty_assignment|class_list|syllabus|sample_quiz|major_exam|tos|activity_rubrics|grade_sheets|sample_ims|acknowledgement|attendance|other], title, file_path, metadata_json)
- reviews(id, portfolio_id, reviewer_id, decision[approved|rejected|changes_requested], remarks, created_at)
- audit_logs(id, user_id, action, entity_type, entity_id, meta_json, created_at)

### Storage

- Use Laravel Filesystem: local for dev, S3/Wasabi-ready for prod
- File paths: storage/app/portfolios/{userId}/{portfolioId}/{type}/{filename}
- Max size/config via validation rules

### Authentication & Authorization

- Laravel Breeze/Jetstream for auth scaffolding
- Policies/Gates for `Portfolio`, `PortfolioItem`
- Middleware: role checks (`admin`, `chair`, `faculty`, `auditor`)

### Core Use Flows

1) Faculty creates portfolio for a subject (AY + term auto-suggest) → uploads checklist items → submit.

2) Program Chair views queue per course → approve/reject with remarks → status updates + notifications.

3) Admin/Auditor browse by course/year/term/subject/user → export PDF/ZIP of artifacts.

### UI Structure (Blade)

- `resources/views/layouts/app.blade.php`
- Dashboards
  - Faculty: `views/faculty/dashboard.blade.php` – subjects grouped by course/year; per-subject portfolio links (Syllabus, IMs, etc.)
  - Chair: `views/chair/review_queue.blade.php`
  - Admin/Auditor: `views/admin/reports.blade.php`
- Portfolio pages
  - `views/portfolio/show.blade.php` – checklist with statuses, upload components per item type
  - `views/portfolio/review.blade.php`

### Routes (high-level)

- `/dashboard` (role-aware)
- `/portfolios` CRUD
- `/portfolios/{id}/items` CRUD upload/download
- `/portfolios/{id}/submit` POST
- `/reviews/{id}/decision` POST (chair only)
- `/courses/{course}/subjects` list
- `/reports/exports` GET

### Controllers

- `Auth\\` (Breeze/Jetstream)
- `ProfileController` – profile update
- `CourseController`, `SubjectController`
- `PortfolioController` – create/show/submit/export
- `PortfolioItemController` – upload/delete/download
- `ReviewController` – approve/reject
- `ReportController` – filters, exports (PDF/ZIP)

### Approval Workflow

- Status transitions: draft → submitted → approved/rejected (with remarks)
- Event listeners
  - `PortfolioSubmitted`, `PortfolioApproved`, `PortfolioRejected`
- Notifications (mail + database): notify chair on submit; notify faculty on decision

### Validation & Checklists

- Enforce required item types before allowing submit (config-driven)
- Config file: `config/portfolio.php`
```php
return [
  'required_items' => [
    'syllabus','sample_quiz','major_exam','tos','activity_rubrics','grade_sheets','sample_ims','acknowledgement','attendance','class_list','faculty_assignment'
  ],
];
```


### Reporting/Export

- Single portfolio export as ZIP; printable summary PDF
- Course/term report: completion rates, approval statuses

### Seeders

- `CourseSeeder` for 8 programs
- Optional demo subjects per course/year/term
- Admin user and a sample chair/faculty

### Security & Compliance

- File access via signed routes; policy checks on download
- Size/type validation; virus scan hook ready (optional)
- Audit logs for CRUD + decisions

### Deployment

- `.env` for DB/storage/mail
- Storage symlink (`php artisan storage:link`)
- Queue workers for notifications/events

### Milestones

1. Project scaffold, auth, roles, seeders
2. Courses/subjects models & dashboards
3. Portfolio + items CRUD
4. Submit/approve workflow + notifications
5. Reports/exports + audit logs
6. Polish, tests, docs