<?php

namespace TrackingActivityLog\TrackAdminUser\Block\Adminhtml;

class Index extends \Magento\Backend\Block\Template
{

    protected $adminUserActionsLogsFactory;
    protected $json;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \TrackingActivityLog\TrackAdminUser\Model\AdminUserActionsLogsFactory $adminUserActionsLogsFactory,
        \Magento\Framework\Serialize\Serializer\Json $json
       
    ) {
        parent::__construct($context);
        $this->adminUserActionsLogsFactory = $adminUserActionsLogsFactory;
        $this->json = $json;        
    }

    // Return data as an array
    public function getListLogs()
    {
        $collection = $this->adminUserActionsLogsFactory->create()->getCollection();
        $collection->setOrder('id');

        return $collection;
    }

    // Return User Activity Logs collection as an array
    public function getViewLogs($id)
    {
        $userLog = $this->adminUserActionsLogsFactory->create()->load($id);

        //$data['befor_save'] = $this->json->unserialize($value->getDataBeforeSave(), true);
        //$data['after_save'] = $this->json->unserialize($value->getDataAfterSave(), true);

        $beforSave = json_decode($userLog->getDataBeforeSave(), true);
        $afterSave = json_decode($userLog->getDataAfterSave(), true);

        $result = [];
        if(!empty($beforSave) && !empty($afterSave))
        {
            $result = $this->checkDifferenceOfActivityLogs($beforSave, $afterSave);
        }
        $result['username'] = $userLog->getUserName();

        /*echo "<pre>"; 
        print_r($result);
        print_r($beforSave);
        print_r($afterSave);
        exit;*/

        //echo "<pre>"; print_r($result); exit;
        return $result;
    }
    // Get Diff of User Activity Logs and return array
    public function checkDifferenceOfActivityLogs($aArray1, $aArray2)
    {
        $aReturn = array();
        //echo "<pre>"; print_r($aArray1); exit; 
        foreach ($aArray1 as $mKey => $mValue) { 
            if (array_key_exists($mKey, $aArray2)) {
                if (is_array($mValue)) { 
                    $aRecursiveDiff = $this->checkDifferenceOfActivityLogs($mValue, $aArray2[$mKey]);
                    if (count($aRecursiveDiff)) 
                    {                         
                        $aReturn[$mKey][0] = $aRecursiveDiff;
                        $aReturn[$mKey][1] = $aArray2[$mKey];                        
                    } 
                } else { 
                    if ($mValue != $aArray2[$mKey]) { 
                        $aReturn[$mKey][0] = $mValue;
                        $aReturn[$mKey][1] = $aArray2[$mKey];
                        //$aReturn[0]['attribute'] = $mKey;
                        //$aReturn[1][$mKey] = $aArray2[$mKey];
                        //$aReturn[1]['attribute'] = $mKey;
                    }
                   

                } 
            } else { 
                //$aReturn[$mKey] = $mValue;    // if 'key' is in first array but not that 'key' in second array, if we want that key in result.
            } 
        } 

       // echo "<pre>"; print_r($aReturn);exit;
        return $aReturn; 
    }

    public function getArrayCompare($array1, $array2) {
        $diff = [];
        // Left-to-right
        foreach ($array1 as $key => $value)
        {   
            //$key = str_replace('"', '', $key);
            if (!array_key_exists($key,$array2))
            {
                $diff[0][$key] = $value;
            }
            elseif (is_array($value))
            {
                 if (!is_array($array2[$key]))
                 {
                    /*foreach ($value as $k => $v) {
                        //echo "<pre>"; print_r($v);exit;
                        if(is_array($v))
                        {
                            $diff[0][$key] = $v;
                        }
                    }*/
                    $diff[0][$key] = $value;
                    $diff[1][$key] = $array2[$key];
                 }
                else
                {
                    $new = $this->getArrayCompare($value, $array2[$key]);
                    if ($new == false)
                    {
                         if (isset($new[0])) $diff[0][$key] = $new[0];
                         if (isset($new[1])) $diff[1][$key] = $new[1];
                    }
                 }
            } 
            elseif ($array2[$key] !== $value) 
            {
                 $diff[0][$key] = $value;
                 $diff[1][$key] = $array2[$key];
            }
        }

        return $diff;
    }

}
