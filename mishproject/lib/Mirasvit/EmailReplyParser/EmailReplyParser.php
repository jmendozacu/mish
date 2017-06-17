<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Help Desk MX
 * @version   1.1.0
 * @build     1285
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */



/**
 * This file is part of the EmailReplyParser package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */


/**
 * @author William Durand <william.durand1@gmail.com>
 */
class EmailReplyParser
{
    /**
     * @param string $text An email as text.
     *
     * @return Email
     */
    public static function read($text)
    {
        $parser = new EmailParser();

        return $parser->parse($text);
    }

    /**
     * @param string $text An email as text.
     *
     * @return string
     */
    public static function parseReply($text)
    {
        return EmailReplyParser::read($text)->getVisibleText();
    }
}
