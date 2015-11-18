<?php

final class PhabricatorEditEngineConfigurationEditor
  extends PhabricatorApplicationTransactionEditor {

  public function getEditorApplicationClass() {
    return 'PhabricatorTransactionsApplication';
  }

  public function getEditorObjectsDescription() {
    return pht('Edit Configurations');
  }

  public function getTransactionTypes() {
    $types = parent::getTransactionTypes();

    $types[] = PhabricatorTransactions::TYPE_VIEW_POLICY;
    $types[] = PhabricatorTransactions::TYPE_EDIT_POLICY;

    $types[] = PhabricatorEditEngineConfigurationTransaction::TYPE_NAME;
    $types[] = PhabricatorEditEngineConfigurationTransaction::TYPE_PREAMBLE;
    $types[] = PhabricatorEditEngineConfigurationTransaction::TYPE_ORDER;
    $types[] = PhabricatorEditEngineConfigurationTransaction::TYPE_DEFAULT;

    return $types;
  }

  protected function validateTransaction(
    PhabricatorLiskDAO $object,
    $type,
    array $xactions) {

    $errors = parent::validateTransaction($object, $type, $xactions);
    switch ($type) {
      case PhabricatorEditEngineConfigurationTransaction::TYPE_NAME:
        $missing = $this->validateIsEmptyTextField(
          $object->getName(),
          $xactions);

        if ($missing) {
          $error = new PhabricatorApplicationTransactionValidationError(
            $type,
            pht('Required'),
            pht('Form name is required.'),
            nonempty(last($xactions), null));

          $error->setIsMissingFieldError(true);
          $errors[] = $error;
        }
        break;
    }

    return $errors;
  }

  protected function getCustomTransactionOldValue(
    PhabricatorLiskDAO $object,
    PhabricatorApplicationTransaction $xaction) {

    switch ($xaction->getTransactionType()) {
      case PhabricatorEditEngineConfigurationTransaction::TYPE_NAME:
        return $object->getName();
      case PhabricatorEditEngineConfigurationTransaction::TYPE_PREAMBLE;
        return $object->getPreamble();
      case PhabricatorEditEngineConfigurationTransaction::TYPE_ORDER:
        return $object->getFieldOrder();
      case PhabricatorEditEngineConfigurationTransaction::TYPE_DEFAULT:
        $field_key = $xaction->getMetadataValue('field.key');
        return $object->getFieldDefault($field_key);
    }
  }

  protected function getCustomTransactionNewValue(
    PhabricatorLiskDAO $object,
    PhabricatorApplicationTransaction $xaction) {

    switch ($xaction->getTransactionType()) {
      case PhabricatorEditEngineConfigurationTransaction::TYPE_NAME:
      case PhabricatorEditEngineConfigurationTransaction::TYPE_PREAMBLE;
      case PhabricatorEditEngineConfigurationTransaction::TYPE_ORDER:
      case PhabricatorEditEngineConfigurationTransaction::TYPE_DEFAULT:
        return $xaction->getNewValue();
    }
  }

  protected function applyCustomInternalTransaction(
    PhabricatorLiskDAO $object,
    PhabricatorApplicationTransaction $xaction) {

    switch ($xaction->getTransactionType()) {
      case PhabricatorEditEngineConfigurationTransaction::TYPE_NAME:
        $object->setName($xaction->getNewValue());
        return;
      case PhabricatorEditEngineConfigurationTransaction::TYPE_PREAMBLE;
        $object->setPreamble($xaction->getNewValue());
        return;
      case PhabricatorEditEngineConfigurationTransaction::TYPE_ORDER:
        $object->setFieldOrder($xaction->getNewValue());
        return;
      case PhabricatorEditEngineConfigurationTransaction::TYPE_DEFAULT:
        $field_key = $xaction->getMetadataValue('field.key');
        $object->setFieldDefault($field_key, $xaction->getNewValue());
        return;
    }

    return parent::applyCustomInternalTransaction($object, $xaction);
  }

  protected function applyCustomExternalTransaction(
    PhabricatorLiskDAO $object,
    PhabricatorApplicationTransaction $xaction) {

    switch ($xaction->getTransactionType()) {
      case PhabricatorEditEngineConfigurationTransaction::TYPE_NAME:
      case PhabricatorEditEngineConfigurationTransaction::TYPE_PREAMBLE;
      case PhabricatorEditEngineConfigurationTransaction::TYPE_ORDER;
      case PhabricatorEditEngineConfigurationTransaction::TYPE_DEFAULT:
        return;
    }

    return parent::applyCustomExternalTransaction($object, $xaction);
  }

}