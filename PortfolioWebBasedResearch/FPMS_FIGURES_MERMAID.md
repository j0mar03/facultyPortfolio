# FPMS Figures (Mermaid Source)

Use these Mermaid diagrams as source figures for your manuscript.  
Target filenames in Overleaf `figures/` are noted per figure.

---

## Figure 1: FPMS Role-Based Workflow Architecture
**Target file:** `fig01_fpms_workflow_architecture.png`

```mermaid
flowchart LR
    A[Faculty] --> B[Create Portfolio]
    B --> C[Upload Required Documents]
    C --> D{Completeness Check}
    D -- Incomplete --> C
    D -- Complete --> E[Submit Portfolio]

    E --> F[Chair/Reviewer Queue]
    F --> G{Review Decision}
    G -- Approve --> H[Approved]
    G -- Reject --> I[Rejected]

    H --> J[Admin/Auditor Monitoring]
    I --> J
    J --> K[Report/Export Evidence]
```

---

## Figure 2: Portfolio Status Distribution
**Target file:** `fig02_fpms_status_distribution.png`

```mermaid
pie showData
    title Portfolio Status Distribution (N=162)
    "Draft (133, 82.10%)" : 133
    "Approved (22, 13.58%)" : 22
    "Rejected (7, 4.32%)" : 7
```

---

## Figure 3: Processing Duration Summary
**Target file:** `fig03_fpms_processing_time_boxplot.png`

```mermaid
xychart-beta
    title "Processing Duration Summary (minutes)"
    x-axis ["Median", "Mean"]
    y-axis "Minutes" 0 --> 16000
    bar [4981.13, 14582.01]
```

---

## Figure 4: Review Turnaround Summary
**Target file:** `fig04_fpms_review_turnaround_boxplot.png`

```mermaid
xychart-beta
    title "Review Turnaround Summary (minutes)"
    x-axis ["Median", "Mean"]
    y-axis "Minutes" 0 --> 26000
    bar [24785.23, 23712.91]
```

---

## Figure 5: Construct Means (Survey)
**Target file:** `fig05_fpms_construct_means.png`

```mermaid
xychart-beta
    title "Construct Means (Likert 1-5; SUS shown on 0-100 scale separately)"
    x-axis ["WI", "CS", "RT", "GT", "PU", "PEOU", "BI"]
    y-axis "Mean" 0 --> 5
    bar [4.53, 4.75, 4.53, 4.30, 4.17, 4.17, 4.26]
```

---

## Figure 6: Results Flow (Operational + Survey to Findings)
**Target file:** `fig06_fpms_results_flow.png`

```mermaid
flowchart TD
    A[Operational Records] --> A1[162 Portfolios]
    A --> A2[Complete: 29/162 = 17.90%]
    A --> A3[Processing: Mean 14,582.01 / Median 4,981.13 min]
    A --> A4[Review Turnaround: Mean 23,712.91 / Median 24,785.23 min]
    A --> A5[Reviews: 29, Pending: 0]
    A --> A6[Audit Logs: Not Populated]

    B[Survey Data N=35] --> B1[WI 4.53]
    B --> B2[CS 4.75]
    B --> B3[RT 4.53]
    B --> B4[GT 4.30]
    B --> B5[SUS 62.00]
    B --> B6[TAM: PU 4.17, PEOU 4.17, BI 4.26]

    A1 --> C[Problem 1: Workflow Profile]
    A3 --> C
    A4 --> C
    B1 --> C
    B3 --> C

    A2 --> D[Problem 2: Compliance/Governance Profile]
    A6 --> D
    B2 --> D
    B4 --> D

    B5 --> E[Problem 3: Usability/Acceptance]
    B6 --> E

    C --> F[Integrated Conclusion]
    D --> F
    E --> F

    F --> G[FPMS is deployable and governance-oriented;
subscale reliability is exploratory;
audit evidence needs fuller logs]
```
