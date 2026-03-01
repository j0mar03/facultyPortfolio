# CHAPTER IV
## PRESENTATION, ANALYSIS, AND INTERPRETATION OF DATA

This chapter presents the empirical results of the Machine Learning-Based Early Warning System for predicting late or incomplete faculty portfolio submissions in a web-based Faculty Portfolio Management System. Results are organized according to the specific problems and objectives in Chapter I and the statistical treatment defined in Chapter III.

---

## 4.1 Dataset Profile and Label Distribution

This section presents the final modeling dataset after extraction, cleaning, feature construction, and target labeling.

### 4.1.1 Final Dataset Summary

| Dataset Component | Value |
|---|---:|
| Total records extracted and eligible | 143 |
| Records removed (invalid/duplicate/missing critical fields) | 0 |
| Final eligible records | 143 |
| Training set records (time-aware split) | 100 |
| Test set records (time-aware split) | 43 |
| Number of features used in modeling | 99 |
| Prediction target | At-Risk (1) vs Not At-Risk (0) |

The dataset was split using a time-aware strategy (70% older records for training, 30% newer records for testing) to preserve temporal realism.

### 4.1.2 Class Distribution

| Class Label | Count | Percentage |
|---|---:|---:|
| At-Risk (1) | 119 | 83.22% |
| Not At-Risk (0) | 24 | 16.78% |
| Total | 143 | 100% |

Interpretation:

- The dataset is class-imbalanced, with At-Risk cases as the majority class.
- Because of this imbalance, model assessment emphasizes not only accuracy but also precision, recall, F1-score, PR-AUC, and calibration reliability.

---

## 4.2 Features Used for Risk Prediction

This section addresses Specific Problem 1 by documenting the historical and behavioral factors represented in the model feature set.

### 4.2.1 Feature Groups Included in the Experiment

| Feature Group | Examples |
|---|---|
| Portfolio behavior indicators | `resubmission_count`, `portfolio_duration_days` |
| Portfolio completeness proxies | `item_count`, `item_type_count` |
| Required artifact indicators | `has_major_exam`, `has_sample_quiz`, `has_tos`, `has_class_list`, `has_grade_sheets`, `has_activity_rubrics` |
| Class offering context | `off_term`, `off_subject_id`, `off_faculty_id`, `off_year_start`, `off_section_num` |
| Class resource presence | `off_has_assignment_doc`, `off_has_im_link`, `off_has_syllabus_link` |

Interpretation:

- The experiment used operational system variables that are available in institutional records and can support practical early warning decisions.
- To avoid leakage, direct post-outcome indicators were excluded from the final model matrix.
- This section identifies factors used by the models; statistical significance ranking of individual feature effects was not part of this experiment run.

---

## 4.3 Predictive Performance of Candidate Models

This section addresses Specific Problems 2 and 3 by presenting model performance on the hold-out test set.

### 4.3.1 Model Evaluation Metrics (Test Set)

| Model | Accuracy | Precision | Recall | F1-score | ROC-AUC | PR-AUC |
|---|---:|---:|---:|---:|---:|---:|
| Logistic Regression | 97.67% | 100.00% | 97.22% | 98.59% | 1.0000 | 1.0000 |
| Random Forest | 95.35% | 94.74% | 100.00% | 97.30% | 1.0000 | 1.0000 |
| Gradient Boosting | 95.35% | 94.74% | 100.00% | 97.30% | 0.8571 | 0.9850 |

Interpretation:

- All three models produced high predictive performance on this dataset.
- Logistic Regression achieved the highest accuracy and F1-score.
- Random Forest and Gradient Boosting achieved perfect recall (100%), which is important in early warning settings where missing an at-risk case is costly.
- Random Forest and Logistic Regression both achieved perfect ROC-AUC and PR-AUC in this test run, while Gradient Boosting showed lower ROC-AUC and slightly lower PR-AUC.

### 4.3.2 Confusion Matrices (Test Set)

#### Logistic Regression

|  | Predicted Not At-Risk | Predicted At-Risk |
|---|---:|---:|
| Actual Not At-Risk | 7 (TN) | 0 (FP) |
| Actual At-Risk | 1 (FN) | 35 (TP) |

#### Random Forest

|  | Predicted Not At-Risk | Predicted At-Risk |
|---|---:|---:|
| Actual Not At-Risk | 5 (TN) | 2 (FP) |
| Actual At-Risk | 0 (FN) | 36 (TP) |

#### Gradient Boosting

|  | Predicted Not At-Risk | Predicted At-Risk |
|---|---:|---:|
| Actual Not At-Risk | 5 (TN) | 2 (FP) |
| Actual At-Risk | 0 (FN) | 36 (TP) |

Derived operational interpretation:

- Logistic Regression produced no false positives but had one false negative.
- Random Forest and Gradient Boosting produced zero false negatives but with two false positives each.
- For early warning deployment, a zero-false-negative behavior is often preferred because missing truly at-risk submissions may delay intervention.

---

## 4.4 Calibration and Risk Reliability

This section reports calibration reliability using Brier Score (lower is better).

### 4.4.1 Calibration Indicators

| Model | Brier Score | Calibration Assessment |
|---|---:|---|
| Logistic Regression | 0.0342 | Good calibration |
| Random Forest | 0.0335 | Best calibration among three |
| Gradient Boosting | 0.0456 | Weaker calibration relative to LR and RF |

Interpretation:

- All models show acceptable probability reliability in this run.
- Random Forest has the lowest Brier Score, indicating the most reliable risk probabilities among tested models.

---

## 4.5 Statistical Comparison of Model Performance

This section addresses Specific Problem 5 by testing pairwise differences using McNemar's test at `alpha = 0.05`.

### 4.5.1 McNemar's Test Results

| Model Pair | b | c | Test Statistic (chi-square) | p-value | Decision at alpha = 0.05 |
|---|---:|---:|---:|---:|---|
| Logistic Regression vs Random Forest | 2 | 1 | 0.0000 | 1.000000 | Fail to Reject H0 |
| Logistic Regression vs Gradient Boosting | 2 | 1 | 0.0000 | 1.000000 | Fail to Reject H0 |
| Random Forest vs Gradient Boosting | 0 | 0 | 0.0000 | 1.000000 | Fail to Reject H0 |

Interpretation:

- Pairwise error differences were not statistically significant in this test run.
- The models are statistically comparable under the present dataset and split.

### 4.5.2 Hypothesis Decision

- Null Hypothesis (H0): There is no significant difference in predictive performance among selected machine learning algorithms.
- Decision: Fail to Reject H0.
- Conclusion: At `alpha = 0.05`, no statistically significant pairwise difference was detected among Logistic Regression, Random Forest, and Gradient Boosting.

---

## 4.6 Deployment-Oriented Model Selection

This section synthesizes metric performance, calibration, and operational priorities for model selection.

### 4.6.1 Final Model Selection Rationale

| Selection Criterion | Logistic Regression | Random Forest | Gradient Boosting |
|---|---|---|---|
| Recall priority (early warning) | 97.22% | 100.00% | 100.00% |
| F1-score | 98.59% | 97.30% | 97.30% |
| Brier reliability | 0.0342 | 0.0335 | 0.0456 |
| ROC/PR behavior | 1.0000 / 1.0000 | 1.0000 / 1.0000 | 0.8571 / 0.9850 |
| False negatives | 1 | 0 | 0 |

Selected model for deployment-oriented early warning: **Random Forest**.

Rationale:

- It achieved perfect recall (no missed at-risk cases) on the test set.
- It has the best Brier Score, supporting probability-based intervention thresholds.
- It matched top ROC-AUC and PR-AUC behavior in this run.

---

## 4.7 Synthesis of Findings by Research Question

### Research Question 1
What historical and behavioral factors are associated with late or incomplete faculty portfolio submissions?

- Risk prediction used portfolio behavior indicators, portfolio content/completeness proxies, artifact presence indicators, and class offering context variables. These factors are operationally relevant for detecting potential submission risk.

### Research Question 2
Can machine learning models accurately predict at-risk submissions before the deadline?

- Yes. All tested models achieved high predictive performance (accuracy from 95.35% to 97.67%, recall from 97.22% to 100.00%, and high ROC-AUC/PR-AUC values).

### Research Question 3
Which algorithm provides the best predictive performance?

- Logistic Regression produced the highest accuracy and F1-score, while Random Forest provided perfect recall and best Brier reliability. For early warning operations where missed at-risk cases are critical, Random Forest is selected.

### Research Question 4
How can prediction results be integrated into the existing system?

- The experiment generated validated risk-scoring outputs and model comparison evidence to support system integration. The selected model can be connected to chair-facing risk tagging and intervention dashboards in the next implementation step.

### Research Question 5
Is there a statistically significant difference among selected models?

- No. McNemar's tests yielded `p = 1.000000` for all pairs; therefore, no significant pairwise difference was observed at `alpha = 0.05`.

---

## 4.8 Chapter Summary

This chapter presented the dataset characteristics, model performance, confusion matrices, calibration reliability, and statistical comparison results of the proposed early warning system. The empirical outputs indicate that machine learning can effectively identify at-risk faculty portfolio submissions. Although pairwise model differences were not statistically significant, Random Forest was selected for deployment-oriented use because it achieved perfect recall and the best calibration reliability (lowest Brier Score), supporting proactive chair-level intervention in the Faculty Portfolio Management System.
