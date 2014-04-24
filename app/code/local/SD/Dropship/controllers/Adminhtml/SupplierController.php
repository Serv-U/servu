<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SupplierController
 *
 * @author dustinmiller
 */
class SD_Dropship_Adminhtml_SupplierController 
    extends Mage_Adminhtml_Controller_Action
{

    /**
     * Init actions
     *
     * @return SD_Dropship_Adminhtml_SupplierController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('catalog/sd_dropship')
            ->_addBreadcrumb(Mage::helper('sd_dropship')->__('Catalog'), Mage::helper('sd_dropship')->__('Catalog'))
            ->_addBreadcrumb(Mage::helper('sd_dropship')->__('Manage Suppliers'), Mage::helper('sd_manager')->__('Manage Suppliers Information'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('sd_dropship/adminhtml_supplier'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_redirect('*/*/');
    }

    public function editAction()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('sd_dropship/supplier');
        /* @var $model SD_Manager_Model_Manufacturer */

        // 2. Initial checking
        if ($id) {
            $model->load($id);/*die('<br>#stop');*/
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('This supplier no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        } else {
            $attribute_code = $this->getRequest()->getParam('attribute_code');
            $option_id      = $this->getRequest()->getParam('option_id');
            $store_id       = $this->getRequest()->getParam('store_id');
            
            $model->loadFromAttribute($attribute_code, $option_id, $store_id);/*die('<br>#stop');*/
        }


        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('sd_dropship_supplier', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb($id ? Mage::helper('adminhtml')->__('Edit Page') : Mage::helper('adminhtml')->__('New Page'), $id ? Mage::helper('adminhtml')->__('Edit Page') : Mage::helper('adminhtml')->__('New Page'))
            ->_addContent($this->getLayout()->createBlock('sd_dropship/adminhtml_supplier_edit')->setData('action', $this->getUrl('*/supplier/save')));
        
            $this->renderLayout();

    }

    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {

            $model = Mage::getModel('sd_dropship/supplier');

            $model->setData($data);
	        if (!$this->getRequest()->getParam('id')) {
	            $model->setData('attribute_code', $this->getRequest()->getParam('attribute_code'));
	            $model->setData('attribute_option_id', $this->getRequest()->getParam('option_id'));
	            $model->setData('attribute_value_store_id', $this->getRequest()->getParam('store_id'));
	        }

            Mage::dispatchEvent('sd_dropship_adminhtml_supplier_prepare_save', array('info' => $model, 'request' => $this->getRequest()));


            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The manufacturer information was successfully saved'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);

                // redirect back to edit form
		if ($id = $this->getRequest()->getParam('id')) {
                    $this->_redirect('*/*/edit', array('id' => $id));
		} else {
                    $attribute_code = $this->getRequest()->getParam('attribute_code');
		    $option_id      = $this->getRequest()->getParam('option_id');
		    $store_id       = $this->getRequest()->getParam('store_id');
	            $this->_redirect('*/*/edit', array(
	                	'attribute_code' => $attribute_code,
	                	'option_id' => $option_id,
	                	'store_id' => $store_id
	             ));
		}

                return;
            }
        }
    }

    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('id')) {
            $title = "";
            try {
                $model = Mage::getModel('sd_dropship/supplier');
                $model->load($id);
                $title = $model->getName();
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Supplier information was successfully deleted'));
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                //Mage::dispatchEvent('adminhtml_cmspage_on_delete', array('title' => $title, 'status' => 'fail'));
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find information to delete'));
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sd_dropship/supplier');
    }
}

?>