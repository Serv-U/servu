function wpShowMenuPopup(objMenu, popupId)
{
    if (typeof wpCustommenuTimerHide[popupId] != 'undefined') clearTimeout(wpCustommenuTimerHide[popupId]);
    objMenu = $(objMenu.id); var popup = $(popupId); if (!popup) return;
    wpCustommenuTimerShow[popupId] = setTimeout(function() {
        popup.style.display = 'block';
        objMenu.addClassName('active');
        var popupWidth = 0;
        if (CUSTOMMENU_POPUP_WIDTH)
        {
            popup.style.width = CUSTOMMENU_POPUP_WIDTH + 'px';
        }
        else
        {
            // --- get auto width of Popup ---
            var copyPopup = document.createElement('div');
            copyPopup.innerHTML = popup.innerHTML;
            $(copyPopup).setStyle({'float': 'left', 'display': 'none;'});
            var parent = document.getElementsByTagName('BODY')[0];
            parent.appendChild(copyPopup);
            var popupWidth = copyPopup.getWidth();
            parent.removeChild(copyPopup);
            // --- / get auto width of Popup ---
            popup.style.width = popupWidth + 'px';
        }
        var pos = wpPopupPos(objMenu, popup.getHeight());
        popup.style.top = pos.top + 'px';
        popup.style.left = pos.left + 'px';
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
                $(menuId).removeClassName('active');
            }
        }
    }, CUSTOMMENU_POPUP_DELAY_BEFORE_HIDING);
}

function wpPopupOver(element, event, popupId, menuId)
{
    if (typeof wpCustommenuTimerHide[popupId] != 'undefined') clearTimeout(wpCustommenuTimerHide[popupId]);
}

function wpPopupPos(objMenu, h)
{
    var xTop = 0;
    var pos = objMenu.cumulativeOffset();
    var wraper = $('custommenu');
    // ---
    var posWraper = wraper.cumulativeOffset();
    var xLeft = wraper.getWidth();
    var xTop1 = pos.top - posWraper.top;
    var winHeight = wpGetClientHeight();
    var scrollTop = wpGetScrollTop();
    var bottomOffset = 0;
    if (CUSTOMMENU_POPUP_BOTTOM_OFFSET > 0) bottomOffset = CUSTOMMENU_POPUP_BOTTOM_OFFSET;
    var xTop2 = scrollTop + winHeight - (posWraper.top + h) - bottomOffset;
    if ((winHeight-bottomOffset) < h || xTop1 < xTop2)
        xTop = xTop1; // --- beside with item of menu
    else
        xTop = xTop2; // --- pinned to the bottom of the window
    return {'top': xTop, 'left': xLeft};
}

function wpFilterResults (n_win, n_docel, n_body)
{
    var n_result = n_win ? n_win : 0;
    if (n_docel && (!n_result || (n_result > n_docel))) n_result = n_docel;
    return n_body && (!n_result || (n_result > n_body)) ? n_body : n_result;
}

function wpGetClientHeight()
{
    return wpFilterResults (
        window.innerHeight ? window.innerHeight : 0,
        document.documentElement ? document.documentElement.clientHeight : 0,
        document.body ? document.body.clientHeight : 0
    );
}

function wpGetScrollTop()
{
    scrollY = 0;
    if (typeof window.pageYOffset == 'number')
    {
        scrollY = window.pageYOffset;
    }
    else if (document.documentElement && document.documentElement.scrollTop)
    {
        scrollY = document.documentElement.scrollTop;
    }
    else if (document.body && document.body.scrollTop)
    {
        scrollY = document.body.scrollTop;
    }
    else if (window.scrollY)
    {
        scrollY = window.scrollY;
    }
    return scrollY;
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
