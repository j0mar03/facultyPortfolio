# **RESEARCH METHODOLOGY**

This chapter presents and describes the method of research, sources of data, population and sampling approach, research instruments, data-gathering procedure, statistical treatment, ethical considerations, and data analysis framework used in the development and evaluation of the Faculty Portfolio Management System (FPMS).

## Research Design

This study employs a **design-and-development research approach** with **quantitative descriptive evaluation**. The design is appropriate because the study focuses on building and validating a web-based system that addresses a real institutional workflow problem in higher education.

The development component follows a practical system development cycle: requirements analysis, architecture and database design, module implementation, testing, pilot use, and post-implementation evaluation. The evaluation component applies quantitative measures to profile process outcomes from available records and assess acceptability among intended users.

Specifically, the study evaluates the system using the core variables aligned with the Statement of the Problem:

1. Workflow efficiency (process organization, status tracking, and measurable processing/turnaround indicators)
2. Compliance and governance support (required-document completeness, role-based governance, and traceability/report readiness indicators)
3. Usability and user acceptance (SUS and TAM constructs)

This design is consistent with prior information systems studies where both artifact functionality and measurable institutional utility are required outputs.

## Sources of Data

The study uses both primary and secondary data sources.

**Primary Sources** include actual records and user-generated data from FPMS operation and evaluation activities, such as:

1. Portfolio status logs and timestamps
2. Required-document upload/completion records
3. Review actions and decision timestamps (when available in extracted records)
4. Administrative report outputs
5. Survey responses from faculty, chairs, and administrators

**Secondary Sources** include supporting references from Chapter II literature, institutional quality assurance policies, and system governance standards relevant to higher education compliance workflows.

## Data Availability Note

For the currently analyzed operational extraction window (`database/prod_dump.sql`, dump completed 2026-02-14 04:45:53), portfolio workflow records were available, while some governance/review logs were absent (`reviews`, `audit_logs`, `imports`, `jobs`, `failed_jobs` had zero inserted rows in the dump). Accordingly, indicators dependent on those tables are treated as not measurable for that dataset and are reported transparently as such.

## Population, Respondents, and Sampling Technique

The target respondents are stakeholders directly involved in faculty portfolio workflows:

1. Faculty members
2. Program chairs/reviewers
3. Academic administrators (and/or auditors, if included)

For system performance variables (workflow, completeness, turnaround), the study uses **complete enumeration** of eligible FPMS records within the selected evaluation period.

For usability and acceptance evaluation, the study uses **purposive sampling** of active system users who have completed actual workflow tasks.

Planned respondent profile for survey component (to be finalized before final manuscript):

- Faculty: `[n = __ ]`
- Chairs/Reviewers: `[n = __ ]`
- Administrators/Auditors: `[n = __ ]`
- Total: `[n = __ ]`

## Research Instrument

The study uses a multi-part researcher-developed evaluation instrument supported by system-generated logs.

### Chapter I - Chapter III Alignment Matrix

1. **Problem 1: Workflow efficiency**
   - Data source: portfolio logs (and review logs when available)
   - Measures: processing duration, status-flow indicators, and review turnaround duration when measurable
   - Survey support: WI and RT sections
2. **Problem 2: Compliance and governance support**
   - Data source: completeness records, role-based access behavior, report/export evidence, and audit logs when available
   - Measures: completeness rates, incomplete submission rates, and governance/traceability indicators with explicit availability flags
   - Survey support: CS and GT sections
3. **Problem 3: Usability and user acceptance**
   - Data source: user survey
   - Measures: SUS score, TAM constructs (PU, PEOU, BI)
   - Survey support: SUS and TAM sections

### 1. Operational Performance Extraction Sheet

A structured data extraction guide is used to capture objective process indicators from FPMS records:

1. Processing duration from portfolio start to submission
2. Review turnaround duration from submission to decision (if review records exist)
3. Required-document completeness rates
4. Governance/traceability indicators (role-constrained actions, report/export availability, and audit events if present)
5. Status transition frequencies (draft, submitted, approved, rejected)

### 2. Usability and Acceptance Questionnaire

A survey instrument is used to assess user perceptions of the developed system.

The instrument includes:

1. **System Usability Scale (SUS)** items for usability
2. **TAM-aligned items** for acceptance dimensions:
   - Perceived Usefulness
   - Perceived Ease of Use
   - Behavioral Intention to Use

Responses use a five-point Likert scale.

| Rating | Numerical Scale | Verbal Interpretation | Interpretation Level |
|---|---|---|---|
| 5 | 4.21 - 5.00 | Strongly Agree | Highly Acceptable |
| 4 | 3.41 - 4.20 | Agree | Acceptable |
| 3 | 2.61 - 3.40 | Neutral | Moderately Acceptable |
| 2 | 1.81 - 2.60 | Disagree | Slightly Acceptable |
| 1 | 1.00 - 1.80 | Strongly Disagree | Not Acceptable |

### 3. Governance and Traceability Checklist

A checklist is used to verify system governance capabilities, including:

1. Role-based access enforcement
2. Audit trail and action traceability (or explicit notation of unavailable audit records)
3. Availability of report/export evidence

## Data-Gathering Procedure

Data gathering is conducted in phased sequence.

### Phase 1: Preparation and Instrument Finalization

1. Finalize inclusion criteria for records and respondents.
2. Validate survey and extraction instruments.
3. Orient participants regarding study scope and consent.

### Phase 2: System Operation and Record Capture

1. Operate FPMS within actual institutional workflow conditions.
2. Capture system logs and document status records during the evaluation period.
3. Extract objective metrics for workflow and completeness; compute turnaround/traceability indicators only where source records are available.

### Phase 3: Survey Administration

1. Administer usability and acceptance questionnaire to eligible users.
2. Check response completeness and consistency.
3. Encode and prepare data for statistical analysis.

### Phase 4: Data Quality Assurance

1. Remove duplicate or invalid records.
2. Verify timestamp consistency for duration calculations.
3. Apply missing-data handling rules and document exclusions.

## Statistical Treatment of Data

The study uses descriptive and inferential statistics, where applicable.

### 1. Descriptive Statistics

1. Frequency and percentage distributions
2. Weighted mean and standard deviation for Likert-based variables
3. Mean/median process durations for operational metrics

Formula for percentage:

`P = (n / N) x 100`

Where:
- `P` = percentage
- `n` = frequency
- `N` = total cases

### 2. Operational Comparisons

This study uses post-implementation operational records from the FPMS period. Since historical manual-process baseline records are unavailable in comparable digital form, evaluation focuses on descriptive post-implementation metrics for:

1. Processing time
2. Review turnaround time (if measurable from available logs)
3. Completeness rate
4. Governance-support indicators (where quantifiable from available records)

Optional tests (based on data distribution and sample properties and only when analytically feasible):

1. Mann-Whitney U or Kruskal-Wallis tests for subgroup comparisons
2. Chi-square or Fisher's exact test for categorical differences

### 3. Usability and Acceptance Analysis

1. SUS overall score computation
2. Weighted mean per TAM construct
3. Positive response rate (Agree + Strongly Agree)

### 4. Reliability Analysis

For multi-item constructs, internal consistency is assessed using Cronbach's alpha.

## Data Analysis Framework

Analysis is organized around the study variables:

1. **Workflow Efficiency (Problem 1)**
   - Compare process organization, status tracking performance, and measurable turnaround indicators
2. **Compliance and Governance Support (Problem 2)**
   - Measure required-document completeness and governance/traceability evidence with explicit data-availability notes
3. **Usability and User Acceptance (Problem 3)**
   - Evaluate SUS and TAM-aligned perception scores

The interpretation integrates numerical results with observed workflow behavior to produce practical implementation conclusions.

## Ethical Considerations

The study observes ethical and data privacy requirements for institutional systems research.

1. Participation in survey components is voluntary and based on informed consent.
2. Only authorized institutional records are used.
3. Personal identifiers are removed or anonymized during analysis.
4. Data access is restricted to the research team.
5. Findings are presented in aggregate form to prevent respondent identification.
6. The system is evaluated as decision-support infrastructure; formal administrative decisions remain under authorized personnel.

## Methodological Delimitations

1. Findings are limited to the institutional context and evaluation period covered.
2. Operational outcomes depend on actual usage behavior during implementation.
3. Some literature comparators are abstract-only and are used only as supplementary context.
4. External factors outside system records (for example sudden policy changes) are outside the measured model scope.

## Chapter Summary

This chapter established the methodological framework for developing and evaluating the Faculty Portfolio Management System using a design-and-development approach with quantitative evaluation. It specified the data sources, respondent coverage, instruments, collection procedures, statistical treatment, and ethical safeguards that guide the assessment of workflow efficiency, compliance completeness, turnaround time, usability, and governance outcomes.
