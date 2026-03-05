#!/usr/bin/env python3
import math
import json
import random
from datetime import datetime
from collections import defaultdict

SQL_PATH = 'database/prod_dump.sql'
OUT_JSON = 'scripts/ch4_experiment_outputs.json'
OUT_MD = 'scripts/ch4_experiment_outputs.md'

RANDOM_SEED = 42
random.seed(RANDOM_SEED)


def parse_insert_values_blob(blob):
    rows = []
    i = 0
    n = len(blob)
    while i < n:
        while i < n and blob[i] != '(':
            i += 1
        if i >= n:
            break
        i += 1
        row = []
        cur = []
        in_str = False
        escape = False
        while i < n:
            ch = blob[i]
            if in_str:
                if escape:
                    cur.append(ch)
                    escape = False
                elif ch == '\\':
                    escape = True
                elif ch == "'":
                    in_str = False
                else:
                    cur.append(ch)
            else:
                if ch == "'":
                    in_str = True
                elif ch == ',':
                    tok = ''.join(cur).strip()
                    row.append(tok)
                    cur = []
                elif ch == ')':
                    tok = ''.join(cur).strip()
                    row.append(tok)
                    rows.append(row)
                    i += 1
                    break
                else:
                    cur.append(ch)
            i += 1
    return rows


def load_table_rows(sql_text, table):
    marker = f"INSERT INTO `{table}` VALUES "
    idx = 0
    all_rows = []
    while True:
        start = sql_text.find(marker, idx)
        if start == -1:
            break
        end = sql_text.find(';\n', start)
        if end == -1:
            end = sql_text.find(';', start)
        blob = sql_text[start + len(marker):end]
        all_rows.extend(parse_insert_values_blob(blob))
        idx = end + 1
    return all_rows


def to_none(v):
    if v == 'NULL' or v == '':
        return None
    return v


def to_int(v, default=0):
    if v is None:
        return default
    try:
        return int(v)
    except Exception:
        return default


def parse_dt(v):
    if not v or v == 'NULL':
        return None
    return datetime.strptime(v, '%Y-%m-%d %H:%M:%S')


def sigmoid(x):
    if x >= 0:
        z = math.exp(-x)
        return 1.0 / (1.0 + z)
    z = math.exp(x)
    return z / (1.0 + z)


def dot(a, b):
    return sum(x * y for x, y in zip(a, b))


class LogisticRegressionGD:
    def __init__(self, lr=0.05, epochs=500, l2=0.001):
        self.lr = lr
        self.epochs = epochs
        self.l2 = l2
        self.w = None
        self.b = 0.0

    def fit(self, X, y):
        n = len(X)
        d = len(X[0]) if n else 0
        self.w = [0.0] * d
        self.b = 0.0
        for _ in range(self.epochs):
            grad_w = [0.0] * d
            grad_b = 0.0
            for i in range(n):
                z = dot(self.w, X[i]) + self.b
                p = sigmoid(z)
                err = p - y[i]
                for j in range(d):
                    grad_w[j] += err * X[i][j]
                grad_b += err
            for j in range(d):
                grad_w[j] = grad_w[j] / n + self.l2 * self.w[j]
                self.w[j] -= self.lr * grad_w[j]
            self.b -= self.lr * (grad_b / n)

    def predict_proba(self, X):
        return [sigmoid(dot(self.w, x) + self.b) for x in X]


class DecisionTreeNode:
    __slots__ = ('feature', 'thr', 'left', 'right', 'prob', 'leaf')

    def __init__(self, leaf=False, prob=0.5, feature=None, thr=None, left=None, right=None):
        self.leaf = leaf
        self.prob = prob
        self.feature = feature
        self.thr = thr
        self.left = left
        self.right = right


def gini_impurity(labels):
    n = len(labels)
    if n == 0:
        return 0.0
    p = sum(labels) / n
    return 1.0 - p * p - (1 - p) * (1 - p)


class DecisionTreeClassifierSimple:
    def __init__(self, max_depth=5, min_samples_split=8, max_features=None):
        self.max_depth = max_depth
        self.min_samples_split = min_samples_split
        self.max_features = max_features
        self.root = None

    def _best_split(self, X, y, feats):
        n = len(y)
        base = gini_impurity(y)
        best_gain = 0.0
        best = None
        for f in feats:
            vals = sorted(set(row[f] for row in X))
            if len(vals) <= 1:
                continue
            # candidate thresholds from midpoints
            candidates = []
            for i in range(len(vals) - 1):
                candidates.append((vals[i] + vals[i + 1]) / 2.0)
            for thr in candidates:
                ly, ry = [], []
                for i in range(n):
                    if X[i][f] <= thr:
                        ly.append(y[i])
                    else:
                        ry.append(y[i])
                if not ly or not ry:
                    continue
                g = base - (len(ly) / n) * gini_impurity(ly) - (len(ry) / n) * gini_impurity(ry)
                if g > best_gain:
                    best_gain = g
                    best = (f, thr)
        return best, best_gain

    def _build(self, X, y, depth):
        n = len(y)
        p = sum(y) / n if n else 0.0
        if depth >= self.max_depth or n < self.min_samples_split or p in (0.0, 1.0):
            return DecisionTreeNode(leaf=True, prob=p)

        d = len(X[0])
        feats = list(range(d))
        if self.max_features is not None and self.max_features < d:
            random.shuffle(feats)
            feats = feats[:self.max_features]

        best, gain = self._best_split(X, y, feats)
        if best is None or gain <= 1e-8:
            return DecisionTreeNode(leaf=True, prob=p)

        f, thr = best
        XL, yL, XR, yR = [], [], [], []
        for i in range(n):
            if X[i][f] <= thr:
                XL.append(X[i]); yL.append(y[i])
            else:
                XR.append(X[i]); yR.append(y[i])

        left = self._build(XL, yL, depth + 1)
        right = self._build(XR, yR, depth + 1)
        return DecisionTreeNode(leaf=False, feature=f, thr=thr, left=left, right=right)

    def fit(self, X, y):
        self.root = self._build(X, y, 0)

    def _pred_one(self, x, node):
        while not node.leaf:
            node = node.left if x[node.feature] <= node.thr else node.right
        return node.prob

    def predict_proba(self, X):
        return [self._pred_one(x, self.root) for x in X]


class RandomForestSimple:
    def __init__(self, n_trees=60, max_depth=6, min_samples_split=6):
        self.n_trees = n_trees
        self.max_depth = max_depth
        self.min_samples_split = min_samples_split
        self.trees = []

    def fit(self, X, y):
        n = len(y)
        d = len(X[0])
        mf = max(1, int(math.sqrt(d)))
        self.trees = []
        for _ in range(self.n_trees):
            idxs = [random.randrange(n) for _ in range(n)]
            Xb = [X[i] for i in idxs]
            yb = [y[i] for i in idxs]
            t = DecisionTreeClassifierSimple(max_depth=self.max_depth, min_samples_split=self.min_samples_split, max_features=mf)
            t.fit(Xb, yb)
            self.trees.append(t)

    def predict_proba(self, X):
        allp = [t.predict_proba(X) for t in self.trees]
        n = len(X)
        out = []
        for i in range(n):
            out.append(sum(p[i] for p in allp) / len(allp))
        return out


class RegressionStump:
    def __init__(self):
        self.f = None
        self.thr = None
        self.left_val = 0.0
        self.right_val = 0.0

    def fit(self, X, r):
        n = len(r)
        d = len(X[0])
        best_sse = float('inf')
        best = None
        for f in range(d):
            vals = sorted(set(row[f] for row in X))
            if len(vals) <= 1:
                continue
            for i in range(len(vals) - 1):
                thr = (vals[i] + vals[i + 1]) / 2.0
                L, R = [], []
                for j in range(n):
                    (L if X[j][f] <= thr else R).append(r[j])
                if not L or not R:
                    continue
                lv = sum(L) / len(L)
                rv = sum(R) / len(R)
                sse = sum((v - lv) ** 2 for v in L) + sum((v - rv) ** 2 for v in R)
                if sse < best_sse:
                    best_sse = sse
                    best = (f, thr, lv, rv)
        if best is None:
            self.f = 0
            self.thr = 0.0
            v = sum(r) / len(r) if r else 0.0
            self.left_val = v
            self.right_val = v
        else:
            self.f, self.thr, self.left_val, self.right_val = best

    def predict(self, X):
        return [self.left_val if x[self.f] <= self.thr else self.right_val for x in X]


class GradientBoostedStumpsLogit:
    def __init__(self, n_estimators=120, lr=0.08):
        self.n_estimators = n_estimators
        self.lr = lr
        self.base = 0.0
        self.stumps = []

    def fit(self, X, y):
        pos = sum(y)
        neg = len(y) - pos
        self.base = math.log((pos + 1e-6) / (neg + 1e-6))
        F = [self.base] * len(y)
        self.stumps = []
        for _ in range(self.n_estimators):
            p = [sigmoid(v) for v in F]
            r = [y[i] - p[i] for i in range(len(y))]
            s = RegressionStump()
            s.fit(X, r)
            pred = s.predict(X)
            for i in range(len(F)):
                F[i] += self.lr * pred[i]
            self.stumps.append(s)

    def predict_proba(self, X):
        F = [self.base] * len(X)
        for s in self.stumps:
            p = s.predict(X)
            for i in range(len(F)):
                F[i] += self.lr * p[i]
        return [sigmoid(v) for v in F]


def confusion(y_true, y_pred):
    tp = fp = tn = fn = 0
    for yt, yp in zip(y_true, y_pred):
        if yt == 1 and yp == 1:
            tp += 1
        elif yt == 0 and yp == 1:
            fp += 1
        elif yt == 0 and yp == 0:
            tn += 1
        else:
            fn += 1
    return tn, fp, fn, tp


def metrics(y_true, prob, thr=0.5):
    y_pred = [1 if p >= thr else 0 for p in prob]
    tn, fp, fn, tp = confusion(y_true, y_pred)
    n = len(y_true)
    acc = (tp + tn) / n if n else 0.0
    prec = tp / (tp + fp) if (tp + fp) else 0.0
    rec = tp / (tp + fn) if (tp + fn) else 0.0
    f1 = (2 * prec * rec / (prec + rec)) if (prec + rec) else 0.0
    brier = sum((p - y) ** 2 for p, y in zip(prob, y_true)) / n if n else 0.0
    return {
        'accuracy': acc,
        'precision': prec,
        'recall': rec,
        'f1': f1,
        'brier': brier,
        'confusion_matrix': {'tn': tn, 'fp': fp, 'fn': fn, 'tp': tp},
        'y_pred': y_pred,
    }


def roc_auc(y_true, scores):
    pos = [s for y, s in zip(y_true, scores) if y == 1]
    neg = [s for y, s in zip(y_true, scores) if y == 0]
    if not pos or not neg:
        return 0.5
    gt = 0.0
    ties = 0.0
    for p in pos:
        for n in neg:
            if p > n:
                gt += 1
            elif p == n:
                ties += 1
    return (gt + 0.5 * ties) / (len(pos) * len(neg))


def pr_auc(y_true, scores):
    pairs = sorted(zip(scores, y_true), key=lambda x: x[0], reverse=True)
    tp = 0
    fp = 0
    fn_total = sum(y_true)
    if fn_total == 0:
        return 0.0
    points = [(0.0, 1.0)]
    for s, y in pairs:
        if y == 1:
            tp += 1
        else:
            fp += 1
        rec = tp / fn_total
        prec = tp / (tp + fp)
        points.append((rec, prec))
    points.sort(key=lambda x: x[0])
    area = 0.0
    for i in range(1, len(points)):
        r1, p1 = points[i - 1]
        r2, p2 = points[i]
        area += (r2 - r1) * ((p1 + p2) / 2.0)
    return area


def mcnemar(y_true, pred_a, pred_b):
    b = c = 0
    for yt, a, bpred in zip(y_true, pred_a, pred_b):
        ca = (a == yt)
        cb = (bpred == yt)
        if ca and not cb:
            b += 1
        elif cb and not ca:
            c += 1
    if b + c == 0:
        return {'b': b, 'c': c, 'chi2': 0.0, 'p_value': 1.0}
    chi2 = ((abs(b - c) - 1) ** 2) / (b + c)
    # chi-square df=1 survival function
    p = math.erfc(math.sqrt(chi2 / 2.0))
    return {'b': b, 'c': c, 'chi2': chi2, 'p_value': p}


def build_dataset(sql_text):
    portfolios_raw = load_table_rows(sql_text, 'portfolios')
    offerings_raw = load_table_rows(sql_text, 'class_offerings')
    items_raw = load_table_rows(sql_text, 'portfolio_items')

    offerings = {}
    for r in offerings_raw:
        # id, subject_id, academic_year, term, section, assignment_document, instructional_materials_link, syllabus_link, faculty_id, created_at, updated_at
        offerings[to_int(r[0])] = {
            'subject_id': to_int(to_none(r[1]), 0),
            'academic_year': to_none(r[2]) or '',
            'term': to_int(to_none(r[3]), 0),
            'section': to_none(r[4]) or '',
            'has_assignment': 1 if to_none(r[5]) else 0,
            'has_im_link': 1 if to_none(r[6]) else 0,
            'has_syllabus_link': 1 if to_none(r[7]) else 0,
            'faculty_id': to_int(to_none(r[8]), 0),
            'created_at': parse_dt(to_none(r[9])),
        }

    item_counts = defaultdict(int)
    type_counts = defaultdict(lambda: defaultdict(int))
    for r in items_raw:
        pid = to_int(r[1])
        t = to_none(r[3]) or 'unknown'
        item_counts[pid] += 1
        type_counts[pid][t] += 1

    rows = []
    for r in portfolios_raw:
        # id,user_id,class_offering_id,status,resubmission_count,submitted_at,approved_at,created_at,updated_at
        pid = to_int(r[0])
        class_id = to_int(r[2])
        off = offerings.get(class_id)
        if not off:
            continue
        created_at = parse_dt(to_none(r[7]))
        updated_at = parse_dt(to_none(r[8]))
        submitted_at = parse_dt(to_none(r[5]))
        status = to_none(r[3]) or 'draft'

        # target: at-risk if not submitted/approved (draft/rejected)
        y = 1 if status in ('draft', 'rejected') else 0

        year_start = 0
        if off['academic_year'] and '-' in off['academic_year']:
            try:
                year_start = int(off['academic_year'].split('-')[0])
            except Exception:
                year_start = 0

        section_digits = ''.join(ch for ch in off['section'] if ch.isdigit())
        section_num = int(section_digits) if section_digits else 0

        duration_days = 0.0
        if created_at and updated_at:
            duration_days = max(0.0, (updated_at - created_at).total_seconds() / 86400.0)

        tc = type_counts[pid]
        rows.append({
            'portfolio_id': pid,
            'created_at': created_at,
            'y': y,
            'status': status,
            'features': {
                'resubmission_count': to_int(to_none(r[4]), 0),
                'item_count': item_counts[pid],
                'item_type_count': len(tc),
                'has_major_exam': 1 if tc.get('major_exam', 0) > 0 else 0,
                'has_sample_quiz': 1 if tc.get('sample_quiz', 0) > 0 else 0,
                'has_tos': 1 if tc.get('tos', 0) > 0 else 0,
                'has_class_list': 1 if tc.get('class_list', 0) > 0 else 0,
                'has_grade_sheets': 1 if tc.get('grade_sheets', 0) > 0 else 0,
                'has_activity_rubrics': 1 if tc.get('activity_rubrics', 0) > 0 else 0,
                'off_term': off['term'],
                'off_subject_id': off['subject_id'],
                'off_faculty_id': off['faculty_id'],
                'off_year_start': year_start,
                'off_section_num': section_num,
                'off_has_assignment_doc': off['has_assignment'],
                'off_has_im_link': off['has_im_link'],
                'off_has_syllabus_link': off['has_syllabus_link'],
                'portfolio_duration_days': duration_days,
                'was_submitted': 1 if submitted_at else 0,
            }
        })

    # remove clear leakage features
    leakage = {'was_submitted'}
    for row in rows:
        for k in leakage:
            row['features'].pop(k, None)

    return rows


def one_hot_and_scale(train_rows, test_rows):
    # identify feature types
    feat_names = sorted(train_rows[0]['features'].keys()) if train_rows else []
    cat_feats = {'off_term', 'off_subject_id', 'off_faculty_id', 'off_year_start', 'off_section_num'}

    cat_levels = {f: set() for f in cat_feats}
    for row in train_rows:
        for f in cat_feats:
            cat_levels[f].add(row['features'][f])

    cat_levels = {f: sorted(v) for f, v in cat_levels.items()}

    num_feats = [f for f in feat_names if f not in cat_feats]

    def vec(row):
        out = []
        for f in num_feats:
            out.append(float(row['features'][f]))
        for f in sorted(cat_feats):
            v = row['features'][f]
            for lv in cat_levels[f]:
                out.append(1.0 if v == lv else 0.0)
        return out

    X_train = [vec(r) for r in train_rows]
    X_test = [vec(r) for r in test_rows]
    y_train = [r['y'] for r in train_rows]
    y_test = [r['y'] for r in test_rows]

    # standardize numeric block only
    nnum = len(num_feats)
    if X_train:
        means = [sum(row[j] for row in X_train) / len(X_train) for j in range(nnum)]
        stds = []
        for j in range(nnum):
            var = sum((row[j] - means[j]) ** 2 for row in X_train) / max(1, len(X_train) - 1)
            stds.append(math.sqrt(var) if var > 1e-12 else 1.0)
        for X in (X_train, X_test):
            for row in X:
                for j in range(nnum):
                    row[j] = (row[j] - means[j]) / stds[j]

    feature_dim = len(X_train[0]) if X_train else 0
    return X_train, y_train, X_test, y_test, feature_dim, len(num_feats)


def train_and_eval(rows):
    # time-aware split
    rows = [r for r in rows if r['created_at'] is not None]
    rows.sort(key=lambda r: r['created_at'])
    split = int(len(rows) * 0.7)
    train_rows = rows[:split]
    test_rows = rows[split:]

    X_train, y_train, X_test, y_test, d, nnum = one_hot_and_scale(train_rows, test_rows)

    lr = LogisticRegressionGD(lr=0.07, epochs=700, l2=0.001)
    lr.fit(X_train, y_train)
    p_lr = lr.predict_proba(X_test)

    rf = RandomForestSimple(n_trees=80, max_depth=6, min_samples_split=6)
    rf.fit(X_train, y_train)
    p_rf = rf.predict_proba(X_test)

    gb = GradientBoostedStumpsLogit(n_estimators=140, lr=0.08)
    gb.fit(X_train, y_train)
    p_gb = gb.predict_proba(X_test)

    out = {
        'dataset_counts': {
            'total_records': len(rows),
            'train_records': len(train_rows),
            'test_records': len(test_rows),
            'feature_count': d,
            'numeric_feature_count': nnum,
            'at_risk_count_total': sum(r['y'] for r in rows),
            'not_at_risk_count_total': len(rows) - sum(r['y'] for r in rows),
            'at_risk_count_train': sum(y_train),
            'at_risk_count_test': sum(y_test),
        },
        'models': {}
    }

    model_probs = {
        'Logistic Regression': p_lr,
        'Random Forest': p_rf,
        'Gradient Boosting': p_gb,
    }

    preds = {}
    for name, probs in model_probs.items():
        m = metrics(y_test, probs)
        m['roc_auc'] = roc_auc(y_test, probs)
        m['pr_auc'] = pr_auc(y_test, probs)
        preds[name] = m['y_pred']
        del m['y_pred']
        out['models'][name] = m

    out['mcnemar'] = {
        'LR_vs_RF': mcnemar(y_test, preds['Logistic Regression'], preds['Random Forest']),
        'LR_vs_GB': mcnemar(y_test, preds['Logistic Regression'], preds['Gradient Boosting']),
        'RF_vs_GB': mcnemar(y_test, preds['Random Forest'], preds['Gradient Boosting']),
    }

    return out


def to_pct(v):
    return f"{v*100:.2f}%"


def render_md(res):
    dc = res['dataset_counts']
    lines = []
    lines.append('# Chapter IV Experiment Outputs')
    lines.append('')
    lines.append('## Dataset Counts')
    lines.append('')
    lines.append(f"- Total records: {dc['total_records']}")
    lines.append(f"- Train records: {dc['train_records']}")
    lines.append(f"- Test records: {dc['test_records']}")
    lines.append(f"- Features used: {dc['feature_count']}")
    lines.append(f"- At-Risk total: {dc['at_risk_count_total']}")
    lines.append(f"- Not At-Risk total: {dc['not_at_risk_count_total']}")
    lines.append('')
    lines.append('## Model Metrics (Test Set)')
    lines.append('')
    lines.append('| Model | Accuracy | Precision | Recall | F1-score | ROC-AUC | PR-AUC | Brier |')
    lines.append('|---|---:|---:|---:|---:|---:|---:|---:|')
    for name, m in res['models'].items():
        lines.append(
            f"| {name} | {to_pct(m['accuracy'])} | {to_pct(m['precision'])} | {to_pct(m['recall'])} | {to_pct(m['f1'])} | {m['roc_auc']:.4f} | {m['pr_auc']:.4f} | {m['brier']:.4f} |"
        )
    lines.append('')
    lines.append('## Confusion Matrices (Test Set)')
    lines.append('')
    for name, m in res['models'].items():
        cm = m['confusion_matrix']
        lines.append(f"### {name}")
        lines.append('')
        lines.append(f"- TN: {cm['tn']}")
        lines.append(f"- FP: {cm['fp']}")
        lines.append(f"- FN: {cm['fn']}")
        lines.append(f"- TP: {cm['tp']}")
        lines.append('')
    lines.append('## McNemar Pairwise Tests (alpha=0.05)')
    lines.append('')
    lines.append('| Pair | b | c | chi-square | p-value |')
    lines.append('|---|---:|---:|---:|---:|')
    for pair, v in res['mcnemar'].items():
        lines.append(f"| {pair} | {v['b']} | {v['c']} | {v['chi2']:.4f} | {v['p_value']:.6f} |")
    lines.append('')
    return '\n'.join(lines)


def main():
    with open(SQL_PATH, 'r', encoding='utf-8', errors='ignore') as f:
        sql_text = f.read()

    rows = build_dataset(sql_text)
    res = train_and_eval(rows)

    with open(OUT_JSON, 'w', encoding='utf-8') as f:
        json.dump(res, f, indent=2)

    md = render_md(res)
    with open(OUT_MD, 'w', encoding='utf-8') as f:
        f.write(md)

    print(json.dumps(res['dataset_counts'], indent=2))
    print('\nWrote:', OUT_JSON)
    print('Wrote:', OUT_MD)


if __name__ == '__main__':
    main()
