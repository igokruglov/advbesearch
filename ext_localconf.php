<?php
if (!defined("TYPO3_MODE")) {
    die ("Access denied.");
}

if (TYPO3_MODE == 'BE') {
    $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['sysext/sys_action/class.tx_sysaction.php'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'class.ux_tx_sysaction.php';

}

?>
