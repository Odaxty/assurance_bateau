<?php
namespace App\Tests\Service;

use App\Entity\Boat;
use App\Entity\Account;
use App\Service\InsuranceCalculator;
use PHPUnit\Framework\TestCase;

class InsuranceCalculatorTest extends TestCase
{
    public function testCalculateTotalBoat(): void
    {
        $calculator = new InsuranceCalculator();
        $account = new Account();

        $boat1 = new Boat();
        $boat1->setPurchasePrice(100000); //prime attendue : 2000

        $boat2 = new Boat();
        $boat2->setPurchasePrice(3000000); //prime attendue : 30 000

        $account->addBoat($boat1);
        $account->addBoat($boat2);
        $price1 = $calculator->calculatePremium($boat1);
        $price2 = $calculator->calculatePremium($boat2);
        $total=$price1 + $price2;

        $this->assertEquals(32000, $total); //vérification total
    }
}
