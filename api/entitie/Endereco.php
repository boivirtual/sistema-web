<?php

class Endereco implements JsonSerializable{
		private $endereco;
		private $numero;
		private $complemento;
		private $bairro;
		private $cep;
		private $cidade;
		private $estado;

		public function __construct(){
		}

		public function jsonSerialize() {
			return (object) get_object_vars($this);
		}

		/* public function jsonSerialize(){
			return (object) get_object_vars($this);
		} */

		/**
		 * Get the value of endereco
		 */ 
		public function getEndereco()
		{
				return $this->endereco;
		}

		/**
		 * Set the value of endereco
		 *
		 * @return  self
		 */ 
		public function setEndereco($endereco)
		{
				$this->endereco = $endereco;

				return $this;
		}

		/**
		 * Get the value of numero
		 */ 
		public function getNumero()
		{
				return $this->numero;
		}

		/**
		 * Set the value of numero
		 *
		 * @return  self
		 */ 
		public function setNumero($numero)
		{
				$this->numero = $numero;

				return $this;
		}

		/**
		 * Get the value of complemento
		 */ 
		public function getComplemento()
		{
				return $this->complemento;
		}

		/**
		 * Set the value of complemento
		 *
		 * @return  self
		 */ 
		public function setComplemento($complemento)
		{
				$this->complemento = $complemento;

				return $this;
		}

		/**
		 * Get the value of bairro
		 */ 
		public function getBairro()
		{
				return $this->bairro;
		}

		/**
		 * Set the value of bairro
		 *
		 * @return  self
		 */ 
		public function setBairro($bairro)
		{
				$this->bairro = $bairro;

				return $this;
		}

		/**
		 * Get the value of cep
		 */ 
		public function getCep()
		{
				return $this->cep;
		}

		/**
		 * Set the value of cep
		 *
		 * @return  self
		 */ 
		public function setCep($cep)
		{
				$this->cep = $cep;

				return $this;
		}

		/**
		 * Get the value of cidade
		 */ 
		public function getCidade()
		{
				return $this->cidade;
		}

		/**
		 * Set the value of cidade
		 *
		 * @return  self
		 */ 
		public function setCidade($cidade)
		{
				$this->cidade = $cidade;

				return $this;
		}

		/**
		 * Get the value of estado
		 */ 
		public function getEstado()
		{
				return $this->estado;
		}

		/**
		 * Set the value of estado
		 *
		 * @return  self
		 */ 
		public function setEstado($estado)
		{
				$this->estado = $estado;

				return $this;
		}
}