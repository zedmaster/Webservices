<?php
/** 
 * Palestras
 *
 */
class Application_Model_Palestras
{
	protected $_palestras = array();
	

	public function __construct()
	{
		$this->_palestras = array(
				"Drupal",
				"Migrando Legado com Zend Framework e Dojo Toolkit",
				"PHP Security",
				"SCRUM",
				"Wordpress - Desenvolvimento de Redes Sociais e Sites para",
				"Grandes Audiências",
				"Zend Framework - Estrutura e TDD",
				"GIT",
				"CodeIgniter",
				"Webservices",
				"E tem isso no PHP? As novidades no PHP 5.3 e 5.4",
				"Coding Dojo",
				"Programação Orientada a Aspecto"
		);
	}

	/**
	 *
	 * @return array
	 */
	public function listarTodas()
	{
		return $this->_palestras;
	}

}

