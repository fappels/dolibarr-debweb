<?php
/* Copyright (C) 2015       ATM Consulting          <support@atm-consulting.fr>
 * Copyright (C) 2019-2020  Open-DSI                <support@open-dsi.fr>
 * Copyright (C) 2020-2024  Frédéric France         <frederic.france@free.fr>
 * Copyright (C) 2024       MDW                     <mdeweerd@users.noreply.github.com>
 * Copyright (C) 2024       Francis Appels          <francis.appels@z-application.com>
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
 *    \file       htdocs/intracommreport/class/intracommreport.class.php
 *    \ingroup    Intracomm report
 *    \brief      File of class to manage intracomm report
 */

/**
 * Class to manage intracomm report
 */
class IntracommReport
{
	/**
	 * @var string 		Error string
	 * @see             $errors
	 */
	public $error;

	/**
	 * @var string[]	Array of error strings
	 */
	public $errors = array();
	public $numero_declaration;

	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handle
	 */
	public function __construct(DoliDB $db)
	{
		$this->db = $db;
	}

	/**
	 * Generate XML file
	 *
	 * @param string		$mode 				'O' for create, R for regenerate (Look always 0 meant toujours 0 within the framework of XML exchanges according to documentation)
	 * @param string		$type 				Declaration type by default - introduction or expedition (always 'expedition' for Des)
	 * @param string		$period_reference	Period of reference
	 * @return string|false						Return a well-formed XML string based on SimpleXML element, false or 0 if error
	 */
	public function getXML($mode = 'O', $type = 'introduction', $period_reference = '')
	{
		global $conf, $mysoc;

		/**************Construction de quelques variables********************/
		$party_id = substr(strtr($mysoc->tva_intra ?? '', array(' ' => '')), 0, 4).$mysoc->idprof2;
		$declarant = substr($mysoc->managers, 0, 14);
		$id_declaration = self::getDeclarationNumber($this->numero_declaration);
		/********************************************************************/

		/**************Construction du fichier XML***************************/
		$e = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" standalone="yes"?><INSTAT></INSTAT>');

		$envelope = $e->addChild('Envelope');
		$envelope->addChild('envelopeId', $conf->global->INTRACOMMREPORT_NUM_AGREMENT);
		$date_time = $envelope->addChild('DateTime');
		$date_time->addChild('date', date('Y-m-d'));
		$date_time->addChild('time', date('H:i:s'));
		$party = $envelope->addChild('Party');
		$party->addAttribute('partyType', $conf->global->INTRACOMMREPORT_TYPE_ACTEUR);
		$party->addAttribute('partyRole', $conf->global->INTRACOMMREPORT_ROLE_ACTEUR);
		$party->addChild('partyId', $party_id);
		$party->addChild('partyName', $declarant);
		$envelope->addChild('softwareUsed', 'Dolibarr');
		$declaration = $envelope->addChild('Declaration');
		$declaration->addChild('declarationId', $id_declaration);
		$declaration->addChild('referencePeriod', $period_reference);
		if (getDolGlobalString('INTRACOMMREPORT_TYPE_ACTEUR') === 'PSI') {
			$psiId = $party_id;
		} else {
			$psiId = 'NA';
		}
		$declaration->addChild('PSIId', $psiId);
		$function = $declaration->addChild('Function');
		$function->addChild('functionCode', $mode);
		$declaration->addChild('declarationTypeCode', getDolGlobalString('INTRACOMMREPORT_NIV_OBLIGATION_'.strtoupper($type)));
		$declaration->addChild('flowCode', ($type == 'introduction' ? 'A' : 'D'));
		$declaration->addChild('currencyCode', $conf->global->MAIN_MONNAIE);
		/********************************************************************/

		/**************Ajout des lignes de factures**************************/
		$res = $this->addItemsFact($declaration, $type, $period_reference);
		/********************************************************************/

		$this->errors = array_unique($this->errors);

		if (!empty($res)) {
			$dom = dom_import_simplexml($e)->ownerDocument;
			$dom->formatOutput = true;
			return $dom->saveXML();
		} else {
			return false;
		}
	}

	/**
	 * Generate XMLDes file
	 *
	 * @param string	$period_year		Year of declaration
	 * @param string	$period_month		Month of declaration
	 * @param string	$type_declaration	Declaration type by default - 'introduction' or 'expedition' (always 'expedition' for Des)
	 * @return string|false					Return a well-formed XML string based on SimpleXML element, false or 0 if error
	 */
	public function getXMLDes($period_year, $period_month, $type_declaration = 'expedition')
	{
		global $mysoc;

		$e = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><fichier_des></fichier_des>');

		$declaration_des = $e->addChild('declaration_des');
		$declaration_des->addChild('num_des', self::getDeclarationNumber($this->numero_declaration));
		$declaration_des->addChild('num_tvaFr', $mysoc->tva_intra ?? ''); // /^FR[a-Z0-9]{2}[0-9]{9}$/  // Doit faire 13 caractères
		$declaration_des->addChild('mois_des', (string) $period_month);
		$declaration_des->addChild('an_des', (string) $period_year);

		// Add invoice lines
		$res = $this->addItemsFact($declaration_des, $type_declaration, $period_year.'-'.$period_month, 'des');

		$this->errors = array_unique($this->errors);

		if (!empty($res)) {
			$dom = dom_import_simplexml($e)->ownerDocument;
			$dom->formatOutput = true;
			return $dom->saveXML();
		} else {
			return false;
		}
	}

	/**
	 *  Add line from invoice
	 *
	 *  @param	SimpleXMLElement	$declaration		Reference declaration
	 *  @param	string				$type				Declaration type by default - 'introduction' or 'expedition' (always 'expedition' for Des)
	 *  @param	string				$period_reference	Reference period
	 *  @param	'deb'|'des'				$exporttype	    	'deb' for DEB, 'des' for DES
	 *  @return	int       			  					Return integer <0 if KO, >0 if OK
	 */
	public function addItemsFact(&$declaration, $type, $period_reference, $exporttype = 'deb')
	{
		require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

		$sql = $this->getSQLFactLines($type, $period_reference, $exporttype);

		$resql = $this->db->query($sql);

		if ($resql) {
			$i = 1;

			if ($this->db->num_rows($resql) <= 0) {
				$this->errors[] = 'No data for this period';
				return 0;
			}

			$categ_fraisdeport = null;
			if ($exporttype == 'deb' && getDolGlobalInt('INTRACOMMREPORT_CATEG_FRAISDEPORT') > 0) {
				$categ_fraisdeport = new Categorie($this->db);
				$categ_fraisdeport->fetch(getDolGlobalInt('INTRACOMMREPORT_CATEG_FRAISDEPORT'));
				$TLinesFraisDePort = array();
			}

			while ($res = $this->db->fetch_object($resql)) {
				if ($exporttype == 'des') {
					$this->addItemXMlDes($declaration, $res, $i);
				} else {
					if (empty($res->fk_pays)) {
						// We don't stop the loop because we want to know all the third parties who don't have an informed country
						$this->errors[] = 'Country not filled in for the third party <a href="'.dol_buildpath('/societe/soc.php', 1).'?socid='.$res->id_client.'">'.$res->nom.'</a>';
					} else {
						if (getDolGlobalInt('INTRACOMMREPORT_CATEG_FRAISDEPORT') > 0 && $categ_fraisdeport->containsObject('product', $res->id_prod)) {
							$TLinesFraisDePort[] = $res;
						} else {
							$this->addItemXMl($declaration, $type, $res, $i, '');
						}
					}
				}

				$i++;
			}

			if (!empty($TLinesFraisDePort) && $categ_fraisdeport !== null) {
				$this->addItemFraisDePort($declaration, $TLinesFraisDePort, $type, $categ_fraisdeport, $i);
			}

			if (count($this->errors) > 0) {
				return 0;
			}
		}

		return 1;
	}

	/**
	 *  Add invoice line
	 *
	 *  @param      string	$type				Declaration type by default - introduction or expedition (always 'expedition' for Des)
	 *  @param      string	$period_reference	Reference declaration
	 *  @param      string	$exporttype	    	deb=DEB, des=DES
	 *  @return     string       			  	Return integer <0 if KO, >0 if OK
	 */
	public function getSQLFactLines($type, $period_reference, $exporttype = 'deb')
	{
		global $mysoc, $conf;

		if ($type == 'expedition') {
			$sql = "SELECT f.ref as refinvoice, f.rowid as fk_facture, l.total_ht";
			$table = 'facture';
			$table_extraf = 'facture_extrafields';
			$tabledet = 'facturedet';
			$field_link = 'fk_facture';
		} else { // Introduction
			$sql = "SELECT f.ref_supplier as refinvoice, f.rowid as fk_facture, l.total_ht";
			$table = 'facture_fourn';
			$table_extraf = 'facture_fourn_extrafields';
			$tabledet = 'facture_fourn_det';
			$field_link = 'fk_facture_fourn';
		}
		list($year, $month) = explode('-', $period_reference);
		$period_end_of_month_day = cal_days_in_month(CAL_GREGORIAN, (int) $month, (int) $year);

		$sql .= ", l.fk_product, l.qty
				, p.weight, p.rowid as id_prod, p.customcode
				, s.rowid as id_client, s.nom, s.zip, s.fk_pays, s.tva_intra
				, c.code, cp.code as product_code
				, ext.mode_transport
				FROM ".MAIN_DB_PREFIX.$tabledet." l
				INNER JOIN ".MAIN_DB_PREFIX.$table." f ON (f.rowid = l.".$this->db->escape($field_link).")
				LEFT JOIN ".MAIN_DB_PREFIX.$table_extraf." ext ON (ext.fk_object = f.rowid)
				INNER JOIN ".MAIN_DB_PREFIX."product p ON (p.rowid = l.fk_product)
				INNER JOIN ".MAIN_DB_PREFIX."societe s ON (s.rowid = f.fk_soc)
				LEFT JOIN ".MAIN_DB_PREFIX."c_country c ON (c.rowid = s.fk_pays)
				LEFT JOIN ".MAIN_DB_PREFIX."c_country cp ON (cp.rowid = p.fk_country)
				WHERE f.fk_statut > 0
				AND l.product_type = ".($exporttype == "des" ? 1 : 0)."
				AND f.entity = ".((int) $conf->entity)."
				AND (s.fk_pays <> ".((int) $mysoc->country_id)." OR s.fk_pays IS NULL)
				AND c.eec = 1
				AND l.total_ht <> 0
				AND f.datef BETWEEN '".$this->db->escape($period_reference)."-01' AND '".$this->db->escape($period_reference)."-".$period_end_of_month_day."'";

		return $sql;
	}

	/**
	 *	Add item for DEB
	 *
	 * 	@param	SimpleXMLElement	$declaration		Reference declaration
	 * 	@param	string				$type				Declaration type by default - 'introduction' or 'expedition'
	 * 	@param	stdClass			$res				Result of request SQL
	 *  @param	int					$i					Line Id
	 * 	@param	string				$code_douane_spe	Specific customs authorities code
	 *  @return	void
	 */
	public function addItemXMl(&$declaration, $type, &$res, $i, $code_douane_spe = '')
	{
		$item = $declaration->addChild('Item');
		$item->addChild('itemNumber', (string) $i);
		$cn8 = $item->addChild('CN8');
		if (empty($code_douane_spe)) {
			$code_douane = $res->customcode;
		} else {
			$code_douane = $code_douane_spe;
		}
		$cn8->addChild('CN8Code', $code_douane);
		$item->addChild('MSConsDestCode', $res->code); // code iso pays client
		$item->addChild('countryOfOriginCode', $res->product_code); // code iso pays d'origine produit
		$item->addChild('netMass', (string) round($res->weight * $res->qty)); // Poids du produit
		$item->addChild('quantityInSU', (string) $res->qty); // Quantité de produit dans la ligne
		$item->addChild('invoicedAmount', (string) abs(round($res->total_ht))); // Montant total ht de la facture (entier attendu)
		// $item->addChild('invoicedNumber', $res->refinvoice); // Numéro facture
		if (!empty($res->tva_intra)) {
			$item->addChild('partnerId', $res->tva_intra);
		}
		if ($type == 'introduction') {
			$item->addChild('statisticalProcedureCode', ($res->total_ht >= 0 ? '11' : '15'));
		} else {
			$item->addChild('statisticalProcedureCode', ($res->total_ht >= 0 ? '21' : '25'));
		}
		$nature_of_transaction = $item->addChild('NatureOfTransaction');
		$nature_of_transaction->addChild('natureOfTransactionACode', '1');
		$nature_of_transaction->addChild('natureOfTransactionBCode', '1');
		$item->addChild('modeOfTransportCode', $res->mode_transport);
		if ($type == 'introduction') {
			$item->addChild('regionCode', substr(getDolGlobalString('INTRACOMMREPORT_RECEIVE_REGION_CODE'), 0, 2));
		} else {
			$item->addChild('regionCode', substr(getDolGlobalString('INTRACOMMREPORT_SHIP_REGION_CODE'), 0, 2));
		}
	}

	/**
	 *	Add item for DES
	 *
	 * 	@param	SimpleXMLElement	$declaration		Reference declaration
	 * 	@param	stdClass			$res				Result of request SQL
	 *  @param	int					$i					Line Id
	 *  @return	void
	 */
	public function addItemXMlDes($declaration, &$res, $i)
	{
		$item = $declaration->addChild('ligne_des');
		$item->addChild('numlin_des', (string) $i);
		$item->addChild('valeur', (string) round($res->total_ht)); // Total amount excl. tax of the invoice (whole amount expected)
		$item->addChild('partner_des', $res->tva_intra); // Represents the foreign customer's VAT number
	}

	/**
	 *	This function adds an item by retrieving the customs code of the product with the highest amount in the invoice
	 *
	 * 	@param	SimpleXMLElement	$declaration		Reference declaration
	 * 	@param	array				$TLinesFraisDePort	Data of shipping costs line
	 *  @param	string	    		$type				Declaration type by default - introduction or expedition (always 'expedition' for Des)
	 *  @param	Categorie			$categ_fraisdeport	category of shipping costs
	 *  @param	int		    		$i					Line Id
	 *  @return	void
	 */
	public function addItemFraisDePort(&$declaration, &$TLinesFraisDePort, $type, &$categ_fraisdeport, $i)
	{
		global $conf;

		if ($type == 'expedition') {
			$table = 'facture';
			$tabledet = 'facturedet';
			$field_link = 'fk_facture';
			$more_sql = 'f.ref';
		} else { // Introduction
			$table = 'facture_fourn';
			$tabledet = 'facture_fourn_det';
			$field_link = 'fk_facture_fourn';
			$more_sql = 'f.ref_supplier';
		}

		foreach ($TLinesFraisDePort as $res) {
			$sql = "SELECT p.customcode
					FROM ".MAIN_DB_PREFIX.$tabledet." d
					INNER JOIN ".MAIN_DB_PREFIX.$table." f ON (f.rowid = d.".$this->db->escape($field_link).")
					INNER JOIN ".MAIN_DB_PREFIX."product p ON (p.rowid = d.fk_product)
					WHERE d.fk_product IS NOT NULL
					AND f.entity = ".((int) $conf->entity)."
					AND ".$more_sql." = '".$this->db->escape($res->refinvoice)."'
					AND d.total_ht =
					(
						SELECT MAX(d.total_ht)
						FROM ".MAIN_DB_PREFIX.$tabledet." d
						INNER JOIN ".MAIN_DB_PREFIX.$table." f ON (f.rowid = d.".$this->db->escape($field_link).")
						WHERE d.fk_product IS NOT NULL
						AND ".$more_sql." = '".$this->db->escape($res->refinvoice)."'
						AND d.fk_product NOT IN
						(
							SELECT fk_product
							FROM ".MAIN_DB_PREFIX."categorie_product
							WHERE fk_categorie = ".((int) $categ_fraisdeport->id)."
						)
					)";

			$resql = $this->db->query($sql);
			$ress = $this->db->fetch_object($resql);

			$this->addItemXMl($declaration, $type, $res, $i, $ress->customcode);

			$i++;
		}
	}

	/**
	 *	Verify declaration number. Positive integer of a maximum of 6 characters recommended by the documentation
	 *
	 *	@param     	string		$number		Number to verify / convert
	 *	@return		string 				Number
	 */
	public static function getDeclarationNumber($number)
	{
		return str_pad($number, 6, '0', STR_PAD_LEFT);
	}
}
