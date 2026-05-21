# Prayer Wall Feature - Change Log

## Overview
The Prayer Wall feature (`/pray`) is an actively maintained prayer request management system where logged-in users can share prayer requests, interact with them via emoticons, and mark themselves as "praying for you."

## Change Timeline
- **May 2026**: Added inline Edit/Delete buttons on prayer cards and modal footer for owners/admins.
- **May 2026**: Updated `pray_edit.php` to allow admins to edit any prayer and preserve anonymous posting.
- **May 2026**: Added `current_user_id` and `current_user_is_admin` to prayer list/search API responses for consistent frontend permissions.
- **May 2026**: Added frontend user context injection via `window.prayerWallUser` and cache-busting for `pray_script.js`.
- **May 2026**: Updated documentation to track ongoing changes and support repeated AI updates.

> Note: Whenever a feature is changed, update this documentation and any other related markdown files so the current implementation state is preserved.

---

---

## 📁 Files Created

### Database Setup
- **`setup_prayer_wall.php`** — One-time setup script to create database tables

### Main Page
- **`pray.php`** — The main prayer wall page (authentication protected)

### API Endpoints (in `/api/` folder)
1. **`pray_create.php`** — POST: Create new prayer request
2. **`pray_list.php`** — GET: Retrieve prayers (by category or all)
3. **`pray_reactions.php`** — POST: Add/update emoticon reactions
4. **`pray_praying.php`** — POST: Toggle "praying for you" status
5. **`pray_delete.php`** — POST: Delete prayer (owner or admin only)
6. **`pray_edit.php`** — POST: Edit prayer (owner or admin only)
7. **`pray_search.php`** — GET: Search prayers by keyword

### Frontend Assets
- **`pray_script.js`** — Complete JavaScript for modals, AJAX, and interactions
- **`pray_styles.css`** — Full styling (responsive, accessible, modern design)

---

## 🚀 Setup Instructions

### Step 1: Initialize Database

Visit this URL in your browser (when MySQL is running):
```
http://localhost/bible_tracker/setup_prayer_wall.php
```

This will create 3 tables:
- `prayer_requests` — Main table for prayer requests
- `prayer_reactions` — Tracks emoticon reactions
- `prayer_praying` — Tracks "praying for you" counts

**Expected output:**
```
Tables Created Successfully
- prayer_requests
- prayer_reactions
- prayer_praying
```

### Step 2: Delete Setup Script (Optional, Recommended)
After successful setup, you can delete `setup_prayer_wall.php` to prevent re-running it:
```bash
rm /Applications/XAMPP/xamppfiles/htdocs/bible_tracker/setup_prayer_wall.php
```

### Step 3: Access the Prayer Wall
Navigate to:
```
http://localhost/bible_tracker/pray.php
```

---

## 🎯 Features Implemented

### Core Functionality
✅ **Authentication** — Only logged-in users can access the prayer wall  
✅ **Create Prayers** — Submit title, description, category, anonymous option  
✅ **5 Categories** — Lauda, Multumire, Cerere, Mijlocire, Marturisire  
✅ **View Details** — See full prayer content  
✅ **Emoticon Reactions** — 6 predefined emoticons (🙏, ❤️, 😢, 😊, 🙌, ✝️)  
✅ **"Praying for You" Tracking** — Toggle button, increments counter  
✅ **See Who's Praying** — View list of users actively praying (non-anonymous)  
✅ **Search** — Real-time search across title + description  
✅ **Category Tabs** — Filter by category, newest first  
✅ **Edit Prayers** — Users can edit their own prayers; admins can edit any prayer
✅ **Delete Prayers** — Users delete own prayers; admins delete any prayer
✅ **Inline Card Actions** — Edit/Delete buttons appear on prayer cards for eligible users
✅ **Anonymous Posting** — Users can hide their identity  
✅ **Admin Controls** — Full moderation capabilities  

---

## 📱 User Interface

### Main Page (pray.php)
- **Header** — "Prayer Wall" title + "New Prayer" button
- **Search Bar** — Real-time search (min 2 characters)
- **Category Tabs** — 6 tabs: All, Lauda, Mulțumire, Cerere, Mijlocire, Mărturisire
- **Prayer Cards** — Grid layout with:
  - Prayer title
  - Category badge
  - Submitter name or "Anonymous"
  - Emoticon summary (count by type)
  - "Praying for you" counter
  - "See details" link

### New Prayer Modal
- **Title** (required, max 200 chars)
- **Description** (optional, max 1000 chars)
- **Category** (required dropdown)
- **Anonymous Checkbox**
- Form validation with error messages

### Prayer Details Modal
- **Full Prayer Content** — Title, description, category, submitter, date
- **Emoticon Picker** — 6 buttons, click to react
- **Reactions Display** — Shows count of each emoji reaction
- **"Praying for You" Button** — Toggle with visual feedback (highlighted when active)
- **Praying Users List** — Expandable, shows who's praying
- **Edit/Delete Buttons** — For owner/admin in the modal footer and on prayer cards

---

## 🔌 API Endpoints Reference

### Create Prayer
```
POST /api/pray_create.php
Body: {
  "title": "string (1-200)",
  "description": "string (0-1000)",
  "category": "lauda|multumire|cerere|mijlocire|marturisire",
  "is_anonymous": boolean
}
Response: { "success": bool, "prayer_id": int, "message": string }
```

### List Prayers
```
GET /api/pray_list.php?category=lauda (optional)
Response: {
  "success": true,
  "prayers": { 
    "lauda": [prayer, ...],
    "multumire": [prayer, ...],
    ...
  }
}
```

### Add Emoticon Reaction
```
POST /api/pray_reactions.php
Body: { "prayer_id": int, "emoticon": "🙏" }
Response: { "success": bool, "message": string }
```

### Toggle "Praying for You"
```
POST /api/pray_praying.php
Body: { "prayer_id": int, "action": "add|remove" }
Response: { "success": bool, "praying_count": int, "message": string }
```

### Delete Prayer
```
POST /api/pray_delete.php
Body: { "prayer_id": int }
Response: { "success": bool, "message": string }
```

### Edit Prayer
```
POST /api/pray_edit.php
Body: {
  "prayer_id": int,
  "title": "string",
  "description": "string",
  "category": "string",
  "is_anonymous": boolean
}
Response: { "success": bool, "message": string }
```

### Search Prayers
```
GET /api/pray_search.php?q=keyword
Response: { "success": true, "prayers": [prayer, ...] }
```

---

## 📝 Latest Implementation Notes
- `pray.php` now exposes the current user ID and admin state via `window.prayerWallUser`, which the frontend uses to render Edit/Delete actions correctly.
- `pray_script.js` loads prayer data and normalizes owner/admin permissions for each prayer card.
- Edit/Delete buttons are available directly on the prayer cards and inside the prayer details modal
- `pray_edit.php` now allows admins to update any prayer and preserves anonymous posting state
- `pray_list.php` / `pray_search.php` now return `current_user_id` and `current_user_is_admin` to keep frontend authorization consistent
- Script caching is handled by `pray_script.js?v=2` so latest frontend behavior loads without stale cached JS

## 🎨 Design Details

### Category Colors
- **Lauda** (Praise) — Red (#ff6b6b)
- **Mulțumire** (Thanksgiving) — Teal (#4ecdc4)
- **Cerere** (Request) — Light Red (#ff8787)
- **Mijlocire** (Intercession) — Light Teal (#95e1d3)
- **Mărturisire** (Confession) — Orange (#dda15e)

### Responsive Design
- ✅ Mobile-first approach
- ✅ Works on tablets, phones, desktops
- ✅ Breakpoint: 768px (Bootstrap standard)
- ✅ Touch-friendly buttons and modals

### Accessibility
- ✅ Semantic HTML
- ✅ ARIA labels on interactive elements
- ✅ Keyboard navigation support
- ✅ High contrast text
- ✅ Font sizes: 14px minimum

---

## 🔒 Security Features

### Authentication
- ✅ Session validation on every page and API endpoint
- ✅ Persistent login support (existing cookie system)
- ✅ Auto-redirect to login if not authenticated

### Authorization
- ✅ Users can only edit/delete their own prayers
- ✅ Admins can delete any prayer
- ✅ Permissions checked server-side on all endpoints

### Data Validation
- ✅ Title: required, max 200 chars
- ✅ Description: optional, max 1000 chars
- ✅ Category: must be one of 5 predefined
- ✅ Emoticon: only predefined 6 allowed
- ✅ Prepared statements to prevent SQL injection
- ✅ Input sanitization (htmlspecialchars on output)

### CORS & Requests
- ✅ All AJAX requests use JSON
- ✅ Content-Type validation
- ✅ Proper HTTP status codes (200, 201, 400, 403, 404, 500)

---

## 🧪 Testing Checklist

### Authentication & Access
- [ ] Non-logged-in users redirected to login.php
- [ ] Logged-in users can access pray.php
- [ ] Session persists with "Remember Me" cookie

### Create Prayer
- [ ] Empty title shows error
- [ ] Title > 200 chars shows error
- [ ] Category dropdown required
- [ ] Anonymous checkbox toggles correctly
- [ ] Form clears after successful submission
- [ ] New prayer appears at top of correct category

### View & Search
- [ ] Category tabs filter correctly
- [ ] Search works with 2+ characters
- [ ] Search clears when input cleared
- [ ] "All" tab shows all categories
- [ ] Newest prayers appear first

### Interactions
- [ ] Clicking emoticon adds reaction
- [ ] Same user can only have 1 emoticon per prayer
- [ ] Emoticon count updates in real-time
- [ ] "Praying for You" button toggles
- [ ] Counter increments/decrements
- [ ] Active state highlighted
- [ ] "See who" shows/hides user list

### Edit & Delete
- [ ] Owner can edit their prayer
- [ ] Non-owner cannot edit prayer (test as different user)
- [ ] Admin can edit any prayer
- [ ] Owner can delete their prayer
- [ ] Admin can delete any prayer
- [ ] Delete removes prayer from wall immediately

### Anonymous
- [ ] Anonymous prayers show "Anonymous" instead of name
- [ ] Anonymous user is not listed in "See who" section
- [ ] Non-anonymous shows username

### Admin Features
- [ ] Admin sees delete button on all prayers
- [ ] Admin can delete any prayer
- [ ] Regular user only sees own delete button

### Responsive Design
- [ ] Prayer cards stack on mobile
- [ ] Modals fit on mobile screens
- [ ] Tabs wrap on small screens
- [ ] Touch-friendly button sizes

---

## 🐛 Troubleshooting

### Database Tables Not Created
**Problem:** Setup page shows "No such file" error
**Solution:** 
1. Ensure MySQL is running (`sudo /Applications/XAMPP/xamppfiles/bin/mysqld_safe`)
2. Check socket location in db.php matches system location
3. Verify user `root` exists with empty password

### API Returns 404
**Problem:** API endpoints return 404
**Solution:**
1. Verify all files are in `/api/` folder
2. Check file permissions (should be readable)
3. Verify database tables exist

### Modals Don't Show
**Problem:** Clicking buttons doesn't open modals
**Solution:**
1. Check browser console for JavaScript errors
2. Verify pray_script.js is loaded (check Sources tab)
3. Check Bootstrap 5.3 is loaded from CDN

### Reactions Not Saving
**Problem:** Emoticons/praying reactions disappear on refresh
**Solution:**
1. Check database tables exist (run setup script)
2. Verify API endpoints are accessible
3. Check browser console for 500 errors

### Search Not Working
**Problem:** Search doesn't return results
**Solution:**
1. Search requires 2+ characters minimum
2. Case-insensitive search should work
3. Clear search field to reset to all prayers

---

## 📊 Database Schema

### prayer_requests
```sql
id (INT, PK)
user_id (INT, FK → users.id)
title (VARCHAR 200, NOT NULL)
description (TEXT, optional)
category (ENUM: lauda, multumire, cerere, mijlocire, marturisire)
is_anonymous (BOOLEAN, default FALSE)
created_at (TIMESTAMP, default CURRENT_TIMESTAMP)
updated_at (TIMESTAMP, auto-update)

Indexes: user_id, category, created_at DESC
```

### prayer_reactions
```sql
id (INT, PK)
prayer_id (INT, FK → prayer_requests.id, CASCADE DELETE)
user_id (INT, FK → users.id, CASCADE DELETE)
emoticon (VARCHAR 50)
created_at (TIMESTAMP, default CURRENT_TIMESTAMP)

Unique: (prayer_id, user_id) — one reaction per user per prayer
Indexes: prayer_id, user_id
```

### prayer_praying
```sql
id (INT, PK)
prayer_id (INT, FK → prayer_requests.id, CASCADE DELETE)
user_id (INT, FK → users.id, CASCADE DELETE)
created_at (TIMESTAMP, default CURRENT_TIMESTAMP)

Unique: (prayer_id, user_id) — one "praying" per user per request
Indexes: prayer_id, user_id
```

---

## 🎓 How to Use

### For Users
1. Go to `/pray` page
2. Click "New Prayer" button
3. Fill in title (required) and optional details
4. Choose category
5. Optionally check "Post anonymously"
6. Click "Share Prayer"
7. View prayers in category tabs
8. Click "See details" on any prayer
9. Add emoticon reaction (click emoticon)
10. Toggle "Praying for You" button
11. Click "See who" to view users praying

### For Admins
- All user features, plus:
- Can edit any prayer
- Can delete any prayer
- Full moderation control

---

## ✨ Future Enhancement Ideas

- Email notifications when someone reacts to your prayer
- Prayer categories with descriptions
- Mark prayer as "answered"
- Thread/conversation on prayers
- Prayer request categories (health, financial, spiritual, etc.)
- Statistics dashboard (most prayed, trending prayers)
- Export prayers as PDF
- Prayer reminder notifications
- Like/upvote reactions (not just praying counter)
- Prayer request lifecycle (active, answered, closed)

---

## 📝 Notes

- All forms include proper validation (client + server)
- All API responses are JSON
- All database queries use prepared statements
- Modal system reuses existing sermon_modal.js patterns
- CSS uses Bootstrap 5.3 + custom styles
- JavaScript is vanilla (no jQuery dependency)
- Responsive design tested on mobile/tablet/desktop
- GDPR-friendly (respects anonymous posting)

---
