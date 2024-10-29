<?php
/* Copyright (C) 2004-2018  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2018-2019  Nicolas ZABOURI         <info@inovea-conseil.com>
 * Copyright (C) 2019-2024  Frédéric France         <frederic.france@free.fr>
 * Copyright (C) 2024 Francis Appels <francis.appels@z-application.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup   debweb     Module DebWeb
 *  \brief      DebWeb module descriptor.
 *
 *  \file       htdocs/debweb/core/modules/modDebWeb.class.php
 *  \ingroup    debweb
 *  \brief      Description and activation file for module DebWeb
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';


/**
 *  Description and activation class for module DebWeb
 */
class modDebWeb extends DolibarrModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $langs, $conf;

		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 202252; // TODO Go on page https://wiki.dolibarr.org/index.php/List_of_modules_id to reserve an id number for your module

		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'debweb';

		// Family can be 'base' (core modules),'crm','financial','hr','projects','products','ecm','technic' (transverse modules),'interface' (link with external tools),'other','...'
		// It is used to group modules by family in module setup page
		$this->family = "other";

		// Module position in the family on 2 digits ('01', '10', '20', ...)
		$this->module_position = '90';

		// Gives the possibility for the module, to provide his own family info and position of this family (Overwrite $this->family and $this->module_position. Avoid this)
		//$this->familyinfo = array('myownfamily' => array('position' => '01', 'label' => $langs->trans("MyOwnFamily")));
		// Module label (no space allowed), used if translation string 'ModuleDebWebName' not found (DebWeb is name of module).
		$this->name = preg_replace('/^mod/i', '', get_class($this));

		// DESCRIPTION_FLAG
		// Module description, used if translation string 'ModuleDebWebDesc' not found (DebWeb is name of module).
		$this->description = "DebWebDescription";
		// Used only if file README.md and README-LL.md not found.
		$this->descriptionlong = "DebWebDescription";

		// Author
		$this->editor_name = 'Z-Application';
		$this->editor_url = 'http://www.z-application.com';		// Must be an external online web site
		$this->editor_squarred_logo = '';					// Must be image filename into the module/img directory followed with @modulename. Example: 'myimage.png@debweb'

		// Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated', 'experimental_deprecated' or a version string like 'x.y.z'
		$this->version = '1.0';
		// Url to the file with your last numberversion of this module
		//$this->url_last_version = 'http://www.example.com/versionmodule.txt';

		// Key used in llx_const table to save module status enabled/disabled (where DEBWEB is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);

		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		// To use a supported fa-xxx css style of font awesome, use this->picto='xxx'
		$this->picto = 'fa-globe';

		// Define some features supported by module (triggers, login, substitutions, menus, css, etc...)
		$this->module_parts = array(
			// Set this to 1 if module has its own trigger directory (core/triggers)
			'triggers' => 0,
			// Set this to 1 if module has its own login method file (core/login)
			'login' => 0,
			// Set this to 1 if module has its own substitution function file (core/substitutions)
			'substitutions' => 0,
			// Set this to 1 if module has its own menus handler directory (core/menus)
			'menus' => 0,
			// Set this to 1 if module overwrite template dir (core/tpl)
			'tpl' => 0,
			// Set this to 1 if module has its own barcode directory (core/modules/barcode)
			'barcode' => 0,
			// Set this to 1 if module has its own models directory (core/modules/xxx)
			'models' => 1,
			// Set this to 1 if module has its own printing directory (core/modules/printing)
			'printing' => 0,
			// Set this to 1 if module has its own theme directory (theme)
			'theme' => 0,
			// Set this to relative path of css file if module has its own css file
			'css' => array(
				//    '/debweb/css/debweb.css.php',
			),
			// Set this to relative path of js file if module must load a js on all pages
			'js' => array(
				//   '/debweb/js/debweb.js.php',
			),
			// Set here all hooks context managed by module. To find available hook context, make a "grep -r '>initHooks(' *" on source code. You can also set hook context to 'all'
			/* BEGIN MODULEBUILDER HOOKSCONTEXTS */
			'hooks' => array(
				//   'data' => array(
				//       'hookcontext1',
				//       'hookcontext2',
				//   ),
				//   'entity' => '0',
			),
			/* END MODULEBUILDER HOOKSCONTEXTS */
			// Set this to 1 if features of module are opened to external users
			'moduleforexternal' => 0,
			// Set this to 1 if the module provides a website template into doctemplates/websites/website_template-mytemplate
			'websitetemplates' => 0
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/debweb/temp","/debweb/subdir");
		$this->dirs = array("/debweb/temp");

		// Config pages. Put here list of php page, stored into debweb/admin directory, to use to setup module.
		$this->config_page_url = array("setup.php@debweb");

		// Dependencies
		// A condition to hide module
		$this->hidden = getDolGlobalInt('MODULE_DEBWEB_DISABLED'); // A condition to disable module;
		// List of module class names that must be enabled if this module is enabled. Example: array('always'=>array('modModuleToEnable1','modModuleToEnable2'), 'FR'=>array('modModuleToEnableFR')...)
		$this->depends = array();
		// List of module class names to disable if this one is disabled. Example: array('modModuleToDisable1', ...)
		$this->requiredby = array();
		// List of module class names this module is in conflict with. Example: array('modModuleToDisable1', ...)
		$this->conflictwith = array();

		// The language file dedicated to your module
		$this->langfiles = array("debweb@debweb");

		// Prerequisites
		$this->phpmin = array(7, 1); // Minimum version of PHP required by module
		$this->need_dolibarr_version = array(18, -3); // Minimum version of Dolibarr required by module
		$this->need_javascript_ajax = 0;

		// Messages at activation
		$this->warnings_activation = array(); // Warning to show when we activate module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
		$this->warnings_activation_ext = array(); // Warning to show when we activate an external module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
		//$this->automatic_activation = array('FR'=>'DebWebWasAutomaticallyActivatedBecauseOfYourCountryChoice');
		//$this->always_enabled = true;								// If true, can't be disabled

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(1 => array('DEBWEB_MYNEWCONST1', 'chaine', 'myvalue', 'This is a constant to add', 1),
		//                             2 => array('DEBWEB_MYNEWCONST2', 'chaine', 'myvalue', 'This is another constant to add', 0, 'current', 1)
		// );
		$this->const = array(1 => array('DEBWEB_DEBWEB_ADDON', 'chaine', 'mod_debweb_standard', 'Always standard number model', 1));

		// Some keys to add into the overwriting translation tables
		/*$this->overwrite_translation = array(
			'en_US:ParentCompany'=>'Parent company or reseller',
			'fr_FR:ParentCompany'=>'Maison mère ou revendeur'
		)*/

		if (!isModEnabled("debweb")) {
			$conf->debweb = new stdClass();
			$conf->debweb->enabled = 0;
		}

		// Array to add new pages in new tabs
		/* BEGIN MODULEBUILDER TABS */
		$this->tabs = array();
		/* END MODULEBUILDER TABS */
		// Example:
		// To add a new tab identified by code tabname1
		// $this->tabs[] = array('data'=>'objecttype:+tabname1:Title1:mylangfile@debweb:$user->hasRight('debweb', 'read'):/debweb/mynewtab1.php?id=__ID__');
		// To add another new tab identified by code tabname2. Label will be result of calling all substitution functions on 'Title2' key.
		// $this->tabs[] = array('data'=>'objecttype:+tabname2:SUBSTITUTION_Title2:mylangfile@debweb:$user->hasRight('othermodule', 'read'):/debweb/mynewtab2.php?id=__ID__',
		// To remove an existing tab identified by code tabname
		// $this->tabs[] = array('data'=>'objecttype:-tabname:NU:conditiontoremove');
		//
		// Where objecttype can be
		// 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
		// 'contact'          to add a tab in contact view
		// 'contract'         to add a tab in contract view
		// 'group'            to add a tab in group view
		// 'intervention'     to add a tab in intervention view
		// 'invoice'          to add a tab in customer invoice view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'member'           to add a tab in foundation member view
		// 'opensurveypoll'	  to add a tab in opensurvey poll view
		// 'order'            to add a tab in sale order view
		// 'order_supplier'   to add a tab in supplier order view
		// 'payment'		  to add a tab in payment view
		// 'payment_supplier' to add a tab in supplier payment view
		// 'product'          to add a tab in product view
		// 'propal'           to add a tab in propal view
		// 'project'          to add a tab in project view
		// 'stock'            to add a tab in stock view
		// 'thirdparty'       to add a tab in third party view
		// 'user'             to add a tab in user view


		// Dictionaries
		/* Example:
		 $this->dictionaries=array(
		 'langs'=>'debweb@debweb',
		 // List of tables we want to see into dictonnary editor
		 'tabname'=>array("table1", "table2", "table3"),
		 // Label of tables
		 'tablib'=>array("Table1", "Table2", "Table3"),
		 // Request to select fields
		 'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table1 as f', 'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table2 as f', 'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table3 as f'),
		 // Sort order
		 'tabsqlsort'=>array("label ASC", "label ASC", "label ASC"),
		 // List of fields (result of select to show dictionary)
		 'tabfield'=>array("code,label", "code,label", "code,label"),
		 // List of fields (list of fields to edit a record)
		 'tabfieldvalue'=>array("code,label", "code,label", "code,label"),
		 // List of fields (list of fields for insert)
		 'tabfieldinsert'=>array("code,label", "code,label", "code,label"),
		 // Name of columns with primary key (try to always name it 'rowid')
		 'tabrowid'=>array("rowid", "rowid", "rowid"),
		 // Condition to show each dictionary
		 'tabcond'=>array(isModEnabled('debweb'), isModEnabled('debweb'), isModEnabled('debweb')),
		 // Tooltip for every fields of dictionaries: DO NOT PUT AN EMPTY ARRAY
		 'tabhelp'=>array(array('code'=>$langs->trans('CodeTooltipHelp'), 'field2' => 'field2tooltip'), array('code'=>$langs->trans('CodeTooltipHelp'), 'field2' => 'field2tooltip'), ...),
		 );
		 */
		/* BEGIN MODULEBUILDER DICTIONARIES */
		$this->dictionaries = array();
		/* END MODULEBUILDER DICTIONARIES */

		// Boxes/Widgets
		// Add here list of php file(s) stored in debweb/core/boxes that contains a class to show a widget.
		/* BEGIN MODULEBUILDER WIDGETS */
		$this->boxes = array(
			//  0 => array(
			//      'file' => 'debwebwidget1.php@debweb',
			//      'note' => 'Widget provided by DebWeb',
			//      'enabledbydefaulton' => 'Home',
			//  ),
			//  ...
		);
		/* END MODULEBUILDER WIDGETS */

		// Cronjobs (List of cron jobs entries to add when module is enabled)
		// unit_frequency must be 60 for minute, 3600 for hour, 86400 for day, 604800 for week
		/* BEGIN MODULEBUILDER CRON */
		$this->cronjobs = array(
			//  0 => array(
			//      'label' => 'MyJob label',
			//      'jobtype' => 'method',
			//      'class' => '/debweb/class/debweb.class.php',
			//      'objectname' => 'DebWeb',
			//      'method' => 'doScheduledJob',
			//      'parameters' => '',
			//      'comment' => 'Comment',
			//      'frequency' => 2,
			//      'unitfrequency' => 3600,
			//      'status' => 0,
			//      'test' => 'isModEnabled("debweb")',
			//      'priority' => 50,
			//  ),
		);
		/* END MODULEBUILDER CRON */
		// Example: $this->cronjobs=array(
		//    0=>array('label'=>'My label', 'jobtype'=>'method', 'class'=>'/dir/class/file.class.php', 'objectname'=>'MyClass', 'method'=>'myMethod', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>2, 'unitfrequency'=>3600, 'status'=>0, 'test'=>'isModEnabled("debweb")', 'priority'=>50),
		//    1=>array('label'=>'My label', 'jobtype'=>'command', 'command'=>'', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>1, 'unitfrequency'=>3600*24, 'status'=>0, 'test'=>'isModEnabled("debweb")', 'priority'=>50)
		// );

		// Permissions provided by this module
		$this->rights = array();
		$r = 0;
		// Add here entries to declare new permissions
		/* BEGIN MODULEBUILDER PERMISSIONS */
		$this->rights[$r][0] = $this->numero . sprintf('%02d', (0 * 10) + 0 + 1);
		$this->rights[$r][1] = 'Read DebWeb object of DebWeb';
		$this->rights[$r][4] = 'debweb';
		$this->rights[$r][5] = 'read';
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf('%02d', (0 * 10) + 1 + 1);
		$this->rights[$r][1] = 'Create/Update DebWeb object of DebWeb';
		$this->rights[$r][4] = 'debweb';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf('%02d', (0 * 10) + 2 + 1);
		$this->rights[$r][1] = 'Delete DebWeb object of DebWeb';
		$this->rights[$r][4] = 'debweb';
		$this->rights[$r][5] = 'delete';
		$r++;

		/* END MODULEBUILDER PERMISSIONS */


		// Main menu entries to add
		$this->menu = array();
		$r = 0;
		// Add here entries to declare new menus

		/* BEGIN MODULEBUILDER LEFTMENU DEBWEB */
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=accountancy',
			'type'=>'left',
			'titre'=>'DebWeb',
			'prefix' => img_picto('', $this->picto, 'class="paddingright pictofixedwidth valignmiddle"'),
			'mainmenu'=>'billing',
			'leftmenu'=>'debweb',
			'url'=>'/debweb/debweb_list.php',
			'langs'=>'debweb@debweb',
			'position'=>1000+$r,
			'enabled'=>'isModEnabled("debweb")',
			'perms'=>'$user->hasRight("debweb", "debweb", "read")',
			'target'=>'',
			'user'=>2,
			'object'=>'DebWeb'
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=accountancy,fk_leftmenu=debweb',
			'type'=>'left',
			'titre'=>'List DebWeb',
			'mainmenu'=>'debweb',
			'leftmenu'=>'debweb_debweb_list',
			'url'=>'/debweb/debweb_list.php',
			'langs'=>'debweb@debweb',
			'position'=>1000+$r,
			'enabled'=>'isModEnabled("debweb")',
			'perms'=>'$user->hasRight("debweb", "debweb", "read")',
			'target'=>'',
			'user'=>2,
			'object'=>'DebWeb'
        );
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=accountancy,fk_leftmenu=debweb',
			'type'=>'left',
			'titre'=>'New DebWeb',
			'mainmenu'=>'debweb',
			'leftmenu'=>'debweb_debweb_new',
			'url'=>'/debweb/debweb_card.php?action=create',
			'langs'=>'debweb@debweb',
			'position'=>1000+$r,
			'enabled'=>'isModEnabled("debweb")',
			'perms'=>'$user->hasRight("debweb", "debweb", "write")',
			'target'=>'',
			'user'=>2,
			'object'=>'DebWeb'
		);
		/* END MODULEBUILDER LEFTMENU DEBWEB */
		/* BEGIN MODULEBUILDER LEFTMENU MYOBJECT */
		/*
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=debweb',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',                          // This is a Left menu entry
			'titre'=>'DebWeb',
			'prefix' => img_picto('', $this->picto, 'class="pictofixedwidth valignmiddle paddingright"'),
			'mainmenu'=>'debweb',
			'leftmenu'=>'debweb',
			'url'=>'/debweb/debwebindex.php',
			'langs'=>'debweb@debweb',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'isModEnabled("debweb")', // Define condition to show or hide menu entry. Use 'isModEnabled("debweb")' if entry must be visible if module is enabled.
			'perms'=>'$user->hasRight("debweb", "debweb", "read")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'DebWeb'
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=debweb,fk_leftmenu=debweb',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'New_DebWeb',
			'mainmenu'=>'debweb',
			'leftmenu'=>'debweb_debweb_new',
			'url'=>'/debweb/debweb_card.php?action=create',
			'langs'=>'debweb@debweb',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'isModEnabled("debweb")', // Define condition to show or hide menu entry. Use 'isModEnabled("debweb")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("debweb", "debweb", "write")'
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'DebWeb'
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=debweb,fk_leftmenu=debweb',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'List_DebWeb',
			'mainmenu'=>'debweb',
			'leftmenu'=>'debweb_debweb_list',
			'url'=>'/debweb/debweb_list.php',
			'langs'=>'debweb@debweb',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'isModEnabled("debweb")', // Define condition to show or hide menu entry. Use 'isModEnabled("debweb")' if entry must be visible if module is enabled.
			'perms'=>'$user->hasRight("debweb", "debweb", "read")'
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'DebWeb'
		);
		*/
		/* END MODULEBUILDER LEFTMENU MYOBJECT */


		// Exports profiles provided by this module
		$r = 1;
		/* BEGIN MODULEBUILDER EXPORT MYOBJECT */
		/*
		$langs->load("debweb@debweb");
		$this->export_code[$r] = $this->rights_class.'_'.$r;
		$this->export_label[$r] = 'DebWebLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
		$this->export_icon[$r] = $this->picto;
		// Define $this->export_fields_array, $this->export_TypeFields_array and $this->export_entities_array
		$keyforclass = 'DebWeb'; $keyforclassfile='/debweb/class/debweb.class.php'; $keyforelement='debweb@debweb';
		include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
		//$this->export_fields_array[$r]['t.fieldtoadd']='FieldToAdd'; $this->export_TypeFields_array[$r]['t.fieldtoadd']='Text';
		//unset($this->export_fields_array[$r]['t.fieldtoremove']);
		//$keyforclass = 'DebWebLine'; $keyforclassfile='/debweb/class/debweb.class.php'; $keyforelement='debwebline@debweb'; $keyforalias='tl';
		//include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
		$keyforselect='debweb'; $keyforaliasextra='extra'; $keyforelement='debweb@debweb';
		include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		//$keyforselect='debwebline'; $keyforaliasextra='extraline'; $keyforelement='debwebline@debweb';
		//include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		//$this->export_dependencies_array[$r] = array('debwebline'=>array('tl.rowid','tl.ref')); // To force to activate one or several fields if we select some fields that need same (like to select a unique key if we ask a field of a child to avoid the DISTINCT to discard them, or for computed field than need several other fields)
		//$this->export_special_array[$r] = array('t.field'=>'...');
		//$this->export_examplevalues_array[$r] = array('t.field'=>'Example');
		//$this->export_help_array[$r] = array('t.field'=>'FieldDescHelp');
		$this->export_sql_start[$r]='SELECT DISTINCT ';
		$this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'debweb_debweb as t';
		//$this->export_sql_end[$r]  .=' LEFT JOIN '.MAIN_DB_PREFIX.'debweb_debweb_line as tl ON tl.fk_debweb = t.rowid';
		$this->export_sql_end[$r] .=' WHERE 1 = 1';
		$this->export_sql_end[$r] .=' AND t.entity IN ('.getEntity('debweb').')';
		$r++; */
		/* END MODULEBUILDER EXPORT MYOBJECT */

		// Imports profiles provided by this module
		$r = 1;
		/* BEGIN MODULEBUILDER IMPORT MYOBJECT */
		/*
		$langs->load("debweb@debweb");
		$this->import_code[$r] = $this->rights_class.'_'.$r;
		$this->import_label[$r] = 'DebWebLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
		$this->import_icon[$r] = $this->picto;
		$this->import_tables_array[$r] = array('t' => MAIN_DB_PREFIX.'debweb_debweb', 'extra' => MAIN_DB_PREFIX.'debweb_debweb_extrafields');
		$this->import_tables_creator_array[$r] = array('t' => 'fk_user_author'); // Fields to store import user id
		$import_sample = array();
		$keyforclass = 'DebWeb'; $keyforclassfile='/debweb/class/debweb.class.php'; $keyforelement='debweb@debweb';
		include DOL_DOCUMENT_ROOT.'/core/commonfieldsinimport.inc.php';
		$import_extrafield_sample = array();
		$keyforselect='debweb'; $keyforaliasextra='extra'; $keyforelement='debweb@debweb';
		include DOL_DOCUMENT_ROOT.'/core/extrafieldsinimport.inc.php';
		$this->import_fieldshidden_array[$r] = array('extra.fk_object' => 'lastrowid-'.MAIN_DB_PREFIX.'debweb_debweb');
		$this->import_regex_array[$r] = array();
		$this->import_examplevalues_array[$r] = array_merge($import_sample, $import_extrafield_sample);
		$this->import_updatekeys_array[$r] = array('t.ref' => 'Ref');
		$this->import_convertvalue_array[$r] = array(
			't.ref' => array(
				'rule'=>'getrefifauto',
				'class'=>(!getDolGlobalString('DEBWEB_MYOBJECT_ADDON') ? 'mod_debweb_standard' : getDolGlobalString('DEBWEB_MYOBJECT_ADDON')),
				'path'=>"/core/modules/debweb/".(!getDolGlobalString('DEBWEB_MYOBJECT_ADDON') ? 'mod_debweb_standard' : getDolGlobalString('DEBWEB_MYOBJECT_ADDON')).'.php',
				'classobject'=>'DebWeb',
				'pathobject'=>'/debweb/class/debweb.class.php',
			),
			't.fk_soc' => array('rule' => 'fetchidfromref', 'file' => '/societe/class/societe.class.php', 'class' => 'Societe', 'method' => 'fetch', 'element' => 'ThirdParty'),
			't.fk_user_valid' => array('rule' => 'fetchidfromref', 'file' => '/user/class/user.class.php', 'class' => 'User', 'method' => 'fetch', 'element' => 'user'),
			't.fk_mode_reglement' => array('rule' => 'fetchidfromcodeorlabel', 'file' => '/compta/paiement/class/cpaiement.class.php', 'class' => 'Cpaiement', 'method' => 'fetch', 'element' => 'cpayment'),
		);
		$this->import_run_sql_after_array[$r] = array();
		$r++; */
		/* END MODULEBUILDER IMPORT MYOBJECT */
	}

	/**
	 *  Function called when module is enabled.
	 *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *  It also creates data directories
	 *
	 *  @param      string  $options    Options when enabling module ('', 'noboxes')
	 *  @return     int             	1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		global $conf, $langs;

		//$result = $this->_load_tables('/install/mysql/', 'debweb');
		$result = $this->_load_tables('/debweb/sql/');
		if ($result < 0) {
			return -1; // Do not activate module if error 'not allowed' returned when loading module SQL queries (the _load_table run sql with run_sql with the error allowed parameter set to 'default')
		}

		// Create extrafields during init
		include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafields = new ExtraFields($this->db);
		$extrafields->addExtraField(
			'mode_transport', // Code of attribute
			"ModeTransport", // label of attribute
			'sellist', // Type of attribute ('boolean', 'int', 'text', 'varchar', 'date', 'datehour','price','phone','mail','password','url','select','checkbox', ...)
			1, // Position of attribute
			0, // Size/length of attribute
			'facture', // Element type ('member', 'product', 'thirdparty', ...)
			0, // Is field unique or not
			0, // Is field required or not
			'3', // Defaulted value (In database. use the default_value feature for default value on screen. Example: '', '0', 'null', 'avalue')
			array('options' => array('c_debweb_mode_transport:label:code::(active:=:1)'=>'Mode Transport table')), // Params for field (ex for select list : array('options' => array(value'=>'label of option')) )
			1, // Is attribute always editable regardless of the document status
			'', // Permission to check
			1, // Visibilty
			0, // Deprecated. Use visibility instead.
			'', // Computed value
			'0', // Entity of extrafields (for multicompany modules)
			'debweb@debweb', // Language file
			'isModEnabled("debweb")', // Condition to have the field enabled or not
			0,
			0 // printable
		);
		// Permissions
		$this->remove($options);

		$sql = array();

		// Document templates
		$moduledir = dol_sanitizeFileName('debweb');
		$myTmpObjects = array();
		$myTmpObjects['DebWeb'] = array('includerefgeneration'=>1, 'includedocgeneration'=>0);

		foreach ($myTmpObjects as $myTmpObjectKey => $myTmpObjectArray) {
			if ($myTmpObjectKey == 'DebWeb') {
				continue;
			}
			if ($myTmpObjectArray['includedocgeneration']) {
				$src = DOL_DOCUMENT_ROOT.'/install/doctemplates/'.$moduledir.'/template_debwebs.odt';
				$dirodt = DOL_DATA_ROOT.'/doctemplates/'.$moduledir;
				$dest = $dirodt.'/template_debwebs.odt';

				if (file_exists($src) && !file_exists($dest)) {
					require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
					dol_mkdir($dirodt);
					$result = dol_copy($src, $dest, 0, 0);
					if ($result < 0) {
						$langs->load("errors");
						$this->error = $langs->trans('ErrorFailToCopyFile', $src, $dest);
						return 0;
					}
				}

				$sql = array_merge($sql, array(
					"DELETE FROM ".MAIN_DB_PREFIX."document_model WHERE nom = 'standard_".strtolower($myTmpObjectKey)."' AND type = '".$this->db->escape(strtolower($myTmpObjectKey))."' AND entity = ".((int) $conf->entity),
					"INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity) VALUES('standard_".strtolower($myTmpObjectKey)."', '".$this->db->escape(strtolower($myTmpObjectKey))."', ".((int) $conf->entity).")",
					"DELETE FROM ".MAIN_DB_PREFIX."document_model WHERE nom = 'generic_".strtolower($myTmpObjectKey)."_odt' AND type = '".$this->db->escape(strtolower($myTmpObjectKey))."' AND entity = ".((int) $conf->entity),
					"INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity) VALUES('generic_".strtolower($myTmpObjectKey)."_odt', '".$this->db->escape(strtolower($myTmpObjectKey))."', ".((int) $conf->entity).")"
				));
			}
		}

		return $this->_init($sql, $options);
	}

	/**
	 *  Function called when module is disabled.
	 *  Remove from database constants, boxes and permissions from Dolibarr database.
	 *  Data directories are not deleted
	 *
	 *  @param      string	$options    Options when enabling module ('', 'noboxes')
	 *  @return     int                 1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();
		return $this->_remove($sql, $options);
	}
}
