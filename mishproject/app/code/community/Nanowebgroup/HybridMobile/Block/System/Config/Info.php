<?php

class Nanowebgroup_HybridMobile_Block_System_Config_Info extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$validate = Mage::helper('hybridmobile')->validateAction();
		$email = Mage::getStoreConfig('hybrid_mobile/registration/email', Mage::app()->getStore());



		if($validate) {
			$html = 
			'<div style="background:#FFFFFF scroll #2c2c2c; border-style:solid; border-width:thin; margin-bottom:10px;padding:36px 36px 36px 36px; ">
				<ul>
					<li>
						<h3>Welcome to Giganto™ - The Magento Mobile Store</h3>
						<p>
						Giganto™ is a mobile extension for Magento commerce designed by ecommerce professionals and optimized for positive sales conversions. 
						<br>
						<br>Your Giganto™ extension is<strong style=color:#35AC19> Active!</strong>
						<br>
						<h4 style="color:#2c2c2c">Support Requests</h4>
							Please contact support if you require assistance: <a href="mailto:support@nanowebgroup.com">support@nanowebgroup.com</a>
						</p>
					</li>
				</ul>
			</div>';

		} elseif (!$validate && $email) {
			$html = 
			'<div  style="background:#FFFFFF scroll #2c2c2c; border-style:solid; border-width:thin; margin-bottom:10px;padding:36px 36px 36px 36px; ">
				<ul>
					<li>
						<h3>Welcome to Giganto™ - The Magento Mobile Store</h3>
						<p>
						Giganto™ is a mobile extension for Magento commerce designed by ecommerce professionals and optimized for positive sales conversions. 
						<br>Your <strong> free 30 day trial </strong> has expired.
						<br>For more information about pricing and details please click <a href="http://nanowebgroup.com/product/giganto-mobile-ecommerce-for-magento-2/" target="_blank">here</a>. 
						<br>
						<h4>Needs Urgent Attention</h4>
							&#8226;&nbsp;Your free trial has expired, please <a href="http://nanowebgroup.com/product/giganto-mobile-ecommerce-for-magento-2/" target="_blank">Subscribe to Giganto™</a> 
							<br><br>
							Please contact sales if you require assistance: <a href="mailto:sales@nanowebgroup.com">sales@nanowebgroup.com</a>
						</p>
					</li>
				</ul>
			</div>';
		}
		
		else {
			$html = 
			'<div style="background:#FFFFFF scroll #2c2c2c; border-style:solid; border-width:thin; margin-bottom:10px;padding:36px 36px 36px 36px; ">
				<ul>
					<li>
						<h3>Welcome to Giganto™ - The Magento Mobile Store</h3>
						<p>
						Giganto™ is a mobile extension for Magento commerce designed by ecommerce professionals and optimized for positive sales conversions. 
						<br>This theme automatically grants you a <strong> free 30 day trial </strong> by putting in your email address below. 
						<br>For more information about pricing and details please click <a href="http://nanowebgroup.com/product/giganto-mobile-ecommerce-for-magento-2/" target="_blank">here</a>. 
						<br>
						<h4>Mobile Launch Checklist</h4>
							&#8226;&nbsp;Enter your email to get a free trial.
							<br>&#8226;&nbsp;Enable theme
							<br>&#8226;&nbsp;Select your store colors.
							<br>&#8226;&nbsp;Upload your logo.
							<br>&#8226;&nbsp;Select your menu options.
							<br>&#8226;&nbsp;Select your home page categories and products.
							<br>&#8226;&nbsp;Upload your slider images, which are 1242px by 600px each.
							<br>&#8226;&nbsp;Remember to purchase a subscription before 30 days are up: <a href="http://nanowebgroup.com/product/giganto-mobile-ecommerce-for-magento-2/" target="_blank">Subscribe to Giganto™</a> 
							<br><br>
							Email support if you require assistance: <a href="mailto:support@nanowebgroup.com">support@nanowebgroup.com</a>
						</p>
					</li>
				</ul>
			</div>';
		}
        
        return $html;
    }
}
