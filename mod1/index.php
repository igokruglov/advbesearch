<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 1999-2005 Kasper Skaarhoj (kasperYYYY@typo3.com)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * Module: Database integrity check
 *
 * This module lets you check if all pages and the records relate properly to each other
 *
 * @author Kasper Sk�rh�j <kasperYYYY@typo3.com>
 */


unset($MCONF);
require('conf.php');
require($BACK_PATH . 'init.php');
require_once('tx_johadvbesearch_querygenerator.php');

require_once('tx_johadvbesearch.php');
require_once('tx_johadvbesearch_t3libxml.php');



$BE_USER->modAccess($MCONF, 1);


// **************************
// Setting english (ONLY!) LOCAL_LANG
// **************************
$LOCAL_LANG = Array(
    'default' => Array(
        'tables' => 'Tables:',
        'fixLostRecord' => Array('target'=>'Click to move this lost record to rootlevel (pid=0)'),
        'doktype' => Array('target'=>'Document types:'),
        'pages' => Array('target'=>'Pages:'),
        'total_pages' => Array('target'=>'Total number of pages:'),
        'deleted_pages' => Array('target'=>'Marked-deleted pages:'),
        'hidden_pages' => Array('target'=>'Hidden pages:'),
        'relations' => Array('target'=>'Relations:'),
        'relations_description' => Array('target'=>'This will analyse the content of the tables and check if there are \'empty\' relations between records or if files are missing from their expected position.'),

        'files_many_ref' => Array('target'=>'Files referenced from more than one record:'),
        'files_no_ref' => Array('target'=>'Files with no references at all (delete them!):'),
        'files_no_file' => Array('target'=>'Missing files:'),
        'select_db' => Array('target'=>'Select fields:'),
        'group_db' => Array('target'=>'Group fields:'),

        'tree' => 'The Page Tree:',
        'tree_description' => Array('target'=>'This shows all pages in the system in one large tree. Beware that this will probably result in a very long document which will also take some time for the server to compute!'),
        'records' => Array('target'=>'Records Statistics:'),
        'records_description' => Array('target'=>'This shows some statistics for the records in the database. This runs through the entire page-tree and therefore it will also load the server heavily!'),
        'search' => Array('target'=>$GLOBALS['LANG']->sL('LLL:EXT:joh_advbesearch/mod1/locallang_mod.php:searchwholedatabase')),
        'search_description' => Array('target'=>'This searches through all database tables and records for a text string.'),
        'filesearch' => Array('target'=>'Search all filenames for pattern'),
        'filesearch_description' => Array('target'=>'Will search recursively for filenames in the PATH_site (subdirs to the website path) matching a certain regex pattern.'),
        'title' => Array('target'=>'Search predefined views')
    )
);


// ***************************
// Script Classes
// ***************************
class tx_johadvbesearch_index
{
    var $MCONF = array();
    var $MOD_MENU = array();
    var $MOD_SETTINGS = array();
    var $doc;

    var $content;
    var $menu;

    /**
     * @return [type]  ...
     */
    function init()
    {
        global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;
        $this->MCONF = $GLOBALS['MCONF'];
        $this->menuConfig();

        $this->doc = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('mediumDoc');
        $this->doc->form = '<form action="" method="POST">';
        $this->doc->backPath = $BACK_PATH;
        // JavaScript
        $this->doc->JScode = '
				<script language="javascript" type="text/javascript">
				script_ended = 0;
				function jumpToUrl(URL) {
				document.location = URL;
				}
				</script>
				';
        $this->doc->tableLayout = Array(
            'defRow' => Array(
                '0' => Array('<TD valign="top">', '</td>'),
                '1' => Array('<TD valign="top">', '</td>'),
                'defCol' => Array(
                    '<TD><img src="' . $this->doc->backPath . 'clear.gif" width="15" height="1"></td><td valign="top">',
                    '</td>'
                )
            )
        );
   }

    /**
     * @return [type]  ...
     */
    function menuConfig()
    {
        global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;

        // MENU-ITEMS:
        // If array, then it's a selector box menu
        // If empty string it's just a variable, that'll be saved.
        // Values NOT in this array will not be saved in the settings-array for the module.
        $this->MOD_MENU = array(
        'function' => array(
                //'search' => $GLOBALS['LANG']->sL('LLL:EXT:joh_advbesearch/mod1/locallang_mod.php:fullsearch'),
                'search' => $GLOBALS['LANG']->sL('LLL:EXT:joh_advbesearch/mod1/locallang_mod.php:advquery'),
                0 => '[ MENU ]',
            ),
            'search' => array(
                'query' => $GLOBALS['LANG']->sL('LLL:EXT:joh_advbesearch/mod1/locallang_mod.php:advquery')
            ),  
  /*
            'function' => array(
                'search' => 'Advanced search',//$GLOBALS['LANG']->sL('LLL:EXT:joh_advbesearch/mod1/locallang_mod.php:fullsearch'),
                 0 => 'Open search module',
            ),
            'search' => array(
                //'query' => '',$GLOBALS['LANG']->sL('LLL:EXT:joh_advbesearch/mod1/locallang_mod.php:advquery'),
                                        
            ),
*/
            'search_query_smallparts' => '',
            'search_result_labels' => '',
            'labels_noprefix' => '',
            'options_sortlabel' => '',
            'show_deleted' => '',

            'queryConfig' => '', // Current query
            'queryTable' => '', // Current table
            'queryFields' => '', // Current tableFields
            'queryLimit' => '', // Current limit
            'queryOrder' => '', // Current Order field
            'queryOrderDesc' => '', // Current Order field descending flag
            'queryOrder2' => '', // Current Order2 field
            'queryOrder2Desc' => '', // Current Order2 field descending flag
            'queryGroup' => '', // Current Group field

            'storeArray' => '', // Used to store the available Query config memory banks
            'storeQueryConfigs' => '', // Used to store the available Query configs in memory

            'search_query_makeQuery' => array(
                'all' => $GLOBALS['LANG']->sL('LLL:EXT:joh_advbesearch/mod1/locallang_mod.php:selectrecords'),
                'count' => $GLOBALS['LANG']->sL('LLL:EXT:joh_advbesearch/mod1/locallang_mod.php:countresults'),
                'explain' => $GLOBALS['LANG']->sL('LLL:EXT:joh_advbesearch/mod1/locallang_mod.php:explainquery'),
                'csv' => $GLOBALS['LANG']->sL('LLL:EXT:joh_advbesearch/mod1/locallang_mod.php:csvexport'),
                'xml' => $GLOBALS['LANG']->sL('LLL:EXT:joh_advbesearch/mod1/locallang_mod.php:xmlexport')
            ),
            'sword' => ''
        );
        // CLEANSE SETTINGS
        $this->OLD_MOD_SETTINGS = t3lib_BEfunc::getModuleData($this->MOD_MENU, '', $this->MCONF['name'], 'ses');
        $this->MOD_SETTINGS = t3lib_BEfunc::getModuleData($this->MOD_MENU, \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('SET'), $this->MCONF['name'],
            'ses');
        if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('queryConfig')) {
            $qA = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('queryConfig');
            $this->MOD_SETTINGS = t3lib_BEfunc::getModuleData($this->MOD_MENU, array('queryConfig' => serialize($qA)),
                $this->MCONF['name'], 'ses');
        }
        $addConditionCheck = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('qG_ins');
        foreach ($this->OLD_MOD_SETTINGS as $key => $val) {
            if (substr($key, 0,
                    5) == 'query' && $this->MOD_SETTINGS[$key] != $val && $key != 'queryLimit' && $key != 'use_listview'
            ) {
                $setLimitToStart = 1;
                if ($key == 'queryTable' && !$addConditionCheck) {
                    $this->MOD_SETTINGS['queryConfig'] = '';
                }
            }
            if ($key == 'queryTable' && $this->MOD_SETTINGS[$key] != $val) {
                $this->MOD_SETTINGS['queryFields'] = '';
            }
        }
        if ($setLimitToStart) {
            $currentLimit = explode(',', $this->MOD_SETTINGS['queryLimit']);
            if ($currentLimit[1]) {
                $this->MOD_SETTINGS['queryLimit'] = '0,' . $currentLimit[1];
            } else {
                $this->MOD_SETTINGS['queryLimit'] = '0';
            }
            $this->MOD_SETTINGS = t3lib_BEfunc::getModuleData($this->MOD_MENU, $this->MOD_SETTINGS,
                $this->MCONF['name'], 'ses');
        }
    }

    /**
     * @return [type]  ...
     */
    function main()
    {
        global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;
        
       //exit;
        $this->content .= $this->doc->startPage($LANG->getLL('title'));
        
         if (!$GLOBALS['BE_USER']->userTS['advBESearch.']['disableTopMenu']) {
            $this->menu = t3lib_BEfunc::getFuncMenu(0, 'SET[function]', $this->MOD_SETTINGS['function'],
                        $this->MOD_MENU['function']);
              
        }
        switch ($this->MOD_SETTINGS['function']) {
            case 'search':
                $this->func_search();
                break;
            default:
                $this->func_default();
                break;
        }

        if ($BE_USER->mayMakeShortcut()) {
            $this->content .= $this->doc->spacer(20) . $this->doc->section('',
                    $this->doc->makeShortcutIcon('', 'function,search,search_query_makeQuery', $this->MCONF['name']));
        }
    }

    /**
     * @return [type]  ...
     */
    function printContent()
    {

        $this->content .= $this->doc->endPage();
        echo $this->content;
    }

    /**
     * @return [type]  ...
     */
    function func_search()
    {
        global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;

        $fullsearch = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_johadvbesearch');
        //$this->content .= $this->doc->header($LANG->getLL('search'));
        $this->content .= $this->doc->header($GLOBALS['LANG']->sL('LLL:EXT:joh_advbesearch/mod1/locallang_mod.php:title'));
        $this->content .= $this->doc->spacer(5);

        if (!$GLOBALS['BE_USER']->userTS['advBESearch.']['disableTopMenu']) {
            $menu2 = t3lib_BEfunc::getFuncMenu(0, 'SET[search]', $this->MOD_SETTINGS['search'],
                $this->MOD_MENU['search']);
        }
        
        if ($this->MOD_SETTINGS['search'] == 'query' && !$GLOBALS['BE_USER']->userTS['advBESearch.']['disableTopMenu']) {
             $menu2 .= t3lib_BEfunc::getFuncMenu(0, 'SET[search_query_makeQuery]',
                    $this->MOD_SETTINGS['search_query_makeQuery'],
                    $this->MOD_MENU['search_query_makeQuery']) . '&nbsp;';
            if (!$GLOBALS['BE_USER']->userTS['advBESearch.']['disableTopCheckboxes']) {
                $menu2 .= '<br />';
                //$menu2 .= t3lib_BEfunc::getFuncCheck($GLOBALS['SOBE']->id, 'SET[search_query_smallparts]', $this->MOD_SETTINGS['search_query_smallparts']).'&nbsp;'.$GLOBALS['LANG']->sL('LLL:EXT:joh_advbesearch/mod1/locallang_mod.php:showsqlparts').'<br />';
                //$menu2 .= t3lib_BEfunc::getFuncCheck($GLOBALS['SOBE']->id, 'SET[search_result_labels]', $this->MOD_SETTINGS['search_result_labels']).'&nbsp;'.$GLOBALS['LANG']->sL('LLL:EXT:joh_advbesearch/mod1/locallang_mod.php:formattedstrings').'<br />';
                //$menu2 .= t3lib_BEfunc::getFuncCheck($GLOBALS['SOBE']->id, 'SET[labels_noprefix]', $this->MOD_SETTINGS['labels_noprefix']).'&nbsp;'.$GLOBALS['LANG']->sL('LLL:EXT:joh_advbesearch/mod1/locallang_mod.php:nooriginalvalues').'<br />';
                //$menu2 .= t3lib_BEfunc::getFuncCheck($GLOBALS['SOBE']->id, 'SET[options_sortlabel]', $this->MOD_SETTINGS['options_sortlabel']).'&nbsp;'.$GLOBALS['LANG']->sL('LLL:EXT:joh_advbesearch/mod1/locallang_mod.php:sortbylabel').'<br />';
                //$menu2 .= t3lib_BEfunc::getFuncCheck($GLOBALS['SOBE']->id, 'SET[show_deleted]', $this->MOD_SETTINGS['show_deleted']).'&nbsp;'.$GLOBALS['LANG']->sL('LLL:EXT:joh_advbesearch/mod1/locallang_mod.php:showdeleted');
            }
        }
        $this->content .= $this->doc->section('', $this->menu);//$this->doc->divider(5);
        $this->content .= $this->doc->section('', $menu2) . $this->doc->spacer(10);
        
        switch ($this->MOD_SETTINGS['search']) {
            case 'query':
                $this->content .= $fullsearch->queryMaker();
                break;
            case 'raw':
            default:
                $this->content .= $this->doc->section('Search options:', $fullsearch->form(), 0, 1);
                $this->content .= $this->doc->section('Result:', $fullsearch->search(), 0, 1);
                break;
        }

    }

    /**
     * Menu
     *
     * @return [type]  ...
     */
    function func_default()
    {
        global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;

        $this->content .= $this->doc->header($LANG->getLL('title'));
        $this->content .= $this->doc->spacer(5);
        $this->content .= $this->doc->section('', $this->menu);
        $this->content .= $this->doc->section('<A HREF="index.php?SET[function]=search">' . $LANG->getLL('search') . '</a>',
            $LANG->getLL('search_description'), 1, 1, 0, 1);
        $this->content .= $this->doc->spacer(50);
    }
}

// Include extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/joh_advbesearch/mod1/index.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/joh_advbesearch/mod1/index.php']);
}


// Make instance:
$SOBE = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_johadvbesearch_index');
$SOBE->init();
$SOBE->main();
$SOBE->printContent();
?>
