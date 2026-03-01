# ABSTRACT

This study developed and evaluated a Machine Learning-Based Early Warning System for predicting late or incomplete faculty portfolio submissions in a web-based Faculty Portfolio Management System. The study addressed the need to shift portfolio monitoring from manual and reactive follow-ups to proactive, data-driven intervention support for department chairs and academic administrators.

Using institutional historical records, a supervised binary classification dataset was prepared with the target label At-Risk (1) versus Not At-Risk (0). The final dataset contained 143 eligible records, with 119 At-Risk (83.22%) and 24 Not At-Risk (16.78%) cases. A time-aware split was applied (100 training and 43 test records). Three models were developed and compared: Logistic Regression, Random Forest, and Gradient Boosting.

Results showed high predictive performance across models. Logistic Regression achieved 97.67% accuracy and 98.59% F1-score. Random Forest and Gradient Boosting both achieved 100.00% recall, while Random Forest provided the best probability reliability with the lowest Brier Score (0.0335), compared with Logistic Regression (0.0342) and Gradient Boosting (0.0456). Pairwise McNemar tests indicated no statistically significant differences among model errors at alpha = 0.05 (all p-values = 1.000000), leading to failure to reject the null hypothesis.

Although statistical differences were not significant, Random Forest was selected as the deployment-oriented model because of its zero false negatives and superior calibration reliability for risk-threshold decision support. The study concludes that machine learning can effectively support early identification of at-risk submissions and can be integrated into the existing system to enable timely, targeted interventions.

**Keywords:** machine learning, early warning system, faculty portfolio submission, risk prediction, random forest, calibration, McNemar test
