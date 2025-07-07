<?php

class Utils
{
    public static function formatDate($date)
    {
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    }

    public static function pmt($taux, $nbPeriodes, $capital)
    {
        if ($taux == 0) {
            return $capital / $nbPeriodes;
        }
        return ($capital * $taux) / (1 - pow(1 + $taux, -$nbPeriodes));
    }
}
