# From Paper-Based Compliance to Digital Governance:
# Design and Development of a Role-Based Faculty Portfolio Management System

## Abstract
Higher education institutions require faculty members to submit instructional and compliance documents every term. In many departments, this process remains fragmented across paper folders, email threads, chat attachments, and ad hoc cloud storage, resulting in delayed reviews, inconsistent completeness checks, and weak audit traceability. This study presents the design and development of a web-based Faculty Portfolio Management System (FPMS) built to address these operational and governance gaps. The system was implemented using Laravel and MySQL with role-based workflows for faculty, program chairs, administrators, and auditors. Core capabilities include required-document enforcement, class-offering-linked portfolio submission, role-scoped review decisions, reusable document library support, and exportable compliance evidence.

The paper adopts a Design Science Research (DSR) approach for artifact development and specifies an evaluation protocol for operational and usability validation. Current implementation evidence from the analyzed backup window reports 143 total portfolios, 16.78% complete-submission rate (24/143), and 12,319.96 minutes mean processing duration (`created_at` to `submitted_at`, submitted records). Review-turnaround and action-level audit indicators were not measurable in that extraction because `reviews` and `audit_logs` contained no inserted rows. The developed artifact demonstrates practical relevance and technical viability for institutional quality assurance workflows and provides a replicable model for digital compliance governance in higher education.

**Keywords:** faculty portfolio management, academic compliance, workflow automation, role-based access control, design science research, higher education information systems

## 1. Introduction
### 1.1 Background of the Study
Academic units routinely require faculty evidence for teaching and compliance monitoring, including syllabi, assessment tools, class records, grade sheets, and supporting instructional materials. Despite this requirement, many institutions still operate with manual or semi-digital processes that fragment records and slow down evaluation cycles.

In the observed context, portfolio submissions were performed through mixed channels, making verification and retrieval difficult. Reviewers spent substantial time consolidating files before evaluation, and administrators faced delays when producing evidence for internal monitoring and external accreditation activities.

### 1.2 Problem Statement
The pre-development workflow exhibited the following recurrent issues:

1. Fragmented submission pathways (physical and digital channels with no single source of truth).
2. Inconsistent enforcement of required documents before review.
3. Long reviewer turnaround caused by manual consolidation and status tracking.
4. Weak role separation among faculty, chairs, administrators, and auditors.
5. Limited auditability and export-ready reporting for compliance evidence.

### 1.3 Purpose and Objectives
This study was conducted to design and develop a web-based FPMS that formalizes submission, review, and reporting workflows for academic compliance.

**General Objective**
Develop a role-based Faculty Portfolio Management System that improves completeness, traceability, and governance of instructional document workflows.

**Specific Objectives**
1. Implement role-based access and workflow routing for faculty, chair, admin, and auditor users.
2. Link portfolios to class offerings, terms, courses, and academic years.
3. Enforce required-document completeness before submission.
4. Support structured review decisions with remarks and status transitions.
5. Provide document reuse features to reduce repetitive uploads.
6. Provide reporting and export features for compliance and audit requirements.

### 1.4 Research Questions
1. How can a role-based information system reduce operational friction in faculty portfolio submission and review?
2. To what extent does the system improve document completeness and workflow traceability?
3. How usable and acceptable is the developed system for intended stakeholders?

### 1.5 Significance of the Study
This study benefits:

- **Faculty Members:** clearer compliance requirements and more efficient submission.
- **Program Chairs:** structured review queues and improved course-level oversight.
- **Administrators/Auditors:** consolidated monitoring and export-ready evidence.
- **Institution:** stronger governance, accountability, and accreditation readiness.

### 1.6 Scope and Delimitations
The system covers internal institutional workflows for portfolio creation, upload, submission, review, and reporting. It does not include public access workflows, AI-based document assessment, or long-term predictive analytics in the current version.

## 2. Review of Related Literature and Conceptual Basis
### 2.1 Digital Compliance Workflows in Higher Education
Prior studies on academic information systems indicate that workflow formalization improves monitoring quality, accountability, and processing consistency when compared with ad hoc manual processes.

### 2.2 Role-Based Access and Governance
Role-based access control (RBAC) is a standard governance mechanism for reducing access ambiguity and ensuring function-specific data visibility in institutional systems.

### 2.3 Usability and Acceptance in Educational Information Systems
Usability and user acceptance significantly affect sustained adoption of institutional platforms; therefore, standardized measures (e.g., SUS and TAM-derived indicators) are appropriate for validation.

### 2.4 Conceptual Framework
The study follows an **Input-Process-Output** logic:

- **Input:** compliance pain points, stakeholder role requirements, institutional document policies.
- **Process:** system design, development, role enforcement, pilot deployment, and evaluation.
- **Output:** validated FPMS artifact with measurable workflow and governance outcomes.

## 3. Methodology
### 3.1 Research Design
The study uses **Design Science Research (DSR)** for artifact creation and **quantitative evaluation** for validation. DSR is appropriate because the core contribution is a practical artifact that addresses a real institutional problem.

### 3.2 Development Setting and Participants
The system targets four primary stakeholder groups:

1. Faculty users
2. Program chairs
3. Academic administrators
4. Auditors/quality assurance users

For publishable evaluation, define participant counts per role and inclusion criteria before data collection.

### 3.3 Data to be Collected for Validation
The evaluation dataset should include:

1. **Process Efficiency Metrics**
   - Mean portfolio processing duration (start-to-submit)
   - Mean review turnaround time (submit-to-decision), when review records are available
2. **Compliance Quality Metrics**
   - Completeness rate of required documents per portfolio
   - Rejection/resubmission rates
3. **Usability and Acceptance Metrics**
   - System Usability Scale (SUS) score
   - TAM-based constructs (Perceived Usefulness, Perceived Ease of Use, Behavioral Intention)

### 3.4 Instrumentation
1. System-generated logs (timestamps, status transitions, completion status)
2. Structured usability survey (SUS)
3. User acceptance survey (5-point Likert scale)
4. Optional expert content/feature validation checklist

### 3.5 Statistical Treatment
1. **Descriptive statistics:** frequency, percentage, weighted mean, standard deviation
2. **Comparative tests (if assumptions are met):**
   - Paired t-test or Wilcoxon signed-rank test for pre/post duration metrics
   - Chi-square test for completeness/rejection distribution changes
3. **Reliability checks:** Cronbach’s alpha for multi-item survey constructs
4. **Effect size reporting:** Cohen’s d or rank-biserial effect size where applicable

### 3.6 Ethical Considerations
- Secure institutional research approval before full evaluation.
- Use informed consent for all participants.
- De-identify user-level records before analysis.
- Restrict exports and analysis datasets to authorized researchers only.

## 4. System Design and Development
### 4.1 Technical Architecture
The FPMS is implemented with Laravel and relational data modeling. Major entities include users, courses, subjects, class offerings, portfolios, portfolio items, reviews, and reusable faculty documents.

### 4.2 Workflow Design
1. Faculty receives class offering assignment.
2. Faculty creates portfolio and uploads required artifacts.
3. System blocks submission until required documents are complete.
4. Chair/admin reviews submitted portfolio and records decision.
5. Admin/auditor monitors system-wide status and exports evidence.

### 4.3 Governance Controls
- Role-scoped access routes and action restrictions
- Course-based review scope for chairs
- Status-controlled transitions (`draft -> submitted -> approved/rejected`)
- Exportable audit package generation

## 5. Results Template (Populate During Evaluation)
Use this section as the final publishable results structure.

### 5.1 Participant Profile
**Table 1. Respondent Distribution by Role**

| Role | n | % |
|---|---:|---:|
| Faculty | [ ] | [ ] |
| Chair | [ ] | [ ] |
| Admin/Auditor | [ ] | [ ] |
| **Total** | [ ] | 100 |

### 5.2 Operational Efficiency (Pre vs Post)
**Table 2. Processing and Turnaround Metrics**

| Metric | Pre-implementation Mean (SD) | Post-implementation Mean (SD) | Test | p-value |
|---|---:|---:|---|---:|
| Portfolio processing duration | N/A (no comparable legacy digital baseline) | 12,319.96 minutes | Descriptive only | N/A |
| Review turnaround time | N/A | N/A (no review records in analyzed backup) | N/A | N/A |

### 5.3 Compliance Quality
**Table 3. Completeness and Review Outcomes**

| Indicator | Baseline | With FPMS | Difference |
|---|---:|---:|---:|
| Complete submissions (%) | N/A (no comparable legacy baseline) | 16.78 | N/A |
| Rejected submissions (%) | N/A (no comparable legacy baseline) | 0.00 | N/A |
| Resubmission rate (%) | N/A (no comparable legacy baseline) | 0.00 | N/A |

### 5.4 Usability and Acceptance
**Table 4. SUS and TAM-Based Results**

| Construct | Mean | SD | Verbal Interpretation |
|---|---:|---:|---|
| SUS (overall) | [ ] | [ ] | [ ] |
| Perceived Usefulness | [ ] | [ ] | [ ] |
| Perceived Ease of Use | [ ] | [ ] | [ ] |
| Behavioral Intention | [ ] | [ ] | [ ] |

### 5.5 Reliability
**Table 5. Internal Consistency**

| Scale | Cronbach’s alpha | Interpretation |
|---|---:|---|
| TAM instrument | [ ] | [ ] |
| Expert checklist (if used) | [ ] | [ ] |

## 6. Discussion
Interpret findings around the three core claims:

1. **Efficiency claim:** the system structures workflow and provides measurable processing indicators.
2. **Compliance claim:** completeness enforcement is operationalized, with measurable submission-status outcomes.
3. **Governance claim:** role boundaries and report/export controls are implemented; action-level governance evidence depends on audit-log availability.

Discuss both strengths and constraints (sample size, single-site implementation, policy context).

## 7. Conclusion
This study presents a role-based Faculty Portfolio Management System designed to resolve fragmented and inefficient academic compliance workflows. The artifact-level contribution is a deployable governance-oriented platform with required-document enforcement, structured workflow control, and report/export mechanisms for quality assurance operations. Current evidence supports an initial post-implementation profile using available operational records, while fuller claims on review-turnaround and audit traceability require additional datasets with populated review and audit logs.

## 8. Recommendations
1. Integrate notification automation for pending submissions and overdue reviews.
2. Add document version history for stronger traceability.
3. Expand multi-campus validation to improve external generalizability.
4. Develop longitudinal analytics for term-over-term compliance monitoring.

## 9. Submission Alignment Checklist
Before journal/conference submission, complete the following:

1. Replace all bracketed placeholders `[ ]` with actual evaluated values.
2. Add full literature citations aligned with target venue style (APA/IEEE/Elsevier).
3. Add ethics approval code/clearance statement.
4. Attach instrument appendices (SUS form, TAM items, expert checklist).
5. Conform manuscript to the target venue template and word limit.

## 10. Starter References (To Expand and Format Per Target Venue)
- Davis, F. D. (1989). Perceived usefulness, perceived ease of use, and user acceptance of information technology.
- Hevner, A. R., March, S. T., Park, J., & Ram, S. (2004). Design science in information systems research.
- ISO 9241-11. Ergonomics of human-system interaction — Usability definitions and concepts.
- Brooke, J. (1996). SUS: A quick and dirty usability scale.
