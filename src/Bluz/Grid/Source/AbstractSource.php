<?php
/**
 * Copyright (c) 2013 by Bluz PHP Team
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @namespace
 */
namespace Bluz\Grid\Source;

use Bluz\Grid\Grid;

/**
 * Adapter
 *
 * @category Bluz
 * @package  Grid
 *
 * @author   Anton Shevchuk
 * @created  15.08.12 11:52
 */
abstract class AbstractSource
{
    /**
     * @var mixed
     */
    protected $source;

    /**
     * @var array
     */
    protected $filters = [
        Grid::FILTER_EQ => '=',
        Grid::FILTER_NE => '!=',
        Grid::FILTER_GT => '>',
        Grid::FILTER_GE => '>=',
        Grid::FILTER_LT => '<',
        Grid::FILTER_LE => '<=',
        Grid::FILTER_LIKE => 'like',
    ];

    /**
     * Setup adapter source
     *
     * @param $source
     * @return AbstractSource
     */
    abstract public function setSource($source);

    /**
     * Process source
     *
     * @param array $settings
     * @return AbstractSource
     */
    abstract public function process(array $settings = []);
}
