-- System KPI report for Faculty Portfolio DB
-- Run after restoring the SQL dump into MySQL/MariaDB.

-- 1) Processing & turnaround from portfolio workflow
SELECT
  COUNT(*) AS total_portfolios,
  SUM(status = 'draft') AS draft_count,
  SUM(status = 'submitted') AS submitted_count,
  SUM(status = 'approved') AS approved_count,
  SUM(status = 'rejected') AS rejected_count,
  ROUND(100 * SUM(status IN ('submitted','approved','rejected')) / NULLIF(COUNT(*),0), 2) AS completeness_pct,
  ROUND(AVG(TIMESTAMPDIFF(HOUR, created_at, submitted_at)), 2) AS avg_processing_hours,
  ROUND(AVG(TIMESTAMPDIFF(HOUR, submitted_at, COALESCE(approved_at, updated_at))), 2) AS avg_turnaround_hours,
  MIN(created_at) AS first_record_at,
  MAX(created_at) AS last_record_at
FROM portfolios;

-- 2) Status breakdown by month
SELECT
  DATE_FORMAT(created_at, '%Y-%m') AS month,
  COUNT(*) AS total,
  SUM(status = 'draft') AS draft_count,
  SUM(status = 'submitted') AS submitted_count,
  SUM(status = 'approved') AS approved_count,
  SUM(status = 'rejected') AS rejected_count
FROM portfolios
GROUP BY DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month;

-- 3) Governance indicators from review activity
SELECT
  COUNT(*) AS total_reviews,
  SUM(decision = 'approved') AS approved_reviews,
  SUM(decision = 'rejected') AS rejected_reviews,
  SUM(decision = 'changes_requested') AS changes_requested_reviews,
  ROUND(AVG(TIMESTAMPDIFF(HOUR, p.submitted_at, r.created_at)), 2) AS avg_review_lag_hours
FROM reviews r
JOIN portfolios p ON p.id = r.portfolio_id;

-- 4) Governance indicators from audit trail
SELECT
  COUNT(*) AS total_audit_events,
  COUNT(DISTINCT user_id) AS actors,
  COUNT(DISTINCT CONCAT(entity_type, ':', entity_id)) AS entities_touched,
  MIN(created_at) AS first_audit_at,
  MAX(created_at) AS last_audit_at
FROM audit_logs;

-- 5) Audit event breakdown (top actions)
SELECT
  action,
  COUNT(*) AS event_count
FROM audit_logs
GROUP BY action
ORDER BY event_count DESC, action ASC;

-- 6) Import/queue health (if import pipeline is used)
SELECT
  COUNT(*) AS total_imports,
  SUM(status = 'pending') AS pending_imports,
  SUM(status = 'processing') AS processing_imports,
  SUM(status = 'completed') AS completed_imports,
  SUM(status = 'failed') AS failed_imports,
  ROUND(AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)), 2) AS avg_import_duration_minutes
FROM imports;

-- 7) Queue failure signal
SELECT COUNT(*) AS failed_jobs_count FROM failed_jobs;
