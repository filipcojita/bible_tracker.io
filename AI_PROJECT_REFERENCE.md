# Bible Tracker Project Reference

## Project Overview

This is a PHP web application running under XAMPP in `/Applications/XAMPP/xamppfiles/htdocs/bible_tracker`.
! IMPORTANT !
The developer is continuosly testing your latest changes on the live website
by uploading the latest changes to the files there.
Any new changes in the database (such as tables) must be refferenced to the developer,
becuase the queries have to be mannualy entered in the PHPAdmin of the project there. 

The project includes:
- User authentication and session management
- Daily Bible meditation submission tracking
- A sermon notes upload calendar for Friday and Sunday dates
- An admin area for sermon statistics and management
- Additional features such as prayer wall functionality and attendance-related pages

## Key Files and Purpose

### `dashboard.php`
- Main user dashboard.
- Handles daily meditation submissions for the last 3 days only.
- Prevents duplicate submission dates.
- Shows the five most recent submission dates by default, with a "Show all" toggle.
- Loads Bootstrap, project styles from `styles.css`, and sermon styles from `sermon_styles.css`.
- Embeds the sermon calendar component via `include 'sermon_calendar.php'`.
- The page is split into two columns now:
  - Left: meditation submission form and recent entries
  - Right: sermon calendar

### `sermon_calendar.php`
- Existing sermon calendar implementation.
- Determines allowed sermon submission dates using Friday and Sunday logic.
- Limits selectable dates to the current month and the previous month.
- Shows current and previous month navigation only.
- Displays submitted sermon days and selectable sermon upload days.
- Uses inline calendar styling and renders the calendar table.

### `sermon_upload.php`
- Handles sermon note upload requests.
- Validates that uploads are only for Friday or Sunday dates.
- Validates that uploads are only for the current month or previous month and not future dates.
- Prevents duplicate sermon uploads for the same date per user.
- Accepts file uploads and validates type/size.
- Stores sermon submissions in `sermon_submissions` table.
- Returns JSON responses for upload and GET detail inquiries.

### `sermon_modal.js`
- Manages sermon upload and sermon view modals.
- Creates upload and view modals dynamically in the page.
- Handles file validation, AJAX upload to `sermon_upload.php`, and reload on success.
- Fetches sermon submission details for viewing.
- Provides download link generation using `sermon_download.php`.

### `sermon_styles.css`
- Styles sermon calendar and modal UI.
- Defines modal appearance, form controls, button states, and calendar classes.
- Includes responsive layout rules for smaller screens.
- Includes loading spinner styles.

### `sermon_download.php`
- Added to support direct sermon file download from the view modal.
- Verifies user ownership or admin access before serving the file.
- Streams downloaded file with proper headers.

## Important Behavior Rules

### Daily Meditation Submission
- Only allowed for dates within the last 3 days.
- The submission form includes `date`, `passage`, and `reflection` fields.
- Duplicate date submissions are blocked.
- Past submission dates are shown as a list and limited to five visible entries by default.

### Sermon Calendar Uploads
- Only Friday and Sunday dates are permitted.
- Current and previous month are allowed; future dates are blocked.
- Calendar button navigation is clamped to the previous and current month.
- Existing sermon submissions show as submitted days with a checkmark.
- Clickable calendar days either open upload modal or view modal.

## Recent Updates Made

- Fixed the recent submissions list so only the newest 5 entries display by default.
- Added `submission-item.hidden { display: none; }` to `styles.css`.
- Implemented explicit inline hiding for extra list items.
- Integrated the existing sermon calendar functionality into `dashboard.php` via `include 'sermon_calendar.php'`.
- Structured the dashboard into a two-column layout using Bootstrap grid classes.
- Added `sermon_download.php` for file download support.
- Ensured `dashboard.php` syntax is valid with `php -l`.

## Notes for Future AI or Developer Assistance

- When modifying the calendar, preserve the Friday/Sunday rule and current/previous month boundary logic.
- Keep sermon-related UI and behavior consistent with `sermon_modal.js` and `sermon_styles.css`.
- Use existing `sermon_upload.php` upload validation flow for new features.
- For changes to the dashboard layout, modify `dashboard.php` using Bootstrap column structure.
- If adding new sermon upload capabilities, update both the calendar component and the modal/endpoint logic.
- After making changes to any feature, update any existing markdown documentation files related to that feature (for example `PRAYER_WALL_DOCUMENTATION.md`) so implementation notes and timelines remain current.

## Feature Documentation

Quick links to feature-specific documentation:

- [Daily Meditation Documentation](DAILY_MEDITATION_DOCUMENTATION.md)
- [Sermon Upload Documentation](SERMON_UPLOAD_DOCUMENTATION.md)
- [Prayer Wall Documentation](PRAYER_WALL_DOCUMENTATION.md)
- [Attendance Documentation](ATTENDANCE_DOCUMENTATION.md)

Also see general and project-level docs:

- [Project README](README.md)
- [Soundboard README](soundboard/README.md)
