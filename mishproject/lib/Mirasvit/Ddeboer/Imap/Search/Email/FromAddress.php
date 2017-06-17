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



// namespace Mirasvit_Ddeboer\Imap\Search\Email;

// use Mirasvit_Ddeboer\Imap\Search\Email;

/**
 * Represents a "From" email address condition. Messages must have been sent
 * from the specified email address in order to match the condition.
 */
class Mirasvit_Ddeboer_Imap_Search_Email_FromAddress extends Mirasvit_Ddeboer_Imap_Search_Email
{
    /**
     * Returns the keyword that the condition represents.
     *
     * @return string
     */
    public function getKeyword()
    {
        return 'FROM';
    }
}
