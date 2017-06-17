<?php
// echo "<pre>";
// print_r($_GET);
//exit();
$mageFilename = 'app/Mage.php';
require_once $mageFilename;
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app();
$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
$sql        = $connection->query("UPDATE personallogistic SET status='1' WHERE mail='".$_GET['email']."' AND hash='".$_GET['hash']."' AND status='2'");

if (!empty($_GET)) 
{
    if($sql)
    {
        echo '<div class="statusmsg">Your account has been activated, you can now login</div>';
       
    } 
    else 
    {
        // No match -> invalid url or account has already been activated.
        echo '<div class="statusmsg">The url is either invalid or you already have activated your account.</div>';
    }
 } else{
    // Invalid approach
    echo '<div class="statusmsg">Invalid approach, please use the link that has been send to your email.</div>';
 }   
        
                 
