function wpShowMenuPopup(objMenu, popupId, catId, first)
{
    if (typeof wpCustommenuTimerHide[popupId] != 'undefined') clearTimeout(wpCustommenuTimerHide[popupId]);
    objMenu = $(objMenu.id); var popup = $(popupId); if (!popup) return;
    wpCustommenuTimerShow[popupId] = setTimeout(function() {
        popup.style.display = 'block';
        objMenu.addClassName('active');
        var popupWidth = CUSTOMMENU_POPUP_WIDTH;
        if (!popupWidth) popupWidth = popup.getWidth() + 10;
        var pos = wpPopupPos(objMenu, popupWidth);

        //Position Menu Arrow
        var menuId = "menu" + catId;
        var menuOffset  = getOffset(document.getElementById('custommenu')).left;
        var categoryWidth = $(menuId).getWidth();
        var categoryLeft = getOffset(document.getElementById(menuId)).left;
        var categoryMiddle = (categoryWidth / 2) + categoryLeft - menuOffset - 10;
        $('menu_arrow').style.left = categoryMiddle + 'px';
        $('menu_arrow').style.top = pos.top + 2 + 'px';
        document.getElementById('menu_arrow').style.display = 'block'; 
        popup.style.top = pos.top + 'px';
        //Offset popup for first category
        if (first == true){
            pos.left -= 10;
        }
        
        popup.style.left = pos.left + 'px';
        wpSetPopupZIndex(popup);
        if (CUSTOMMENU_POPUP_WIDTH) popup.style.width = CUSTOMMENU_POPUP_WIDTH + 'px';
    }, CUSTOMMENU_POPUP_DELAY_BEFORE_DISPLAYING);
}
        
function wpHideMenuPopup(element, event, popupId, menuId)
{
    if (typeof wpCustommenuTimerShow[popupId] != 'undefined') clearTimeout(wpCustommenuTimerShow[popupId]);
    element = $(element.id); var popup = $(popupId); if (!popup) return;
    var current_mouse_target = null;
    if (event.toElement)
    {
        current_mouse_target = event.toElement;
    }
    else if (event.relatedTarget)
    {
        current_mouse_target = event.relatedTarget;
    }
    wpCustommenuTimerHide[popupId] = setTimeout(function() {
        if (!wpIsChildOf(element, current_mouse_target) && element != current_mouse_target)
        {
            if (!wpIsChildOf(popup, current_mouse_target) && popup != current_mouse_target)
            {
                popup.style.display = 'none';
                document.getElementById('menu_arrow').style.display = 'none'; 
                $(menuId).removeClassName('active');
            }
        }
    }, CUSTOMMENU_POPUP_DELAY_BEFORE_HIDING);
}

function wpPopupOver(element, event, popupId, menuId)
{
    if (typeof wpCustommenuTimerHide[popupId] != 'undefined') clearTimeout(wpCustommenuTimerHide[popupId]);
}

function wpPopupPos(objMenu, w)
{
    var pos = objMenu.cumulativeOffset();
    var wraper = $('custommenu');
    var posWraper = wraper.cumulativeOffset();
    var wWraper = wraper.getWidth() - CUSTOMMENU_POPUP_RIGHT_OFFSET_MIN;
    var xTop = pos.top - posWraper.top + CUSTOMMENU_POPUP_TOP_OFFSET;
    var xLeft = pos.left - posWraper.left;
    if ((xLeft + w) > wWraper) xLeft = wWraper - w;
    return {'top': xTop, 'left': xLeft};
}

function wpIsChildOf(parent, child)
{
    if (child != null)
    {
        while (child.parentNode)
        {
            if ((child = child.parentNode) == parent)
            {
                return true;
            }
        }
    }
    return false;
}

function wpSetPopupZIndex(popup)
{
    $$('.wp-custom-menu-popup').each(function(item){
       item.style.zIndex = '9999';
    });
    popup.style.zIndex = '10000';
}

function getOffset( obj ) {
    var x = 0;
    var y = 0;
    while( obj && !isNaN( obj.offsetLeft ) && !isNaN( obj.offsetTop ) ) {
        x += obj.offsetLeft - obj.scrollLeft;
        y += obj.offsetTop - obj.scrollTop;
        obj = obj.offsetParent;
    }
    return { top: y, left: x };
}  