   var $j = jQuery.noConflict();
        
    $j(document).ready(function() {
        //Show module
        $j('#quick-add-cart').css('display','block');
        
        //Bind form submit to Button
        $j('#quickaddsubmit').bind('click', function(){
            $j('#quickCartForm').submit();
        });
        
        //Bind form submit to Enter Key
        $j('#quickCartForm input').keypress(function(e) {
            var code = e.which;
            if (code == 13 && $j('#quickaddsubmit').css('display') != 'none') {
                $j('#quickaddsubmit').focus();
                $j('#quickCartForm').submit();
            }
        });
        
        //Handle form submission
        $j('#quickCartForm').submit(function() {
            //Clear form's error message
            $j('#qc-error-msg').html("");
            
            //Check for empty values...
            var empty_form = true;
            $j(".quickcartsku").each(function(){
                //Find fields with values
                if(!$j(this).val() == ""){
                    empty_form = false;
                    
                    //Strip all characters from the end of config skus
                    var strSku = $j(this).val()
                    $j.each(["#", "*", "^", "="], function(index, value) {
                        if(strSku.indexOf(value) != -1){
                            clearQCHtml('#' + $j(this).attr('id') + 'msg');
                            strSku = strSku.substr(0, strSku.indexOf(value));
                        }
                    });
                    $j(this).val(strSku);
                }
                
                //Clear previous messages if field is empty
                if($j(this).val() == ""){
                    clearQCHtml('#' + $j(this).attr('id') + 'msg');
                    //$j("#" + $j(this).attr('id') + "msg").html('');
                }
                
                //Clear messages and options if sku is changed
                $j(this).unbind('change');
                $j(this).bind('change', function(e){
                    clearQCHtml('#' + $j(this).attr('id') + 'msg');
                });
            });
            
            //Return error if form is empty
            if(empty_form === true){
                $j('#qc-error-msg').html("Please enter a SKU.");
                return false;
            }
            
            //Disable submit button to prevent duplicate submissions
            showLoadingGraphic();

            //Get form values
            var quickcartdata = {};
            var number_of_fields = 0;
            
            $j(".quickcartsku").each(function(){
                quickcartdata[this.id] = $j("#" + this.id).val();
                number_of_fields++;
            });
            quickcartdata.number_of_fields = number_of_fields
            
            //Get user's selected options for configurable products
            var missing_options = false;
            if ($j(".quick-cart-select")[0]){
                $j(".quick-cart-select").each(function(){
                    //Verify all options selected for each item
                    if(!$j("#" + this.id).val()){
                        showQCHtml('#qc-error-msg', 'Please select all options');
                        missing_options = true;
                        return false;
                    }
                    
                    //Get sku and container div (ie: quickaddsku1)
                    var sku_container = $j(this).parent('div').parent('div').attr('id');
                    sku_container = sku_container.replace(/msg+$/, "");
                    //var sku = $j('#' + sku_container).val();

                    //Get selected options
                    var attribute_id = this.id;
                    attribute_id = attribute_id.replace(sku_container + '_','');
                    var selected_option = $j("#" + this.id).val();

                    if(quickcartdata[sku_container + 'options']){
                        quickcartdata[sku_container + 'options'] += ";" + attribute_id + ":"+ selected_option;
                    }
                    else{
                        quickcartdata[sku_container + 'options'] = attribute_id + ":"+ selected_option;
                    }
                    return true;
                });
            }
            
            //Prevent form submission if options are missing
            if(missing_options === true){
                enableQCSubmit();
                return false;
            }
            
            callCartAjax(quickcartdata);
            return false;
        });

    });
    
    function resetForm(){
        $j(".quickcartsku").each(function(){
            $j("#" + this.id).val('');
            $j("#" + this.id + "msg").html('');
            $j("#" + this.id).removeClass('itemadded');
        });

        enableQCSubmit();
        $j('#qc-error-msg').html("");
    }

    function showLoadingGraphic(){
        $j("#quickaddsubmit").attr("disabled", "disabled");
        $j("#quickaddsubmit").css("display", "none");
        $j("#quick-add-links").css("display", "none");
        $j("#quickaddloading").css('display','block');
    }
    
    function enableQCSubmit(){
        $j("#quickaddloading").css('display','none');
        $j("#quickaddsubmit").removeAttr('disabled');
        $j("#quick-add-links").css("display", "block");
        $j("#quickaddsubmit").css("display", "block");
    }
    
    function addSelectChange(){ 
        $j('.quick-cart-select').unbind('change');
        $j('.quick-cart-select').bind('change', function(){
            var dropdownid = $j(this).attr('id');
            callNextAttrAjax(dropdownid);
        });
    }
    
    function addPartialMatches(sku, field_id){
        $j('#'+field_id).val(sku);
        $j('#'+field_id+"msg").html('');
    }
    
    function clearQCHtml(key){
        $j(key).hide('slow');
        $j(key).html('');
    }
    
    function showQCHtml(key, message){
        $j(key).html(message);
        $j(key).show();
//        $j(key).show('slow');
    }
    
    function quickCartAjax(quickcartdata, ajaxurl){
        $j.ajax({
            type: "POST",
            dataType: "json",
            url: ajaxurl,
            data: quickcartdata,
            success: function(data){
                if(data.msg == "success"){
                    $j('#QCCartSummary').html(data.cartQty + " in Cart: <span style='float: right; font-weight: bold;'>" + data.cartPrice + "</span>");
                    
                    //Update Cart quantity in Header
                    $j('.quick-access .top-link-cart').html('My Cart (' + data.cartQty + ')');
                    
                    //Update information in Sidebar Cart
                    $j('.block-cart').html(data.sidebar_cart);
/*                    
                    //Rebuild Cart if user is on Shopping Cart page
                    if(data.cart_page_body != ''){
                        //$j('#shopping-cart-table').html(data.in_shopping_cart);
                        $j('.col-main').html(data.cart_page_body);
                        $j('.totals').prepend(data.cart_page_totals);
                        $j('.checkout-types').html(data.checkout_types);
                        $j('.shipping-coupon').html(data.shipping_coupon);
                    }
*/
                    for (var field_id in quickcartdata) {
                        if (quickcartdata.hasOwnProperty(field_id) && quickcartdata[field_id]) {
                            if(data[field_id + 'options']){
                                showQCHtml('#' + field_id + 'msg', data[field_id + 'msg'] + "<br/>" + data[field_id + 'options']);
                            }
                            else{
                                switch(data[field_id + 'msg']){
                                    case 'notfound':
                                        showQCHtml('#' + field_id + 'msg', '<div class="error">Unable to find SKU</div>');
                                        break;
                                    case 'itemadded':
                                        //Clear sku from field
                                        $j("#" + field_id).val('');
                                        
                                        //Check if required minimum applied
                                        var QCMinimum = '';
                                        if(data[field_id + 'minimum'] == true){
                                            QCMinimum = ('* <br/><em>*Minimum Required Quanity</em><br/>');
                                        }
                                        
                                        //Display saved message
                                        showQCHtml('#' + field_id + 'msg', '<div class="success">' + data[field_id + 'sku'] + ' added to Cart' + QCMinimum + '</div>');
                                        break;
                                    default:
                                        showQCHtml('#' + field_id + 'msg', data[field_id + 'msg']);
                                }
                            }
                            $j('#' + field_id).focus();
                        }
                    }
                }
                else{
                    $j('#qc-error-msg').html('Unable to add SKUs');
                }

                $j("#quickaddloading").css('display','none');
                
                enableQCSubmit();
                
                //Bind onchange event to config dropdowns
                addSelectChange();    
            },
            error: function(e){
                //console.log(e);
                //console.log(e.status);
                $j('#qc-error-msg').html('Unable to add item(s) to cart');
                enableQCSubmit();
            }
        });
    }
    
    function nextConfigAttr(select_id, ajaxurl){
        //Clear form's error message
        $j('#qc-error-msg').html("");

        //Remove all dropdowns after changed dropdown
        $j('#' + select_id).nextAll().remove();
        
        //Do not run if THIS dropdown was changed to select option
        if($j('#' + select_id).val() == ''){
            return false;
        }
        
        //Disable select fields to prevent duplicate requests
        $j('#' + select_id).prevAll().prop('disabled', true);
        $j('#' + select_id).prop('disabled', true);

        //Disable Submit button
        showLoadingGraphic();
        
        //Get field values
        var field_id = select_id.split("_");
        field_id = field_id[0];
        //var selected_value = $j('#' + select_id).val();
        
        var attributedata = {};
        attributedata.sku = $j('#' + field_id).val();
        attributedata.field_id = field_id;
        attributedata.select_id = select_id;
        
        $j("#" + field_id + 'msg .quick-cart-select').each( function() {
            if($j(this).val()){
                attributedata[$j(this).attr('id')] = $j(this).val();
            }
        });
        
        //Send Request
        $j.ajax({
            type: "POST",
            dataType: "json",
            url: ajaxurl,
            data: attributedata,
            success: function(data){
                //Append new select field
                $j("#" + attributedata.field_id + "optioncontainer").append(data.next_attribute);
                
                //Re-enable select fields
                $j('#' + attributedata.select_id).prevAll().prop('disabled', false);
                $j('#' + attributedata.select_id).prop('disabled', false);  
                
                //Enable only if options are not disabled
                //This prevents submitting while options are being retrieved
                if($j(".quick-cart-select").is(":disabled") == false){
                    //Bind onchange event to config dropdowns
                    addSelectChange();
                    //Show submit button
                    enableQCSubmit();
                }
            },
            error: function(e){
//                $j('#qc-error-msg').html('Unable to get options');
                showQCHtml('#qc-error-msg', 'Unable to get options');

                //Re-enable select fields and submit button
                $j('#' + select_id).prevAll().prop('disabled', false);
                $j('#' + select_id).prop('disabled', false);
                enableQCSubmit();
            }
        });           
    }