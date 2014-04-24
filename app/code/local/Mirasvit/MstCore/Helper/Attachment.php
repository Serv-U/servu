<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Sphinx Search Ultimate
 * @version   2.3.1
 * @revision  666
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_MstCore_Helper_Attachment extends Mage_Core_Helper_Data
{
	/**
	 * also add to layout
	 * <action method="addJs"><script>mirasvit/core/jquery.min.js</script></action>
     * <action method="addJs"><script>mirasvit/core/jquery.MultiFile.js</script></action>
	 */
	public function getFileInputHtml()
	{
		return "<input type='file' class='multi' name='attachment[]' id='attachment'>";
	}

    public function getAttachments($type, $entityId) {
        return Mage::getModel('mstcore/attachment')->getCollection()
            ->addFieldToFilter('type', $type)
            ->addFieldToFilter('entity_id', $entityId)
            ;
    }

    public function hasAttachments() {
    	return isset($_FILES['attachment']['name'][0]) && $_FILES['attachment']['name'][0] != '';
    }

    public function saveAttachments($type, $entityId) {
        if (!$this->hasAttachments()) {
            return false;
        }
        $i = 0;
        foreach($_FILES['attachment']['name'] as $name) {
            if ($name == '') {
                continue;
            }
            //@tofix - need to check for max upload size and alert error
            $body = file_get_contents(addslashes($_FILES['attachment']['tmp_name'][$i]));

            $attachment = Mage::getModel('mstcore/attachment')
                ->setName($name)
                ->setType(strtoupper($_FILES['attachment']['type'][$i]))
                ->setSize($_FILES['attachment']['size'][$i])
                ->setBody($body)
                ->setType($type)
                ->setEntityId($entityId)
                ->save();
            $i++;
        }
        return true;
    }
}