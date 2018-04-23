<?php

class Ignovate_Driver_Block_Adminhtml_Driver_Grid_Renderer_Aadhaar extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $debug = true;
        if($row->getFileAadhaar()==""){
            return "";
        }
        else{
            $downloadLink = Mage::getBaseUrl('media') . 'driver' . DS . 'docs' . DS . $row->getFileAadhaar();
            return "<a href='" . $downloadLink . "' download>Download File</a>";
        }
    }
}