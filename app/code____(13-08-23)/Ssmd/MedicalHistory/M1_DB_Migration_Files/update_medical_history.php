<?php

// Data Migration of Medical_history Module.


// ============================= Databases ===========================================
$db1 = "ssmd_m1";
$db2 = "ssmd_m2";


// ============================ Database connection  check ===========================
// First DB Connection Check
$mysqli1 = new mysqli("localhost", "root", "", $db1);
// Check connection
if ($mysqli1 -> connect_errno) {

    echo PHP_EOL . 'Failed to connect to MySQL DB1:'. $mysqli1 -> connect_error;

    //echo "Failed to connect to MySQL: " . $mysqli1 -> connect_error;
    //exit();
}

// Second DB Connection Check
$mysqli2 = new mysqli("localhost", "root", "", $db2);

if ($mysqli2->connect_errno) {
    echo PHP_EOL . 'Failed to connect to MySQL DB2:'. $mysqli2 -> connect_error;

    //echo "Failed to connect to MySQL: " . $mysqli2->connect_error;
    //exit();
}



// ================================= Tables ======================================

$eav_attribute            			 = 'mg_eav_attribute';
$customer_entity_m1 	  			 = 'mg_customer_entity';
$customer_entity_int      			 = 'mg_customer_entity_int';
$customer_entity_varchar 			 = 'mg_customer_entity_varchar';
$customer_medical_history 			 = 'customer_medical_history';
$customer_medical_history_order_date = 'customer_medical_history_order_date';
$sales_order_m1 					 = 'mg_sales_flat_order';

// ================================ Medical History Migration ============================


// Varchar Data
$queryCustomer = "SELECT DISTINCT cust.`entity_id`,cust.`updated_at`
											FROM $db1.$eav_attribute attr
											INNER JOIN $db1.$customer_entity_varchar custVarchar ON attr.`attribute_id` = custVarchar.`attribute_id`
											INNER JOIN $db1.$customer_entity_m1 cust ON cust.`entity_id` = custVarchar.`entity_id`
											WHERE (`attribute_code` = 'patient_name' AND `value` IS NOT NULL)
											OR (`attribute_code` = 'patient_last_name' AND `value` IS NOT NULL)
											OR (`attribute_code` = 'dateofbirth' AND `value` IS NOT NULL)
											OR (`attribute_code` = 'phys_contact' AND `value` IS NOT NULL)
											";

$resultCustomer = $mysqli1->query($queryCustomer);
$count=0;
while($fieldinfoCustomerVarchar = $resultCustomer->fetch_array(MYSQLI_ASSOC))
{
    $str="";
    $testAr = [];
    $customerId   = $fieldinfoCustomerVarchar['entity_id'];
    $updateDate   = $fieldinfoCustomerVarchar['updated_at'];

    //echo PHP_EOL ."Customer ID ".$customerId."<br>";

    //$customerId = '241620';

    $primary_phys_name = '"None"';
    $primary_phys_phone = '"None"';
    $primary_phys_email = '"None"';
    $medicalcond = '"None"';
    $medications = '"None"';
    $allergies = '"None"';
    $skin_concerns = '"None"';
    $skin_type = '"None"';
    $sunscreen_wear = '"None"';
    $use_of_retinoid = '"None"';
    $use_of_topicals = '"None"';
    $other_hair_removal = '"None"';

    // =======================  Start to store data in db (2-06-23) ========================================

    // Fetching customer recent order's increment_id and created_date

    $queryOrders = "SELECT * FROM $db1.$sales_order_m1 WHERE `customer_id` = ".$customerId." ORDER BY `entity_id` DESC LIMIT 1";
    $resultOrders = $mysqli1->query($queryOrders);

    while($infoOrders = $resultOrders->fetch_array(MYSQLI_ASSOC)){

        $fetchOrder = "SELECT * FROM $db2.$customer_medical_history_order_date WHERE `recent_order_increment_id` = '".$infoOrders['increment_id']."'";
        $resultOrder = $mysqli2->query($fetchOrder);
        $orderCount = mysqli_num_rows($resultOrder);

        if($orderCount==0)		// If order increment id found in table Don't insert again.
        {
            $insertOrdersQuery = "INSERT INTO $db2.$customer_medical_history_order_date
                      			(`customer_id`, `recent_order_increment_id`, `recent_order_date`)
                      		 VALUES
                      			(".$infoOrders['customer_id'].", '".$infoOrders['increment_id']."', '".$infoOrders['created_at']."') ";

            $mysqli2->query($insertOrdersQuery);
        }
    }

    // =======================  End to store data in db (2-06-23) ========================================


    $queryCustomerVarchar = "SELECT * FROM $db1.$customer_entity_varchar WHERE `entity_id` = ".$customerId."";
    $resultCustomerVarchar = $mysqli1->query($queryCustomerVarchar);

    while($fieldinfoCustomerVarchar = $resultCustomerVarchar->fetch_array(MYSQLI_ASSOC))
    {

        $attributeId = $fieldinfoCustomerVarchar['attribute_id'];

        $queryEavAttrVarchar = "SELECT *
								FROM $db1.$eav_attribute
								WHERE `attribute_id` = ".$attributeId."
								AND `attribute_id` BETWEEN 140 AND 245";

        $resultEavAttrVarchar = $mysqli1->query($queryEavAttrVarchar);

        while($fieldinfoEavAttrVarchar = $resultEavAttrVarchar->fetch_array(MYSQLI_ASSOC))
        {
            $attributeCodeVarchar = $fieldinfoEavAttrVarchar['attribute_code'];

            // Varchar DataType values
            //echo PHP_EOL ."VARCHAR DATA";

            $fieldinfoCustomerVarchar['value'] = str_replace('"','\'',$fieldinfoCustomerVarchar['value']);

            if($attributeCodeVarchar == 'patient_name')
            {
                if(isset($fieldinfoCustomerVarchar['value'])&&($fieldinfoCustomerVarchar['value'] != null) )
                {
                    $firstname = '"'.$fieldinfoCustomerVarchar['value'].'"';
                }
                else
                {
                    $firstname = '"None"';
                }

            }

            if($attributeCodeVarchar == 'patient_last_name')
            {
                if(isset($fieldinfoCustomerVarchar['value'])&&($fieldinfoCustomerVarchar['value'] != null) )
                {
                    $lastname = '"'.$fieldinfoCustomerVarchar['value'].'"';
                }
                else
                {
                    $lastname = '"None"';
                }

            }

            if($attributeCodeVarchar == 'dateofbirth')
            {
                if(isset($fieldinfoCustomerVarchar['value'])&&($fieldinfoCustomerVarchar['value'] != null) )
                {
                    $dateofbirth = '"'.$fieldinfoCustomerVarchar['value'].'"';
                }
                else
                {
                    $dateofbirth = '"None"';
                }
            }
            if($attributeCodeVarchar == 'phys_contact')
            {
                if(isset($fieldinfoCustomerVarchar['value'])&&($fieldinfoCustomerVarchar['value'] != null) )
                {
                    $phys_contact = '"'.$fieldinfoCustomerVarchar['value'].'"';
                }
                else
                {
                    $phys_contact = '"None"';
                }
            }
            if($attributeCodeVarchar == 'primary_phys_name')
            {
                if(isset($fieldinfoCustomerVarchar['value'])&&($fieldinfoCustomerVarchar['value'] != null) )
                {
                    $primary_phys_name = '"'.$fieldinfoCustomerVarchar['value'].'"';
                }
                else
                {
                    $primary_phys_name = '"None"';
                }
            }
            if($attributeCodeVarchar == 'primary_phys_phone')
            {
                if(isset($fieldinfoCustomerVarchar['value'])&&($fieldinfoCustomerVarchar['value'] != null) )
                {
                    $primary_phys_phone = '"'.$fieldinfoCustomerVarchar['value'].'"';
                }
                else
                {
                    $primary_phys_phone = '"None"';
                }
            }
            if($attributeCodeVarchar == 'primary_phys_email')
            {
                if(isset($fieldinfoCustomerVarchar['value'])&&($fieldinfoCustomerVarchar['value'] != null) )
                {
                    $primary_phys_email = '"'.$fieldinfoCustomerVarchar['value'].'"';
                }
                else
                {
                    $primary_phys_email = '"None"';
                }
            }
            if($attributeCodeVarchar == 'medicalcond')
            {
                $medicalcond = $fieldinfoCustomerVarchar['value'];
                if($medicalcond == 'n')
                {
                    $medicalcond = '"None"';
                }
                else
                {
                    $medicalcond = '"'.$fieldinfoCustomerVarchar['value'].'"';
                }
            }
            if($attributeCodeVarchar == 'medications')
            {
                $medications = $fieldinfoCustomerVarchar['value'];
                if($medications == 'n')
                {
                    $medications = '"None"';
                }
                else
                {
                    $medications = '"'.$fieldinfoCustomerVarchar['value'].'"';
                }
            }
            if($attributeCodeVarchar == 'allergies')
            {
                $allergies = $fieldinfoCustomerVarchar['value'];
                if($allergies == 'n')
                {
                    $allergies = '"None"';
                }
                else
                {
                    $allergies = '"'.$fieldinfoCustomerVarchar['value'].'"';
                }
            }
            if($attributeCodeVarchar == 'skin_concerns')
            {
                $skin_concerns = $fieldinfoCustomerVarchar['value'];
                if($skin_concerns == 'n')
                {
                    $skin_concerns = '"None"';
                }
                else
                {
                    $skin_concerns = '"'.$fieldinfoCustomerVarchar['value'].'"';
                }
            }
            if($attributeCodeVarchar == 'skin_type')
            {
                $skin_type = $fieldinfoCustomerVarchar['value'];
                if($skin_type == 'n')
                {
                    $skin_type = '"None"';
                }
                else
                {
                    $skin_type = '"'.$fieldinfoCustomerVarchar['value'].'"';
                }
            }
            if($attributeCodeVarchar == 'sunscreen_wear')
            {
                $sunscreen_wear = $fieldinfoCustomerVarchar['value'];
                if($sunscreen_wear == 'n')
                {
                    $sunscreen_wear = '"None"';
                }
                else
                {
                    $sunscreen_wear = '"'.$fieldinfoCustomerVarchar['value'].'"';
                }
            }
            if($attributeCodeVarchar == 'use_of_retinoid')
            {
                $use_of_retinoid = $fieldinfoCustomerVarchar['value'];
                if($use_of_retinoid == 'n')
                {
                    $use_of_retinoid = '"None"';
                }
                else
                {
                    $use_of_retinoid = '"'.$fieldinfoCustomerVarchar['value'].'"';
                }
            }
            if($attributeCodeVarchar == 'use_of_topicals')
            {
                $use_of_topicals = $fieldinfoCustomerVarchar['value'];
                if($use_of_topicals == 'n')
                {
                    $use_of_topicals = '"None"';
                }
                else
                {
                    $use_of_topicals = '"'.$fieldinfoCustomerVarchar['value'].'"';
                }
            }
            if($attributeCodeVarchar == 'other_hair_removal')
            {
                $other_hair_removal = $fieldinfoCustomerVarchar['value'];
                if($other_hair_removal == 'n')
                {
                    $other_hair_removal = '"None"';
                }
                else
                {
                    $other_hair_removal = '"'.$fieldinfoCustomerVarchar['value'].'"';
                }
            }
        }

    }


    // Int Data

    $eyepress = 'false';
    $pastuse = '"No"';
    $past_use_upneeq = '"No"';
    $upneeq_blood_pressure = '"No"';
    $depression_meds_use = '"No"';
    $sjogren_syndrome = '"No"';
    $low_lying_eyelids = '"No"';
    $unwanted_hair = '"No"';
    $past_use_vaniqa = '"No"';

    $queryCustomerInt = "SELECT * FROM $db1.$customer_entity_int WHERE `entity_id` = ".$customerId."";
    $resultCustomerInt = $mysqli1->query($queryCustomerInt);

    while($fieldinfoCustomerInt = $resultCustomerInt->fetch_array(MYSQLI_ASSOC))
    {

        $attributeId = $fieldinfoCustomerInt['attribute_id'];

        $queryEavAttrVarchar = "SELECT *
								FROM $db1.$eav_attribute
								WHERE `attribute_id` = ".$attributeId."
								AND `attribute_id` BETWEEN 140 AND 245";

        $resultEavAttrVarchar = $mysqli1->query($queryEavAttrVarchar);

        while($fieldinfoEavAttrInt = $resultEavAttrVarchar->fetch_array(MYSQLI_ASSOC))
        {
            $attributeCodeInt = $fieldinfoEavAttrInt['attribute_code'];

            // Int DataType values
            // echo PHP_EOL ."INT DATA";
            if($attributeCodeInt == 'genderform')
            {
                $genderform = $fieldinfoCustomerInt['value'];
                if($genderform == 7)
                {
                    $genderform = '"Male"';
                }
                else if($genderform == 8)
                {
                    $genderform = '"Female"';
                }
            }
            if($attributeCodeInt == 'breastfeeding')
            {
                $breastfeeding = $fieldinfoCustomerInt['value'];
                if($breastfeeding == 3)
                {
                    $breastfeeding = '"Agreed"';
                }
                else
                {
                    $breastfeeding = '"Not applicable / Not answered"';
                }

            }

            if($attributeCodeInt == 'eyepress')
            {
                $eyepress = $fieldinfoCustomerInt['value'];
                if($eyepress == 4)
                {
                    $eyepress = '"true"';
                }
            }
            if($attributeCodeInt == 'pastuse')
            {
                $pastuse = $fieldinfoCustomerInt['value'];
                if($pastuse == 1)
                {
                    $pastuse = '"Yes"';
                }
                else
                {
                    $pastuse = '"No"';
                }
            }

            if($attributeCodeInt == 'past_use_upneeq')
            {

                $past_use_upneeq = $fieldinfoCustomerInt['value'];
                if($past_use_upneeq == 1)
                {
                    $past_use_upneeq = '"Yes"';
                }
                else
                {
                    $past_use_upneeq = '"No"';
                }
            }
            if($attributeCodeInt == 'upneeq_blood_pressure')
            {
                $upneeq_blood_pressure = $fieldinfoCustomerInt['value'];
                if($upneeq_blood_pressure == 1)
                {
                    $upneeq_blood_pressure = '"Yes"';
                }
                else
                {
                    $upneeq_blood_pressure = '"No"';
                }
            }
            if($attributeCodeInt == 'depression_meds_use')
            {
                $depression_meds_use = $fieldinfoCustomerInt['value'];
                if($depression_meds_use == 1)
                {
                    $depression_meds_use = '"Yes"';
                }
                else
                {
                    $depression_meds_use = '"No"';
                }
            }
            if($attributeCodeInt == 'sjogren_syndrome')
            {
                $sjogren_syndrome = $fieldinfoCustomerInt['value'];
                if($sjogren_syndrome == 1)
                {
                    $sjogren_syndrome = '"Yes"';
                }
                else
                {
                    $sjogren_syndrome = '"No"';
                }
            }
            if($attributeCodeInt == 'low_lying_eyelids')
            {
                $low_lying_eyelids = $fieldinfoCustomerInt['value'];
                if($low_lying_eyelids == 1)
                {
                    $low_lying_eyelids = '"Yes"';
                }
                else
                {
                    $low_lying_eyelids = '"No"';
                }
            }
            if($attributeCodeInt == 'unwanted_hair')
            {
                $unwanted_hair = $fieldinfoCustomerInt['value'];
                if($unwanted_hair == 1)
                {
                    $unwanted_hair = '"Yes"';
                }
                else
                {
                    $unwanted_hair = '"No"';
                }
            }
            if($attributeCodeInt == 'past_use_vaniqa')
            {
                $past_use_vaniqa = $fieldinfoCustomerInt['value'];
                if($past_use_vaniqa == 1)
                {
                    $past_use_vaniqa = '"Yes"';
                }
                else
                {
                    $past_use_vaniqa = '"No"';
                }
            }
        }

    }

    // Int Values
    if($genderform == "" || $genderform == null)
    {
        $genderform = '""';
    }
    if($breastfeeding == "" || $breastfeeding == null)
    {
        $breastfeeding = '""';
    }

    if($eyepress == "" || $eyepress == null)
    {
        $eyepress = '""';
    }

    if(isset($pastuse) && ($pastuse == '"Yes"'))
    {
        $pastuse = '"Yes"';
    }
    else
    {
        $pastuse = '"No"';
    }
    if(isset($past_use_upneeq) && ($past_use_upneeq == '"Yes"'))
    {
        $past_use_upneeq = '"Yes"';
    }
    else
    {
        $past_use_upneeq = '"No"';
    }
    if(isset($upneeq_blood_pressure) && ($upneeq_blood_pressure == '"Yes"'))
    {
        $upneeq_blood_pressure = '"Yes"';
    }
    else
    {
        $upneeq_blood_pressure = '"No"';
    }
    if(isset($depression_meds_use) && ($depression_meds_use == '"Yes"'))
    {
        $depression_meds_use = '"Yes"';
    }
    else
    {
        $depression_meds_use = '"No"';
    }
    if(isset($sjogren_syndrome) && ($sjogren_syndrome == '"Yes"'))
    {
        $sjogren_syndrome = '"Yes"';
    }
    else
    {
        $sjogren_syndrome = '"No"';
    }

    if(isset($low_lying_eyelids) && ($low_lying_eyelids == '"Yes"'))
    {
        $low_lying_eyelids = '"Yes"';
    }
    else
    {
        $low_lying_eyelids = '"No"';
    }

    if(isset($unwanted_hair) && ($unwanted_hair == '"Yes"'))
    {
        $unwanted_hair = '"Yes"';
    }
    else
    {
        $unwanted_hair = '"No"';
    }
    if(isset($past_use_vaniqa) && ($past_use_vaniqa == '"Yes"'))
    {
        $past_use_vaniqa = '"Yes"';
    }
    else
    {
        $past_use_vaniqa = '"No"';
    }


    // ======================== Updated Medical History Start =======================================

    $querySelectMH = "SELECT * FROM $db2.$customer_medical_history
						WHERE `customer_id` = $customerId
						AND `updated_at` != '".$updateDate."'
						AND `order_number` = 'm1-data'";

    $resultSelectMH = $mysqli2->query($querySelectMH);
    while($infoSelectMH = $resultSelectMH->fetch_array(MYSQLI_ASSOC))
    {
        if(isset($infoSelectMH['customer_id']) && $infoSelectMH['customer_id'] != null)
        {
            $changedMedicalHistory_id 			= $infoSelectMH['id'];
            $changedMedicalHistory_questionId 	= $infoSelectMH['question_id'];
            $changedMedicalHistory_customerId 	= $infoSelectMH['customer_id'];
            $changedMedicalHistory_questionText = $infoSelectMH['question_text'];
            $changedMedicalHistory_response 	= $infoSelectMH['response'];


            //$currentTimeStamp 	= date('Y-m-d H:i:s');
            $m1_updatedAt   	= $updateDate;
            $m2_updatedAt		= $infoSelectMH['updated_at'];

            if($m1_updatedAt > $m2_updatedAt)		// If M1 medical_history updated by customer then & only then update
            {

                $changedMedicalHistory_updatedAt 	= $m1_updatedAt;

                switch ($changedMedicalHistory_questionId) {
                    case 1:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $firstname;
                        break;
                    case 2:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $lastname;
                        break;
                    case 3:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $genderform;
                        break;
                    case 4:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $breastfeeding;
                        break;
                    case 5:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $dateofbirth;
                        break;
                    case 6:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $phys_contact;
                        break;
                    case 7:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $primary_phys_name;
                        break;
                    case 8:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $primary_phys_phone;
                        break;
                    case 9:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $primary_phys_email;
                        break;
                    case 10:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $medicalcond;
                        break;
                    case 11:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $medications;
                        break;
                    case 12:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $allergies;
                        break;

                    case 13:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $eyepress;
                        break;
                    case 14:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $pastuse;
                        break;

                    case 15:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $skin_concerns;
                        break;

                    case 16:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $skin_type;
                        break;
                    case 17:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $sunscreen_wear;
                        break;
                    case 18:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $use_of_retinoid;
                        break;
                    case 19:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $use_of_topicals;
                        break;
                    case 20:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $past_use_upneeq;
                        break;
                    case 21:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $upneeq_blood_pressure;
                        break;
                    case 23:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $depression_meds_use;
                        break;
                    case 24:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $sjogren_syndrome;
                        break;
                    case 25:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $low_lying_eyelids;
                        break;
                    case 26:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $unwanted_hair;
                        break;
                    case 27:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $past_use_vaniqa;
                        break;
                    case 28:
                        $questionText = $changedMedicalHistory_questionText;
                        $response     = $other_hair_removal;
                        break;

                    default:
                        // code...
                        break;
                }

                $medHistUpdate = "UPDATE $db2.$customer_medical_history
										SET
	                      			 `question_id` 		= $changedMedicalHistory_questionId,
	                      			 `customer_id` 		= $customerId,
	                      			 `question_text` 	= '".$questionText."',
	                      			 `response`			= $response,
	                      			 `unique_id` 		= '1fadfasdfs',
	                      			 `status`			= '1',
	                      			 `order_number` 	= 'm1-data',
	                      			 `updated_at` 		= '".$changedMedicalHistory_updatedAt."'
	                      			 WHERE `id` = $changedMedicalHistory_id
	                      ";
                $mysqli2->query($medHistUpdate);

                $count++;
                echo PHP_EOL."Updating Data For Customer ID ".$customerId;
                echo PHP_EOL."<br>";
            } // End of date check

        }

    }

    // ======================== Updated Medical History End ========================================

}

$mysqli1->close();
$mysqli2->close();


echo "<br>";
echo PHP_EOL.$count." Record's Updated Successfully"; exit;
