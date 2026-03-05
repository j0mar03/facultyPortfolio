# BTEMS FPMS Results Fill-Up Template

Use this file to encode your final survey and analysis outputs once.
After you fill this up, I can transfer everything into `BTEMS_FINAL_PAPER_ASSEMBLY_FPMS.md`.

---

## 1. Study Window and Sample

- Evaluation period start: `2025-11-12 03:59:55`
- Evaluation period end: `2026-02-14 03:26:54`
- Faculty respondents (`n`): `27`
- Chair/Reviewer respondents (`n`): `4`
- Admin/Auditor respondents (`n`): `4` (Auditor)
- Total respondents (`N`): `35`

### 1.1 Respondent Profile Table (Final Values)

| Role | n | % |
|---|---:|---:|
| Faculty | 27 | 77.14 |
| Chair/Reviewer | 4 | 11.43 |
| Admin/Auditor | 4 | 11.43 |
| Total | 35 | 100 |

---

## 2. Operational Metrics (System Logs)

- Total portfolios: `143`
- Complete submissions count: `24`
- Complete submissions %: `16.78`
- Incomplete submissions count: `119`
- Incomplete submissions %: `83.22`
- Mean processing duration (`created_at` to `submitted_at`, submitted only): `12,319.96`
- SD processing duration: `N/A (not yet provided from log extract)`
- Review records available? (`Yes/No`): `No`
- Mean review turnaround (if available): `N/A`
- SD review turnaround (if available): `N/A`

### 2.1 Workflow and Turnaround Table

| Metric | FPMS Mean (SD) | Unit | Notes |
|---|---:|---|---|
| Processing duration | 12,319.96 (N/A) | minutes | Mean from `created_at` to `submitted_at` for submitted records (`n=24`) |
| Review turnaround | N/A | minutes | No review records available in extracted dataset |

### 2.2 Compliance and Governance Table

| Indicator | FPMS Value | Unit/Notes |
|---|---:|---|
| Complete submissions | 16.78 | % (24/143) |
| Incomplete submissions | 83.22 | % (119/143) |
| Rejection/resubmission rate | 0.00 | % (as reported in operational summary) |
| Role-based access enforcement status | Implemented | Role-based schema and workflow controls present |
| Traceability/report readiness indicator | Partial | Portfolio timestamps available; review/audit rows absent in extraction |

---

## 3. Survey Construct Results (Means and SD)

Fill using your computed construct-level outputs.

| Construct | Mean | SD | Interpretation |
|---|---:|---:|---|
| Workflow Improvement (WI) | 4.53 | 0.21 | Highly Acceptable |
| Completeness Support (CS) | 4.75 | 0.19 | Highly Acceptable |
| Review Turnaround Support (RT) | 4.53 | 0.17 | Highly Acceptable |
| Governance and Traceability (GT) | 4.30 | 0.17 | Highly Acceptable |
| Perceived Usefulness (PU) | 4.17 | 0.30 | Acceptable |
| Perceived Ease of Use (PEOU) | 4.17 | 0.17 | Acceptable |
| Behavioral Intention (BI) | 4.26 | 0.21 | Highly Acceptable |

### 3.1 Problem-Aligned Grouped Means

| Problem Cluster | Formula | Mean | SD | Interpretation |
|---|---|---:|---:|---|
| Problem 1: Workflow Profile | mean(WI + RT items) | 4.53 | 0.19 | Highly Acceptable |
| Problem 2: Compliance/Governance | mean(CS + GT items) | 4.53 | 0.18 | Highly Acceptable |
| Problem 3: Usability/Acceptance | SUS + TAM summary | SUS=62.00; TAM=4.20 | SUS SD=2.25; TAM SD=0.23 | SUS: Marginal/Acceptable; TAM: Acceptable |

---

## 4. SUS Results

### 4.1 SUS Item Means (Optional but Recommended)

| SUS Item | Mean Response (1-5) |
|---|---:|
| SUS1 | 4.03 |
| SUS2 | 3.94 |
| SUS3 | 4.34 |
| SUS4 | 2.89 |
| SUS5 | 4.06 |
| SUS6 | 2.91 |
| SUS7 | 4.00 |
| SUS8 | 3.00 |
| SUS9 | 4.03 |
| SUS10 | 2.91 |

### 4.2 SUS Overall

- SUS overall score (0-100): `62.00`
- SUS SD: `2.25`
- SUS interpretation band: `Marginal (acceptable with improvements needed)`

| Construct | Mean | SD | Interpretation |
|---|---:|---:|---|
| SUS overall score | 62.00 | 2.25 | Marginal / Acceptable |

---

## 5. Reliability (Cronbach's Alpha)

| Scale | Cronbach's alpha | Interpretation |
|---|---:|---|
| WI | 0.109 | Very low internal consistency |
| CS | -0.045 | Not acceptable; possible low variance/item direction issue |
| RT | -0.395 | Not acceptable; possible low variance/item direction issue |
| GT | 0.170 | Very low internal consistency |
| PU | 0.896 | Good internal consistency |
| PEOU | -0.097 | Not acceptable; possible low variance/item direction issue |
| BI | -0.088 | Not acceptable; possible low variance/item direction issue |
| SUS (optional) | 0.110 | Very low internal consistency (raw-item alpha) |

---

## 6. Optional Inferential Statistics

If you ran subgroup or comparative tests, fill this. If not, keep as `Not applied` with reason.

| Comparison | Test Used | Statistic | p-value | Effect Size | Decision |
|---|---|---:|---:|---:|---|
| Example: Faculty vs Chairs (PU) |  |  |  |  |  |
| Example: Role vs SUS |  |  |  |  |  |

- Inferential analysis status: `Not applied`
- Reason (if not applied): `Small and imbalanced role groups (Faculty=27, Chair=4, Auditor=4); analysis focused on descriptive post-implementation profiling.`

---

## 7. Final Results Paragraphs (Paste-Ready)

### 7.1 Abstract Numeric Sentence

`From the evaluation window 2025-11-12 03:59:55 to 2026-02-14 03:26:54, FPMS recorded 143 portfolios with a completeness rate of 16.78% (24/143) and mean processing duration of 12,319.96 minutes (SD not available in current log summary). Usability and acceptance results showed SUS 62.00, PU 4.17, PEOU 4.17, and BI 4.26; reliability coefficients ranged from -0.395 to 0.896.`

### 7.2 Results Summary (for Discussion)

`The findings indicate that FPMS provided measurable workflow structuring and compliance support. Workflow indicators showed a high perceived workflow profile (WI=4.53; RT=4.53), while governance/compliance indicators showed high perceived support (CS=4.75; GT=4.30) and an observed completeness rate of 16.78%. User evaluation reflected acceptable to highly acceptable perception levels, with TAM construct means from 4.17 to 4.26 and SUS at 62.00.`

### 7.3 Conclusion Evidence Sentence

`Based on post-implementation records and user evaluation, the FPMS demonstrated practical value as a role-based compliance workflow platform, with strongest evidence in process structuring, completeness support, and user acceptance; however, review-turnaround and action-level audit conclusions remain limited by absent review/audit records in the extracted dataset.`

---

## 8. Submission-Ready Checks

- [x] All placeholders in this file replaced with numbers/text
- [ ] Values match your SPSS/Excel/R output exactly
- [ ] Same values copied into `BTEMS_FINAL_PAPER_ASSEMBLY_FPMS.md`
- [ ] Abstract, Results, Discussion, Conclusion use consistent numbers
- [ ] Similarity check target maintained (<20%)
