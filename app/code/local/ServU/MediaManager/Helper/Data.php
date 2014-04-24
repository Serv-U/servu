<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Data
 *
 * @author dustinmiller
 */
class ServU_MediaManager_Helper_Data extends Mage_Core_Helper_Abstract {
    
    public function magentoVersion() {
        return version_compare(Mage::getVersion(), '1.4', '<');
    }
    
    /**
     * @desc Get array of file extensions from file type
     * @param string $filetype
     * @return array $extensions 
     */
    public function getMediaTypeExtensions($mediatype){
        
        switch($mediatype){
            case "pdfs":
            default:
                $extensions = array('pdf');
                break;
            case "images":
                $extensions = array('jpg','gif','png','jpeg');
                break;
            case "documents":
                $extensions = array('doc','docx','odt','txt','rtf');
                break;
            case "spreadsheets":
                $extensions = array('xls','xlsx','ods','csv');
                break;
            case "zips":
                $extensions = array('zip','rar');
                break;
            case "urls":
                $extensions = array('url');
                break;
            case "videos":
                $extensions = array('video');
                break;
//            case "autocad":
//                $extensions = array('dwg');
//                break;                
        }
        
        return $extensions;
    }
    
    public function fileTypeHasFiles($type){
        $extensions = $this->getMediaTypeExtensions($type);
        $collection = Mage::getModel('mediamanager/browse')->getCollection()
                ->addFieldToFilter('file_extension', array('in' => $extensions))
                ->getFirstItem();
        
        if($collection->getData('id')){
            return true;
        }
        return false;
    }
    
    /**
     * @desc Identify file's folder based on file's extension
     * @param string $file_extension
     * @return string $extension_folder 
     */
    public function getFileFolder($file_extension){
        switch ($file_extension) {
            case 'pdf':
                $extension_folder = 'pdf';
                break;
            case 'zip':
            case 'rar':
                $extension_folder = 'zip';
                break;
            case 'jpg':
            case 'jpeg':
            case 'gif':
            case 'png':
                $extension_folder = 'images';
                break;
            case 'xls':
            case 'xlsx':
            case 'ods':
            case 'csv':
                $extension_folder = 'spreadsheets';
                break;
            case 'doc':
            case 'docx':
            case 'odt':
            case 'txt':
            case 'rtf':
                $extension_folder = 'documents';
                break;
            default:
                $extension_folder = 'misc';
                break;
        }
        
        return $extension_folder;
    }

    
    /**
     * @desc Build public Media Manager file link 
     * @param object $file 
     * @return string $html
     */
    public function buildFileLink($file){
        $extension = $file->getData('file_extension');
        
        if($this->fileIsUrl($extension) || $this->fileExists($file->id)){
            $rel = $this->getAnchorRel($file);
            $target = $this->getAnchorTarget($extension);
            $url = urldecode($this->getFileURL($extension, $file->getData('file_name')));
            $file_size = (!$this->fileIsUrl($extension)) ? $this->formatFileSize($file->getData('file_size')) : "";

            return "<li class='".$extension."'><a href='".$url."' $rel $target title='".$file->getData('file_title')."'>".$file->getData('file_title')."</a> $file_size </li>";
        }
    }
    
    public function fileExists($file_id){
        $absolute_filepath = $this->getAbsoluteFilePath($file_id, false);
        if(is_file($absolute_filepath)){
            return true;
        }
        return false;
    }
    
    /**
     * @desc Get absolute path for files
     * @param int $fileId
     * @return string $file_path
     */
    public function getAbsoluteFilePath($fileId = null){
        $model = Mage::getModel('mediamanager/mediamanager')->load($fileId);
        $file_extension = $model->getData('file_extension');
        $file_folder = $this->getFileFolder($file_extension);
        $file_name = $model->getData('file_name');
        
        $file_path = Mage::getBaseDir('media'). DS;
        $file_path .= "MediaManager" . DS . $file_folder . DS . $file_name;        

        return $file_path;
    }
        
    public function getAnchorRel($file){
        $rel = "rel='";
        $rel .= ($file->file_extension == 'video') ? "prettyPhoto " : "";
        $rel .= (in_array($file->file_extension, array('jpg','jpeg','gif','png'))) ? "prettyPhoto[media_manager] " : "";
        $rel .= ($file->no_follow == 1) ? "nofollow" : "";
        $rel .= "'";
        
        return $rel;
    }
    
    public function getAnchorTarget($extension){
        return ($extension == 'pdf' || $extension == 'url') ? "target='blank'" : "";
    }
    
    public function getFileURL($file_extension, $file_name){
        if($file_extension == "url" || $file_extension == "video"){
            return $file_name;
        } else {
            $file_folder = $this->getFileFolder($file_extension);
            $file_path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
            $file_path .= "MediaManager/" . $file_folder . "/" . $file_name;
            return urldecode( $file_path );
        }
    }
    
    /**
     * @desc Format file's size for displaying on front end
     * @param int $file_size
     * @return string $file_size
     */
    public function formatFileSize($file_size = null){
        if($file_size > 1073741824){
            $file_size = ceil($file_size/1073741824);
            //$file_size = round($file_size/1073741824, 2);
            $file_size = $file_size . " GB";
        }
        if($file_size > 1048576){
            $file_size = ceil($file_size/1048576);
            //$file_size = round($file_size/1048576, 2);
            $file_size = $file_size . " MB";
        }
        elseif($file_size > 1024){
            $file_size = ceil($file_size/1024);
            //$file_size = round($file_size/1024, 2);
            $file_size = $file_size . " KB";
        }
        elseif($file_size > 0){
            $file_size = $file_size . " B";
        }
        else{
//            $file_size = "n/a";
            return null;
        }
        
        return '('.$file_size.')';
    }
    
    public function fileIsUrl($extension){
        if($extension == 'video' || $extension == 'url'){
            return true;
        }
        return false;
    }

    public function getManufacturerArray(){
        $attribute = Mage::getModel('eav/entity_attribute')
                ->loadByCode('catalog_product', 'manufacturer');

        $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($attribute->getData('attribute_id'))
                ->setStoreFilter(0, false);

        $preparedManufacturers = array();
        foreach ($valuesCollection as $value) {
            $preparedManufacturers[$value->getOptionId()] = $value->getValue();
        }
        
        return $preparedManufacturers;
    }
        
    public function setPaginationIncrements($pager){
        $pager->setAvailableLimit(array(30=>30,90=>90,150=>150));
//        $pager->setAvailableLimit(array(30=>30,90=>90,150=>150,'all'=>'all'));
        return $pager;
    }
    
    public function formatURL($file_name){
        if(strpos($file_name, 'http://') === 0){
            return $file_name;
        }
        return 'http://' . $file_name;
    }
    
    public function viewFileFromAdmin($id){
        $file = Mage::getmodel('mediamanager/mediamanager')->getCollection()
                    ->addFilter('id',$id)
                    ->getFirstItem();
        $file_extension = $file->getData('file_extension');
        $file_name = $file->getData('file_name');
        
        //Return Google Doc View url
        //return 'http://docs.google.com/viewer?url=' . Mage::helper('mediamanager')->getFileURL($file_extension, $file_name);
        return Mage::helper('mediamanager')->getFileURL($file_extension, $file_name);
    }
    
    public function getSearchTypes(){
        return array(
            "filename"      => "File Name",
            "manufacturer"  => "Manufacturer Number",
            "sku"           => "Serv-U SKU",
        );
    }

}

?>
