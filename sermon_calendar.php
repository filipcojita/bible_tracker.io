<?php
// Helper function to determine if a date is Friday or Sunday
function isFridayOrSunday($date) {
    $dayOfWeek = $date->format('w'); // 0 = Sunday, 5 = Friday
    
    // Allow specific dates: April 9 and 10, 2026
    $dateString = $date->format('Y-m-d');
    $specialDates = ['2026-04-09', '2026-04-10'];
    if (in_array($dateString, $specialDates)) {
        return true;
    }
    
    return $dayOfWeek === '0' || $dayOfWeek === '5';
}

// Helper function to check if submission is allowed for a date
function isDateAllowedForSubmission($dateString) {
    $today = new DateTime();
    $targetDate = new DateTime($dateString);
    
    $currentMonth = (int)$today->format('m');
    $currentYear = (int)$today->format('Y');
    $targetMonth = (int)$targetDate->format('m');
    $targetYear = (int)$targetDate->format('Y');
    
    $previousMonth = $currentMonth === 1 ? 12 : $currentMonth - 1;
    $previousYear = $currentMonth === 1 ? $currentYear - 1 : $currentYear;
    
    $isCurrentMonth = ($targetYear === $currentYear && $targetMonth === $currentMonth);
    $isPreviousMonth = ($targetYear === $previousYear && $targetMonth === $previousMonth);
    
    if (!($isCurrentMonth || $isPreviousMonth)) return false;
    if ($targetDate > $today) return false;
    
    return true;
}

// Get submissions
$userId = $_SESSION['user_id'];
$submissions = [];

$stmt = $conn->prepare("SELECT sermon_date, id FROM sermon_submissions WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $submissions[$row['sermon_date']] = $row['id'];
}

// Dates
$today = new DateTime();
$currentYear = (int)$today->format('Y');
$currentMonth = (int)$today->format('m');

$displayYear = isset($_GET['sermon_year']) ? (int)$_GET['sermon_year'] : $currentYear;
$displayMonth = isset($_GET['sermon_month']) ? (int)$_GET['sermon_month'] : $currentMonth;

// Allowed range
$previousMonth = $currentMonth === 1 ? 12 : $currentMonth - 1;
$previousYear = $currentMonth === 1 ? $currentYear - 1 : $currentYear;

// ✅ FIX: clamp instead of reset
if ($displayYear < $previousYear || 
   ($displayYear === $previousYear && $displayMonth < $previousMonth)) {
    $displayYear = $previousYear;
    $displayMonth = $previousMonth;
}

if ($displayYear > $currentYear || 
   ($displayYear === $currentYear && $displayMonth > $currentMonth)) {
    $displayYear = $currentYear;
    $displayMonth = $currentMonth;
}
?>

<style>
/* ✅ YOUR ORIGINAL STYLES (UNCHANGED) */
.sermon-calendar-container {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
}

.sermon-calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.sermon-calendar-header h3 {
    margin: 0;
    font-size: 1.3rem;
    font-weight: bold;
}

.sermon-calendar-nav {
    display: flex;
    gap: 10px;
}

.sermon-calendar-nav button {
    padding: 5px 12px;
    border: 1px solid #dee2e6;
    background-color: white;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s;
}

.sermon-calendar-nav button:hover {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

.sermon-calendar-nav button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.sermon-calendar-table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
    border-radius: 8px;
    overflow: hidden;
}

.sermon-calendar-table th {
    background-color: #007bff;
    color: white;
    padding: 12px;
    text-align: center;
    font-weight: bold;
}

.sermon-calendar-table td {
    padding: 10px;
    border: 1px solid #dee2e6;
    text-align: center;
    height: 60px;
    position: relative;
}

.sermon-calendar-day {
    cursor: pointer;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    font-weight: 500;
    transition: all 0.3s;
    position: relative;
}

.sermon-calendar-day.other-month {
    background-color: #f0f0f0;
    color: #999;
    cursor: default;
}

.sermon-calendar-day.selectable {
    background-color: #e7f3ff;
    border: 2px solid #007bff;
}

.sermon-calendar-day.selectable:hover {
    background-color: #cfe8ff;
}

.sermon-calendar-day.submitted {
    background-color: #28a745;
    color: white;
}

.sermon-calendar-day.submitted::after {
    content: "✓";
    position: absolute;
    font-size: 1.2rem;
}

.sermon-calendar-day.submitted:hover {
    background-color: #218838;
}

.sermon-calendar-day.future {
    color: #999;
    cursor: default;
}

.sermon-calendar-day.today {
    border: 3px solid #ffc107;
    font-weight: bold;
}

.empty-cell {
    background-color: #f9f9f9;
    cursor: default;
}
</style>

<div class="sermon-calendar-container">
    <div class="sermon-calendar-header">
        <h3>📅 Încarcă Notițe Predică</h3>
        <div class="sermon-calendar-nav">
            <button type="button" id="sermon-prev-month" onclick="changeSermonMonth(-1)">← Anterior</button>
            <span id="sermon-month-year" style="padding: 5px 15px; font-weight: bold;">
                <?php 
                $monthNames = ['Ianuarie','Februarie','Martie','Aprilie','Mai','Iunie','Iulie','August','Septembrie','Octombrie','Noiembrie','Decembrie'];
                echo $monthNames[$displayMonth - 1] . ' ' . $displayYear;
                ?>
            </span>
            <button type="button" id="sermon-next-month" onclick="changeSermonMonth(1)">Următor →</button>
        </div>
    </div>

    <table class="sermon-calendar-table">
    <thead>
        <tr>
            <th>Dum</th>
            <th>Lun</th>
            <th>Mar</th>
            <th>Mier</th>
            <th>Joi</th>
            <th>Vin</th>
            <th>Sâm</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $firstDay = new DateTime("$displayYear-$displayMonth-01");
        $firstDayOfWeek = (int)$firstDay->format('w');
        $daysInMonth = (int)$firstDay->format('t');

        $prevMonthDate = clone $firstDay;
        $prevMonthDate->modify('-1 day');
        $daysInPrevMonth = (int)$prevMonthDate->format('t');

        $week = [];

        for ($i = $firstDayOfWeek - 1; $i >= 0; $i--) {
            $prevDate = $daysInPrevMonth - $i;
            $week[] = [
                'day' => $prevDate,
                'date' => '',
                'class' => 'other-month'
            ];
        }

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateStr = sprintf("%04d-%02d-%02d", $displayYear, $displayMonth, $day);
            $dateObj = new DateTime($dateStr);
            $dayOfWeek = (int)$dateObj->format('w');

            $isFriorSun = isFridayOrSunday($dateObj);
            $isAllowed = isDateAllowedForSubmission($dateStr);
            $isSubmitted = isset($submissions[$dateStr]);
            $isToday = $dateStr === $today->format('Y-m-d');
            $isFuture = $dateObj > $today;

            $cellClass = 'sermon-calendar-day';

            if ($isFuture) {
                $cellClass .= ' future';
            } else if ($isSubmitted) {
                $cellClass .= ' submitted';
            } else if ($isFriorSun && $isAllowed) {
                $cellClass .= ' selectable';
            }

            if ($isToday) {
                $cellClass .= ' today';
            }

            $week[] = [
                'day' => $day,
                'date' => $dateStr,
                'class' => $cellClass,
                'submitted_id' => $isSubmitted ? $submissions[$dateStr] : null,
                'friday_sunday' => $isFriorSun,
                'allowed' => $isAllowed
            ];

            if ($dayOfWeek === 6 || $day === $daysInMonth) {
                if (count($week) < 7) {
                    $nextDay = 1;
                    while (count($week) < 7) {
                        $week[] = [
                            'day' => $nextDay,
                            'date' => '',
                            'class' => 'other-month'
                        ];
                        $nextDay++;
                    }
                }
                ?>
                <tr>
                    <?php foreach ($week as $cell): ?>
                        <td class="<?= $cell['class'] === 'sermon-calendar-day' ? '' : 'empty-cell' ?>">
                            <?php if (!empty($cell['date'])): ?>
                                <div class="<?= $cell['class'] ?>" 
                                     data-date="<?= $cell['date'] ?>"
                                     data-submitted-id="<?= $cell['submitted_id'] ?>"
                                     data-allowed="<?= $cell['allowed'] ? '1' : '0' ?>"
                                     data-friday-sunday="<?= $cell['friday_sunday'] ? '1' : '0' ?>"
                                     onclick="handleSermonDayClick(this)">
                                    <?= $cell['day'] ?>
                                </div>
                            <?php else: ?>
                                <div class="sermon-calendar-day other-month">
                                    <?= $cell['day'] ?>
                                </div>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <?php
                $week = [];
            }
        }
        ?>
    </tbody>
</table>
</div>

<script>
// ✅ FIXED JS ONLY (styles untouched)

let sermonCurrentMonth = <?= $displayMonth ?>;
let sermonCurrentYear = <?= $displayYear ?>;

let today = new Date();
let currentMonth = today.getMonth() + 1;
let currentYear = today.getFullYear();

let previousMonth = currentMonth === 1 ? 12 : currentMonth - 1;
let previousYear = currentMonth === 1 ? currentYear - 1 : currentYear;

function updateSermonNavButtons() {
    const prevBtn = document.getElementById('sermon-prev-month');
    const nextBtn = document.getElementById('sermon-next-month');

    const isAtPrevious =
        sermonCurrentYear === previousYear &&
        sermonCurrentMonth === previousMonth;

    const isAtCurrent =
        sermonCurrentYear === currentYear &&
        sermonCurrentMonth === currentMonth;

    prevBtn.disabled = isAtPrevious;
    nextBtn.disabled = isAtCurrent;
}

function changeSermonMonth(direction) {
    let newMonth = sermonCurrentMonth + direction;
    let newYear = sermonCurrentYear;

    if (newMonth > 12) {
        newMonth = 1;
        newYear++;
    } else if (newMonth < 1) {
        newMonth = 12;
        newYear--;
    }

    const isBeforePrevious =
        newYear < previousYear ||
        (newYear === previousYear && newMonth < previousMonth);

    const isAfterCurrent =
        newYear > currentYear ||
        (newYear === currentYear && newMonth > currentMonth);

    if (isBeforePrevious || isAfterCurrent) return;

    const url = new URL(window.location);
    url.searchParams.set('sermon_month', newMonth);
    url.searchParams.set('sermon_year', newYear);
    window.location.href = url.toString();
}

function handleSermonDayClick(el) {
    console.log("CLICKED:", el);

    const date = el.dataset.date;

    const id = el.dataset.submittedId && el.dataset.submittedId !== 'null'
        ? el.dataset.submittedId
        : null;

    const allowed = el.dataset.allowed === '1';
    const isFS = el.dataset.fridaySunday === '1';

    if (id) {
        showSermonViewModal(date, id);
    } else if (allowed && isFS) {
        showSermonUploadModal(date);
    }
}

document.addEventListener('DOMContentLoaded', updateSermonNavButtons);
</script>