# CHAPTER IV DIAGRAMS (MERMAID)

Use the following diagrams as insertion-ready figures for Chapter IV.

## Figure 4.1. Chapter IV Experimental Pipeline

```mermaid
flowchart LR
    A[Raw Faculty Portfolio Records<br/>n=143] --> B[Data Preparation<br/>Cleaning + Feature Engineering]
    B --> C[Time-Aware Split<br/>Train=100 | Test=43]
    C --> D1[Logistic Regression]
    C --> D2[Random Forest]
    C --> D3[Gradient Boosting]
    D1 --> E[Model Evaluation]
    D2 --> E
    D3 --> E
    E --> F[Metrics<br/>Accuracy, Precision, Recall, F1, ROC-AUC, PR-AUC]
    E --> G[Reliability<br/>Brier Score]
    E --> H[Statistical Comparison<br/>McNemar Test]
    F --> I[Deployment-Oriented Selection]
    G --> I
    H --> I
    I --> J[Selected Model: Random Forest]
```

## Figure 4.2. Class Distribution of Final Dataset

```mermaid
pie title Class Distribution (n=143)
    "At-Risk (119, 83.22%)" : 119
    "Not At-Risk (24, 16.78%)" : 24
```

## Figure 4.3. Test-Set Accuracy Comparison

```mermaid
flowchart LR
    T[Test Set n=43] --> M1[Logistic Regression<br/>Accuracy = 97.67%]
    T --> M2[Random Forest<br/>Accuracy = 95.35%]
    T --> M3[Gradient Boosting<br/>Accuracy = 95.35%]

    M1 --> R1[Rank 1]
    M2 --> R2[Rank 2 tie]
    M3 --> R2

    D1[Delta vs best:<br/>RF = -2.32 pp<br/>GB = -2.32 pp]
    M1 -.-> D1
    M2 -.-> D1
    M3 -.-> D1

    classDef best fill:#c8f7dc,stroke:#1b7f3b,stroke-width:2px,color:#0f5132;
    classDef good fill:#e8f1ff,stroke:#2357a5,stroke-width:1.5px,color:#1a365d;
    classDef note fill:#fff6db,stroke:#a07900,stroke-width:1px,color:#6b4e00;
    class M1,R1 best;
    class M2,M3,R2 good;
    class D1 note;
```

## Figure 4.4. Recall and False Negative Trade-off

```mermaid
flowchart TB
    O[Early Warning Objective<br/>Maximize Recall and Minimize FN] --> Z

    subgraph Z[Model Outcomes]
        direction LR
        A[Logistic Regression<br/>Recall = 97.22%<br/>FN = 1]
        B[Random Forest<br/>Recall = 100.00%<br/>FN = 0]
        C[Gradient Boosting<br/>Recall = 100.00%<br/>FN = 0]
    end

    A --> IA[Near-optimal detection;<br/>1 at-risk case missed]
    B --> IB[Optimal detection;<br/>no at-risk case missed]
    C --> IC[Optimal detection;<br/>no at-risk case missed]

    IB --> S[Operational priority zone<br/>Recall 100% and FN 0]
    IC --> S

    classDef priority fill:#c8f7dc,stroke:#1b7f3b,stroke-width:2px,color:#0f5132;
    classDef acceptable fill:#e8f1ff,stroke:#2357a5,stroke-width:1.5px,color:#1a365d;
    classDef insight fill:#fff6db,stroke:#a07900,stroke-width:1px,color:#6b4e00;
    class B,C,IB,IC,S priority;
    class A,IA acceptable;
    class O insight;
```

## Figure 4.5. Brier Score (Calibration Reliability)

```mermaid
flowchart LR
    L[Calibration Criterion<br/>Lower Brier = Better Reliability] --> R1

    R1[Rank 1<br/>Random Forest<br/>Brier = 0.0335] --> R2[Rank 2<br/>Logistic Regression<br/>Brier = 0.0342]
    R2 --> R3[Rank 3<br/>Gradient Boosting<br/>Brier = 0.0456]

    R1 -.-> N1[Best calibrated]
    R2 -.-> N2[Close to best<br/>Delta = +0.0007]
    R3 -.-> N3[Lower reliability<br/>Delta = +0.0121]

    classDef best fill:#c8f7dc,stroke:#1b7f3b,stroke-width:2px,color:#0f5132;
    classDef mid fill:#e8f1ff,stroke:#2357a5,stroke-width:1.5px,color:#1a365d;
    classDef weak fill:#fde2e2,stroke:#b02a37,stroke-width:1.5px,color:#6b0f1a;
    classDef note fill:#fff6db,stroke:#a07900,stroke-width:1px,color:#6b4e00;
    class R1,N1 best;
    class R2,N2 mid;
    class R3,N3 weak;
    class L note;
```

## Figure 4.6. McNemar Pairwise Significance Results

```mermaid
flowchart LR
    AL[Significance Level<br/>alpha = 0.05]

    AL --> P1[LR vs RF<br/>b=2, c=1<br/>chi-square=0.0000<br/>p=1.000000]
    AL --> P2[LR vs GB<br/>b=2, c=1<br/>chi-square=0.0000<br/>p=1.000000]
    AL --> P3[RF vs GB<br/>b=0, c=0<br/>chi-square=0.0000<br/>p=1.000000]

    P1 --> D1[Fail to Reject H0]
    P2 --> D2[Fail to Reject H0]
    P3 --> D3[Fail to Reject H0]

    D1 --> G[Global Interpretation<br/>No significant pairwise difference]
    D2 --> G
    D3 --> G

    classDef test fill:#e8f1ff,stroke:#2357a5,stroke-width:1.5px,color:#1a365d;
    classDef decision fill:#fff6db,stroke:#a07900,stroke-width:1px,color:#6b4e00;
    classDef summary fill:#c8f7dc,stroke:#1b7f3b,stroke-width:2px,color:#0f5132;
    class P1,P2,P3 test;
    class D1,D2,D3,AL decision;
    class G summary;
```
