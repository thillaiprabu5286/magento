<?php

class Ignovate_Driver_Block_Adminhtml_Driver_Grid_Renderer_License extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $debug = true;
        if($row->getFileLicense()==""){
            return "";
        }
        else{
            $downloadLink = Mage::getBaseUrl('media') . 'driver' . DS . 'docs' . DS . $row->getFileLicense();
            return "<a href='" . $downloadLink . "' download>Download File</a>";
        }
    }
}