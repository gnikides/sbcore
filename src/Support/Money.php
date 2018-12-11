<?php namespace App\Support;

use App\Exceptions\MathException;

class Money
{
    protected $cents;
    const SCALE             = 6;
    const ROUND_HALF_UP     = PHP_ROUND_HALF_UP;
    const ROUND_HALF_DOWN   = PHP_ROUND_HALF_DOWN;
    const ROUND_HALF_EVEN   = PHP_ROUND_HALF_EVEN;
    const ROUND_HALF_ODD    = PHP_ROUND_HALF_ODD;
    const ROUND_UP          = 5;
    const ROUND_DOWN        = 6;

    public function __construct($cents='')
    {
        $this->cents = $this->setCents($cents);
    }

    public function add(Money $money)
    {
        if (function_exists('bcadd')) {
            $amount = bcadd($this->cents, $money->getCents(), self::SCALE);
        } else {
            $amount = $this->cents + $money->getCents();
        }
        return new Money($amount);
    }

    public function subtract(Money $money)
    {
        if (function_exists('bcsub')) {
            $amount = bcsub($this->cents, $money->getCents(), self::SCALE);
        } else {
            $amount = $this->cents - $money->getCents();
        }
        return new Money($amount);
    }

    public function multiply($by)
    {
        if (function_exists('bcmul')) {
            $amount = bcmul($this->cents, $by, self::SCALE);
        } else {
            $amount = $this->cents * $by;
        }
        return new Money($this->round($amount));
    }

    public function divide($by)
    { 
        $this->assertNonZero($by);
        if (function_exists('bcdiv')) {
            $amount = bcdiv($this->cents, $by, self::SCALE);
        } else {
            $amount = $this->cents / $by;
        }
        return new Money($this->round($amount));
    }

    public function compareTo(Money $money)
    {
        if (function_exists('bccomp')) {
            return bccomp($this->cents, $money->getCents(), self::SCALE);
        }
        return $this->cents == $money->getCents();
    }

    public function equals(Money $money)
    { 
        return $this->compareTo($money) == 0;
    }

    public function greaterThan(Money $money)
    { 
        return $this->compareTo($money) == 1;
    }

    public function greaterThanOrEqual(Money $money)
    {
        return $this->greaterThan($money) || $this->equals($money);
    }

    public function round($cents, $rounding=self::ROUND_HALF_DOWN)
    {
        $this->assertRounding($rounding);
        if ($rounding === self::ROUND_UP) {
            return (int) ceil($cents);
        }
        if ($rounding === self::ROUND_DOWN) {
            return (int) floor($cents);
        }
        return (int)round($cents, 0, $rounding);
    }

    public function allocate(array $ratios)
    {
        $remainder = $this->cents;
        $results = [];
        $total = array_sum($ratios);
        foreach ($ratios as $ratio) {
            $share = (int) floor($this->cents * $ratio / $total);
            $results[] = new Money($share);
            $remainder -= $share;
        }
        for ($i = 0; $remainder > 0; $i++) {
            $results[$i]->addCents(1);
            $remainder--;
        }
        return $results;
    }

    /**
     * Must be numeric string - bcmath doesn't understand floats & strings
     *
     * @param $cents
     * @throws MathException
     * @internal param string $number
     */
    public function assertNumeric($cents)
    {
        if (is_float($cents)) {
            throw new MathException(sprintf('The cents "%s" must not be a float.', $cents));
        }
        // @todo
//         if (!is_numeric($cents)) {
//          pr($cents);
//             throw new MathException(sprintf('The cents "%s" must be numeric.', $cents));
//         }
    }
    
    private function assertNonZero($value)
    {
        /*
            All these true for var_dump($x == 0);
            '00'
            '0.00'
            '00.00'
            ''
            0
            '0'
            null    
            '0.00000'
        */ 
        if ($value == 0) {
            throw new MathException("Zero value not allowed");
        }
    }

    public function assertRounding($rounding)
    {
        if (!in_array($rounding, [
                self::ROUND_HALF_DOWN,
                self::ROUND_HALF_EVEN,
                self::ROUND_HALF_ODD,
                self::ROUND_HALF_UP,
                self::ROUND_UP,
                self::ROUND_DOWN])) {
            throw new MathException('Rounding mode not permitted');
        }
    }

    public function setCents($cents)
    { 
        $this->assertNumeric($cents);
        $this->cents = $cents;
        return $this->cents;
    }

    public function getCents()
    {
        return $this->cents;
    }

    public function addCents($cents=1)
    {
        $this->cents += $cents;
        return $this->cents;
    }
      
    public function __toString()
    {
        return (string)$this->cents;
    } 
}