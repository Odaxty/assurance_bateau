<?php

namespace App\Service;

use App\Entity\Boat;

class InsuranceCalculator
{
    private const tau_standard = 0.02;
    private const tau_lux = 0.015;
    private const remise_voilier = 0.10;
    private const remise_fidelite = 0.05;

    public function calculatePremium(Boat $boat): float
    {
        $price = $boat->getPurchasePrice();

        $rate = $this->getBaseRate($price);
        $total = $price * $rate;

        $total -= $total * $this->getSmartDiscount($boat);

        return $total;
    }

    private function getBaseRate(float $price): float
    {
        return ($price >= 1_000_000) ? self::tau_lux : self::tau_standard;
    }

    private function getSmartDiscount(Boat $boat): float
    {
        $discount = 0;

        if (stripos($boat->getName(), 'voilier') !== false) {
            $discount += self::remise_voilier;
        }

        if ($boat->getAccount() && count($boat->getAccount()->getBoats()) > 1) {
            $discount += self::remise_fidelite;
        }

        return $discount;
    }
}
