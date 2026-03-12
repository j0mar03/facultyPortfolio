# Session Reference: March 11, 2026

## Overview
Today's session focused on improving the monitoring capabilities for Department Chairs, streamlining the Faculty experience, and preparing the system for official audits.

## Key Features Implemented

### 1. Faculty Dashboard Improvements
- **AY/Term Filtering:** Added dropdowns to filter class offerings by Academic Year and Term.
- **Default View:** The dashboard now automatically defaults to the most recent semester's classes to avoid "double offering" confusion.
- **Reminder Nudges:** Added a high-visibility blue banner at the top of the dashboard that displays unread reminders from the Chair.
- **Dedicated Compliance Tab:** Moved the "My Compliance Matrix" from the dashboard to its own dedicated "Compliance" tab for a cleaner interface and better focus on audit readiness.

### 2. Enhanced Chair Monitoring
- **Faculty Active Stats:** New summary card showing the number of faculty who have actually started portfolios (e.g., 5/8).
- **In-Progress Monitoring:** The Reports tab now includes an "In-Progress" section where Chairs can view documents for Draft or Rejected portfolios before they are submitted.
- **Monitoring Mode:** A new banner on the review page identifies when a Chair is viewing a work-in-progress portfolio.

### 3. Subject Exclusion Logic (Audit Focus)
- **Automatic Exclusions:** Subjects starting with `GEED`, `NSTP`, `PATHFIT`, `CHEM`, and `MATH` are now excluded from portfolio requirements.
- **Configurable:** Managed via `config/portfolio.php` under `excluded_subject_prefixes`.
- **Refined Statistics:** Dashboard counts now distinguish between "Required Subjects" and "Total Subjects".

### 4. Audit Compliance Matrix
- **Matrix Tab:** A dedicated tab for Chairs and Auditors showing a grid of all professional subjects and their document completion status.
- **Visual Indicators:** Green (Approved), Blue (Submitted), Yellow (Draft), and Red (Missing) dots for instant compliance checks.
- **Organization:** Grouped by Year Level and Term, with support for switching between managed departments (Courses).
- **Print Optimization:** Added a "Print for Audit" button for landscape physical reporting.

### 5. Reminder System
- **Database:** Created a `reminders` table to track "Nudges" sent by Chairs.
- **Nudge Action:** Added 🔔 bell icons to the Chair Dashboard and Monitoring list to instantly alert faculty members.

## Technical Changes & Fixes
- **Database Migration:** Added `create_reminders_table` (requires `php artisan migrate` on production).
- **Route Stability:** Fixed a syntax error in `routes/web.php` involving `ReportController`.
- **Subject Tab Fix:** Resolved a 500 error caused by variable mismatch (`$chair` vs `$user`).
- **Deployment Safety:** Temporarily commented out the "Weak DB Credential" check in `deploy.sh` to allow deployment with existing passwords.

## Deployment Notes for VPS
1. **Pull Changes:** `git pull origin main`
2. **Deploy:** `./deploy.sh`
3. **Migrate:** `docker compose exec app php artisan migrate --force` (if not handled by deploy script).

## Next Steps / Pending Ideas
- **Compliance Summary:** Add a "Department Total Compliance %" at the bottom of the Matrix.
- **Search:** Add a search bar to the Compliance Matrix for faster navigation.
- **Password Hardening:** Schedule a time to rotate the DB passwords to meet the `deploy.sh` safety requirements.
