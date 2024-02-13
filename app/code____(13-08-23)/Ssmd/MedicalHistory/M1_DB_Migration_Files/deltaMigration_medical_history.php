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
    // ============================= New Customers Will Be Inserted/Migrated ===========================

    $query = "SELECT * FROM $db2.$customer_medical_history WHERE `customer_id` = ".$customerId." ";
    $res = $mysqli2->query($query);
    $inf_cont = mysqli_num_rows($res);

    if($inf_cont==0)
    {

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


        $insertQuery = "INSERT INTO $db2.$customer_medical_history
                  	(`question_id`, `customer_id`, `question_text`, `response`,`unique_id`,`status`,`order_number`,`updated_at`)
                  VALUES
                        ( 1, $customerId, 'First Name', $firstname, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 2, $customerId, 'Last Name',  $lastname, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 3, $customerId, 'Gender',  $genderform, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 4, $customerId, 'Pregnant or breastfeeding',  $breastfeeding, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 5, $customerId, 'Date of Birth', $dateofbirth, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 6, $customerId, 'Phone Number', $phys_contact, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 7, $customerId, 'Physician\'s Name', $primary_phys_name, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 8, $customerId, 'Physician\'s Phone', $primary_phys_phone, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 9, $customerId, 'Physician\'s Email', $primary_phys_email, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 10, $customerId, 'List any MEDICAL CONDITIONS.', $medicalcond, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 11, $customerId, 'List any MEDICATIONS you are currently taking (including eye drops).', $medications, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 12, $customerId, 'List any ALLERGIES to medications.', $allergies, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 13, $customerId, 'Eye problems', $eyepress, '1fadfasdfs', '1','m1-data','".$updateDate."'),
                        ( 14, $customerId, 'Have you ever used Latisse in the past?', $pastuse, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 15, $customerId, 'List any skin concerns or goals', $skin_concerns, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 16, $customerId, 'Describe skin type', $skin_type, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 17, $customerId, 'How often do you wear sunscreen?', $sunscreen_wear, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 18, $customerId, 'Are you using a product containing a non-prescription retinoid, such as retinol or retinyl palmitate?', $use_of_retinoid, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 19, $customerId, 'Have you used any prescription topicals (cream, gel, etc) for acne, skin lightening or aging?', $use_of_topicals, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),

                        ( 20, $customerId, 'Have you used Upneeq in the past?', $past_use_upneeq, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 21, $customerId, 'Do you take medication for high blood pressure?', $upneeq_blood_pressure, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 23, $customerId, 'Do you take medication for depression?', $depression_meds_use, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 24, $customerId, 'Do you have Sjogren’s syndrome?', $sjogren_syndrome, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 25, $customerId, 'Do your low-lying eyelids interfere with any day-to-day functions?', $low_lying_eyelids, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 26, $customerId, 'Do you have unwanted facial hair?', $unwanted_hair, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 27, $customerId, 'Have you used Vaniqa in the past?', $past_use_vaniqa, '1fadfasdfs', '1', 'm1-data','".$updateDate."'),
                        ( 28, $customerId, 'Other hair removal methods used', $other_hair_removal, '1fadfasdfs', '1', 'm1-data','".$updateDate."')

                    ";

        $mysqli2->query($insertQuery);
        $count++;
        echo PHP_EOL."Inserting Data For Customer ID ".$customerId;
        echo PHP_EOL."<br>";

    }

    else{
        echo PHP_EOL." Customer Available With ID ".$customerId;
        echo PHP_EOL."<br>";
    }
}

$mysqli1->close();
$mysqli2->close();

echo "<br>";
echo PHP_EOL.$count." New Record's Inserted Successfully"; exit;
