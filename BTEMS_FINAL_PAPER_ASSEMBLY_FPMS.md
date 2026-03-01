# BTEMS 2026 Final Paper Assembly File

## Conference Track
Technology & Engineering (Information Systems / Business Informatics)

## Proposed Paper Title
From Paper-Based Compliance to Digital Governance: Development and Evaluation of a Role-Based Faculty Portfolio Management System for Higher Education

## Authors and Affiliations
- Author 1: [Full Name], [Department], [Institution], [Country], [Email]
- Author 2: [Full Name], [Department], [Institution], [Country], [Email]
- Corresponding Author: [Name and Email]

---

## Abstract
[Use final version from `BTEMS_DETAILED_ABSTRACT_FPMS.md` and ensure all numeric placeholders are completed.]

## Keywords
faculty portfolio management system; workflow automation; academic compliance; role-based access control; usability; higher education information systems

---

## 1. Introduction
Higher education institutions routinely collect faculty instructional and compliance records to support academic monitoring and quality assurance. In many settings, these processes remain fragmented across paper-based and semi-digital channels, resulting in inconsistent completeness checks, delayed reviewer action, and weak process traceability. These problems reduce institutional efficiency and complicate evidence preparation for accreditation and audit requirements.

To address this, the present study developed a web-based Faculty Portfolio Management System (FPMS) designed for role-based governance and workflow formalization. The evaluation is organized into three priority domains: workflow efficiency, compliance and governance support, and usability/user acceptance.

### 1.1 Statement of the Problem
This study addresses the following questions:
1. To what extent does the system improve portfolio workflow efficiency in terms of process organization, status tracking, and review turnaround support?
2. To what extent does the system improve compliance and governance support in terms of required-document completeness, role-based governance, and traceability/report readiness?
3. What is the level of system usability and user acceptance as measured by SUS and TAM constructs?

### 1.2 Objectives
General Objective:
- To develop and evaluate a role-based web-based FPMS for higher education compliance workflows.

Specific Objectives:
1. Evaluate workflow efficiency outcomes after FPMS implementation.
2. Evaluate compliance and governance support outcomes after FPMS implementation.
3. Evaluate usability and user acceptance outcomes using SUS and TAM.

---

## 2. Related Literature (Thematic Synthesis)
The literature indicates that digital workflow systems can improve administrative process consistency, traceability, and coordination in higher education. Full-text evidence from your synthesis highlights the relevance of document management systems, e-portfolio tools, and faculty appraisal/governance platforms. However, current evidence shows a persistent gap in integrated systems that simultaneously provide completeness support, structured review routing, and audit-ready reporting with measurable operational outcomes.

A second gap concerns evaluation quality: many studies are descriptive or conceptual, with limited combined reporting of operational metrics, usability/acceptance outcomes, and reliability statistics. These gaps justify the need for a deployable FPMS artifact and a stronger evidence-driven evaluation model.

---

## 3. Methodology
### 3.1 Research Design
The study used a design-and-development approach with quantitative descriptive evaluation.

### 3.2 Respondents and Data Sources
Respondents included active FPMS users (faculty, chairs/reviewers, administrators/auditors). Operational data were extracted from system records and timestamps.

Populate before submission:
- Faculty respondents: [n=__]
- Chair/Reviewer respondents: [n=__]
- Administrator/Auditor respondents: [n=__]
- Total respondents: [n=__]
- Evaluation period: 2025-11-12 03:59:55 to 2026-02-14 03:26:54 (from `database/prod_dump.sql`; dump completed 2026-02-14 04:45:53)

### 3.3 Instruments
1. Operational log extraction sheet:
   - processing duration
   - review turnaround duration
   - document completeness indicators
   - governance/traceability indicators
2. Survey instrument:
   - SUS (10 items)
   - TAM-aligned items (PU, PEOU, BI)
3. Governance checklist:
   - role enforcement
   - traceability
   - report/export readiness

### 3.4 Statistical Treatment
1. Frequency, percentage, weighted mean, standard deviation
2. SUS scoring (standard method)
3. Positive response rate
4. Cronbach’s alpha for reliability
5. Optional inferential tests for subgroup comparisons where sample size allows

---

## 4. Results
### 4.1 Respondent Profile
| Role | n | % |
|---|---:|---:|
| Faculty | [ ] | [ ] |
| Chair/Reviewer | [ ] | [ ] |
| Admin/Auditor | [ ] | [ ] |
| Total | [ ] | 100 |

### 4.2 Post-Implementation Workflow and Turnaround Outcomes
| Metric | FPMS Mean (SD) | Unit | Notes |
|---|---:|---|---|
| Processing duration | 12,319.96 | minutes | Mean from `created_at` to `submitted_at` (submitted records, n=24) |
| Review turnaround | N/A (0 review records) | minutes | `reviews` table has no inserted records in analyzed backup |

### 4.3 Compliance and Governance Outcomes
| Indicator | FPMS Value | Unit/Notes |
|---|---:|---|
| Complete submissions | 16.78 | % of total portfolios (24 of 143; status in submitted/approved/rejected) |
| Incomplete submissions | 83.22 | % of total portfolios (119 of 143; draft status) |
| Rejection/resubmission rate | 0.00 | % of submitted portfolios (no rejected records; all `resubmission_count` observed as 0) |
| Role-based access enforcement status | Implemented (not directly quantifiable in this dump) | Roles are schema-supported (`users.role` and role-based design) |
| Traceability/report readiness indicator | Partial | Workflow timestamp traceability available (`created_at`, `updated_at`, `submitted_at`); `audit_logs`/`reviews` inserts are 0 in this backup |

### 4.4 Usability and Acceptance Outcomes
| Construct | Mean | SD | Interpretation |
|---|---:|---:|---|
| SUS overall score | [ ] | [ ] | [ ] |
| Perceived Usefulness | [ ] | [ ] | [ ] |
| Perceived Ease of Use | [ ] | [ ] | [ ] |
| Behavioral Intention | [ ] | [ ] | [ ] |

### 4.5 Reliability
| Scale | Cronbach’s alpha | Interpretation |
|---|---:|---|
| WI | [ ] | [ ] |
| CS | [ ] | [ ] |
| RT | [ ] | [ ] |
| GT | [ ] | [ ] |
| PU | [ ] | [ ] |
| PEOU | [ ] | [ ] |
| BI | [ ] | [ ] |

---

## 5. Discussion
Interpret findings according to the three priority problems:
1. Workflow efficiency
2. Compliance and governance support
3. Usability and user acceptance

Link results to Chapter II themes and identify where FPMS extends prior evidence (especially integrated compliance workflow and measurable operational outcomes).

---

## 6. Conclusion
The study developed and evaluated a role-based Faculty Portfolio Management System that addresses fragmented faculty document workflows in higher education. The FPMS contributes a practical digital governance model that integrates portfolio workflow support, compliance-oriented completeness controls, and traceability/reporting functions. Based on the analyzed backup window, the study provides initial operational evidence for processing and completeness indicators, while review-turnaround and action-level audit indicators require additional datasets with populated review and audit logs.

---

## 7. Recommendations
1. Implement notification automation for pending submissions and deadlines.
2. Add document versioning and change history.
3. Expand evaluation across additional departments/campuses.
4. Conduct longitudinal analysis across multiple academic terms.

---

## 8. Ethical Considerations
- Informed consent for survey participants
- Authorized institutional data use only
- De-identification/anonymization during analysis
- Aggregate-only reporting

---

## 9. Similarity and Submission Compliance
Before final BTEMS upload:
1. Run Turnitin similarity check (target below 20%).
2. Ensure references and in-text citations are consistent.
3. Align formatting with official BTEMS template.
4. Confirm all placeholders are replaced with final values.

---

## 10. Quick Final-Submission Checklist
- [ ] Final abstract with numeric results
- [ ] Completed results tables
- [ ] Reliability outputs included
- [ ] Discussion tied directly to SOP questions
- [ ] BTEMS template formatting applied
- [ ] Similarity check passed
- [ ] Author details finalized
