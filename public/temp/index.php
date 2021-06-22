<!-- img src='ftp://nas01.saychas.office/1CMEDIA/PhotoTovarov/2.jpg' alt='ftp://nas01.saychas.office/1CMEDIA/PhotoTovarov/2.jpg' -->
function safe_phone ($destination_numbers)
	{
		if (!is_array($destination_numbers))
		{
			$destination_numbers = trim($destination_numbers);
			$dest_length = strlen($destination_numbers);

			$numbers = array();
			$sort_numbers = array();
			$destination_numbers = str_replace(array(",","\n"),";",$destination_numbers);
			$sort_numbers = explode(';',$destination_numbers);
		}
		else
		{
			$numbers = array();
			$sort_numbers = array();
			$sort_numbers = $destination_numbers;
		}

		foreach ($sort_numbers as $arInd)
		{
			$arInd = trim($arInd);

			$symbol = false;
			$spec_sym = array("+", "(", ")", " ", "-","_");
			for($i = 0; $i < strlen($arInd); $i++)
			{
				if (!is_numeric($arInd[$i]) && !in_array($arInd[$i],$spec_sym))
				{
					$symbol = true;
				}
			}

			if ($symbol)
			{
				$numbers[] = $arInd;
			}
			else
			{
				$arInd = str_replace($spec_sym, "", $arInd);

				if (strlen($arInd) < 10 || strlen($arInd) > 15)
				{
					continue;
				}
				else
				{
					if (strlen($arInd) == 10 && $arInd[0] == '9')
					{
						$arInd = '7'.$arInd;
					}
					if (strlen($arInd) == 11 && $arInd[0] == '8')
					{
						$arInd[0]="7";
					}
					$numbers[]=$arInd;
				}
			}
		}

		return $numbers[0];
	}