<?php

return [
    // Minimum attendance percentage (present + late count as attended)
    'threshold' => (int) env('ATTENDANCE_THRESHOLD', 75),

    // Weekly holidays: 0 = Sunday, 1 = Monday ... 6 = Saturday
    // Nepal now: Saturday + Sunday  -> "6,0"
    // If only Saturday becomes the holiday -> "6"
    'weekend_days' => array_map(
        'intval',
        array_filter(explode(',', (string) env('ATTENDANCE_WEEKEND_DAYS', '6,0')), 'strlen')
    ),

    // A student is flagged "at risk" only after this many recorded classes,
    // so one absence in the first class doesn't show 0%.
    'min_sessions_for_risk' => 3,
];