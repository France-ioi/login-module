<?php

namespace App\LoginModule\Shared;

use DateTimeInterface;

/**
 * Since Laravel 7, models serialize dates (toArray / toJson) in ISO-8601
 * format. Keep the Laravel 5.4 format ("Y-m-d H:i:s") so the JSON returned
 * to platforms and in data exports does not change.
 */
trait SerializesLegacyDates
{
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->getDateFormat());
    }
}
