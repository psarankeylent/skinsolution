if ($data) {
$id = $this->getRequest()->getParam('id');

$model = $this->_objectManager->create(\Backend\Photos\Model\Photos::class)->load($id);
if (!$model->getId() && $id) {
$this->messageManager->addErrorMessage(__('This Photos no longer exists.'));
return $resultRedirect->setPath('*/*/');
}

$model->setData($data);

try {
$model->save();
$this->messageManager->addSuccessMessage(__('You saved the Photos.'));
$this->dataPersistor->clear('backend_Photos');

if ($this->getRequest()->getParam('back')) {
return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
}
return $resultRedirect->setPath('*/*/');
} catch (LocalizedException $e) {
$this->messageManager->addErrorMessage($e->getMessage());
} catch (\Exception $e) {
$this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Photos.'));
}

$this->dataPersistor->set('backend_Photos', $data);
return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
}<?php
