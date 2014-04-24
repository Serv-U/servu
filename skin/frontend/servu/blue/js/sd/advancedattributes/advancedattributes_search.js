function ajaxsend() {
    var href = document.location.href;
    var qstring = filters.toString();

    qstring = qstring.replace(/\,/g,'&');
    
    if(qstring != '') {
        qstring += '&';
    }
    
    if (href.indexOf('#') >= 0) {
        href = href.substr(0, href.indexOf('#'));
    }
    
    var filterKeys = Object.keys(pageOptions);
    for (var i = 0; i < filterKeys.length; i++) {
        var matched = href.match(new RegExp(filterKeys[i] + '=[^&]*', 'i')); 

        if(matched) {
            href = href.replace(new RegExp(filterKeys[i] + '=[^&]*', 'i'), filterKeys[i] + '=' + pageOptions[filterKeys[i]]);
        }
    }
    
    showLoading();
    
    $j.ajax({
       url: href,
       type: 'post',
       data: 'advancedattributes=true&'+qstring+pageOptions.convert(),
       dataType: 'json',
       success: function(data) { 
            $j(".col-main").html(data['col_main_content']);
            $j(".block.block-layered-nav").replaceWith(data['filter_content']);

            if (data['pricing_content']) {
                priceConfiguration = data['pricing_content'];
                initializePriceSlider();
            }

            updateUrl();
            hideLoading();
        },
        error: function(error) {
           hideLoading();
        }
      });
}

function updateUrl() {
    var qstring = filters.toString();
    qstring = qstring.replace(/\,/g,'&');

    var href = document.location.href;

    if (href.indexOf('#') >= 0) {
        href = href.substr(0, href.indexOf('#'));
    }

    if(qstring != '') {
        qstring+='&'
    }
    
    href += '#' + qstring+pageOptions.convert();
    document.location.href = href;
}

function showLoading() {
    $j(".attribute-loader").show();
    $j(".attribute-loader-box").show();
}

function hideLoading() {
    $j(".attribute-loader").hide();
    $j(".attribute-loader-box").hide();
}

function pageLoad() {
    var href = document.location.href;
    var originalURL = href;
    
    if(href.indexOf('?')>0){
        var subQuery = '';
        if(href.indexOf('#')>0){
            subQuery = href.substring(href.indexOf('?'),href.indexOf('#'));
        } else {
            subQuery = href.substring(href.indexOf('?'),href.length);
        }
        href = href.replace(subQuery,'');
        var pagerFilters = subQuery.split(/&/);
        console.log(pagerFilters);
    }

    if(href.indexOf('#')>0){
        
        var filterKeys = Object.keys(pageOptions);
        for (var i = 0; i < filterKeys.length; i++) {
            var matched = href.match(new RegExp(filterKeys[i] + '=[^&]*', 'i')); 
            href = href.replace(new RegExp(filterKeys[i] + '=[^&]*', 'i'), '');
            if(matched) {
                console.log(pageOptions);
                pageOptions[filterKeys[i]] = matched[0].replace(filterKeys[i]+'=','');
            }
        }
        
        var hash = href.substr(href.indexOf('#'), href.length);
        var extraFilters = hash.substring(hash.indexOf('#')+1);
        extraFilters = extraFilters.split(/&/);

        for (var x = 0; x < extraFilters.length; x++) {
            if(extraFilters[x] && extraFilters[x] != '' && extraFilters[x] != '?') {
                filters.push(extraFilters[x]);
            }
        }

        if(originalURL.indexOf('#')>0){
            ajaxsend();
        }
    }
}

function priceFilterSearch(array, value) {
    for (var i = 0; i < array.length; i++) {
        if(array[i].indexOf(value) >= 0) {
            return i;
        }
    }
    return -1;
}

function filterChosen(filter) {
    var index = filters.indexOf(filter);
    
    if((filter.indexOf('price[0]') >= 0)) {
        index = priceFilterSearch(filters, 'price[0]');
        if(index >= 0){
            filters[index] = filter;
        }
    }
    else if(filter.indexOf('price[1]') >= 0) {
        index = priceFilterSearch(filters, 'price[1]');
        if(index >= 0){
            filters[index] = filter;
        }
    }   
    
    if(index < 0){
        filters.push(filter);
    }

    ajaxsend();
}

function sorter(subhref) {
    var direction = subhref.match(new RegExp('dir' + '=[^&]*', 'i')); 
    var order = subhref.match(new RegExp('order' + '=[^&]*', 'i'));

    direction = direction[0].substring(direction[0].indexOf('dir=')+4);
    order = order[0].substring(order[0].indexOf('order=')+6);

    pageOptions.dir = direction;
    pageOptions.order = order;
    
    ajaxsend();
}

function removePrices(){
    for(var i = 0; i < filters.length; i++){
        if(filters[i].indexOf('price') >= 0){
            filters.splice(i,1);
            i--;
        }
    }
}

function removeItem(array, item){
    for(var i = 0; i < array.length; i++){
        if(array[i]==item){
            array.splice(i,1);
            i--;
        }
    }
}

function initializePriceSlider() {
    var currentValues = {
        min_price: parseInt($j('#pricing_low_input').val()),
        max_price: parseInt($j('#pricing_high_input').val())
    }
    
    if(priceConfiguration === null){
        priceConfiguration = currentValues;
    } 
    else {
        if(currentValues.min_price < priceConfiguration.min_price){
            $j('#pricing_low_label').text(priceConfiguration.min_price);
        }
        if(currentValues.max_price > priceConfiguration.max_price){
            $j('#pricing_high_label').text(priceConfiguration.max_price);
        }
    }
    
    $j("div span.label:contains('Price:') ~ span.value").text('$'+currentValues.min_price + ' - $' + currentValues.max_price);

    $j('#price-range-slider').slider({
        range: true,
        min: priceConfiguration.min_price,
        max: priceConfiguration.max_price,
        values: [currentValues.min_price, currentValues.max_price],
        step:1,
        slide: function(event, ui) {
            if (ui.values[0] == ui.values[1]) {
                return false;
            }
            
            var index = $j(ui.handle).index();
            var value = Math.floor(ui.value);

            if(index == 1) {
                $j('#pricing_low_label').text(value);
            }
            else {
                $j('#pricing_high_label').text(value);
            }

        },
        stop: function(event, ui) {
            if (ui.values[0] == ui.values[1]) {
                return false;
            }
            
            var index = $j(ui.handle).index();
            
            if(index == 1) {
                var minVal = $j('#price-range-slider').slider('values', 0);

                if(minVal == priceConfiguration.min_price){
                    removeItem(filters, 'price[0]='+currentValues.min_price);
                    ajaxsend();
                }
                else{
                    filterChosen('price[0]='+$j('#pricing_low_label').text());
                }
            }
            else {
                var maxVal = $j('#price-range-slider').slider('values', 1);
                if(maxVal == priceConfiguration.max_price){
                    removeItem(filters, 'price[1]='+currentValues.max_price);
                    ajaxsend();
                }
                else{
                    filterChosen('price[1]='+$j('#pricing_high_label').text());
                }
            }
        }
        
    });
}

$j(document).ready(function() {
    pageLoad();
    initializePriceSlider();
    
    $j('body').on('click', '.checkbox-list li', function(event) {
        $j(this).toggleClass('active');
        
        var filter = $j(this).find('input[type="checkbox"]').val();

        var isActive = false;
        for(var i = 0; i < filters.length; i++){
            if(filters[i]==filter){
                isActive = true;
            }
        }
        
        if(isActive) {
            $j(this).find('input[type="checkbox"]').prop('checked', false);
            removeItem(filters, filter);
            ajaxsend();
       }
        else {
            $j(this).find('input[type="checkbox"]').prop('checked', true);
            filterChosen(filter);
        }
    })

    $j('body').on('click', '.subcat-toggle', function(e) {
        $j(this).toggleClass('active'); 
        $j(this).toggleClass('show hide'); 
        
        $j(this).closest('li').nextAll(".subcat-list").first().slideToggle();
        e.stopPropagation();
        return false;
    });
    
    $j('body').on('click', '.pager .pages li a', function(event) {
        pageOptions.p = this.href.substring(this.href.indexOf('p=')+2);
        ajaxsend();
        event.preventDefault();
    });
    
    $j('body').on('click', '.sorter .view-mode a', function(event) {
        pageOptions.mode = this.href.substring(this.href.indexOf('mode=')+5);
        ajaxsend();
        event.preventDefault();
    });

    $j('body').on('change', '.sd-advanced-dropdown-filter', function(event) {
        var filter = this.value;
        filterChosen(filter);
     });
     
    //$j('body').on('click', '.block-layered-nav .checkbox-list input', function(event) {
        //var filter = this.value;
        //var isActive = false;
        //for(var i = 0; i < filters.length; i++){
            //if(filters[i]==filter){
                //isActive = true;
            //}
        //}
        
        //if(isActive) {
            //removeItem(filters, filter);
            //ajaxsend();
        //}
        //else {
           // filterChosen(filter);
        //}
        
     //});
             
    $j('body').on('change','.sd-advanced-dropdown-sorter', function(event) {
        var subhref = this.value.substring(this.value.indexOf('?')+1);
        sorter(subhref);
    });
    
    $j('body').on('click', '.block-layered-nav .block-content .actions a', function(event) {
        filters = [];
        ajaxsend();
        event.preventDefault();
    });
    
    $j('body').on('click', '.block-layered-nav .currently .btn-remove', function(event) {
        var remove = this.href.substring(this.href.lastIndexOf('?')+1);
        if(remove == 'price'){
            removePrices();
        }
        else {
          removeItem(filters, remove);  
        }
        ajaxsend();
        event.preventDefault();
    });
  
})

var pageOptions = {
    p: 1,
    mode: 'grid',
    dir : 'desc',
    convert: function() { return 'p='+this.p+'&mode='+this.mode+'&dir='+this.dir; },
    reset: function() {this.p=1,this.mode='grid',this.dir='asc'},
};
var priceConfiguration = null;
var pageReplace = null;
var pageNumber = null;
var filters = new Array();
var pageClick = false;