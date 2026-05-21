# Sermon Upload Documentation

## Overview
The sermon upload feature allows authenticated users to upload sermon notes for specific Friday and Sunday dates via a calendar interface. Uploads are limited to the current and previous month, and users can view or download their uploaded files.

## Files Involved
- `dashboard.php` — embeds the sermon calendar component
- `sermon_calendar.php` — renders the sermon calendar and handles date navigation
- `sermon_modal.js` — creates upload/view modals and handles AJAX interaction
- `sermon_upload.php` — validates uploads, stores files, and returns submission details
- `sermon_styles.css` — calendar and modal styling
- `sermon_download.php` — downloads sermon files with permission checks
- `view_sermon.php` / `sermon_admin.php` — additional admin and view interfaces

## Allowed Dates
- Only Friday and Sunday dates are eligible for sermon uploads.
- A special exception exists for `2026-04-09` and `2026-04-10`.
- Uploads are allowed only for:
  - the current month
  - the previous month
- Future dates are blocked.

## Calendar Behavior
- The calendar shows a single month at a time.
- Navigation is restricted to the previous month and current month only.
- Submitted sermon days display with a green checkmark.
- Eligible Friday/Sunday days are marked as selectable.
- Clicking a selectable date opens the upload modal.
- Clicking an already submitted date opens the view modal.

## Upload Validation
`sermon_upload.php` enforces:
- required `sermon_date`
- `sermon_date` must be Friday or Sunday
- `sermon_date` must be in current or previous month
- no duplicate upload for the same user/date
- file upload required
- maximum file size: 10MB
- allowed file types: `txt`, `pdf`, `doc`, `docx`, `odt`, `pages`, and common image types (`jpg`, `jpeg`, `png`, `gif`, `webp`)
- allowed MIME types: text/plain, application/pdf, document, and image MIME types

## Upload Flow
1. User clicks a selectable calendar day.
2. `sermon_modal.js` opens the upload modal and sets `sermon_date`.
3. User selects a file and submits.
4. JS validates file size and extension before upload.
5. The file is sent to `sermon_upload.php` via `FormData`.
6. The server validates the date, duplicate submission, file type, and size.
7. The file is stored under `uploads/sermons/` with a unique filename.
8. If the file is `.txt`, word and line counts are calculated.
9. A record is inserted into `sermon_submissions`.
10. On success, the calendar refreshes to show the submitted day.

## Viewing and Downloading
- Submitted sermon days open a view modal via `sermon_modal.js`.
- View modal requests details from `sermon_upload.php?id={submissionId}`.
- `sermon_download.php` streams the file to the browser with ownership/admin validation.
- Admins and owners can access submission details/download links.

## Permission Rules
- Uploads are allowed only for logged-in users.
- Viewing submission details is only allowed to the owner or an admin.
- Download requests are also restricted to the owner or admin.

## Implementation Notes
- `sermon_calendar.php` uses helper functions to determine eligible dates and allowed months.
- `sermon_modal.js` dynamically builds modals, handles file validation, and posts to the upload endpoint.
- If accepted file types change, update both the JS allowed extensions and PHP allowed MIME/extension lists.
- If date rules change, update `isFridayOrSunday()` and `isSubmissionAllowed()` in both `sermon_calendar.php` and `sermon_upload.php`.

## Maintenance Notes
- Keep this document updated after any changes to sermon upload behavior, calendar rules, or file type/validation logic.
