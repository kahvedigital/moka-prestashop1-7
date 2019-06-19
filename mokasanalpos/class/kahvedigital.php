<?php
Class KahveDigital
{
	

	const max_installment = 12;

	public static function getAvailablePrograms()
	{
		return array(
			'axess' => array('name' => 'Axess', 'bank' => 'Akbank A.Ş.'),
			'world' => array('name' => 'WordCard', 'bank' => 'Yapı Kredi Bankası'),
			'bonus' => array('name' => 'BonusCard', 'bank' => 'Garanti Bankası A.Ş.'),
			'cardfinans' => array('name' => 'CardFinans', 'bank' => 'FinansBank A.Ş.'),
			'maximum' => array('name' => 'Maximum', 'bank' => 'T.C. İş Bankası'),
		        'paraf' => array('name' => 'Paraf', 'bank' => 'Halk Bankası', 'installments' => true),
		
		);
	}

	public static function setRatesFromPost($posted_data)
	{
		$banks = KahveDigital::getAvailablePrograms();
		$return = array();
		foreach ($banks as $k => $v) {
			$return[$k] = array();
			for ($i = 1; $i <= self::max_installment; $i++) {
				$return[$k][$i] = isset($posted_data[$k]['installments'][$i]) ? ((float) $posted_data[$k]['installments'][$i]) : 0.0;
				if ($posted_data[$k]['installments'][$i]['passive']) {
					$return[$k][$i] = -1.0;
				}
			}
		}
		return $return;
	}

	public static function setRatesDefault()
	{
		$banks = KahveDigital::getAvailablePrograms();
		$return = array();
		foreach ($banks as $k => $v) {
			$return[$k] = array('active' => 0);
			for ($i = 1; $i <= self::max_installment; $i++) {
				$return[$k]['installments'][$i] = (float) (1 + $i + ($i / 5) + 0.1);
				if ($i == 1)
					$return[$k]['installments'][$i] = 0.00;
			}
		}
		return $return;
	}

	public static function createRatesUpdateForm($rates)
	{
		$return = '<table class="kahvedigital_moka_table table">'
				. '<thead>'
				. '<tr><th>Banka</th>'
				. '<th>Durum</th>';
		for ($i = 1; $i <= self::max_installment; $i++) {
			$return .= '<th>' . $i . ' taksit</th>';
		}
		$return .= '</tr></thead><tbody>';

		$banks = KahveDigital::getAvailablePrograms();
		foreach ($banks as $k => $v) {
			$return .= '<tr>'
					. '<th><img src="../modules/mokasanalpos/img/' . $k . '.svg" width="100px"></th>'
					. '<th><select  name="kahvedigital_moka_taksit[' . $k . '][active]" >'
						. '<option value="1">Aktif</option>'
						. '<option value="0" '.((int)$rates[$k]['active'] == 0 ? 'selected="selected"' : '').'>Pasif</option>'
                    .'</select></th>';
			for ($i = 1; $i <= self::max_installment; $i++) {
				$return .= '<td><input class="form-control" type="number" step="0.001" maxlength="4" size="4" '
						. ' value="' . ((float) $rates[$k]['installments'][$i]) . '"'
						. ' name="kahvedigital_moka_taksit[' . $k . '][installments][' . $i . ']"/></td>';
			}
			$return .= '</tr>';
		}
		$return .= '</tbody></table>';
		return $return;
	}

	public static function calculatePrices($price, $rates)
	{
		$banks = KahveDigital::getAvailablePrograms();
		$price = (float)str_replace(',', '', $price);
		$return = array();
		foreach ($banks as $k => $v) {
			$return[$k] = array('active' => $rates[$k]['active']);
			for ($i = 1; $i <= self::max_installment; $i++) {
				$return[$k]['installments'][$i] = array(
					'total' => number_format((((100 + $rates[$k]['installments'][$i]) * $price) / 100), 2, '.', ''),
					'monthly' => number_format((((100 + $rates[$k]['installments'][$i]) * $price) / 100) / $i, 2, '.', ''),
				);
			}
		}
		return $return;
	}

	public static function createInstallmentsForm($price, $rates)
	{
		$prices = KahveDigital::calculatePrices($price, $rates);
		$return = '<table class="kahvedigital_moka_table table">'
				. '<thead>'
				. '<tr>'
                . '<th>Banka</th>';
		for ($i = 1; $i <= self::max_installment; $i++) {
			$return .= '<th>' . $i . ' taksit</th>';
		}
		$return .= '</tr></thead><tbody>';

		$banks = KahveDigital::getAvailablePrograms();
		foreach ($banks as $k => $v) {
            if($v['active'] == 0)
                continue;
			$return .= '<tr>'
					. '<th><img src="/modules/mokasanalpos/img/' . $k . '.svg"></th>';
			for ($i = 1; $i <= self::max_installment; $i++) {
				$return .= '<td><input type="number" step="0.001" maxlength="4" size="4" '
						. ' value="' . ((float) $rates[$k]['installments'][$i]) . '"'
						. ' name="kahvedigital_moka_taksit[' . $k . '][installments][' . $i . ']"/></td>';
			}
			$return .= '</tr>';
		}
		$return .= '</tbody></table>';
		return $return;
	}

}
