<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The Open Group Test Suite License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Test\TestCase\Database;

use Cake\Database\Expression\IdentifierExpression;
use Cake\Database\FunctionsBuilder;
use Cake\Database\ValueBinder;
use Cake\TestSuite\TestCase;

/**
 * Tests FunctionsBuilder class
 */
class FunctionsBuilderTest extends TestCase
{
    /**
     * Setups a mock for FunctionsBuilder
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->functions = new FunctionsBuilder();
    }

    /**
     * Tests generating a generic function call
     *
     * @return void
     */
    public function testArbitrary()
    {
        $function = $this->functions->MyFunc(['b' => 'literal']);
        $this->assertInstanceOf('Cake\Database\Expression\FunctionExpression', $function);
        $this->assertSame('MyFunc', $function->getName());
        $this->assertSame('MyFunc(b)', $function->sql(new ValueBinder()));

        $function = $this->functions->MyFunc(['b'], ['string'], 'integer');
        $this->assertSame('integer', $function->getReturnType());
    }

    /**
     * Tests generating a SUM() function
     *
     * @return void
     */
    public function testSum()
    {
        $function = $this->functions->sum('total');
        $this->assertInstanceOf('Cake\Database\Expression\FunctionExpression', $function);
        $this->assertSame('SUM(total)', $function->sql(new ValueBinder()));
        $this->assertSame('float', $function->getReturnType());

        $function = $this->functions->sum('total', ['integer']);
        $this->assertInstanceOf('Cake\Database\Expression\FunctionExpression', $function);
        $this->assertSame('SUM(total)', $function->sql(new ValueBinder()));
        $this->assertSame('integer', $function->getReturnType());
    }

    /**
     * Tests generating a AVG() function
     *
     * @return void
     */
    public function testAvg()
    {
        $function = $this->functions->avg('salary');
        $this->assertInstanceOf('Cake\Database\Expression\FunctionExpression', $function);
        $this->assertSame('AVG(salary)', $function->sql(new ValueBinder()));
        $this->assertSame('float', $function->getReturnType());
    }

    /**
     * Tests generating a MAX() function
     *
     * @return void
     */
    public function testMAX()
    {
        $function = $this->functions->max('created', ['datetime']);
        $this->assertInstanceOf('Cake\Database\Expression\FunctionExpression', $function);
        $this->assertSame('MAX(created)', $function->sql(new ValueBinder()));
        $this->assertSame('datetime', $function->getReturnType());
    }

    /**
     * Tests generating a MIN() function
     *
     * @return void
     */
    public function testMin()
    {
        $function = $this->functions->min('created', ['date']);
        $this->assertInstanceOf('Cake\Database\Expression\FunctionExpression', $function);
        $this->assertSame('MIN(created)', $function->sql(new ValueBinder()));
        $this->assertSame('date', $function->getReturnType());
    }

    /**
     * Tests generating a COUNT() function
     *
     * @return void
     */
    public function testCount()
    {
        $function = $this->functions->count('*');
        $this->assertInstanceOf('Cake\Database\Expression\FunctionExpression', $function);
        $this->assertSame('COUNT(*)', $function->sql(new ValueBinder()));
        $this->assertSame('integer', $function->getReturnType());
    }

    /**
     * Tests generating a CONCAT() function
     *
     * @return void
     */
    public function testConcat()
    {
        $function = $this->functions->concat(['title' => 'literal', ' is a string']);
        $this->assertInstanceOf('Cake\Database\Expression\FunctionExpression', $function);
        $this->assertSame('CONCAT(title, :param0)', $function->sql(new ValueBinder()));
        $this->assertSame('string', $function->getReturnType());
    }

    /**
     * Tests generating a COALESCE() function
     *
     * @return void
     */
    public function testCoalesce()
    {
        $function = $this->functions->coalesce(['NULL' => 'literal', '1', 'a'], ['a' => 'date']);
        $this->assertInstanceOf('Cake\Database\Expression\FunctionExpression', $function);
        $this->assertSame('COALESCE(NULL, :param0, :param1)', $function->sql(new ValueBinder()));
        $this->assertSame('date', $function->getReturnType());
    }

    /**
     * Tests generating a NOW(), CURRENT_TIME() and CURRENT_DATE() function
     *
     * @return void
     */
    public function testNow()
    {
        $function = $this->functions->now();
        $this->assertInstanceOf('Cake\Database\Expression\FunctionExpression', $function);
        $this->assertSame('NOW()', $function->sql(new ValueBinder()));
        $this->assertSame('datetime', $function->getReturnType());

        $function = $this->functions->now('date');
        $this->assertInstanceOf('Cake\Database\Expression\FunctionExpression', $function);
        $this->assertSame('CURRENT_DATE()', $function->sql(new ValueBinder()));
        $this->assertSame('date', $function->getReturnType());

        $function = $this->functions->now('time');
        $this->assertInstanceOf('Cake\Database\Expression\FunctionExpression', $function);
        $this->assertSame('CURRENT_TIME()', $function->sql(new ValueBinder()));
        $this->assertSame('time', $function->getReturnType());
    }

    /**
     * Tests generating a EXTRACT() function
     *
     * @return void
     */
    public function testExtract()
    {
        $function = $this->functions->extract('day', 'created');
        $this->assertInstanceOf('Cake\Database\Expression\FunctionExpression', $function);
        $this->assertSame('EXTRACT(day FROM created)', $function->sql(new ValueBinder()));
        $this->assertSame('integer', $function->getReturnType());

        $function = $this->functions->datePart('year', 'modified');
        $this->assertInstanceOf('Cake\Database\Expression\FunctionExpression', $function);
        $this->assertSame('EXTRACT(year FROM modified)', $function->sql(new ValueBinder()));
        $this->assertSame('integer', $function->getReturnType());
    }

    /**
     * Tests generating a DATE_ADD() function
     *
     * @return void
     */
    public function testDateAdd()
    {
        $function = $this->functions->dateAdd('created', -3, 'day');
        $this->assertInstanceOf('Cake\Database\Expression\FunctionExpression', $function);
        $this->assertSame('DATE_ADD(created, INTERVAL -3 day)', $function->sql(new ValueBinder()));
        $this->assertSame('datetime', $function->getReturnType());

        $function = $this->functions->dateAdd(new IdentifierExpression('created'), -3, 'day');
        $this->assertInstanceOf('Cake\Database\Expression\FunctionExpression', $function);
        $this->assertSame('DATE_ADD(created, INTERVAL -3 day)', $function->sql(new ValueBinder()));
    }

    /**
     * Tests generating a DAYOFWEEK() function
     *
     * @return void
     */
    public function testDayOfWeek()
    {
        $function = $this->functions->dayOfWeek('created');
        $this->assertInstanceOf('Cake\Database\Expression\FunctionExpression', $function);
        $this->assertSame('DAYOFWEEK(created)', $function->sql(new ValueBinder()));
        $this->assertSame('integer', $function->getReturnType());

        $function = $this->functions->weekday('created');
        $this->assertInstanceOf('Cake\Database\Expression\FunctionExpression', $function);
        $this->assertSame('DAYOFWEEK(created)', $function->sql(new ValueBinder()));
        $this->assertSame('integer', $function->getReturnType());
    }

    /**
     * Tests generating a RAND() function
     *
     * @return void
     */
    public function testRand()
    {
        $function = $this->functions->rand();
        $this->assertInstanceOf('Cake\Database\Expression\FunctionExpression', $function);
        $this->assertSame('RAND()', $function->sql(new ValueBinder()));
        $this->assertSame('float', $function->getReturnType());
    }
}
