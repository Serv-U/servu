<?php

class Aero_Catalogrequest_Block_Adminhtml_Catalogrequest_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('catalogrequestGrid');
      $this->setDefaultSort('catalogrequest_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('catalogrequest/catalogrequest')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('catalogrequest_id', array(
          'header'    => Mage::helper('catalogrequest')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'catalogrequest_id',
      ));

      $this->addColumn('fname', array(
          'header'    => Mage::helper('catalogrequest')->__('First Name'),
          'align'     =>'left',
          'index'     => 'fname',
      ));

      $this->addColumn('lname', array(
          'header'    => Mage::helper('catalogrequest')->__('Last Name'),
          'align'     =>'left',
          'index'     => 'lname',
      ));

      $this->addColumn('zip', array(
          'header'    => Mage::helper('catalogrequest')->__('Zip Code'),
          'align'     =>'left',
          'index'     => 'zip',
      ));

      $this->addColumn('time_added', array(
        'header'    => Mage::helper('catalogrequest')->__('Created At'),
        'align'     => 'left',
        'index'     => 'time_added',
        'type'      => 'datetime',
        'width'     => '160px',
      ));
	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('catalogrequest')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('status', array(
          'header'    => Mage::helper('catalogrequest')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Pending',
              2 => 'Processed',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('catalogrequest')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('catalogrequest')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('catalogrequest')->__('CSV'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('catalogrequest_id');
        $this->getMassactionBlock()->setFormFieldName('catalogrequest');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('catalogrequest')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('catalogrequest')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('catalogrequest/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('catalogrequest')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('catalogrequest')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}