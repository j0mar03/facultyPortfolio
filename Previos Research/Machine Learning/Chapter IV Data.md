Yes. Here are the actual outputs from your local faculty portfolio dataset run:

  Counts

  - Total records: 143
  - Train records: 100
  - Test records: 43
  - Features used: 99
  - At-Risk total: 119
  - Not At-Risk total: 24

  Test Metrics

  - Logistic Regression: Accuracy 97.67%, Precision 100.00%, Recall 97.22%, F1 98.59%, ROC-AUC 1.0000, PR-AUC 1.0000, Brier 0.0342
  - Random Forest: Accuracy 95.35%, Precision 94.74%, Recall 100.00%, F1 97.30%, ROC-AUC 1.0000, PR-AUC 1.0000, Brier 0.0335
  - Gradient Boosting: Accuracy 95.35%, Precision 94.74%, Recall 100.00%, F1 97.30%, ROC-AUC 0.8571, PR-AUC 0.9850, Brier 0.0456

  Confusion Matrices

  - Logistic Regression: TN 7, FP 0, FN 1, TP 35
  - Random Forest: TN 5, FP 2, FN 0, TP 36
  - Gradient Boosting: TN 5, FP 2, FN 0, TP 36

  McNemar p-values (alpha = 0.05)

  - LR vs RF: b=2, c=1, chi-square=0.0000, p-value=1.000000
  - LR vs GB: b=2, c=1, chi-square=0.0000, p-value=1.000000
  - RF vs GB: b=0, c=0, chi-square=0.0000, p-value=1.000000

  Saved outputs:

  - scripts/ch4_experiment_outputs.md
  - scripts/ch4_experiment_outputs.json

  If you want, I can now insert these directly into CHAPTER_IV.md in thesis-ready format.