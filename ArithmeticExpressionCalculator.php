<?php

namespace ArithmeticExpressionCalculator;


/**
 * Class ArithmeticExpressionCalculator
 *
 * @purpose : This class operate arithmetic expression operations
 *
 * @created_by : Arif Khan
 * @created_at : 5th Feb, 2019 at 11.30 AM
 *
 * @package App\CustomLibraries
 */
class ArithmeticExpressionCalculator
{
    protected $expression;
    protected $expression_types =[
        'constant' => 1,
        'variable' => 2
    ];
    protected $expression_type;
    protected $expression_array = [];
    protected $parameters;
    protected $precision;
    protected $debug;
    protected $operators;

    /**
     * ArithmeticExpressionCalculator constructor.
     * @param string $expression
     * @param int $expression_type Should be 1 => Constant OR 2 => Variable
     * @param array $arguments
     * @param bool $debug
     */
    public function __construct($expression, $expression_type = 1, $arguments =[], $debug = false)
    {
        $this->expression = $expression;
        $this->expression_type = $expression_type;
        $this->debug = $debug;
        $this->operators = [
            '+' => ['precedence' => 0, 'associativity' => 'left'],
            '-' => ['precedence' => 0, 'associativity' => 'left'],
            '*' => ['precedence' => 1, 'associativity' => 'left'],
            '/' => ['precedence' => 1, 'associativity' => 'left'],
            '%' => ['precedence' => 1, 'associativity' => 'left'],
            '^' => ['precedence' => 2, 'associativity' => 'right']
        ];

        // If expression_types is variable then replace all variables with it's values
        if($this->expression_type == $this->expression_types['variable'] && !empty($arguments))
        {
            $this->expression = str_replace(array_keys($arguments),array_values($arguments),$this->expression);
        }

        // Convert expression to array of constant / variables
        $this->expression_array = array_map('trim', explode(' ', $this->expression));;
    }

    /**
     * syntaxAnalyzer()
     *
     * @purpose : This function check syntax of a arithmetic expression
     *
     * @created_by : Arif Khan
     * @created_at : 5th Feb, 2019 at 11.30 AM
     *
     * @return bool
     * @throws \HttpException
     */
    public function syntaxAnalyzer()
    {
        $this->convertInfixExpressionArrayToPostfixExpressionArray();
        return true;
    }

    /**
     * convertInfixExpressionToPostfixExpression()
     *
     * @purpose : This function convert Infix Expression To Postfix Expression
     *
     * @created_by : Arif Khan
     * @created_at : 5th Feb, 2019 at 11.30 AM
     *
     * @return bool
     * @throws \HttpException
     */
    public function convertInfixExpressionToPostfixExpression()
    {
        $postfix_expression_array = $this->convertInfixExpressionArrayToPostfixExpressionArray();
        return implode(' ',$postfix_expression_array);
    }

    /**
     * calculateExpression()
     *
     * @purpose : This function calculate a arithmetic expression
     *
     * @created_by : Arif Khan
     * @created_at : 5th Feb, 2019 at 11.30 AM
     *
     * @return bool
     * @throws \HttpException
     */
    public function calculateExpression()
    {
        // Set expression_type to constant for calculate expression
        $this->expression_type = $this->expression_types['constant'];

        $postfix_expression_array = $this->convertInfixExpressionArrayToPostfixExpressionArray();
        return $this->calculatePostfixExpressionArray($postfix_expression_array);
    }

    /**
     * convertInfixArrayToPostfixArray()
     *
     * @purpose : This function convert infix expression to postfix expression. This function based on shunting_yard algorithm
     *
     * @created_by : Arif Khan
     * @created_at : 5th Feb, 2019 at 11.30 AM
     *
     * @return array|\SplQueue
     * @throws \HttpException
     */
    protected function convertInfixExpressionArrayToPostfixExpressionArray()
    {
        $stack = new \SplStack();
        $output = new \SplQueue();
        $operator_count = 0;
        $operand_count = 0;

        // Scan expression from left to right
        foreach ($this->expression_array as $expression_variable)
        {
            // If Expression Type is constant and Expression Variable is numeric then it is a operand add it to queue
            // If Expression Type is variable Expression Variable is alphanumeric then it is a operand add it to queue
            if (($this->expression_type == $this->expression_types['constant'] && is_numeric($expression_variable)) || ($this->expression_type == $this->expression_types['variable'] && ctype_alnum($expression_variable)))
            {
                $operand_count++;
                $output->enqueue($expression_variable);
            }
            else
            {
                // If Expression Variable is operator
                if (isset($this->operators[$expression_variable]))
                {
                    $operator_count++;
                    $operator_1 = $expression_variable;

                    // Repeatedly pop from Stack and add to queue each operator which has the same precedence as or higher precedence than operator.
                    while ($this->hasOperator($stack) && ($operator_2 = $stack->top()) && $this->hasLowerPrecedence($operator_1, $operator_2))
                    {
                        $output->enqueue($stack->pop());
                    }

                    // Add operator to Stack.
                    $stack->push($operator_1);
                }
                else
                {
                    // If a left parenthesis is encountered, push it onto Stack.
                    if ($expression_variable === '(')
                    {
                        $stack->push($expression_variable);
                    }
                    else
                    {
                        // If a right parenthesis is encountered ,then:
                        if (')' === $expression_variable)
                        {
                            // If stack is not empty then
                            // Repeatedly pop from Stack and add to queue each operator until a left parenthesis is encountered.
                            while (count($stack) > 0 && '(' !== $stack->top())
                            {
                                $output->enqueue($stack->pop());
                            }

                            // If Stack is empty when right parenthesis is encountered
                            if (count($stack) === 0)
                            {
                                throw new \HttpException('Mismatched parenthesis in input',400);
                            }

                            // Remove the left Parenthesis from stack
                            $stack->pop();
                        }
                        else
                        {
                            // Invalid Character
                            throw new \HttpException(sprintf('Invalid token: %s', $expression_variable),400);
                        }
                    }
                }
            }
        }

        // If Stack have any operator than add it to queue
        while ($this->hasOperator($stack))
        {
            $output->enqueue($stack->pop());
        }

        // If Stack have any value
        if (count($stack) > 0)
        {
            throw new \HttpException('Mismatched parenthesis or misplaced number in input',400);
        }

        // Operands should be greater than 1 from operator for valid arithmetic expression
        if ($operand_count !== $operator_count+1)
        {
            throw new \HttpException('Operands should be greater than 1 from operator for valid arithmetic expression',400);
        }

        return iterator_to_array($output);
    }

    /**
     * hasOperator()
     *
     * @purpose : This function check if a stack have any operator
     *
     * @created_by : Arif Khan
     * @created_at : 5th Feb, 2019 at 11.30 AM
     *
     * @param \SplStack $stack
     * @return bool
     */
    protected function hasOperator(\SplStack $stack)
    {
        return count($stack) > 0 && ($top = $stack->top()) && isset($this->operators[$top]);
    }

    /**
     * hasLowerPrecedence()
     *
     * @purpose : This function check if operator_1 (op1) have lower and equal precedence from operator_2 (op2)
     *
     * @created_by : Arif Khan
     * @created_at : 5th Feb, 2019 at 11.30 AM
     *
     * @param $operator_1
     * @param $operator_2
     * @return bool
     */
    protected function hasLowerPrecedence($operator_1, $operator_2)
    {
        $op1 = $this->operators[$operator_1];
        $op2 = $this->operators[$operator_2];
        return ('left' === $op1['associativity'] && $op1['precedence'] === $op2['precedence']) || $op1['precedence'] < $op2['precedence'];
    }

    /**
     * calculatePostfixExpressionArray()
     *
     * @purpose : This function calculate a Postfix Expression Array
     *
     * @created_by : Arif Khan
     * @created_at : 5th Feb, 2019 at 11.30 AM
     *
     * @param $postfix_expression_array
     * @return mixed
     * @throws \HttpException
     */
    protected function calculatePostfixExpressionArray($postfix_expression_array)
    {
        $stack = new \SplStack();

        foreach ($postfix_expression_array as $postfix_expression_variable)
        {
            if (is_numeric($postfix_expression_variable))
            {
                $stack->push((float)$postfix_expression_variable);
                continue;
            }

            switch ($postfix_expression_variable)
            {
                case '+':
                    $stack->push($stack->pop() + $stack->pop());
                    break;
                case '-':
                    $n = $stack->pop();
                    $stack->push($stack->pop() - $n);
                    break;
                case '*':
                    $stack->push($stack->pop() * $stack->pop());
                    break;
                case '/':
                    $n = $stack->pop();
                    $stack->push($stack->pop() / $n);
                    break;
                case '%':
                    $n = $stack->pop();
                    $stack->push($stack->pop() % $n);
                    break;
                case '^':
                    $n = $stack->pop();
                    $stack->push(pow($stack->pop(), $n));
                    break;
                default:
                    throw new \HttpException('Invalid Operator',400);
                    break;
            }
        }

        return $stack->top();
    }
}
