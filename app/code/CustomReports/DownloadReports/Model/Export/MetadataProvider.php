<?php

namespace CustomReports\DownloadReports\Model\Export; 
 
 
use Magento\Framework\View\Element\UiComponentInterface; 
use Magento\Ui\Component\MassAction\Filter; 
use Magento\Framework\Locale\ResolverInterface; 
use Magento\Framework\Stdlib\DateTime\TimezoneInterface; 
 
class MetadataProvider extends \Magento\Ui\Model\Export\MetadataProvider 
{ 
 /** 
 * @var \Magento\Ui\Model\BookmarkManagement 
 */ 
 protected $_bookmarkManagement; 
 
 /** 
 * MetadataProvider constructor. 
 * @param Filter $filter 
 * @param TimezoneInterface $localeDate 
 * @param ResolverInterface $localeResolver 
 * @param \Magento\Ui\Model\BookmarkManagement $bookmarkManagement 
 * @param string $dateFormat 
 * @param array $data 
 */ 
 public function __construct( 
 Filter $filter, 
 TimezoneInterface $localeDate, 
 ResolverInterface $localeResolver, 
 \Magento\Ui\Model\BookmarkManagement $bookmarkManagement, 
 $dateFormat = 'M j, Y H:i:s A', 
 array $data = []) 
 { 
 parent::__construct($filter, $localeDate, $localeResolver, $dateFormat, $data); 
 $this->_bookmarkManagement = $bookmarkManagement; 
 } 
 
 protected function getActiveColumns($component){ 
 $bookmark = $this->_bookmarkManagement->getByIdentifierNamespace('current', $component->getName()); 
 
 $config = $bookmark->getConfig(); 
 $columns = $config['current']['columns']; 
 $_activeColumns = []; 
 foreach ($columns as $column => $config){ 
 if(true === $config['visible'] && $column != 'ids'){ 
 $_activeColumns[] = $column; 
 } 
 } 
 return $_activeColumns; 
 } 
 /** 
 * @param UiComponentInterface $component 
 * @return UiComponentInterface[] 
 * @throws \Exception 
 */ 
 protected function getColumns(UiComponentInterface $component) 
 { 
 if (!isset($this->columns[$component->getName()])) { 
 
 $activeColumns = $this->getActiveColumns($component); 
 
 $columns = $this->getColumnsComponent($component); 
 foreach ($columns->getChildComponents() as $column) { 
 if ($column->getData('config/label') && $column->getData('config/dataType') !== 'actions') { 
 if(in_array($column->getName(), $activeColumns)) { 
 $this->columns[$component->getName()][$column->getName()] = $column; 
 } 
 } 
 } 
 } 
 return $this->columns[$component->getName()]; 
 } 
} 