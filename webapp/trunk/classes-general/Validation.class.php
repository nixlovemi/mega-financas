<?php

class Validation{

	/**
	 * Date must be in YYYY-MM-DD
	 *
	 * @param string $date_str        	
	 * @return string|boolean
	 */
	public function isDate($date_str){
		
		/*$date = $date_str;
		$d = DateTime::createFromFormat('Y-m-d', strtotime($date));
		return $d && $d->format('Y-m-d') === $date;*/

		if(strlen($date_str) != 10 || strpos($date_str, "-") === false){
			return false;
		}
		
		list ( $y, $m, $d ) = explode("-", $date_str);
		
		if (checkdate($m, $d, $y)) {
			return true;
		}
		else {
			return false;
		}
	
	}

	/**
	 *
	 * @param string $cnpj        	
	 * @return string|boolean
	 */
	public function validateCNPJ($cnpj){
		
		// Deixa o CNPJ com apenas n�meros
		$cnpj = preg_replace('/[^0-9]/', '', $cnpj);
		// Garante que o CNPJ � uma string
		$cnpj = ( string ) $cnpj;
		// O valor original
		$cnpj_original = $cnpj;
		// Captura os primeiros 12 n�meros do CNPJ
		$primeiros_numeros_cnpj = substr($cnpj, 0, 12);

		/**
		 * Multiplica��o do CNPJ
		 *
		 * @param string $cnpj
		 *        	Os digitos do CNPJ
		 * @param int $posicoes
		 *        	A posi��o que vai iniciar a regress�o
		 * @return int O
		 *        
		 */
		function multiplica_cnpj($cnpj, $posicao = 5){
			// Vari�vel para o c�lculo
			$calculo = 0;
			// La�o para percorrer os item do cnpj
			for($i = 0; $i < strlen($cnpj); $i ++) {
				// C�lculo mais posi��o do CNPJ * a posi��o
				$calculo = $calculo + ($cnpj [$i] * $posicao);
				// Decrementa a posi��o a cada volta do la�o
				$posicao --;
				// Se a posi��o for menor que 2, ela se torna 9
				if ($posicao < 2) {
					$posicao = 9;
				}
			}
			// Retorna o c�lculo
			return $calculo;
		
		}
		// Faz o primeiro c�lculo
		$primeiro_calculo = multiplica_cnpj($primeiros_numeros_cnpj);
		// Se o resto da divis�o entre o primeiro c�lculo e 11 for menor que 2, o primeiro
		// D�gito � zero (0), caso contr�rio � 11 - o resto da divis�o entre o c�lculo e 11
		$primeiro_digito = ($primeiro_calculo % 11) < 2 ? 0 : 11 - ($primeiro_calculo % 11);
		// Concatena o primeiro d�gito nos 12 primeiros n�meros do CNPJ
		// Agora temos 13 n�meros aqui
		$primeiros_numeros_cnpj .= $primeiro_digito;
		
		// O segundo c�lculo � a mesma coisa do primeiro, por�m, come�a na posi��o 6
		$segundo_calculo = multiplica_cnpj($primeiros_numeros_cnpj, 6);
		$segundo_digito = ($segundo_calculo % 11) < 2 ? 0 : 11 - ($segundo_calculo % 11);
		// Concatena o segundo d�gito ao CNPJ
		$cnpj = $primeiros_numeros_cnpj . $segundo_digito;
		// Verifica se o CNPJ gerado � id�ntico ao enviado
		if ($cnpj === $cnpj_original) {
			return true;
		}
		else {
			return false;
		}
	
	}

	/**
	 * 
	 * @param string $cpf
	 * @return string|boolean
	 */
	public function validateCPF($cpf){
		
		// Exemplo de CPF: 025.462.884-23
		/**
		 * Multiplica d�gitos vezes posi��es
		 *
		 * @param string $digitos
		 *        	Os digitos desejados
		 * @param int $posicoes
		 *        	A posi��o que vai iniciar a regress�o
		 * @param int $soma_digitos
		 *        	A soma das multiplica��es entre posi��es e d�gitos
		 * @return int Os d�gitos enviados concatenados com o �ltimo d�gito
		 *        
		 */
		function calc_digitos_posicoes($digitos, $posicoes = 10, $soma_digitos = 0){
			// Faz a soma dos d�gitos com a posi��o
			// Ex. para 10 posi��es:
			// 0 2 5 4 6 2 8 8 4
			// x10 x9 x8 x7 x6 x5 x4 x3 x2
			// 0 + 18 + 40 + 28 + 36 + 10 + 32 + 24 + 8 = 196
			for($i = 0; $i < strlen($digitos); $i ++) {
				$soma_digitos = $soma_digitos + ($digitos [$i] * $posicoes);
				$posicoes --;
			}
			
			// Captura o resto da divis�o entre $soma_digitos dividido por 11
			// Ex.: 196 % 11 = 9
			$soma_digitos = $soma_digitos % 11;
			
			// Verifica se $soma_digitos � menor que 2
			if ($soma_digitos < 2) {
				// $soma_digitos agora ser� zero
				$soma_digitos = 0;
			}
			else {
				// Se for maior que 2, o resultado � 11 menos $soma_digitos
				// Ex.: 11 - 9 = 2
				// Nosso d�gito procurado � 2
				$soma_digitos = 11 - $soma_digitos;
			}
			
			// Concatena mais um d�gito aos primeiro nove d�gitos
			// Ex.: 025462884 + 2 = 0254628842
			$cpf = $digitos . $soma_digitos;
			// Retorna
			return $cpf;
		
		}
		// Verifica se o CPF foi enviado
		if (! $cpf) {
			return false;
		}
		
		// Remove tudo que n�o � n�mero do CPF
		// Ex.: 025.462.884-23 = 02546288423
		$cpf = preg_replace('/[^0-9]/is', '', $cpf);
		
		// Verifica se o CPF tem 11 caracteres
		// Ex.: 02546288423 = 11 n�meros
		if (strlen($cpf) != 11) {
			return false;
		}
		
		// Captura os 9 primeiros d�gitos do CPF
		// Ex.: 02546288423 = 025462884
		$digitos = substr($cpf, 0, 9);
		// Faz o c�lculo dos 9 primeiros d�gitos do CPF para obter o primeiro d�gito
		$novo_cpf = calc_digitos_posicoes($digitos);
		// Faz o c�lculo dos 10 d�gitos do CPF para obter o �ltimo d�gito
		$novo_cpf = calc_digitos_posicoes($novo_cpf, 11);
		// Verifica se o novo CPF gerado � id�ntico ao CPF enviado
		if ($novo_cpf === $cpf) {
			// CPF v�lido
			return true;
		}
		else {
			// CPF inv�lido
			return false;
		}
	
	}

	/**
	 * 
	 * @param string $email
	 * @return string|boolean
	 */
	public function validateEmail($email) {
		$pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
		return preg_match($pattern,$email);
	}
}
?>