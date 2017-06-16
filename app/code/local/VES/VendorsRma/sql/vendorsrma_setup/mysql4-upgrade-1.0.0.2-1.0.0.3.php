<?php


$defautlStatus = array(
    array(
        "title"=>"Pending Approval",
        "store_id"=>0,
        "resolve"=>0,
        "sort_order"=>0,
        "type"=>0,
        "is_delete"=>1,
        "active"=>VES_VendorsRma_Model_Option_Status::STATUS_ENABLED,
        "template_notify_customer"=>"<p>RMA #{{var request.getIncrementId()}} successfully created.</p>",
        "template_notify_vendor"=>'<p>A new RMA #{{var request.getIncrementId()}} is initiated by {{var request.getCustomerName()}} <{{var request.getCustomerEmail()}}> for order <a href="{{var request.getNotifyOrderVendorLink()}}">#{{var request.getOrderIncrementalId()}}</a>.</p>
                    <p>Date: {{var request.getFormattedCreatedAt()}}<br />
                    Request Type:  {{var request.getRequestTypeName()}}<br />
                    Request State:  {{var request.getRequestStateName()}}<br />
                    Package Opened: {{var request.getPackageOpenedLabel()}}</p>
                    <p>Items<br />
                    {{layout handle="vesrma_email_request_item" rma_request=$request}}</p>',
        "template_notify_history"=>"<p>Your RMA has been placed and waiting for approval.</p>",
        "template_notify_admin"=>null,
    ),

    array(
        "title"=>"Approval",
        "store_id"=>0,
        "resolve"=>0,
        "type"=>0,
        "sort_order"=>1,
        "is_delete"=>1,
        "active"=>VES_VendorsRma_Model_Option_Status::STATUS_ENABLED,
        "template_notify_customer"=>'<p>Your RMA #{{var request.getIncrementId()}} has been approved.</p>
                    {{depend request.getNotifyPrintlabelAllowed()}}<p>You can print a "Return Shipping Authorization" label with return address and other information by pressing link below. A "Return Shipping Authorization" label must be enclosed inside your package; you may want to keep a copy of "Return Shipping Authorization" label for your records.</p>
                    {{/depend}}
                    <p>Please send your package to:</p>
                    <p>{{var request.getNotifyRmaAddress()}}</p>
                    {{depend request.getConfirmShippingIsRequired()}}<p>and press "Confirm Sending" button after.</p>{{/depend}}}',
        "template_notify_vendor"=>null,
        "template_notify_history"=>'Your RMA #{{var request.getIncrementId()}} has been approved.
                    {{depend request.getNotifyPrintlabelAllowed()}}You can print a RMA label with return address and other information by clicking the following link:
                    {{var request.getPrintLabelUrl()}}
                    The RMA label must be enclosed inside your package; you may want to keep a copy of the label for your records.{{/depend}}
                    Please send your package to:
                    {{var request.getNotifyRmaAddress()}}{{depend request.getConfirmShippingIsRequired()}}
                    and click the "Confirm Sending" button after.{{/depend}}',
        "template_notify_admin"=>null,
    ),

    array(
        "title"=>"Package sent (Customer)",
        "store_id"=>0,
        "resolve"=>0,
        "type"=>3,
        "sort_order"=>2,
        "is_delete"=>1,
        "active"=>VES_VendorsRma_Model_Option_Status::STATUS_ENABLED,
        "template_notify_customer"=>null,
        "template_notify_vendor"=>'
                    <p>RMA #{{var request.getIncrementId()}} status changed to {{var request.getStatusName()}}</p>
                    <h3>RMA details</h3>
                    <p>ID: #{{var request.getIncrementId()}}<br />
                    Order ID: #<a href="{{var request.getNotifyOrderVendorLink()}}">{{var request.getOrderIncrementalId()}}</a>.<br />
                    Customer: {{var request.getCustomerName()}} <{{var request.getCustomerEmail()}}><br />
                    Date: {{var request.getFormattedCreatedAt()}}<br />
                    Request Type: {{var request.getRequestTypeName()}}<br />
                    Request State:  {{var request.getRequestStateName()}}<br />
                    Package Opened: {{var request.getPackageOpenedLabel()}}</p>
                    <p>Items<br />
                    {{layout handle="vesrma_email_request_item" rma_request=$request}}</p>',
        "template_notify_history"=>null,
        "template_notify_admin"=>null,
    ),


    array(
        "title"=> "Canceled",
        "store_id"=>0,
        "resolve"=>1,
        "type"=>3,
        "sort_order"=>3,
        "is_delete"=>1,
        "active"=>VES_VendorsRma_Model_Option_Status::STATUS_ENABLED,
        "template_notify_customer"=>'Your RMA #{{var request.getIncrementId()}} has been successfully resolved with status "{{var request.getStatusName()}}".',
        "template_notify_vendor"=>'RMA #{{var request.getIncrementId()}} has been canceled by customer
                        <h3>RMA details</h3>
                        <p>ID: #{{var request.getIncrementId()}}<br />
                        Order ID: #<a href="{{var request.getNotifyOrderVendorLink()}}">{{var request.getOrderIncrementalId()}}</a>.<br />
                        Customer: {{var request.getCustomerName()}} <{{var request.getCustomerEmail()}}><br />
                        Date: {{var request.getFormattedCreatedAt()}}<br />
                        Request Type: {{var request.getRequestTypeName()}}<br />
                        Request State:  {{var request.getRequestStateName()}}<br />
                        Package Opened: {{var request.getPackageOpenedLabel()}}</p>
                        <p>Items<br />
                        {{layout handle="vesrma_email_request_item" rma_request=$request}}</p>',
        "template_notify_history"=>'RMA #{{var request.getIncrementId()}} has been successfully resolved with status "{{var request.getStatusName()}}".',
        "template_notify_admin"=>null,
    ),
    
    array(
        "title"=>"Resolved",
        "store_id"=>0,
        "resolve"=>1,
        "type"=>1,
        "sort_order"=>5,
        "is_delete"=>1,
        "active"=>VES_VendorsRma_Model_Option_Status::STATUS_ENABLED,
        "template_notify_customer"=>'Your RMA #{{var request.getIncrementId()}} status changed to "{{var request.getStatusName()}}".',
        "template_notify_vendor"=>'
                    <p>RMA #{{var request.getIncrementId()}} status changed to {{var request.getStatusName()}}</p>
                    <h3>RMA details</h3>
                    <p>ID: #{{var request.getIncrementId()}}<br />
                    Order ID: #<a href="{{var request.getNotifyOrderVendorLink()}}">{{var request.getOrderIncrementalId()}}</a>.<br />
                    Customer: {{var request.getCustomerName()}} <{{var request.getCustomerEmail()}}><br />
                    Date: {{var request.getFormattedCreatedAt()}}<br />
                    Request Type: {{var request.getRequestTypeName()}}<br />
                    Request State:  {{var request.getRequestStateName()}}<br />
                    Package Opened: {{var request.getPackageOpenedLabel()}}</p>
                    <p>Items<br />
                    {{layout handle="vesrma_email_request_item" rma_request=$request}}</p>',
        "template_notify_history"=>null,
        "template_notify_admin"=>null,
    ),

    array(
        "title"=>"Approval (admin)",
        "store_id"=>0,
        "resolve"=>1,
        "type"=>1,
        "sort_order"=>5,
        "is_delete"=>1,
        "active"=>VES_VendorsRma_Model_Option_Status::STATUS_ENABLED,
        "template_notify_customer"=>'Your RMA #{{var request.getIncrementId()}} status changed to "{{var request.getStatusName()}}".',
        "template_notify_vendor"=>'
                    <p>RMA #{{var request.getIncrementId()}} status changed to {{var request.getStatusName()}}</p>
                    <h3>RMA details</h3>
                    <p>ID: #{{var request.getIncrementId()}}<br />
                    Order ID: #<a href="{{var request.getNotifyOrderVendorLink()}}">{{var request.getOrderIncrementalId()}}</a>.<br />
                    Customer: {{var request.getCustomerName()}} <{{var request.getCustomerEmail()}}><br />
                    Date: {{var request.getFormattedCreatedAt()}}<br />
                    Request Type: {{var request.getRequestTypeName()}}<br />
                    Request State:  {{var request.getRequestStateName()}}<br />
                    Package Opened: {{var request.getPackageOpenedLabel()}}</p>
                    <p>Items<br />
                    {{layout handle="vesrma_email_request_item" rma_request=$request}}</p>',
        "template_notify_history"=>null,
        "template_notify_admin"=>'
                    <p>RMA #{{var request.getIncrementId()}} status changed to {{var request.getStatusName()}}</p>
                    <h3>RMA details</h3>
                    <p>ID: #{{var request.getIncrementId()}}<br />
                    Order ID: #<a href="{{var request.getNotifyOrderAdminLink()}}">{{var request.getOrderIncrementalId()}}</a>.<br />
                    Customer: {{var request.getCustomerName()}} <{{var request.getCustomerEmail()}}><br />
                    Date: {{var request.getFormattedCreatedAt()}}<br />
                    Request Type: {{var request.getRequestTypeName()}}<br />
                    Request State:  {{var request.getRequestStateName()}}<br />
                    Package Opened: {{var request.getPackageOpenedLabel()}}</p>
                    <p>Items<br />
                    {{layout handle="vesrma_email_request_item" rma_request=$request}}</p>',
    ),

    array(
        "title"=>"Package sent (Vendor)",
        "store_id"=>0,
        "resolve"=>0,
        "type"=>2,
        "sort_order"=>2,
        "is_delete"=>1,
        "active"=>VES_VendorsRma_Model_Option_Status::STATUS_ENABLED,
        "template_notify_customer"=>'Your RMA #{{var request.getIncrementId()}} status changed to {{var request.getStatusName()}}',
        "template_notify_vendor"=>'
                    <p>RMA #{{var request.getIncrementId()}} status changed to {{var request.getStatusName()}}</p>
                    <h3>RMA details</h3>
                    <p>ID: #{{var request.getIncrementId()}}<br />
                    Order ID: #<a href="{{var request.getNotifyOrderVendorLink()}}">{{var request.getOrderIncrementalId()}}</a>.<br />
                    Customer: {{var request.getCustomerName()}} <{{var request.getCustomerEmail()}}><br />
                    Date: {{var request.getFormattedCreatedAt()}}<br />
                    Request Type: {{var request.getRequestTypeName()}}<br />
                    Request State:  {{var request.getRequestStateName()}}<br />
                    Package Opened: {{var request.getPackageOpenedLabel()}}</p>
                    <p>Items<br />
                    {{layout handle="vesrma_email_request_item" rma_request=$request}}</p>',
        "template_notify_history"=>null,
        "template_notify_admin"=>null,
    ),

);


foreach($defautlStatus as $status){
    $data = array(
        "title"=>$status["title"],
        "store_id"=>$status["store_id"],
        "resolve"=>$status["resolve"],
        "type"=>$status["type"],
        "active"=>$status["active"],
        "sort_order"=>$status["sort_order"],
        "is_delete"=>$status["is_delete"],
    );
    $model = Mage::getModel("vendorsrma/status")->setData($data)->setId();
    try{
        $model->save();
        $temData = array(
            "template_notify_customer"=>$status["template_notify_customer"],
            "template_notify_vendor"=>$status["template_notify_vendor"],
            "template_notify_history"=>$status["template_notify_history"],
            "template_notify_admin"=>$status["template_notify_admin"],
            "store_id"=>$status["store_id"],
            "title"=>$status["title"],
            "status_id"=>$model->getId(),
        );
        $tem = Mage::getModel("vendorsrma/template")->setData($temData)->setId();
        try{
            $tem->save();
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    catch (Exception $e) {
        throw new Exception($e->getMessage());
    }

}


$defautlType = array(
    array(
        "title"=>"Refund",
        "store_id"=>0,
        "sort_order"=>0,
        "active"=>VES_VendorsRma_Model_Option_Status::STATUS_ENABLED,
        "is_delete"=> 1,
    ),
    array(
        "title"=>"Replace",
        "store_id"=>0,
        "sort_order"=>1,
        "active"=>VES_VendorsRma_Model_Option_Status::STATUS_ENABLED,
        "is_delete"=> 1,
    ),
);


foreach($defautlType as $type){
    $data = array(
        "title"=>$type["title"],
        "store_id"=>$type["store_id"],
        "active"=>$type["active"],
        "sort_order"=>$type["sort_order"],
		"is_delete"=>$type["is_delete"],
    );
    $model = Mage::getModel("vendorsrma/type")->setData($data)->setId();
    try{
        $model->save();
    }
    catch (Exception $e) {
        throw new Exception($e->getMessage());
    }

}



$defautlReason = array(
    array(
        "title"=>"Metal Detectors",
        "store_id"=>0,
        "sort_order"=>0,
        "active"=>VES_VendorsRma_Model_Option_Status::STATUS_ENABLED,
    ),
    array(
        "title"=>"It's the wrong type",
        "store_id"=>0,
        "sort_order"=>1,
        "active"=>VES_VendorsRma_Model_Option_Status::STATUS_ENABLED,
    ),
    array(
        "title"=>"It's broken",
        "store_id"=>0,
        "sort_order"=>2,
        "active"=>VES_VendorsRma_Model_Option_Status::STATUS_ENABLED,
    ),
    array(
        "title"=>"Faulty",
        "store_id"=>0,
        "sort_order"=>3,
        "active"=>VES_VendorsRma_Model_Option_Status::STATUS_ENABLED,
    ),
    array(
        "title"=>"I've changed my mind",
        "store_id"=>0,
        "sort_order"=>4,
        "active"=>VES_VendorsRma_Model_Option_Status::STATUS_ENABLED,
    ),
);


foreach($defautlReason as $reason){
    $data = array(
        "title"=>$reason["title"],
        "store_id"=>$reason["store_id"],
        "active"=>$reason["active"],
        "sort_order"=>$reason["sort_order"],
    );
    $model = Mage::getModel("vendorsrma/reason")->setData($data)->setId();
    try{
        $model->save();
    }
    catch (Exception $e) {
        throw new Exception($e->getMessage());
    }

}