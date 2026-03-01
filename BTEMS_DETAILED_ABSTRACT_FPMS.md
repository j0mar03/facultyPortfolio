# Detailed Abstract (BTEMS 2026)

## Title
From Paper-Based Compliance to Digital Governance: Development and Evaluation of a Role-Based Faculty Portfolio Management System for Higher Education

## Abstract
Higher education institutions require faculty members to submit instructional and compliance documents each academic term, yet many departments continue to rely on fragmented submission channels such as paper folders, email attachments, and ad hoc cloud storage. This fragmented process often results in incomplete records, delayed review cycles, weak process traceability, and inefficient preparation of evidence for quality assurance and accreditation. This study developed and evaluated a web-based Faculty Portfolio Management System (FPMS) to address these institutional workflow and governance challenges.

The study followed a design-and-development research approach with quantitative descriptive evaluation. The developed FPMS implemented role-based workflows for faculty, chair/reviewer, administrator, and auditor users. Core system functions included class-offering-linked portfolio creation, required-document completeness support before submission, review decision tracking, status-based workflow transitions, and report/export capabilities for compliance documentation. Evaluation used system-generated operational records and a structured user survey instrument grounded on the System Usability Scale (SUS) and Technology Acceptance Model (TAM) constructs (Perceived Usefulness, Perceived Ease of Use, and Behavioral Intention).

The analysis framework measured three priority domains: (1) workflow efficiency (process organization, status tracking, and review turnaround support), (2) compliance and governance support (required-document completeness, role-based governance, and traceability/report readiness), and (3) usability and user acceptance. Statistical treatment included frequency and percentage distributions, weighted mean and standard deviation, SUS score computation, positive response rates, and reliability testing (Cronbach’s alpha), with descriptive post-implementation reporting of operational indicators.

Initial implementation findings from the analyzed backup window (2025-11-12 to 2026-02-14) indicate that the FPMS provides a feasible governance-oriented platform for centralizing faculty portfolio workflows and reducing process fragmentation. Portfolio records showed 143 total portfolios, a 16.78% complete-submission rate (24/143), and mean processing duration of 12,319.96 minutes (`created_at` to `submitted_at`, submitted records). Review-turnaround and action-level audit indicators were not measurable in this extraction because the `reviews` and `audit_logs` tables contained no inserted records.

This study contributes a deployable information systems artifact and an evaluation framework for higher education compliance operations, demonstrating how role-based digital workflow design can structure compliance processes, support governance readiness, and provide measurable operational evidence where records are available.

## Keywords
faculty portfolio management system; higher education information systems; workflow automation; role-based governance; usability evaluation; academic compliance

---

## BTEMS Submission Version (Populate Numbers Before Final Upload)

This study developed and evaluated a web-based Faculty Portfolio Management System (FPMS) to address fragmented faculty compliance workflows in higher education. Using a design-and-development approach with post-implementation quantitative evaluation, the system implemented role-based access, required-document completeness support, structured review routing, and reporting/export features. Evaluation combined operational logs and user survey data using SUS and TAM constructs. From the analyzed operational backup window, post-implementation indicators are: processing time 12,319.96 minutes (`created_at` to `submitted_at`), review turnaround N/A (no review records in extracted dataset), and completeness rate 16.78% (24/143). SUS, TAM means, and Cronbach’s alpha values are reported in the full manuscript once survey and reliability outputs are finalized. The findings indicate that FPMS is a practical model for institutional compliance management and evidence-oriented workflow governance in higher education.
