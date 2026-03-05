# Chapter IV Experiment Outputs

## Dataset Counts

- Total records: 143
- Train records: 100
- Test records: 43
- Features used: 99
- At-Risk total: 119
- Not At-Risk total: 24

## Model Metrics (Test Set)

| Model | Accuracy | Precision | Recall | F1-score | ROC-AUC | PR-AUC | Brier |
|---|---:|---:|---:|---:|---:|---:|---:|
| Logistic Regression | 97.67% | 100.00% | 97.22% | 98.59% | 1.0000 | 1.0000 | 0.0342 |
| Random Forest | 95.35% | 94.74% | 100.00% | 97.30% | 1.0000 | 1.0000 | 0.0335 |
| Gradient Boosting | 95.35% | 94.74% | 100.00% | 97.30% | 0.8571 | 0.9850 | 0.0456 |

## Confusion Matrices (Test Set)

### Logistic Regression

- TN: 7
- FP: 0
- FN: 1
- TP: 35

### Random Forest

- TN: 5
- FP: 2
- FN: 0
- TP: 36

### Gradient Boosting

- TN: 5
- FP: 2
- FN: 0
- TP: 36

## McNemar Pairwise Tests (alpha=0.05)

| Pair | b | c | chi-square | p-value |
|---|---:|---:|---:|---:|
| LR_vs_RF | 2 | 1 | 0.0000 | 1.000000 |
| LR_vs_GB | 2 | 1 | 0.0000 | 1.000000 |
| RF_vs_GB | 0 | 0 | 0.0000 | 1.000000 |
