# Attendance Feature Documentation

## Overview
The attendance feature records and displays user attendance for events or services. It provides a simple submission page, a success confirmation, a live attendance screen for displays, and backend endpoints for counting and feeding attendance records. The feature integrates with the project's authentication system and supports both manual form submissions and QR/scan-based workflows.

## Files Involved
- `attendance.php` — main attendance submission page (form or scanner entry)
- `attendance_success.php` — confirmation page shown after a successful submission
- `attendance_screen.php` — public display page (live counts or list for screens)
- `attendance_count.php` — lightweight endpoint returning numeric counts (used by dashboards/screens)
- `attendance_feed.php` — endpoint returning recent attendance rows or feed (JSON/HTML)
- `db.php` — shared database connection and helpers used by the attendance endpoints
- `qr_scanner.php` — optional QR scanning integration that can post attendance data

## Typical Flows
- Manual submission
  1. User opens `attendance.php` and fills the form or chooses the appropriate date/service.
  2. The page POSTs to a handler (same file or a dedicated endpoint) which validates the input and inserts an attendance row.
  3. On success the user is redirected to `attendance_success.php`.

- QR/Scanner submission
  1. The user scans a QR code that opens `qr_scanner.php` or `attendance.php` with a token/parameter.
  2. The scanner page posts the scanned token or user identifier to the same attendance handler.
  3. The server validates the token (and authentication if required) and records the attendance.

- Live display and dashboards
  - `attendance_screen.php` polls `attendance_count.php` or `attendance_feed.php` to present live totals or a recent list suitable for display screens.

## Data & Validation
- Typical database record fields (implementation-dependent): `id`, `user_id` (nullable), `name` (nullable), `attendance_date`, `method`, `metadata`, `created_at`.
- Validation rules that should be enforced server-side:
  - Required: date or service identifier, and at least one identifier (user id or name/token) if anonymous submissions are not allowed.
  - Prevent duplicate submissions for the same user and service/date when appropriate.
  - Verify QR tokens or scanner payloads are valid and not expired.
  - Sanitize any free-text fields.

## API Contract (suggested)
- `attendance_count.php`
  - Method: GET
  - Response: JSON { "count": 123, "date": "YYYY-MM-DD" }
  - Usage: called by `attendance_screen.php` to update live totals.

- `attendance_feed.php`
  - Method: GET
  - Query params: `limit`, `since_id` (optional)
  - Response: JSON array of recent attendance records with fields appropriate for display.

## Security & Permissions
- Ensure endpoints verify session/auth when required. Public screens may show aggregate counts but should not expose personally identifiable information.
- For endpoints that return sensitive details (names, contact info), require the user to be authenticated and authorized (admin or staff role).

## UI/UX Notes
- On `attendance.php` keep the submission flow simple: large buttons, clear success feedback, and minimal fields.
- For scanner flows, auto-submit on successful scan to minimize touch interactions.
- On `attendance_screen.php` use polling or websockets for real-time updates. Polling every 5–15 seconds is a reasonable default for low-traffic setups.

## Maintenance & Troubleshooting
- Check `db.php` and existing table schemas to confirm column names and constraints before making changes.
- If counts differ between `attendance_count.php` and the displayed list, ensure both endpoints read from the same source and apply the same filters (date range, service id).
- Logs: add server-side logging for failed token validation and duplicate submission attempts.

## Extension Ideas
- CSV export endpoint for admins.
- Per-service and per-date breakdowns for analytics.
- WebSocket push for truly real-time display updates.

## Where to Look Next
- Review `attendance.php` and `attendance_count.php` for current implementation details and database table names.
- If you want, I can add an admin CSV export and a small test script to simulate scanner submissions.
