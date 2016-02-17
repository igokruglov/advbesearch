<?php

########################################################################
# Extension Manager/Repository config file for ext: "joh_advbesearch"
#
# Auto generated 15-09-2009 11:09
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Advanced DB Search',
    'description' => 'Search predefined views using a query wizard',
    'category' => 'module',
    'shy' => 'false',
    'version' => '0.12.0',
    'dependencies' => '',
    'conflicts' => '',
    'priority' => '',
    'loadOrder' => '',
    'module' => 'mod1',
    'state' => 'beta',
    'uploadfolder' => 'false',
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 'false',
    'lockType' => '',
    'author' => 'JoH',
    'author_email' => 'info@cybercraft.de',
    'author_company' => '',
    'CGLcompliance' => '',
    'CGLcompliance_note' => '',
    'constraints' => array(
        'depends' => array(
            'php' => '5.3.0',
            'typo3' => '6.2.0-6.2.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
    '_md5_values_when_last_written' => 'a:22:{s:9:"ChangeLog";s:4:"28c7";s:10:"README.txt";s:4:"ee2d";s:25:"class.ux_tx_sysaction.php";s:4:"6121";s:12:"ext_icon.gif";s:4:"3b11";s:17:"ext_localconf.php";s:4:"20ff";s:14:"ext_tables.php";s:4:"ae8f";s:16:"locallang_db.php";s:4:"4a93";s:19:"doc/wizard_form.dat";s:4:"1195";s:20:"doc/wizard_form.html";s:4:"d219";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"2c08";s:14:"mod1/index.php";s:4:"81ee";s:18:"mod1/locallang.php";s:4:"6e46";s:22:"mod1/locallang_mod.php";s:4:"6955";s:19:"mod1/moduleicon.gif";s:4:"3b11";s:24:"mod1/querygenerator.diff";s:4:"828c";s:26:"mod1/tx_johadvbesearch.php";s:4:"504e";s:41:"mod1/tx_johadvbesearch_querygenerator.php";s:4:"3864";s:45:"modfunc1/class.tx_johadvbesearch_modfunc1.php";s:4:"9773";s:22:"modfunc1/locallang.php";s:4:"59bc";s:16:"gfx/undelete.gif";s:4:"24ff";s:25:"gfx/undelete_and_edit.gif";s:4:"760c";}',
);

?>