<?php


$defautlTemplate = array(
    array(
        "status"=>1,
        "type"=>0,
        "title"=>"Customer",
        "content_template"=>'<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
                <table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
                    <tr>
                        <td align="center" valign="top" style="padding:20px 0 20px 0">
                            <!-- [ header starts here] -->
                            <table bgcolor="FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">
                                <tr>
                                    <td valign="top">
                                        <a href="{{store url=""}}"><img src="{{var logo_url}}" alt="{{var logo_alt}}" style="margin-bottom:10px;" border="0"/></a>
                                    </td>
                                </tr>
                           		<tr>
            							<td valign="top">
            								<h1 style="font-size:22px; font-weight:normal; line-height:22px; margin:0 0 11px 0;">Hello, {{htmlescape var=$request.getCustomerName()}}</h1>
            								<p style="font-size:12px; line-height:16px; margin:0;">
            									Your RMA request #{{var request.increment_id}} has been processed.<br />
            									If you have any questions about your order please contact us at <a href="mailto:{{config path=\"trans_email/ident_support/email\"}}" style="color:#1E7EC8;">{{config path=\"trans_email/ident_support/email\"}}</a> or call us at <span class="nobr">{{config path=\"general/store_information/phone\"}}</span> Monday - Friday, 8am - 5pm PST.
            								</p>
            							</td>
            					</tr>
            					<tr>
            						<td>
            							Your RMA Request #{{var request.increment_id}} <small>(placed on {{var request.getCreatedAtFormated(\long\')}})</small>
            						</td>
            					</tr>
            					<tr>
            						<td>
            							{{layout handle="vesrma_email_request_item" rma_request=$request}}
            						</td>
            					</tr>
            					<tr>
            						<td bgcolor="#EAEAEA" align="center" style="background:#EAEAEA; text-align:center;"><center><p style="font-size:12px; margin:0;">Thank you, <strong>{{var store.getFrontendName()}}</strong></p></center></td>
            					</tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>'
    ),

    array(
        "status"=> 1,
        "title"=> "Vendor",
        "type"=>1,
        "content_template"=>'<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
    <table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
        <tr>
            <td align="center" valign="top" style="padding:20px 0 20px 0">
                <!-- [ header starts here] -->
                <table bgcolor="FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">
                    <tr>
                        <td valign="top">
                            <a href="{{store url=""}}"><img src="{{var logo_url}}" alt="{{var logo_alt}}" style="margin-bottom:10px;" border="0"/></a>
                        </td>
                    </tr>
                    <!-- [ middle starts here] -->
					<tr>
						<td valign="top">
								<h1 style="font-size:22px; font-weight:normal; line-height:22px; margin:0 0 11px 0;">Hello, {{htmlescape var=$vendor.getTitle()}}</h1>
								<p style="font-size:12px; line-height:16px; margin:0;">
									RMA request #{{var request.increment_id}} has been processed.<br />
									If you have any questions about your order please contact us at <a href="mailto:{{config path=\"trans_email/ident_support/email\"}}" style="color:#1E7EC8;">{{config path=\"trans_email/ident_support/email\"}}</a> or call us at <span class="nobr">{{config path=\"general/store_information/phone\"}}</span> Monday - Friday, 8am - 5pm PST.
								</p>
						</td>
					</tr>
					<tr>
						<td>
							 RMA Request #{{var request.increment_id}} <small>(placed on {{var request.getCreatedAtFormated(\"long\")}})</small>
						</td>
					</tr>
					<tr>
						<td>
							{{layout handle="vesrma_email_request_item" rma_request=$request}}
						</td>
					</tr>
					<tr>
						<td bgcolor="#EAEAEA" align="center" style="background:#EAEAEA; text-align:center;"><center><p style="font-size:12px; margin:0;">Thank you, <strong>{{var store.getFrontendName()}}</strong></p></center></td>
					</tr>
                </table>
            </td>
        </tr>
    </table>
</div>'
    ),
);


foreach($defautlTemplate as $template){
    $data = array(
        "status"=>$template["status"],
        "type"=>$template["type"],
        "title"=>$template["title"],
        "content_template"=>$template["content_template"],
    );
    $model = Mage::getModel("vendorsrma/mestemplate")->setData($data)->setId();
    try{
        $model->save();
    }
    catch (Exception $e) {
        throw new Exception($e->getMessage());
    }

}





