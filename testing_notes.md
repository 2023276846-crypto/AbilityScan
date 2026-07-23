# PWD Matching Algorithm: Testing & Presentation Guide

This guide describes how the **5-Rule Weighted Matching Algorithm** is implemented, tested, and how to present it to your lecturer, supervisor, and examiner.

---

## 1. The Core Algorithm (The Concept)
The algorithm calculates how well a Person with Disability (PWD) candidate matches a Job Vacancy.

### The Formula:
$$Match Score = S(30\%) + P(25\%) + T(20\%) + W(15\%) + C(10\%)$$

* **S (Skills)** – 30% Weight
* **P (Physical Accessibility)** – 25% Weight
* **T (Technology Support)** – 20% Weight
* **W (Work Arrangement)** – 15% Weight
* **C (Sensory/Communication Support)** – 10% Weight

---

## 2. Automated Testing (Backend Math)
We use **PHPUnit** (built into Laravel) to test that the math is 100% correct.

### How to Run the Tests:
Open your terminal in the project directory (`f:\laragon\www\abilityscan`) and run:
```bash
php artisan test --filter MatchAlgorithmTest
```

### Test Case Scenarios Covered:
1. **Perfect Match (100%):** Checks if all candidate attributes match the job requirements, resulting in a perfect score of `100%`.
2. **Zero Match (0%):** Checks if no attributes match, resulting in `0%`.
3. **Partial Match (45%):** Tests a realistic mix:
   * **Skills (S):** 2/4 matched = 50% score (15.0 pts contribution)
   * **Physical (P):** 1/2 matched = 50% score (12.5 pts contribution)
   * **Tech (T):** 0/1 matched = 0% score (0.0 pts contribution)
   * **Work (W):** 1/2 matched = 50% score (7.5 pts contribution)
   * **Sensory (C):** 1/1 matched = 100% score (10.0 pts contribution)
   * **Expected Total:** $15.0 + 12.5 + 0 + 7.5 + 10.0 = \mathbf{45\%}$
   * The test verifies that the algorithm correctly outputs exactly `45%`.

---

## 3. Visual Matching (Frontend UI)
When presenting, show how the score is visually color-coded and presented to the users in the browser:

* **Excellent Match (>= 80%):** Highlighted in **Green**
* **Good Match (60% - 79%):** Highlighted in **Blue**
* **Fair Match (40% - 59%):** Highlighted in **Yellow**
* **Poor Match (< 40%):** Highlighted in **Red**

### How to Demonstrate:
1. Log in as a PWD user.
2. Go to the **Search Jobs** page to view the list of jobs with their colored matching score badges.
3. Click **"View Match"** to open the match breakdown modal which displays the 5 progress bars.
