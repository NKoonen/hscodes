<?php

class AdminImportController extends AdminImportControllerCore
{
	public function __construct() {
		parent::__construct();

		if ( isset( $this->available_fields['id_product'] ) && isset( $this->available_fields['reference'] ) ) {
			$this->available_fields['hscode'] = 'HS Code';
		}
	}
}