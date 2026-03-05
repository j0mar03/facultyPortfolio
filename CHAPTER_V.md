# CHAPTER V
## SUMMARY, CONCLUSIONS, AND RECOMMENDATIONS

This chapter presents the summary of findings, conclusions, and recommendations based on the results of the study titled **Machine Learning-Based Early Warning System for Predicting Late or Incomplete Faculty Portfolio Submissions in a Web-Based Faculty Portfolio Management System**.

---

## 5.1 Summary of Findings

This study developed and evaluated a machine learning-based early warning approach using historical records from the Faculty Portfolio Management System. The objective was to predict at-risk portfolio submissions and identify a suitable model for deployment-oriented decision support.

The major findings are as follows:

1. The final modeling dataset contained 143 eligible records, with 119 At-Risk (83.22%) and 24 Not At-Risk (16.78%) cases, indicating class imbalance.
2. A time-aware split was used (100 training records and 43 test records) to preserve temporal realism.
3. Three supervised models were evaluated: Logistic Regression, Random Forest, and Gradient Boosting.
4. On the test set, Logistic Regression achieved the highest accuracy (97.67%) and F1-score (98.59%).
5. Random Forest and Gradient Boosting achieved perfect recall (100.00%), while Logistic Regression had 97.22% recall.
6. Random Forest obtained the lowest Brier Score (0.0335), followed by Logistic Regression (0.0342) and Gradient Boosting (0.0456), indicating best probability reliability for Random Forest.
7. McNemar pairwise tests showed no statistically significant differences among models at alpha = 0.05 (`p = 1.000000` for all pairs), resulting in failure to reject the null hypothesis.
8. Considering early warning operational needs (especially minimizing false negatives), Random Forest was selected as the deployment-oriented model.

---

## 5.2 Conclusions

Based on the findings, the following conclusions are drawn:

1. Historical portfolio and class-offering data can be used to build an effective early warning model for identifying likely late or incomplete submissions.
2. Machine learning models demonstrated high predictive capability in this institutional dataset, supporting the feasibility of predictive analytics in faculty compliance workflows.
3. While Logistic Regression performed best in accuracy and F1-score, Random Forest is more appropriate for operational early warning because it achieved zero false negatives and the best calibration reliability.
4. There is no statistically significant pairwise performance difference among the tested algorithms under the current dataset and evaluation split.
5. The proposed ML-based early warning approach can provide practical decision support for chairs and administrators by enabling earlier risk-based intervention.

---

## 5.3 Recommendations

In view of the results and study delimitations, the following are recommended:

1. Deploy the Random Forest model as the initial risk-scoring engine in the Faculty Portfolio Management System, with clear risk thresholds for chair-level follow-up.
2. Implement periodic model retraining (e.g., every term or academic year) to maintain accuracy and calibration as submission behavior changes.
3. Expand the dataset by including additional academic periods and records to improve generalizability and robustness.
4. Add richer pre-deadline behavioral indicators (e.g., activity timelines, interim uploads, reminder-response patterns) to improve early-stage prediction.
5. Conduct prospective validation in live operations and compare offline performance with in-system inference behavior.
6. Evaluate fairness and subgroup performance (e.g., by department, subject cluster, and term) to ensure equitable intervention support.
7. Compare additional algorithms and thresholding strategies, including cost-sensitive tuning that explicitly prioritizes false-negative reduction.

---

## 5.4 Suggested Future Research

Future studies may consider:

1. Multi-institution datasets to test external validity of the early warning model.
2. Explainability-enhanced deployment (e.g., local feature contribution displays for each risk prediction).
3. Hybrid models combining statistical rules and ML probabilities for policy-aligned interventions.
4. Impact evaluation studies measuring whether early warning deployment reduces late or incomplete submissions over time.

---

## 5.5 Closing Statement

The study confirms that a machine learning-based early warning system is both technically feasible and operationally valuable for improving faculty portfolio submission monitoring. With calibrated risk scoring and recall-oriented model selection, the system can shift portfolio compliance management from reactive monitoring to proactive intervention.
