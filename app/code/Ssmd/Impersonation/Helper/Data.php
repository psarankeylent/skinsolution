<?php

namespace Ssmd\Impersonation\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
   const MODULE_ENABLE = "impersonation/general/enable";
   const IMPERSONATION_WEBSITE = "impersonation/general/impersonation_website";
   const ADMIN_TOKEN_TIMEOUT = "impersonation/general/admin_token_timout";

   public function getDefaultConfig($path)
   {
       return $this->scopeConfig->getValue($path, \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
   }

   public function isModuleEnabled()
   {
       return (bool) $this->getDefaultConfig(self::MODULE_ENABLE);
   }

   public function getImpersonationWebsite()
   {
       return $this->getDefaultConfig(self::IMPERSONATION_WEBSITE);
   }
   public function checkAdminTokenTimeout()
   {
       $timeout = $this->getDefaultConfig(self::ADMIN_TOKEN_TIMEOUT);
       return $timeout;
   }
 }