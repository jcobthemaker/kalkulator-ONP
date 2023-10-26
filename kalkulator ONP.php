<!DOCTYPE html>
<html>
<body>

<?php

interface PRN
{
    public function calculate(string $input): float;
}

class Stack {
    private $stack;

    public function __construct() {
        $this->stack = [];
    }

    public function push($value) {
        array_push($this->stack, $value);
    }

    public function pop() {
        if ($this->isEmpty()) {
            throw new Exception("Ten stos jest pusty");
        }
        return array_pop($this->stack);
    }

    public function isEmpty() {
        return empty($this->stack);
    }

    public function getArray() {
        return $this->stack;
    }
}

class Calculator implements PRN {
    private $stack;

    private $operatorList = [
        '+' => 1,
        '-' => 1,
        '*' => 2,
        '/' => 2,
        '^' => 3,
    ];

    public function __construct() {
        $this->stack = new Stack();
    }

    public function convertToRPN($infix) {
        $output = [];
        $operatorStack = [];

       
        $tokens = explode(' ', $infix);
        foreach ($tokens as $token) {
            if (is_numeric($token)) {
                $output[] = $token;
            } elseif (array_key_exists($token, $this->operatorList)) {
                while (
                    count($operatorStack) > 0
                    && array_key_exists(end($operatorStack), $this->operatorList)
                    && $this->operatorList[$token] <= $this->operatorList[end($operatorStack)]
                ) {
                    $output[] = array_pop($operatorStack);
                }
                $operatorStack[] = $token;
            } elseif ($token === '(') {
                $operatorStack[] = $token;
            } elseif ($token === ')') {
                while (count($operatorStack) > 0 && end($operatorStack) !== '(') {
                    $output[] = array_pop($operatorStack);
                }
                array_pop($operatorStack); 
            }
        }

        while (count($operatorStack) > 0) {
            $output[] = array_pop($operatorStack);
        }

        return implode(' ', $output);
    }
    
    
    public function calculate(string $input): float {
        $opnForm = $this->convertToRPN($input);

        $tokens = explode(' ', $opnForm);    
        foreach ($tokens as $token) {
            if (is_numeric($token)) {
                $this->stack->push($token);
            } elseif (array_key_exists($token, $this->operatorList)) {
                $operand2 = $this->stack->pop();
                $operand1 = $this->stack->pop();

                switch ($token) {
                    case '+':
                        $result = $operand1 + $operand2;
                        break;
                    case '-':
                        $result = $operand1 - $operand2;
                        break;
                    case '*':
                        $result = $operand1 * $operand2;
                        break;
                    case '/':
                        $result = $operand1 / $operand2;
                        break;
                    case '^':
                        $result = $operand1 ** $operand2;
                        break;
                    default:
                        throw new Exception("Niepoprawny operator: $token");
                }

                $this->stack->push($result);
            }
        }

        if ($this->stack->isEmpty()) {
            throw new Exception("Wyrażenie jest puste");
        } else {
            return (float)$this->stack->pop();
        }
    }
}


$sentence = "12 + ( 2 / 1 ) * 4 + 2 ^ 2";

$newCalculator = new Calculator();
$result = $newCalculator->calculate($sentence);
$resultOPN = $newCalculator-> convertToRPN($sentence);
echo "Wyrażenie infiksowe: $sentence <br>";
echo "Wyrażenie OPN: $resultOPN <br>";
echo "Result: $result";

?>

</body>
</html>