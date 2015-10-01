<?php
namespace app\models\amendmentNumbering;

use app\models\db\Amendment;
use app\models\db\Motion;

class GlobalCompact extends IAmendmentNumbering
{

    /**
     * @return string
     */
    public static function getName()
    {
        return 'Ä1 zu A1 (Globale Zählung)';
    }

    /**
     * @return int
     */
    public static function getID()
    {
        return 1;
    }


    /**
     * @param Amendment $amendment
     * @param Motion $motion
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getAmendmentNumber(Amendment $amendment, Motion $motion)
    {
        $prefixes = [];
        foreach ($motion->consultation->motions as $mot) {
            foreach ($mot->amendments as $amend) {
                $prefixes[] = $amend->titlePrefix;
            }
        }
        $maxRev = static::getMaxTitlePrefixNumber($prefixes);
        return 'Ä' . ($maxRev + 1);
    }

    /**
     * @param Motion $motion
     * @param string $prefix
     * @param null|Amendment $ignore
     * @return Amendment|null
     */
    public function findAmendmentWithPrefix(Motion $motion, $prefix, $ignore = null)
    {
        $prefixNorm = trim(mb_strtoupper($prefix));
        foreach ($motion->consultation->motions as $mot) {
            if ($mot->status == Motion::STATUS_DELETED) {
                continue;
            }
            foreach ($mot->amendments as $amend) {
                $amendPrefixNorm = trim(mb_strtoupper($amend->titlePrefix));
                if ($amendPrefixNorm != '' && $amendPrefixNorm === $prefixNorm
                    && $amend->status != Amendment::STATUS_DELETED
                ) {
                    if ($ignore === null || $ignore->id != $amend->id) {
                        return $amend;
                    }
                }
            }
        }
        return null;
    }
}
