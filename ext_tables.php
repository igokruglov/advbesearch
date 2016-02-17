<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

if (TYPO3_MODE == "BE") {

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule("web", "txjohadvbesearchM1", "", \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . "mod1/");
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::insertModuleFunction(
        'user_task',
        'tx_sysaction',
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'class.ux_tx_sysaction.php',
        'LLL:EXT:sys_action/locallang_tca.php:tx_sys_action'
    );

}

$TCA['USERsearch'] = array(
    'ctrl' => array(
        'title' => 'USER search',
        'label' => 'address',
        'delete' => 'deleted',
        'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'tca.php'
    )
);
?>