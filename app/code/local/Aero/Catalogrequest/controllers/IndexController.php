<?php
class Aero_Catalogrequest_IndexController extends Mage_Core_Controller_Front_Action
{
    const MC_LIST_ID = '';  // Mail Chimp List ID
    
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/catalogrequest?id=15 
    	 *  or
    	 * http://site.com/catalogrequest/id/15 	
    	 */
    	/* 
		$catalogrequest_id = $this->getRequest()->getParam('id');

  		if($catalogrequest_id != null && $catalogrequest_id != '')	{
			$catalogrequest = Mage::getModel('catalogrequest/catalogrequest')->load($catalogrequest_id)->getData();
		} else {
			$catalogrequest = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($catalogrequest == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$catalogrequestTable = $resource->getTableName('catalogrequest');
			
			$select = $read->select()
			   ->from($catalogrequestTable,array('catalogrequest_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$catalogrequest = $read->fetchRow($select);
		}
		Mage::register('catalogrequest', $catalogrequest);
		*/

			
		$this->loadLayout();
                $this->getLayout()->getBlock('head')->setTitle('Catalog Request');
                $this->getLayout()->getBlock('catalogrequest')->setFormAction( Mage::getUrl('*/*/post') );
		$this->renderLayout();

    }
    
    public function postAction()
    {
        $this->process();
    } 
    
    
    public function process()
    {
        if($this->getRequest()->isPost()){
            $request = Mage::getModel('catalogrequest/catalogrequest');
            $data = $this->getRequest()->getPost();
            $data['time_added'] = now();
            $data['country'] = $data['country_id'];
            $data['ip'] = $_SERVER['REMOTE_ADDR'];
            $data['fname'] = ucwords($data['fname']);
            $data['lname'] = ucwords($data['lname']);
            $data['address1'] = ucwords(str_replace(",", " ",$data['address1']));
            $data['address2'] = ucwords(str_replace(",", " ",$data['address2']));
            $data['city'] = ucwords($data['city']);
            if(empty($data['region'])){
                $data['state'] = Mage::getModel('directory/region')->load($data['state'])->getCode();
            } else {
                $data['state'] = $data['region'];
            }

            // Validate
            if(!$errors = $request->validate($data)){
                MAGE::getSingleton('core/session')->addError($errors);
            }

            /**
            *  Mail Chimp Integration
            *  If updates requested add to Mailchimp
            *  Change the MC_LIST_ID at the top to match your MC list id found in your list settings on MailChimp
            */
            
            if(isset($data['updates']) && isset($data['email'])){
                $apikey = Mage::getStoreConfig('mailchimp/general/apikey'); // Get API key from MC Module
                $merge_vars = array('INTERESTS'=> Mage::getStoreConfig('mailchimp/subscribe/interests')); // Get Interests from MC Module            


                try {
                    // You may need to change this URL, consult the MC API
                    $client = new Zend_XmlRpc_Client('http://us1.api.mailchimp.com/1.2/');
                    $response = $client->call('listSubscribe',
                        array($apikey, self::MC_LIST_ID, $data['email'],$merge_vars,'HTML', FALSE ));
                } catch(Exception $e){
                    Mage::getSingleton('adminhtml/session')->addError('Mailchimp failed to connect');
                }
            }

            // Add to database
            try {
                $request->setData($data)->save();
                MAGE::getSingleton('core/session')->addSuccess($this->__('<h2>Thank you</h3> You can expect to receive your catalog in 10-14 days.'));
                $this->_redirect('catalogrequest/');
                return;
                die;
            } catch(Exception $e){
                MAGE::getSingleton('core/session')->addError('Sorry, we\'ve had some trouble saving your request, please call and request a catalog 
(800-797-3788) or email (sales@servu-online.com) to request a catalog');
                $this->_redirect('catalogrequest/');
                return;
            }




        }
        return;

    }
}
