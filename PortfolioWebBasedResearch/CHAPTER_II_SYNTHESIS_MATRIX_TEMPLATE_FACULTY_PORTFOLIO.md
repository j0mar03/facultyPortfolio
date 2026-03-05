# CHAPTER II
## REVIEW OF RELATED LITERATURE AND STUDIES

This matrix is based on your prepared files in:
- `Portfolio Research/Synthesis Included Studies/*.md` (8 full-text syntheses)
- `Portfolio Research/Synthesis Included Studies/Included Items_Abstract.csv` (8 abstract-only items)

Evidence grading used:
- `Full text (PDF available)`
- `Abstract-only`

---

## A. Search Documentation Log

| Date | Database | Search String ID | Query Used | Filters Applied | Results Returned | Notes |
|---|---|---|---|---|---:|---|
| 2026-03-01 | Scopus | Q1 | Portfolio/faculty evaluation + HEI workflow/compliance/system terms | Mixed years in export; screened to 2021-2026 | 27 | Export screened and deduplicated locally. |
| 2026-03-01 | IEEE Xplore | Q2 | Faculty evaluation/e-portfolio/QA/system terms | Mixed years in export; screened to 2021-2026 | 11 | Export screened and deduplicated locally. |
| 2026-03-01 | ACM DL | Q3 | Faculty/e-portfolio/system/usability terms | Mixed years in export; screened to 2021-2026 | 10 | Export screened and deduplicated locally. |

---

## B. Inclusion and Exclusion Criteria

### Inclusion Criteria
1. 2021-2026 publication window.
2. Journal article or conference paper.
3. Relevance to at least one FPMS variable: workflow, compliance/document management, role-based governance, usability/acceptance, reporting/auditability.
4. Traceable citation metadata.

### Exclusion Criteria
1. Duplicate records.
2. Pre-2021 records.
3. Off-scope studies without meaningful transfer to faculty portfolio/compliance workflows.

---

## C. PRISMA-Style Screening Summary

| Stage | Count |
|---|---:|
| Records identified | 48 |
| Duplicates removed | 3 |
| Records screened | 45 |
| Studies included in synthesis matrix | 16 |
| Included with full text | 8 |
| Included as abstract-only | 8 |

Supporting files:
- Detailed screening decisions: `Portfolio Research/SCREENING_DECISIONS.md`
- Full-text synthesis forms: `Portfolio Research/Synthesis Included Studies/*.md`

---

## D. Master Synthesis Matrix (16 Included Studies)

| # | Authors (Year) | Title | Source | Evidence Level | Objective | Method/System Type | Data/Sample | Metrics/Indicators | Key Findings | Limitations | Gap Relevant to FPMS |
|---:|---|---|---|---|---|---|---|---|---|---|---|
| 1 | Fernando-Raguro et al. (2021) | Technology Management Framework for Smart University System in the Philippines | ACM | Full text (PDF available) | Develop smart-university technology management framework | Mixed methods / IT management framework | n=160 across 8 Metro Manila universities | Smartness level across 5 criteria | 50% universities at managed level; roadmap proposed | Geographically limited sample; limited external policy expert input | Macro policy framework only; lacks portfolio workflow, completeness, turnaround metrics |
| 2 | Gutiérrez y Restrepo & Floris (2022) | Supporting Teachers and Students Through a Smart Integrated System for Truly Inclusive Higher Education | ACM | Full text (PDF available) | Present integrated inclusive HEI platform (e-profiles/e-portfolios/chatbot) | Descriptive / Microservices architecture | Sample size NR (percentages from 3 universities) | Accessibility-awareness percentages | Strong accessibility gap among staff supports need for inclusive systems | Conceptual/under-development; no operational deployment metrics | No faculty portfolio compliance workflow, completeness, turnaround evidence |
| 3 | Ikwunne et al. (2021) | Design and Implementation of Collaborative Management System for Effective Learning | Scopus | Full text (PDF available) | Build collaborative web LMS during COVID disruption | DSR / Web-based CLMS | Sample size NR; technical test data | Unit/integration/alpha testing outcomes | Role-based system implemented with repository support | No live-user acceptance/effectiveness validation | Student LMS focus; lacks faculty compliance and review workflow metrics |
| 4 | Lee et al. (2023) | The structure and priorities of researchers' scholarly profile maintenance activities | Scopus | Full text (PDF available) | Analyze scholarly profile maintenance behavior in RIMS | Case study / Web-based RIMS analysis | n=3,738; 27,249 log sessions | Session duration, completeness activities | Identified major activity clusters and differential engagement patterns | Single-institution context | No mandatory submission/review/approval workflow or turnaround metrics |
| 5 | Miyazaki et al. (2021) | Developing a Generic Skill Assessment System Using Rubric and Checklists | Scopus | Full text (PDF available) | Develop web-based rubric/checklist assessment system | DSR / Laravel-based web assessment | n=51 engineering students | Usability preference percentages | 82% preferred new system over spreadsheets | Initial UI limitations; long-term effectiveness untested | Student assessment context; no faculty portfolio governance workflow |
| 6 | Reyes et al. (2023) | FEDesk: A Web and Mobile Document Management System for UST OFED | ACM | Full text (PDF available) | Automate OFED document tracking/storage workflow | DSR (Agile-Waterfall) / Web-mobile DMS | n=26 UAT users + automated tests | SUS, response time, test-case success | SUS=74.90; response mean=279.95 ms; 100% test success after fixes | Limited mobile functionality; no bulk submission; no native editing | Strong comparator but lacks required-document completeness and review turnaround metrics |
| 7 | Tasatanattakool et al. (2023) | E-PORTFOLIO FOR DIGITAL UNIVERSITIES USING SMART CONTRACTS | Scopus | Full text (PDF available) | Develop/evaluate blockchain-enabled e-portfolio concept | DSR / Conceptual model (eSCi-BCT) | n=9 experts | Suitability mean/SD | Overall model rated excellent (mean=4.67) | Conceptual validation only, no deployed operational testing | Advanced architecture but no practical faculty compliance workflow evidence |
| 8 | Zhao et al. (2024) | Construction and Evaluation of KPI Performance Appraisal Model in Personnel Management System | Scopus | Full text (PDF available) | Construct KPI-BSC fuzzy appraisal model | Mixed methods / Mathematical evaluation model | n=90 survey respondents | Perception percentages; fuzzy score | 41.1% saw current system as imperfect; model score=0.6264 > 0.6 baseline | Limitations not explicitly reported | Mathematical appraisal only; no software workflow/completeness/turnaround implementation |
| 9 | Badouri et al. (2025) | Enhancing Medical Education in Morocco: A Conceptual Framework for an ePortfolio System | IEEE | Abstract-only | Propose conceptual ePortfolio framework | Conceptual framework | NR | NR | ePortfolio potential for reflective practice and transition support | Abstract-level evidence only | Domain and artifact-level transfer limited without full methods/results |
| 10 | Zakaria et al. (2025) | Enhancing Lecturer Performance Insights: ML Framework for Predicting Student Evaluations | IEEE | Abstract-only | Improve lecturer evaluation with ML+NLP | Predictive analytics framework | NR | NR | Claims improved prediction and reduced bias | Abstract-level evidence only | Evaluation-prediction focus; not direct compliance workflow system |
| 11 | Sudha et al. (2025) | Assessing Research Quality in Academic Institutions Using Google Scholar Metrics | IEEE | Abstract-only | Automate research quality assessment | Web platform + analytics | NR | NR | Claims improved transparency and reduced manual effort | Abstract-level evidence only | Research-metric context, not faculty portfolio compliance workflow |
| 12 | Balisi et al. (2025) | Web and Mobile Application for Faculty Ranking System (NU Philippines) | IEEE | Abstract-only | Automate faculty reranking | Web-mobile app + analytics | NR | NR | Claims reduced processing time/errors | Abstract-level evidence only | Requires full text for rigorous benchmarking and methods comparison |
| 13 | Srija et al. (2025) | Data-Driven Faculty Appraisal Framework (AHP/TOPSIS/RF) | IEEE | Abstract-only | Improve fairness/transparency in appraisal | Web appraisal + analytics | NR | NR | Claims robust appraisal support | Abstract-level evidence only | Appraisal-centric; no explicit portfolio completeness/review workflow |
| 14 | Vázquez Noguera et al. (2024) | Impact of ANEAES Accreditation on Computer Engineering Program | IEEE | Abstract-only | Evaluate accreditation effects | Institutional impact analysis | NR | NR | Reports quality/process improvements under accreditation | Abstract-level evidence only | QA context support only; no software workflow artifact details |
| 15 | Gantikow et al. (2024) | Evaluating Interactive Concept Maps Produced from E-Portfolios | IEEE | Abstract-only | Improve e-portfolio assessment support | NLP-assisted visualization | NR | NR | Claims motivation/usability potential if workflow integrated | Abstract-level evidence only | Feature-level evidence, not full governance/compliance system |
| 16 | Ozeki et al. (2021) | Exploring the Future Trends of Faculty Development in Japanese Higher Education | IEEE | Abstract-only | Explore faculty development trends and QA links | Survey/comparative analysis | NR | NR | Highlights importance of educational data management | Abstract-level evidence only | Contextual QA support only; lacks direct FPMS workflow measures |

---

## E. Thematic Clustering Table

| Theme | Description | Related Studies (#) | Emerging Insight | Gap for Present Study |
|---|---|---|---|---|
| T1: Faculty Workflow Digitalization | Faculty document/evaluation systems and appraisal platforms | 2, 6, 8, 12, 13 | Digitization improves organization, consistency, and governance potential | Few complete lifecycle implementations (submission -> completeness -> review -> export) |
| T2: E-Portfolio Design and Support Tools | Conceptual and feature-focused e-portfolio studies | 7, 9, 15 | E-portfolio tools aid reflection, review, and learning documentation | Limited operational evidence for compliance-oriented faculty workflows |
| T3: QA/Accreditation and Governance | Institutional QA and policy-driven improvements | 1, 11, 14, 16 | QA context strongly motivates process formalization | Missing concrete system metrics for turnaround/completeness in many studies |
| T4: Information/Records Management Behavior | System interaction and profile-maintenance studies | 3, 4 | Records behavior insights can inform system design | Not focused on mandatory compliance submission pipelines |
| T5: Methodological Evidence Quality | Full-text vs abstract-only evidence | 1-8 vs 9-16 | Full-text set supports stronger claims; abstract-only set provides trend signals | Need more full-text studies with SUS/TAM and operational pre/post evidence |

---

## F. Methodological Comparison Table

| Study # | Research Design | Sample Size | System Implemented? | Main Indicators | Statistical Treatment | Reliability Reported? | Transferability to FPMS |
|---:|---|---:|---|---|---|---|---|
| 1 | Mixed methods | 160 | Framework-level | Smartness criteria levels | Descriptive | NR | Medium |
| 2 | Descriptive (conceptual architecture) | NR | Partially conceptual | Accessibility awareness | Descriptive percentages | NR | Medium |
| 3 | DSR | NR | Yes | Technical test outcomes | Descriptive | NR | Medium |
| 4 | Case study (system logs) | 3,738 users; 27,249 sessions | Existing RIMS analyzed | Session time, completeness activities | Descriptive/log analysis | NR | Medium |
| 5 | DSR | 51 | Yes | Usability preference | Descriptive percentages | NR | Medium |
| 6 | DSR | 26 | Yes | SUS, response time, test success | Descriptive (mean, %) | NR | High |
| 7 | DSR conceptual | 9 experts | Conceptual | Suitability ratings | Mean, SD | NR | Medium |
| 8 | Mixed/modeling | 90 | No (model only) | KPI perceptions, fuzzy score | Percentages, fuzzy eval | NR | Low |

---

## G. Metrics Benchmark Summary

| Metric | Status in Current Evidence | Notes |
|---|---|---|
| SUS | Available in full text (Study #6) | Use as initial benchmark; add more SUS studies if possible. |
| Processing time | Available in full text (Study #6); claimed in abstract-only studies | Prefer full-text numeric extraction only for benchmark tables. |
| Review turnaround time | Not clearly available | Contribution opportunity remains, but requires populated review records in your evaluation dataset. |
| Required-document completeness rate | Not clearly available | Core novelty candidate for Chapter IV; report with transparent data-window coverage. |
| TAM constructs | Limited in current full-text set | Add targeted TAM/usability papers if feasible. |
| Reliability (Cronbach's alpha) | Mostly NR | Report clearly in your own evaluation to strengthen rigor. |

---

## H. Research Gap Synthesis

### H.1 Empirical Gaps
1. Few studies provide a fully integrated faculty portfolio compliance workflow with mandatory document checks and audit-ready reporting.
2. Existing systems often emphasize appraisal/ranking or conceptual architecture rather than end-to-end compliance operations.
3. Operational metrics are fragmented across studies and rarely reported together.

### H.2 Methodological Gaps
1. Many studies rely on descriptive/system demonstration approaches without robust inferential analysis.
2. Survey reliability reporting is inconsistent.
3. Abstract-only evidence limits reproducibility and benchmarking quality.

### H.3 Contextual Gaps
1. Philippine-specific deployable evidence for faculty portfolio governance systems remains limited.
2. Role-based review routing tied to compliance completeness is underrepresented.
3. Export-ready institutional evidence generation is not consistently evaluated.

### H.4 Final Gap Statement

> The reviewed literature shows substantial progress in faculty appraisal tools, e-portfolio concepts, and institutional QA frameworks. However, evidence remains limited for a deployable role-based Faculty Portfolio Management System that unifies required-document completeness enforcement, structured review workflow, and audit-ready reporting with measurable operational and usability outcomes. This study addresses that gap by implementing and evaluating an integrated FPMS in a higher-education compliance context, while explicitly reporting indicators that are not measurable in specific data-extraction windows.

---

## I. Citation Tracking Sheet

For detailed include/exclude decisions and priorities, use:
- `Portfolio Research/SCREENING_DECISIONS.md`
- `Portfolio Research/INCLUDED_STUDIES_SHORTLIST.csv`
- `Portfolio Research/Synthesis Included Studies/Included Items_Abstract.csv`

---

## J. Writing Map for Chapter II

1. Faculty workflow and document-governance problems in HEI.
2. Existing system artifacts and portfolio/evaluation platforms.
3. QA/accreditation and governance requirements.
4. Usability/operational evidence synthesis (full-text priority).
5. Evidence limitations from abstract-only literature.
6. Final research gap leading to FPMS development and evaluation model.

### Next Step
- Use your 8 full-text syntheses as core evidence in prose.
- Use the 8 abstract-only items as supplementary trend support only.
- In Chapter III/IV, prioritize metrics currently missing in literature: `required-document completeness` and `turnaround time` when review records are available.
