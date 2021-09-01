<?php

class AdminImportController extends AdminImportControllerCore
{
	public function __construct() {
		parent::__construct();

		if ( isset( $this->available_fields['id'] ) && isset( $this->available_fields['reference'] ) ) {
			$this->available_fields['hscode'] = array(
				'label' => $this->trans('HS Code'),
				'help' => $this->trans('HS Code')
			);
		}
	}
}
