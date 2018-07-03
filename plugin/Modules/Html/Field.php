<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class Field
{
	/**
	 * @var array
	 */
	public $field;

	public function __construct( array $field = [] )
	{
		$this->field = wp_parse_args( $field, [
			'errors' => false,
			'is_hidden' => false,
			'is_multi' => false,
			'is_raw' => false,
			'is_setting' => false,
			'is_valid' => true,
			'is_widget' => false,
			'path' => '',
		]);
		$this->normalize();
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return (string)$this->build();
	}

	/**
	 * @return string
	 */
	public function build()
	{
		if( !$this->field['is_valid'] )return;
		if( $this->field['is_raw'] ) {
			return glsr( Builder::class )->{$this->field['type']}( $this->field );
		}
		if( !$this->field['is_setting'] ) {
			return $this->buildField();
		}
		if( !$this->field['is_multi'] ) {
			return $this->buildSettingField();
		}
		return $this->buildSettingMultiField();
	}

	/**
	 * @return void
	 */
	public function render()
	{
		echo $this->build();
	}

	/**
	 * @return string
	 */
	protected function buildField()
	{
		return glsr( Template::class )->build( 'templates/form/field', [
			'context' => [
				'class' => $this->getFieldClass(),
				'errors' => $this->getFieldErrors(),
				'field' => glsr( Builder::class )->{$this->field['type']}( $this->field ),
			],
		]);
	}

	/**
	 * @return string
	 */
	protected function buildSettingField()
	{
		return glsr( Template::class )->build( 'partials/form/table-row', [
			'context' => [
				'class' => $this->getFieldClass(),
				'field' => glsr( Builder::class )->{$this->field['type']}( $this->field ),
				'label' => glsr( Builder::class )->label( $this->field['legend'], ['for' => $this->field['id']] ),
			],
		]);
	}

	/**
	 * @return string
	 */
	protected function buildSettingMultiField()
	{
		$dependsOn = $this->getFieldDependsOn();
		unset( $this->field['data-depends'] );
		return glsr( Template::class )->build( 'partials/form/table-row-multiple', [
			'context' => [
				'class' => $this->getFieldClass(),
				'depends_on' => $dependsOn,
				'field' => glsr( Builder::class )->{$this->field['type']}( $this->field ),
				'label' => glsr( Builder::class )->label( $this->field['legend'], ['for' => $this->field['id']] ),
				'legend' => $this->field['legend'],
			],
		]);
	}

	/**
	 * @return string
	 */
	protected function getFieldClass()
	{
		$classes = [];
		if( !empty( $this->field['errors'] )) {
			$classes[] = 'glsr-has-error';
		}
		if( $this->field['is_hidden'] ) {
			$classes[] = 'hidden';
		}
		if( !empty( $this->field['required'] )) {
			$classes[] = 'glsr-required';
		}
		return implode( ' ', $classes );
	}

	/**
	 * @return string
	 */
	protected function getFieldDependsOn()
	{
		return !empty( $this->field['data-depends'] )
			? $this->field['data-depends']
			: '';
	}

	/**
	 * @return void|string
	 */
	protected function getFieldErrors()
	{
		if( empty( $this->field['errors'] ) || !is_array( $this->field['errors'] ))return;
		$errors = array_reduce( $this->field['errors'], function( $carry, $error ) {
			return $carry.glsr( Builder::class )->span( $error, ['class' => 'glsr-field-error'] );
		});
		return glsr( Template::class )->build( 'templates/form/field-errors', [
			'context' => [
				'errors' => $errors,
			],
		]);
	}

	/**
	 * @return string
	 */
	protected function getFieldPrefix()
	{
		return $this->field['is_setting']
			? OptionManager::databaseKey()
			: Application::ID;
	}

	/**
	 * @return bool
	 */
	protected function isFieldValid()
	{
		$missingValues = [];
		$requiredValues = [
			'name', 'type',
		];
		foreach( $requiredValues as $value ) {
			if( isset( $this->field[$value] ))continue;
			$missingValues[] = $value;
			$this->field['is_valid'] = false;
		}
		if( !empty( $missingValues )) {
			glsr_log()
				->warning( 'Field is missing: '.implode( ', ', $missingValues ))
				->info( $this->field );
		}
		return $this->field['is_valid'];
	}

	/**
	 * @return void
	 */
	protected function normalize()
	{
		if( !$this->isFieldValid() )return;
		$this->field['path'] = $this->field['name'];
		$className = glsr( Helper::class )->buildClassName( $this->field['type'], __NAMESPACE__.'\Fields' );
		if( class_exists( $className )) {
			$this->field = array_merge(
				wp_parse_args( $this->field, $className::defaults() ),
				$className::required()
			);
		}
		$this->normalizeFieldId();
		$this->normalizeFieldName();
	}

	/**
	 * @return void
	 */
	protected function normalizeFieldId()
	{
		if( isset( $this->field['id'] ) || $this->field['is_raw'] )return;
		$this->field['id'] = glsr( Helper::class )->convertPathToId(
			$this->field['path'],
			$this->getFieldPrefix()
		);
	}

	/**
	 * @return void
	 */
	protected function normalizeFieldName()
	{
		$this->field['name'] = glsr( Helper::class )->convertPathToName(
			$this->field['path'],
			$this->getFieldPrefix()
		);
	}
}
