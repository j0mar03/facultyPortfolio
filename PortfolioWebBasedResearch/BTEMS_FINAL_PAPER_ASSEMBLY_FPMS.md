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
Higher education units often manage faculty portfolio compliance through fragmented channels (paper folders, email, and ad hoc cloud storage), resulting in weak status visibility and inefficient evidence preparation for quality assurance. This study developed and evaluated a web-based Faculty Portfolio Management System (FPMS) that formalizes role-based workflow for faculty, chair/reviewer, administrator, and auditor users. Using a design-and-development approach with quantitative post-implementation evaluation, the system implemented class-offering-linked portfolio creation, required-document completeness support, review routing, and report/export functions. Evaluation integrated operational records and survey responses (N=35) using System Usability Scale (SUS) and Technology Acceptance Model (TAM) constructs. In the updated window (2025-11-12 to 2026-03-02), FPMS recorded 162 portfolios, a 17.90% complete-submission rate (29/162), and 29 reviewed submissions. Processing duration was 14,582.01/4,981.13 minutes (mean/median), while review-turnaround was 23,712.91/24,785.23 minutes (mean/median). User evaluation showed SUS=62.00 and positive TAM outcomes (PU=4.17, PEOU=4.17, BI=4.26). The study contributes a deployable governance-oriented workflow model with measurable post-implementation indicators for higher education compliance operations. Action-level audit evidence remains limited because `audit_logs` rows were unavailable in the analyzed extraction.

## Keywords
faculty portfolio management system; workflow automation; academic compliance; role-based access control; usability; higher education information systems

---

## 1. Introduction
Higher education institutions routinely collect faculty instructional and compliance records to support academic monitoring and quality assurance. In many settings, these processes remain fragmented across paper-based and semi-digital channels, resulting in inconsistent completeness checks, delayed reviewer action, and weak process traceability. These problems reduce institutional efficiency and complicate evidence preparation for accreditation and audit requirements.

To address this, the present study developed a web-based Faculty Portfolio Management System (FPMS) designed for role-based governance and workflow formalization. The evaluation is organized into three priority domains: workflow efficiency, compliance and governance support, and usability/user acceptance.

### 1.1 Statement of the Problem
This study addresses the following questions:
1. What is the post-implementation workflow profile of the developed FPMS in terms of process organization, status tracking, available operational indicators from logs, and user-perceived turnaround support?
2. What is the post-implementation compliance and governance profile of the developed FPMS in terms of required-document completeness, role-based governance, and available traceability/report-readiness indicators?
3. What is the level of system usability and user acceptance as measured by SUS and TAM constructs?

Indicators requiring unavailable log sources (for example `audit_logs`) are reported as not measurable in the analyzed extraction window.

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

Current evaluated values:
- Faculty respondents: [n=27]
- Chair/Reviewer respondents: [n=4]
- Administrator/Auditor respondents: [n=4] (auditor respondents in this survey batch)
- Total respondents: [n=35]
- Evaluation period: 2025-11-12 03:59:55 to 2026-03-02 10:05:07 (from `prod_dump03022026.sql`)

### 3.3 Instruments
1. Operational log extraction sheet:
   - processing duration
   - review turnaround duration (when review records are available)
   - document completeness indicators
   - governance/traceability indicators (according to available log evidence)
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
| Faculty | 27 | 77.14 |
| Chair/Reviewer | 4 | 11.43 |
| Admin/Auditor | 4 | 11.43 |
| Total | 35 | 100 |

### 4.2 Post-Implementation Workflow and Turnaround Outcomes
| Metric | FPMS Mean (SD) | Unit | Notes |
|---|---:|---|---|
| Processing duration (mean / median) | 14,582.01 / 4,981.13 | minutes | `created_at` to `submitted_at` (submitted/decided records, n=29) |
| Review turnaround (mean / median) | 23,712.91 / 24,785.23 | minutes | portfolio `submitted_at` to review decision timestamp (`reviews`, n=29) |
| Queue-stage coverage | 29 reviewed / 0 pending | count | submitted portfolios with/without matched review record in extraction |

### 4.3 Compliance and Governance Outcomes
| Indicator | FPMS Value | Unit/Notes |
|---|---:|---|
| Complete submissions | 17.90 | % of total portfolios (29 of 162; status in approved/rejected/submitted) |
| Incomplete submissions | 82.10 | % of total portfolios (133 of 162; draft status) |
| Rejection/resubmission rate | 24.14 / 0.00 | % (rejected among complete=7/29; nonzero `resubmission_count` not observed) |
| Role-based access enforcement status | Implemented (not directly quantifiable in this dump) | Roles are schema-supported (`users.role` and role-based design) |
| Traceability/report readiness indicator | Partial | Workflow and review timestamps are available; `audit_logs` inserts remain 0 in this backup |

### 4.4 Usability and Acceptance Outcomes
| Construct | Mean | SD | Interpretation |
|---|---:|---:|---|
| SUS overall score | 62.00 | 2.25 | Marginal / Acceptable |
| Perceived Usefulness | 4.17 | 0.30 | Acceptable |
| Perceived Ease of Use | 4.17 | 0.17 | Acceptable |
| Behavioral Intention | 4.26 | 0.21 | Highly Acceptable |

### 4.5 Reliability
| Scale | Cronbach’s alpha | Interpretation |
|---|---:|---|
| WI | 0.109 | Very low internal consistency |
| CS | -0.045 | Not acceptable; possible low variance/item direction issue |
| RT | -0.395 | Not acceptable; possible low variance/item direction issue |
| GT | 0.170 | Very low internal consistency |
| PU | 0.896 | Good internal consistency |
| PEOU | -0.097 | Not acceptable; possible low variance/item direction issue |
| BI | -0.088 | Not acceptable; possible low variance/item direction issue |
| TAM instrument (PU+PEOU+BI) | 0.781 | Acceptable internal consistency (primary reliability evidence) |

Reliability framing for interpretation:
1. The primary reliability-supported acceptance evidence is the combined TAM instrument (`alpha=0.781`), with PU also showing strong consistency.
2. Subscales with low/negative alpha (WI, CS, RT, GT, PEOU, BI) are treated as exploratory/descriptive in this cycle and are not used for strong psychometric claims.
3. Item refinement plan for the next cycle: run item-total and alpha-if-item-deleted diagnostics, revise low-performing items (including reverse-coded checks), and revalidate using a larger, more role-balanced sample.

---

## 5. Discussion
Interpret findings according to the three priority problems:
1. Workflow efficiency
2. Compliance and governance support
3. Usability and user acceptance

Link results to Chapter II themes and identify where FPMS extends prior evidence (especially integrated compliance workflow and measurable operational outcomes).

---

## 6. Conclusion
The study developed and evaluated a role-based Faculty Portfolio Management System that addresses fragmented faculty document workflows in higher education. The FPMS contributes a practical digital governance model that integrates portfolio workflow support, compliance-oriented completeness controls, and traceability/reporting functions. Based on the analyzed backup window, the study provides measurable operational evidence for processing, completeness, and review-turnaround indicators, while action-level audit indicators remain limited by unavailable `audit_logs` records.

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
- [x] Final abstract with numeric results
- [x] Completed results tables
- [x] Reliability outputs included
- [ ] Discussion tied directly to SOP questions
- [ ] BTEMS template formatting applied
- [ ] Similarity check passed
- [ ] Author details finalized
