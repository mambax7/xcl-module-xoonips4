<?php

require_once XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/class/viewtype/ViewType.class.php';

class Xoonips_ViewTypeKana extends Xoonips_ViewType
{
    public function setTemplate()
    {
        $this->template = $this->dirname.'_viewtype_kana.html';
    }

    public function getInputView($field, $value, $groupLoopId)
    {
        $fieldName = $this->getFieldName($field, $groupLoopId);
        $this->getXoopsTpl()->assign('viewType', 'input');
        $this->getXoopsTpl()->assign('len', $field->getLen());
        $this->getXoopsTpl()->assign('fieldName', $fieldName);
        $this->getXoopsTpl()->assign('value', $value);
        $this->getXoopsTpl()->assign('dirname', $this->dirname);

        return $this->getXoopsTpl()->fetch('db:'.$this->template);
    }

    public function getDisplayView($field, $value, $groupLoopId)
    {
        $fieldName = $this->getFieldName($field, $groupLoopId);
        $this->getXoopsTpl()->assign('viewType', 'confirm');
        $this->getXoopsTpl()->assign('fieldName', $fieldName);
        $this->getXoopsTpl()->assign('value', $value);

        return $this->getXoopsTpl()->fetch('db:'.$this->template);
    }

    public function getEditView($field, $value, $groupLoopId)
    {
        return $this->getInputView($field, $value, $groupLoopId);
    }

    public function getDetailDisplayView($field, $value, $display)
    {
        $this->getXoopsTpl()->assign('viewType', 'detail');
        $this->getXoopsTpl()->assign('value', $value);

        return $this->getXoopsTpl()->fetch('db:'.$this->template);
    }

    public function getSearchView($field, $groupLoopId)
    {
        $fieldName = $this->getFieldName($field, $groupLoopId);
        $this->getXoopsTpl()->assign('viewType', 'search');
        $this->getXoopsTpl()->assign('len', $field->getLen());
        $this->getXoopsTpl()->assign('fieldName', $fieldName);

        return $this->getXoopsTpl()->fetch('db:'.$this->template);
    }

    public function doRegistry($field, &$data, &$sqlStrings, $groupLoopId)
    {
        $tableName = $field->getTableName();
        $tableData;
        $groupData;
        $columnData;
        if ($tableName == $this->dirname.'_item_title') {
            $columnName = $field->getId();
            $value = $data[$this->getFieldName($field, $groupLoopId)];
            if (isset($sqlStrings[$tableName])) {
                $tableData = &$sqlStrings[$tableName];
            } else {
                $tableData = array();
                $sqlStrings[$tableName] = &$tableData;
            }

            if (isset($tableData[$columnName])) {
                $columnData = &$tableData[$columnName];
            } else {
                $columnData = array();
                $tableData[$columnName] = &$columnData;
            }
            $columnData[] = $field->getDataType()->convertSQLStr(trim($value));
        } else {
            $columnName = $field->getColumnName();
            $value = $this->getData($field, $data, $groupLoopId);
            $tableData;
            $columnData;

            if (isset($sqlStrings[$tableName])) {
                $tableData = &$sqlStrings[$tableName];
            } else {
                $tableData = array();
                $sqlStrings[$tableName] = &$tableData;
            }
            if (strpos($tableName, '_extend') !== false) {
                $groupid = $field->getFieldGroupId();
                if (isset($tableData[$groupid])) {
                    $groupData = &$tableData[$groupid];
                } else {
                    $groupData = array();
                    $tableData[$groupid] = &$groupData;
                }

                if (isset($groupData[$columnName])) {
                    $columnData = &$groupData[$columnName];
                } else {
                    $columnData = array();
                    $groupData[$columnName] = &$columnData;
                }
            } else {
                if (isset($tableData[$columnName])) {
                    $columnData = &$tableData[$columnName];
                } else {
                    $columnData = array();
                    $tableData[$columnName] = &$columnData;
                }
            }

            $columnData[] = $field->getDataType()->convertSQLStr($value);
        }
    }
}
