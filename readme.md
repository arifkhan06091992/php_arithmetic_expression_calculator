# PHP Arithmetic Expression Calculator

A simple library to be use for check syntax of a arithmetic expression, convert Infix Expression To Postfix Expression and calculate a arithmetic expression.

## Installation

You can install it using [Composer](https://getcomposer.org).

```shell
composer require arifkhan06091992/php_arithmetic_expression_calculator
```

### Dependencies

- PHP 5.6+

## Basic usage

### Check Syntax Analyzer

You can check artihmatic expression syntax using this package.

```php
$expression = '2 * ( 3 - 2 ) / ( 4 * 5 - 4 ) + 6';
$cal = new ArithmeticExpressionCalculator($expression,1);
$cal->syntaxAnalyzer() // True

$expression = '+ 2 * ( 3 - 2 ) / ( 4 * 5 - 4 ) + 6';
$cal = new ArithmeticExpressionCalculator($expression,1);
$cal->syntaxAnalyzer() // False


$expression = 'a * ( b - a ) / ( c * d - c ) + e';
$cal = new ArithmeticExpressionCalculator($expression,2);
$cal->syntaxAnalyzer() // True

$expression = 'operand1 * ( operand2 - operand1 ) / ( operand3 * operand4 - operand3 ) + operand5';
$cal = new ArithmeticExpressionCalculator($expression,1);
$cal->syntaxAnalyzer() // True
```

### Convert Infix Expression To Postfix Expression

You can check artihmatic expression syntax using this package.

```php
$expression = '2 * ( 3 - 2 ) / ( 4 * 5 - 4 ) + 6';
$cal = new ArithmeticExpressionCalculator($expression,1);
$cal->convertInfixExpressionToPostfixExpression()
// 2 3 2 - * 4 5 * 4 - / 6 +

$expression = '+ 2 * ( 3 - 2 ) / ( 4 * 5 - 4 ) + 6';
$cal = new ArithmeticExpressionCalculator($expression,1);
$cal->convertInfixExpressionToPostfixExpression() // Error


$expression = 'a * ( b - a ) / ( c * d - c ) + e';
$cal = new ArithmeticExpressionCalculator($expression,2);
$cal->convertInfixExpressionToPostfixExpression() // True

$expression = 'operand1 * ( operand2 - operand1 ) / ( operand3 * operand4 - operand3 ) + operand5';
$cal = new ArithmeticExpressionCalculator($expression,1);
$cal->convertInfixExpressionToPostfixExpression() // True
```

### Calculate a Arithmetic Expression

You can check calculate a arithmetic expression using this package.

```php
$expression = '2 * ( 3 - 2 ) / ( 4 * 5 - 4 ) + 6';
$cal = new ArithmeticExpressionCalculator($expression,1);
$cal->calculateExpression()
// 6.125

$expression = '+ 2 * ( 3 - 2 ) / ( 4 * 5 - 4 ) + 6';
$cal = new ArithmeticExpressionCalculator($expression,1);
$cal->calculateExpression() // Error


$expression = 'operand1 * ( operand2 - operand1 ) / ( operand3 * operand4 - operand3 ) + operand5';
$cal = new ArithmeticExpressionCalculator($expression,2, [
        'operand1' => 2,
        'operand2' => 3,
        'operand3' => 4,
        'operand4' => 5,
        'operand5' => 6
    ]);
$cal->calculateExpression() // 6.125
```
