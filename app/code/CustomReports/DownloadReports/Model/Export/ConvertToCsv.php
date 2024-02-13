<?php

namespace CustomReports\DownloadReports\Model\Export; 
 
use \Magento\Ui\Model\Export\ConvertToCsv as DefauleConvertToCsv; 
 
class ConvertToCsv extends DefauleConvertToCsv 
{ 
 
 /** 
 * Returns CSV file 
 * 
 * @return array 
 * @throws LocalizedException 
 */ 
 public function getCsvFile() 
 { 
 $component = $this->filter->getComponent(); 
 
 $name = md5(microtime()); 
 $file = 'export/'. $component->getName() . $name . '.csv'; 
 
 $this->filter->prepareComponent($component); 
 $this->filter->applySelectionOnTargetProvider(); 
 $dataProvider = $component->getContext()->getDataProvider(); 
 $fields = $this->metadataProvider->getFields($component); 
 $options = $this->metadataProvider->getOptions(); 
 
 $this->directory->create('export'); 
 $stream = $this->directory->openFile($file, 'w+'); 
 $stream->lock(); 
 $stream->writeCsv($this->metadataProvider->getHeaders($component)); 
 $i = 1; 
 $searchCriteria = $dataProvider->getSearchCriteria() 
 ->setCurrentPage($i) 
 ->setPageSize($this->pageSize); 
 $totalCount = (int) $dataProvider->getSearchResult()->getTotalCount(); 
 while ($totalCount > 0) { 
 $items = $dataProvider->getSearchResult()->getItems(); 
 foreach ($items as $item) { 
 $this->metadataProvider->convertDate($item, $component->getName()); 
 $stream->writeCsv($this->metadataProvider->getRowData($item, $fields, $options)); 
 } 
 $searchCriteria->setCurrentPage(++$i); 
 $totalCount = $totalCount - $this->pageSize; 
 } 
 $total = $dataProvider->getTotalRow($searchCriteria); 
 $writeTotal = []; 
 foreach ($fields as $column) { 
 $writeTotal[] =$total[$column]; 
 } 
 $stream->writeCsv($writeTotal); 
 
 $stream->unlock(); 
 $stream->close(); 
 
 return [ 
 'type' => 'filename', 
 'value' => $file, 
 'rm' => true // can delete file after use 
 ]; 
 } 
}