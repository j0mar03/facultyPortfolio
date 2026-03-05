# Session Reference - 2026-02-16

This file is a handoff reference of exactly what we completed today for the thesis and app-related research workflow.

## 1. App Debugging Completed

- Issue investigated: CSS looked broken after `git reset --hard origin/main` and `git clean -fd`.
- Root cause found: `public/hot` existed and pointed to `http://localhost:5174`, forcing Laravel Vite hot mode.
- Fix applied:
  1. Removed `public/hot`
  2. Rebuilt assets with `npm run build`
- Result: CSS assets were correctly generated in `public/build/assets`.

## 2. Thesis Direction Finalized

- Agreed study direction:
  - **Machine Learning-Based Early Warning System for Predicting Late or Incomplete Faculty Portfolio Submissions in a Web-Based Faculty Portfolio Management System**
- Methodology decision:
  - **Best version selected:** pure **quantitative predictive study**
  - No optional user-acceptability/TAM phase for this new study.

## 3. Files Created/Updated Today

### Created
1. `CHAPTER_I.md`
2. `CHAPTER_III.md`
3. `CHAPTER_II_SYNTHESIS_MATRIX_TEMPLATE.md`

### Updated
1. `CHAPTER_I.md`
   - Aligned with Chapter III
   - Removed acceptability-focused problem/objective
   - Added statistical-comparison direction
   - Added/standardized metrics: `ROC-AUC`, `PR-AUC`, calibration mention

## 4. Alignment Work Done (Step 1 Completed)

Chapter I and Chapter III are now aligned on:

1. Quantitative predictive design
2. ML model comparison focus
3. Statistical treatment and significance testing
4. Deployment-oriented integration to the existing system

## 5. Step 2 Started (Chapter II Source Base)

- Search key suggestions were prepared for:
  1. Scopus
  2. IEEE Xplore
  3. Web of Science
- Ready-to-fill matrix file prepared:
  - `CHAPTER_II_SYNTHESIS_MATRIX_TEMPLATE.md`
  - Includes search log, inclusion/exclusion criteria, PRISMA-style counts, 40-study matrix, thematic clustering, methodological comparison, benchmark metrics, and gap synthesis.

## 6. Next Action on Wednesday

1. Collect 25-40 studies from Scopus/IEEE/WoS.
2. Encode each selected paper into `CHAPTER_II_SYNTHESIS_MATRIX_TEMPLATE.md`.
3. Build thematic synthesis and finalize Chapter II narrative.

## 7. Important Notes for Continuation

1. Keep terminology consistent:
   - `ROC-AUC` (not AUC-ROC in text, unless required by school style)
   - Include `PR-AUC` for imbalanced classification
2. Keep study scope as **quantitative predictive only**.
3. Preserve one-to-one alignment across:
   - Chapter I Statement of the Problem
   - Chapter I Objectives
   - Chapter III Statistical Treatment

