<?php

namespace App\Models\Traits;

trait ReportStatus
{
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_NO_SUBMISSION = 'no submission';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_RETURNED_FOR_RESUBMISSION = 'returned_for_resubmission';
    const STATUS_REMARKS = 'remarks';

    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function isNoSubmission(): bool
    {
        return $this->status === self::STATUS_NO_SUBMISSION;
    }
}
