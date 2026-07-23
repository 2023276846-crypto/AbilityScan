# Project Updates

## 1. Dynamic 5-Layer Rule Algorithm Control & Restart Feature
We transformed the static 5-layer matching algorithm (S, P, T, W, C rules) into a dynamic, admin-controlled system.

### Database Changes
* Created a new migration for the `algorithm_weights` table to store `s_weight`, `p_weight`, `t_weight`, `w_weight`, and `c_weight`.
* Created the `AlgorithmWeight` Eloquent model.

### Backend Changes
* **`app/Http/Controllers/PWDController.php`**: Updated `calculateMatchScore()` to dynamically fetch rule weights from the `algorithm_weights` table (using Cache for performance) instead of relying on hardcoded values (30%, 25%, 20%, 15%, 10%).
* **`app/Http/Controllers/AdminController.php`**: Added three new endpoints:
  * `getAlgorithmWeights()`: Fetches the current configuration.
  * `updateAlgorithmWeights()`: Validates that weights equal 100% and saves them to the DB.
  * `recalculateMatches()`: The "Restart Machine" feature that iterates through all existing `JobMatch` records and updates their `match_score` based on the newly saved algorithm weights.
* **`routes/api.php`**: Registered the new admin routes (`/admin/algorithm-weights` and `/admin/algorithm-recalculate`).

### Frontend Changes
* **`resources/js/views/Admin/MonitorPerformance.vue`**: 
  * Added an **Algorithm Configuration** UI panel below the analytics charts.
  * Added input fields for Admins to easily tweak the rule percentages.
  * Implemented validation to ensure the total weight exactly equals 100%.
  * Added a **Restart Algorithm Machine** button to trigger the backend recalculation process.

---

## 2. QR Code Scanning & Portfolio Search Bug Fix
* **The Issue:** When an employer scanned a QR code or manually entered an OKU number, the API returned a "Portfolio not found" error despite the user existing.
* **The Cause:** A route collision in `routes/api.php`. The dynamic route `Route::get('/portfolio/{id}')` was placed above `Route::get('/portfolio/search')`. As a result, Laravel intercepted the `/search` request, treating "search" as the ID parameter, which failed the database lookup.
* **The Fix:** Reordered `routes/api.php` to prioritize the exact string route (`/portfolio/search`) above the parameterized route (`/portfolio/{id}`). Also removed duplicate route declarations inside the protected middleware group.
