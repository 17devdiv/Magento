<?php
/**
 *
  * Copyright © 2018 Magenest. All rights reserved.
  * See COPYING.txt for license details.
  *
  * Magenest_Salesforce extension
  * NOTICE OF LICENSE
  *
  * @category Magenest
  * @package  Magenest_Salesforce
  * @author    dangnh@magenest.com

 */

namespace Magenest\Salesforce\Logger;

/**
 * Class Handler
 * @package Magenest\Salesforce\Logger
 */
class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = \Monolog\Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/salesforce.log';
}