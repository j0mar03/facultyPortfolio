# **THE PROBLEM AND ITS SETTING**

## Introduction
Higher education institutions require faculty members to submit portfolio documents every academic term to support instructional monitoring, program governance, and compliance with quality assurance requirements. These records commonly include class lists, syllabi, examinations, rubrics, grade sheets, attendance records, and related instructional materials. In many departments, however, portfolio workflows still depend on fragmented channels such as paper folders, email threads, messaging attachments, and unstructured cloud storage. This fragmented practice creates operational risks: incomplete submissions pass unnoticed, review cycles become delayed, and retrieval of evidence for audits and accreditation becomes time-consuming.

The need for digital governance in faculty documentation is reinforced by related studies in higher education systems. Prior work demonstrates that document management and role-aware workflow systems can improve traceability and reduce manual process friction. At the same time, synthesis of available studies shows that many implementations remain either appraisal-centric, conceptual, or limited to narrow modules, without a fully integrated compliance workflow that combines completeness enforcement, structured review routing, and audit-ready reporting in one platform. This implementation gap is the practical motivation for the present study.

In response to this institutional problem, this research developed a **Faculty Portfolio Management System (FPMS)**, a web-based platform designed to centralize faculty portfolio workflows and formalize accountability across user roles (faculty, chair/reviewer, administrator, and auditor). The system links portfolios to class offerings, enforces required-document completeness prior to submission, supports review decisions with remarks and status transitions, and provides reporting/export features to strengthen evidence preparation for internal and external evaluation.

This study is positioned as an information systems development and evaluation research in higher education governance. It aims not only to produce a functional software artifact but also to generate measurable post-implementation evidence from available operational records, compliance-status outputs, and user acceptability measures.

## Theoretical and Conceptual Framework
This study is guided by an **Input-Process-Output (IPO) framework** and aligned with **Design Science Research (DSR)** logic for artifact-oriented system development.

### Input
The inputs include:
1. Institutional problems in manual/semi-digital faculty portfolio workflows.
2. Role requirements of faculty, chairs, administrators, and auditors.
3. Required-document policies for portfolio compliance.
4. Literature-based design and evaluation indicators (workflow, usability, governance).

### Process
The process includes:
1. Requirements analysis and workflow mapping.
2. Data model and role-based architecture design.
3. FPMS module development and integration.
4. System implementation and controlled operational use.
5. Quantitative evaluation using operational metrics and user survey measures.

### Output
The output is:
1. A deployable, role-based Faculty Portfolio Management System.
2. Quantitative post-implementation evidence on portfolio status behavior, processing duration, completeness support, usability/acceptance, and governance readiness indicators derived from available records.
3. A replicable evaluation framework for similar higher education compliance systems.

## Statement of the Problem
This study is generally aimed at designing, developing, and evaluating a web-based Faculty Portfolio Management System for higher education compliance workflows.

Specifically, it seeks to answer the following priority questions:

1. What is the post-implementation workflow profile of the developed FPMS in terms of process organization, status tracking, and measurable processing/turnaround indicators from available logs?

2. What is the post-implementation compliance and governance profile of the developed FPMS in terms of required-document completeness, role-based governance, and traceability/report readiness?

3. What is the level of usability and user acceptance of the developed FPMS as measured by System Usability Scale (SUS) and Technology Acceptance Model (TAM) constructs?

## Scope and Delimitations of the Study
This study focuses on the development and evaluation of a web-based Faculty Portfolio Management System for internal institutional use in higher education.

### Scope
The system covers:
1. Portfolio creation per assigned class offering.
2. Upload and management of required portfolio documents.
3. Required-document completeness checking before submission.
4. Role-based review workflow (chair/admin decision recording).
5. Administrative monitoring and report/export support.
6. Usability and acceptance assessment among intended user roles.

### Delimitations
The study does not include:
1. Public-facing access for external users.
2. AI-based automatic document content grading/validation.
3. Multi-institution deployment comparison in this phase.
4. Long-term predictive analytics beyond current workflow evaluation.
5. Full-text access to all related studies in Chapter II; abstract-only studies are used as supplementary evidence.
6. Some operational indicators (for example reviewer turnaround and audit-log event counts) depend on data availability in the selected extraction window and may be reported as not measurable when records are absent.

## Significance of the Study
This study is significant to the following stakeholders:

**Faculty Members.** The system provides clearer submission requirements, guided document completion, and improved visibility of portfolio status.

**Program Chairs/Reviewers.** The system supports structured review handling, status traceability, and improved monitoring of pending and completed reviews.

**Administrators and Auditors.** The system improves access to consolidated compliance records and supports report/export preparation for quality assurance and accreditation activities.

**Institutional Management.** The system strengthens governance by formalizing role boundaries, reducing manual workflow risk, and improving readiness for evidence-based evaluation.

**Researchers and Future Developers.** The study contributes an implementable model and evaluation framework for faculty compliance workflow systems in higher education contexts.

## Definition of Terms
The following terms are defined operationally for this study:

**Faculty Portfolio Management System (FPMS).** A web-based information system developed to manage submission, review, and reporting of faculty portfolio documents.

**Workflow Improvement.** The degree to which the system improves organization, tracking, and coordination of portfolio processes compared to manual/semi-digital workflows.

**Required-Document Completeness.** The extent to which all required portfolio document types are present before submission.

**Review Turnaround.** The elapsed time between portfolio submission and reviewer decision recording, when review records are available.

**Role-Based Access Control (RBAC).** Access logic that restricts system functions based on user roles (faculty, chair/reviewer, administrator, auditor).

**Traceability.** The ability of the system to record and monitor status changes and workflow history for accountability, with action-level traceability dependent on audit-log availability.

**Usability.** The extent to which users can effectively and efficiently use FPMS to complete intended tasks.

**User Acceptance.** Users’ perceived usefulness, perceived ease of use, and intention to continue using the system.

**System Usability Scale (SUS).** A standardized ten-item usability measure producing a score from 0 to 100.

**Technology Acceptance Model (TAM) Constructs.** Perceived Usefulness, Perceived Ease of Use, and Behavioral Intention indicators used to evaluate acceptance.
