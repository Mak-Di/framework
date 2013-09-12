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
namespace Bluz\Rest;

use Bluz\Application\Exception\NotFoundException;
use Bluz\Request\AbstractRequest;
use Bluz\Application\Exception\NotImplementedException;

/**
 * Rest
 *
 * @category Bluz
 * @package  Rest
 *
 * @author   Anton Shevchuk
 * @created  13.08.13 13:09
 */
abstract class AbstractRest
{
    /**
     * HTTP Method
     * @var string
     */
    protected $method = AbstractRequest::METHOD_GET;

    /**
     * Identifier
     * @var string
     */
    protected $id;

    /**
     * Params from request
     * @var array
     */
    protected $params = array();

    /**
     * Prepare request for processing
     */
    public function __construct()
    {
        $request = app()->getRequest();

        // rewrite REST with "_method" param
        // this is workaround
        $this->method = strtoupper($request->getParam('_method', $request->getMethod()));

        // try to get uid
        $uri = parse_url($request->getCleanUri(), PHP_URL_PATH);

        $result = explode('/', trim($uri, '/'));

        // Why 3?
        // Because: %module% / %controller% / %id%
        if (sizeof($result) >= 3) {
            $this->id = $result[2];
        }

        // get all params
        $allParams = $request->getAllParams();

        unset($allParams['_method']);

        $this->params = $allParams;
    }

    /**
     * getMethod
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * list of items
     *
     * @param array $params
     * @throws NotImplementedException
     * @return mixed
     */
    protected function index(array $params = array())
    {
        throw new NotImplementedException();
    }

    /**
     * read item
     *
     * @param $id
     * @throws NotImplementedException
     * @return mixed
     */
    protected function get($id)
    {
        throw new NotImplementedException();
    }

    /**
     * create new item
     *
     * @param array $data
     * @throws NotImplementedException
     * @return mixed
     */
    protected function post(array $data)
    {
        throw new NotImplementedException();
    }

    /**
     * update item
     *
     * @param $id
     * @param array $data
     * @throws NotImplementedException
     * @return integer
     */
    protected function put($id, array $data)
    {
        throw new NotImplementedException();
    }

    /**
     * delete item
     *
     * @param $id
     * @throws NotImplementedException
     * @return integer
     */
    protected function delete($id)
    {
        throw new NotImplementedException();
    }

    /**
     * @throws NotImplementedException
     * @throws NotFoundException
     * @return mixed
     */
    public function __invoke()
    {
        $request = app()->getRequest();

        // GET    /module/rest/   -> collection
        // GET    /module/rest/id -> get item
        // POST   /module/rest/   -> create item -> send 201 HTTP
        // PUT    /module/rest/id -> update item
        // DELETE /module/rest/id -> delete item
        switch ($this->method) {
            case AbstractRequest::METHOD_GET:
                if ($this->id) {
                    $result = $this->get($this->id);
                    if (!sizeof($result)) {
                        throw new NotFoundException();
                    }
                    return current($result);
                } else {
                    if ($range = $request->getHeader('Range')) {
                        list(, $offset, $last) = preg_split('/[-=]/', $range);
                        // for better compatibility
                        $this->params['limit'] = isset($this->params['limit'])?$this->params['limit']:($last - $offset);
                        $this->params['offset'] = isset($this->params['offset'])?$this->params['offset']:$offset;
                    }

                    return $this->index($this->params);
                }
                break;
            case AbstractRequest::METHOD_POST:
                if ($this->id) {
                    // POST + ID is incorrect behaviour
                    throw new NotImplementedException();
                }
                $result = $this->post($this->params);
                if (!$result) {
                    throw new NotFoundException();
                }
                header(
                    'Location: '.app()->getRouter()->url($request->getModule(), $request->getController()).'/'.$result,
                    true,
                    201
                );
                return false; // disable view
                break;
            case AbstractRequest::METHOD_PUT:
                if (!$this->id) {
                    // "bulk update collection" is not implemented
                    throw new NotImplementedException();
                }
                $result = $this->put($this->id, $this->params);
                if (!$result) {
                    throw new NotFoundException();
                }
                return false; // disable view
                break;
            case AbstractRequest::METHOD_DELETE:
                if (!$this->id) {
                    // "delete collection" is not implemented
                    throw new NotImplementedException();
                }
                $result = $this->delete($this->id);
                if (!$result) {
                    throw new NotFoundException();
                }
                return false; // disable view
                break;
            default:
                throw new NotImplementedException();
                break;
        }
    }
}
