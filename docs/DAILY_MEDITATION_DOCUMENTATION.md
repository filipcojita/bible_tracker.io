# Daily Meditation Submission Documentation

## Overview
The daily meditation feature allows authenticated users to submit a Bible passage and reflection for the last three calendar days only. Submissions are stored in the `submissions` table and are visible on the user dashboard.

## Files Involved
- `dashboard.php`
- `styles.css`
- `db.php`
- `auth.php`

## Behavior Rules
- Users must be logged in to access `dashboard.php`.
- Only dates within the last 3 days are accepted (`today`, `yesterday`, `day before yesterday`).
- Duplicate submission dates are blocked for the same user.
- The `date` field is limited to `max=today` in the HTML date picker.
- Only the five most recent submission dates are shown by default.
- A "Show all" / "Ascunde" toggle reveals or hides older submissions.

## Submission Flow
1. The user fills the form with:
   - `date`
   - `passage`
   - `reflection`
2. On POST, `dashboard.php` validates the selected date against the last 3 days.
3. The code queries `submissions` for existing entries with the same `user_id` and `date`.
4. If a duplicate exists, the user receives an alert and the submission is rejected.
5. If valid, the new row is inserted into `submissions`.
6. The page reloads and displays a success alert.

## Frontend Logic
- `dashboard.php` renders a JavaScript array `submittedDates` containing past submission dates.
- The date picker change handler checks `submittedDates` and clears the input if the user selects an already submitted date.
- The toggle button reveals hidden submission history beyond the first 5 dates.

## Database Notes
- The feature writes to the `submissions` table, which includes at least:
  - `user_id`
  - `date`
  - `passage`
  - `reflection`
  - `submitted_at` (if present in schema)
- Database changes must be coordinated manually in phpMyAdmin for the live site.

## Maintenance Notes
- If date validation rules change, update both the PHP date logic and the JS `submittedDates` handling.
- If the submission history UI changes, update this documentation accordingly.
