<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\NotificationSchedule\Cron;

class NotificationSchedule
{

    protected $logger;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Renewal\NotificationSchedule\Model\NotificationScheduleFactory $scheduleFactory,
        \Renewal\NotificationReport\Model\NotificationReportFactory $reportFactory,
        \ConsultOnly\ConsultOnlyCollection\Model\ConsultOnlyFactory  $consultonlyFactory,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->scheduleFactory      = $scheduleFactory;
        $this->reportFactory        = $reportFactory;
        $this->consultonlyFactory   = $consultonlyFactory;
        $this->logger               = $logger;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $this->logger->addInfo("Cronjob NotificationSchedule is start executed.");

        try {

            $now    = new \DateTime();
            $toDate = $now->format('Y-m-d');

            $scheduleCollections = $this->scheduleFactory->create()->getCollection();
            if($scheduleCollections->count()>0){
                foreach ($scheduleCollections AS $scheduleArr) {
                    $rxscheduleId       = $scheduleArr['id'];
                    $intervel_days      = $scheduleArr['intervel_days'];
                    $Experation_days    = $scheduleArr['Experation_days'];
                    $next_run           = $scheduleArr['next_run'];
                    $last_run           = $scheduleArr['last_run'];
                }
            }

            if($rxscheduleId){

                if($toDate == $next_run){
                    $now    = new \DateTime();
                    $dateUpto = $now->modify("+".$Experation_days." days")->format('Y-m-d');

                    $consultonlyArr = $this->consultonlyFactory->create()
                            ->getCollection()
                            ->addFieldToFilter('expiration_date', array('gteq' => $toDate))
                            ->addFieldToFilter('expiration_date', array('lteq' => $dateUpto));
                    
                    if($consultonlyArr->count()>0){
                        foreach ($consultonlyArr AS $consultonly) {
                            // save data to rxschedule_log table 
                            $reports = $this->reportFactory->create();
                            $reports->setData('customer_id',$consultonly['customer_id'])
                                ->setData('vh_prescription_id',$consultonly['vh_prescription_id'])
                                ->setData('prescription_name',$consultonly['prescription_name'])
                                ->setData('start_date',$consultonly['start_date'])
                                ->setData('expiration_date',$consultonly['expiration_date'])
                                ->setData('vh_status',$consultonly['vh_status'])
                                ->setData('consultation_type',$consultonly['consultation_type']);
                            //$reports->save(); 
                            if($reports->save()){
                                $saveConsultonly = true;
                            }
                        }
                        if($saveConsultonly){
                            $now    = new \DateTime();
                            $nextRun = $now->modify("+".$intervel_days." days")->format('Y-m-d');
                            $updateNextNLastRun = array('last_run' => $toDate, 'next_run' => $nextRun);
                            //echo "<pre>"; print_r($updateNextNLastRun);
                            $scheduleUpdate = $this->scheduleFactory->create()
                                        ->getCollection()
                                        ->addFieldToFilter("id", $rxscheduleId)
                                        ->getFirstItem();
                            $scheduleUpdate->addData($updateNextNLastRun);
                            $scheduleUpdate->save();
                        }


                    } // END IF consultonlyArr

                }
            }

        } catch (\Exception $e) {

        }

        $this->logger->addInfo("Cronjob NotificationSchedule is end executed.");
        
    }
}

