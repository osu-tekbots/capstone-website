<?php
namespace DataAccess;

class QueryUtils {
    const DATE_STR = 'Y-m-d H:i:s';

    /**
     * Formats a DateTime object as a string
     *
     * @param \DateTime $d
     * @return string
     */
    public static function FormatDate($d) {
        return $d != null ? $d->format(self::DATE_STR) : null;
    }

}