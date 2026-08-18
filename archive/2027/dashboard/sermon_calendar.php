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
.sermon-calendar-container {
    background: linear-gradient(180deg, rgba(16,24,32,0.96) 0%, rgba(24,49,83,0.98) 100%);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 20px;
    padding: 20px;
    margin-top: 20px;
    box-shadow: 0 18px 35px rgba(16,24,32,0.18);
}

.sermon-calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    gap: 12px;
    flex-wrap: wrap;
}

.sermon-calendar-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 800;
    color: #fff;
}

.sermon-calendar-nav {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.sermon-calendar-nav button {
    padding: 7px 14px;
    border: 1px solid rgba(255,255,255,0.18);
    background: rgba(255,255,255,0.08);
    border-radius: 999px;
    cursor: pointer;
    font-weight: 700;
    transition: all 0.3s;
    color: #fff;
}

.sermon-calendar-nav button:hover {
    background: linear-gradient(135deg, var(--camp-red) 0%, var(--camp-red-dark) 100%);
    border-color: transparent;
    color: white;
}

.sermon-calendar-nav button:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}

.sermon-calendar-nav span {
    color: #eef5ff;
    font-weight: 700;
    padding: 5px 10px;
}

.sermon-calendar-table {
    width: 100%;
    border-collapse: collapse;
    background-color: rgba(255,255,255,0.96);
    border-radius: 12px;
    overflow: hidden;
}

.sermon-calendar-table th {
    background: linear-gradient(135deg, #101820 0%, #183153 50%, #c8102e 100%);
    color: white;
    padding: 12px;
    text-align: center;
    font-weight: 700;
}

.sermon-calendar-table td {
    padding: 10px;
    border: 1px solid #dde5ee;
    text-align: center;
    height: 70px;
    position: relative;
    background: rgba(248,250,252,0.9);
}

.sermon-calendar-day {
    cursor: pointer;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    font-weight: 700;
    transition: all 0.3s;
    position: relative;
    background: #edf5ff;
    color: #10213a;
}

.sermon-calendar-day.other-month {
    background-color: #f0f3f7;
    color: #9aa5b1;
    cursor: default;
}

.sermon-calendar-day.selectable {
    background: linear-gradient(135deg, #edf5ff 0%, #dbeeff 100%);
    border: 2px solid #1d5ea8;
    color: #12345b;
}

.sermon-calendar-day.selectable:hover {
    background: linear-gradient(135deg, #dbeeff 0%, #bfdfff 100%);
}

.sermon-calendar-day.submitted {
    background: linear-gradient(135deg, #c8102e 0%, #9d0c24 100%);
    color: white;
}

.sermon-calendar-day.submitted::after {
    content: "✓";
    position: absolute;
    right: 8px;
    bottom: 5px;
    font-size: 0.8rem;
}

.sermon-calendar-day.submitted:hover {
    filter: brightness(1.04);
}

.sermon-calendar-day.future {
    color: #9aa5b1;
    cursor: default;
    background: #f5f7fa;
}

.sermon-calendar-day.today {
    border: 3px solid #f3c853;
    font-weight: 800;
}

.empty-cell {
    background-color: #f3f6fb;
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