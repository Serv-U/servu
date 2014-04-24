<?php

class WP_CustomMenu_Block_Navigation extends Mage_Catalog_Block_Navigation
{
    const CUSTOM_BLOCK_TEMPLATE = 'wp_custom_menu_%d';

    public function showHomeLink()
    {
        return Mage::getStoreConfig('custom_menu/general/show_home_link');
    }

    public function drawCustomMenuItem($category, $level = 0, $last = false, $first = true)
    {
        if (!$category->getIsActive()) return '';

        $html = array();

        $id = $category->getId();
        // --- Static Block ---
        //Only use one static block. Change $sb to $id to enable different blocks for each category
        $sb = 999;
        $blockId = sprintf(self::CUSTOM_BLOCK_TEMPLATE, $sb); // --- static block key
        $blockHtml = $this->getLayout()->createBlock('cms/block')->setBlockId($blockId)->toHtml();
        // --- Sub Categories ---
        $activeChildren = $this->getActiveChildren($category, $level);
        // --- class for active category ---
        $active = ''; if ($this->isCategoryActive($category)) $active = ' act';
        // --- Popup functions for show ---
        $drawPopup = ($blockHtml || count($activeChildren));
        if ($drawPopup)
        {
            $html[] = '<div id="menu' . $id . '" class="menu' . $active . '" onmouseover="wpShowMenuPopup(this, \'popup' . $id . '\', '.$id.', '.json_encode($first).');" onmouseout="wpHideMenuPopup(this, event, \'popup' . $id . '\', \'menu' . $id . '\')">';
        }
        else
        {
            $html[] = '<div id="menu' . $id . '" class="menu' . $active . '">';
        }
        // --- Top Menu Item ---
        $html[] = '<div class="parentMenu">';
        $html[] = '<a href="'.$this->getCategoryUrl($category).'">';
        $name = $this->escapeHtml($category->getName());
        if (Mage::getStoreConfig('custom_menu/general/non_breaking_space'))
            $name = str_replace(' ', '&nbsp;', $name);
        if ($last != true){
            $html[] = '<span>' . $name . '</span> | ';
        }
        else{
            $html[] = '<span>' . $name . '</span>';
        }
        $html[] = '</a>';
        $html[] = '</div>';
        $html[] = '</div>';
        // --- Add Popup block (hidden) ---
        if ($drawPopup)
        {
            // --- Popup function for hide ---
            $html[] = '<div id="popup' . $id . '" class="wp-custom-menu-popup" onmouseout="wpHideMenuPopup(this, event, \'popup' . $id . '\', \'menu' . $id . '\')" onmouseover="wpPopupOver(this, event, \'popup' . $id . '\', \'menu' . $id . '\')">';
            // --- draw Sub Categories ---
            if (count($activeChildren))
            {
                $html[] = '<div class="block1">';
                $html[] = $this->drawColumns($activeChildren, $id, $blockHtml);
                $html[] = '<div class="clearBoth"></div>';
                $html[] = '</div>';
            }
/*ORIGINAL CUSTOM MENU CODE FOR DISPLAYING STATIC BLOCK            
            // --- draw Custom User Block ---
            if ($blockHtml)
            {
                $html[] = '<div class="block2">';
                $html[] = $blockHtml;
                $html[] = '</div>';
            }
*/
          $html[] = '</div>';
         }

        $html = implode("\n", $html);
        return $html;
    }

    public function drawColumns($children, $category_id, $blockHtml)
    {
        $html = '';
        // --- explode by columns ---
        /*Default Custom Menu Options
        $columns = (int)Mage::getStoreConfig('custom_menu/columns/count');
        if ($columns < 1) $columns = 1;
        */
        $max_items_per_column = (int)Mage::getStoreConfig('custom_menu/popup/height');
        if ($max_items_per_column < 1) $max_items_per_column = 1;
        $chunks = $this->explodeByColumns($children, $max_items_per_column);
        // --- draw columns ---
        $lastColumnNumber = count($chunks);
        $i = 1;
        foreach ($chunks as $key => $value)
        {
            if (!count($value)) continue;
            $class = '';
            if ($i == 1) $class.= ' first';
            if ($i == $lastColumnNumber) $class.= ' last';
            if ($i % 2) $class.= ' odd'; else $class.= ' even';
            $html.= '<div class="column' . $class . '">';
            $html.= $this->drawMenuItem($value, 1);
            //Clearance reimplementation to reduce server load and improve usability. See drawItem for further comments
            //if ($i == $lastColumnNumber && $blockHtml) $html .= '<div class="clearance"><a href="'.Mage::getBaseUrl().'product-catalog#cat[]='.$category_id.'&amp;other_filterable_attributes[]=5171">'.$blockHtml.'</a></div>';
            $html.= '</div>';
            $i++;
        }
        return $html;
    }

    protected function getActiveChildren($parent, $level)
    {
        $activeChildren = array();
        // --- check level ---
        $maxLevel = (int)Mage::getStoreConfig('custom_menu/general/max_level');
        if ($maxLevel > 0)
        {
            if ($level >= ($maxLevel - 1)) return $activeChildren;
        }
        // --- / check level ---
        if (Mage::helper('catalog/category_flat')->isEnabled())
        {
            $children = $parent->getChildrenNodes();
            $childrenCount = count($children);
        }
        else
        {
            $children = $parent->getChildren();
            $childrenCount = $children->count();
        }
        $hasChildren = $children && $childrenCount;
        if ($hasChildren)
        {
            foreach ($children as $child)
            {
                if ($child->getIsActive())
                {
                    array_push($activeChildren, $child);
                }
            }
        }
        return $activeChildren;
    }

    private function explodeByColumns($target, $max_items_per_column)
    {
        $original_target = $target;
        $count = count($target);
        if ($count) $target = array_chunk($target, $count);
        $this->_restrictHeight($target, $max_items_per_column);

        if(count($target) > 5){
            $target = $this->explodeByColumns($original_target, $max_items_per_column + 1 );
        }

        return $target;
/* ORIGINAL PLUGIN CODE TO GENERATE AND COMBINE COLUMNS
        $count = count($target);
        if ($count) $target = array_chunk($target, ceil($count / $num));
        $target = array_pad($target, $num, array());
        #return $target;
        if ((int)Mage::getStoreConfig('custom_menu/columns/integrate') && count($target))
        {
            // --- combine consistently numerically small column ---
            // --- 1. calc length of each column ---
            $max = 0; $columnsLength = array();
            foreach ($target as $key => $child)
            {
                $count = 0;
                $this->_countChild($child, 1, $count);
                if ($max < $count) $max = $count;
                $columnsLength[$key] = $count;
            }
            // --- 2. merge small columns with next ---
            $xColumns = array(); $column = array(); $cnt = 0;
            $xColumnsLength = array(); $k = 0;
            foreach ($columnsLength as $key => $count)
            {
                $cnt+= $count;
                if ($cnt > $max && count($column))
                {
                    $xColumns[$k] = $column;
                    $xColumnsLength[$k] = $cnt - $count;
                    $k++; $column = array(); $cnt = $count;
                }
                $column = array_merge($column, $target[$key]);
            }
            $xColumns[$k] = $column;
            $xColumnsLength[$k] = $cnt - $count;
            // --- 3. integrate columns of one element ---
            $target = $xColumns; $xColumns = array(); $nextKey = -1;
            if ($max > 1 && count($target) > 1)
            {
                foreach($target as $key => $column)
                {
                    if ($key == $nextKey) continue;
                    if ($xColumnsLength[$key] == 1)
                    {
                        // --- merge with next column ---
                        $nextKey = $key + 1;
                        if (isset($target[$nextKey]) && count($target[$nextKey]))
                        {
                            $xColumns[] = array_merge($column, $target[$nextKey]);
                            continue;
                        }
                    }
                    $xColumns[] = $column;
                }
                $target = $xColumns;
            }
        }
        return $target;
*/
    }
    
    private function _restrictHeight(&$target, $max_items_per_column)
    {
        //move menu items and their subitems to next column if over max
        foreach ($target as $key => $child)
        {
            $count = 0;
            $this->_countChild($child, 1, $count);
            if($count > $max_items_per_column && count($target[$key]) > 1){
                if(empty($target[$key+1])){
                  $target[$key+1] = array();
                }
                array_unshift($target[$key+1],$target[$key][count($child)-1]);

                unset($target[$key][count($child)-1]);
                $this->_restrictHeight($target, $max_items_per_column);
            }
        }
    }
    
    private function _countChild($children, $level, &$count)
    {
        foreach ($children as $child)
        {
            if ($child->getIsActive())
            {
                $count++; $activeChildren = $this->getActiveChildren($child, $level);
                if (count($activeChildren) > 0) $this->_countChild($activeChildren, $level + 1, $count);
            }
        }
    }

    public function drawMenuItem($children, $level = 1)
    {
        $html = '<div class="itemMenu level' . $level . '">';
        $keyCurrent = $this->getCurrentCategory()->getId();
        foreach ($children as $child)
        {
            if ($child->getIsActive())
            {
                // --- class for active category ---
                $active = '';
                if ($this->isCategoryActive($child))
                {
                    $active = ' actParent';
                    if ($child->getId() == $keyCurrent) $active = ' act';
                }
                // --- format category name ---
                $name = $this->escapeHtml($child->getName());
                if (Mage::getStoreConfig('custom_menu/general/non_breaking_space'))
                    $name = str_replace(' ', '&nbsp;', $name);
                $subCat = '';
                
                if ($level == 1){
                    $subCat = ' subCatMenu';
                }
                else{
                    $subCat = ' subsubCatMenu';
                }
                
                //DM 11-21-13 
                //The follow block of code checks if the category is a clearance category
                //If it is, we apply the appropriate class. Clearance categories should be
                //keep to the last position in the paranet category tree
                $isClearance = strpos($name, 'Clearance');

                if ($isClearance !== false) {
                    $html .= '<div class="clearance"><a href="' . $this->getCategoryUrl($child) . '">' . $name . '</a></div>';
                } else {
                    $html.= '<div class="itemMenuName level' . $level . $active . $subCat . '"><a href="' . $this->getCategoryUrl($child) . '">' . $name . '</a></div>';
                }
                //End
                $activeChildren = $this->getActiveChildren($child, $level);
                if (count($activeChildren) > 0)
                {
                    $html.= '<div class="itemSubMenu level' . $level . '">';
                    $html.= $this->drawMenuItem($activeChildren, $level + 1);
                    $html.= '</div>';
                }
            }
        }
        $html.= '</div>';
        return $html;
    }
}
