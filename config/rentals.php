<?php

return [
    // If true, approval generates a 6-digit OTP and pickup requires OTP entry
    'otp_enabled' => false,

    // If true, damaged/lost returns require at least one photo
    'require_damage_photos' => false,

    // If true, compute damage/lost fees and settlement amounts on return
    'settlement_enabled' => false,

    // If true, scheduled task will auto-cancel expired approvals and release reservations
    'auto_cancel_enabled' => true,
];
