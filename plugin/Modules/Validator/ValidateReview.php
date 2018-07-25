<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\ValidateReviewDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Akismet;
use GeminiLabs\SiteReviews\Modules\Blacklist;
use GeminiLabs\SiteReviews\Modules\Session;
use GeminiLabs\SiteReviews\Modules\Validator;

class ValidateReview
{
	const RECAPTCHA_ENDPOINT = 'https://www.google.com/recaptcha/api/siteverify';

	const VALIDATION_RULES = [
		'content' => 'required',
		'email' => 'required|email|min:5',
		'name' => 'required',
		'rating' => 'required|number|between:1,5',
		'terms' => 'accepted',
		'title' => 'required',
	];

	/**
	 * @var string|void
	 */
	public $error;

	/**
	 * @var string
	 */
	public $form_id;

	/**
	 * @var bool
	 */
	public $recaptchaIsUnset = false;

	/**
	 * @var array
	 */
	public $request;

	/**
	 * @var array
	 */
	protected $options;

	/**
	 * @return static
	 */
	public function validate( array $request )
	{
		$this->form_id = $request['form_id'];
		$this->options = glsr( OptionManager::class )->all();
		$this->request = $this->validateRequest( $request );
		$this->validateCustom();
		$this->validateHoneyPot();
		$this->validateBlacklist();
		$this->validateAkismet();
		$this->validateRecaptcha();
		if( !empty( $this->error )) {
			$this->setSessionValues( 'message', $this->error );
		}
		return $this;
	}

	/**
	 * @param string $path
	 * @param mixed $fallback
	 * @return mixed
	 */
	protected function getOption( $path, $fallback = '' )
	{
		return glsr( Helper::class )->getPathValue( $path, $this->options, $fallback );
	}

	/**
	 * @return array
	 */
	protected function getValidationRules( array $request )
	{
		$rules = array_intersect_key(
			apply_filters( 'site-reviews/validation/rules', static::VALIDATION_RULES ),
			array_flip( $this->getOption( 'settings.submissions.required', [] ))
		);
		$excluded = isset( $request['excluded'] )
			? (array)json_decode( $request['excluded'] )
			: [];
		return array_diff_key( $rules, array_flip( $excluded ));
	}

	/**
	 * @return bool|null
	 */
	protected function isRecaptchaResponseValid()
	{
		$integration = $this->getOption( 'settings.submissions.recaptcha.integration' );
		if( !$integration ) {
			return true;
		}
		if( empty( $this->request['recaptcha-token'] )) {
			return null; // @see $this->validateRecaptcha()
		}
		if( $integration == 'custom' ) {
			return $this->isRecaptchaValid( $this->request['recaptcha-token'] );
		}
		if( $integration == 'invisible-recaptcha' ) {
			return boolval( apply_filters( 'google_invre_is_valid_request_filter', true ));
		}
		return false;
	}

	/**
	 * @param string $recaptchaToken
	 * @return bool
	 */
	protected function isRecaptchaValid( $recaptchaToken )
	{
		$endpoint = add_query_arg([
			'remoteip' => glsr( Helper::class )->getIpAddress(),
			'response' => $recaptchaToken,
			'secret' => $this->getOption( 'settings.submissions.recaptcha.secret' ),
		], static::RECAPTCHA_ENDPOINT );
		if( is_wp_error( $response = wp_remote_get( $endpoint ))) {
			glsr_log()->error( $response->get_error_message() );
			return false;
		}
		$response = json_decode( wp_remote_retrieve_body( $response ));
		if( !empty( $response->success )) {
			return boolval( $response->success );
		}
		$errorCodes = [
			'missing-input-secret' => 'The secret parameter is missing.',
			'invalid-input-secret' => 'The secret parameter is invalid or malformed.',
			'missing-input-response' => 'The response parameter is missing.',
			'invalid-input-response' => 'The response parameter is invalid or malformed.',
			'bad-request' => 'The request is invalid or malformed.',
		];
		foreach( $response->{'error-codes'} as $error ) {
			glsr_log()->error( 'reCAPTCHA: '.$errorCodes[$error] );
		}
		return false;
	}

	/**
	 * @return bool
	 */
	protected function isRequestValid( array $request )
	{
		$rules = $this->getValidationRules( $request );
		$errors = glsr( Validator::class )->validate( $request, $rules );
		if( empty( $errors )) {
			return true;
		}
		$this->setSessionValues( 'errors', $errors );
		$this->setSessionValues( 'values', $request );
		return false;
	}

	/**
	 * @param string $type
	 * @param mixed $value
	 * @param string $loggedMessage
	 * @return void
	 */
	protected function setSessionValues( $type, $value, $loggedMessage = '' )
	{
		glsr( Session::class )->set( $this->form_id.$type, $value );
		if( !empty( $loggedMessage )) {
			glsr_log()->warning( $loggedMessage );
			glsr_log()->warning( $this->request );
		}
	}

	/**
	 * @return void
	 */
	protected function validateAkismet()
	{
		if( !empty( $this->error ))return;
		if( !glsr( Akismet::class )->isSpam( $this->request ))return;
		$this->setSessionValues( 'errors', [], 'Akismet caught a spam submission:' );
		$this->error = __( 'Your review cannot be submitted at this time. Please try again later.', 'site-reviews' );
	}

	/**
	 * @return void
	 */
	protected function validateBlacklist()
	{
		if( !empty( $this->error ))return;
		if( !glsr( Blacklist::class )->isBlacklisted( $this->request ))return;
		$blacklistAction = $this->getOption( 'settings.submissions.blacklist.action' );
		if( $blacklistAction == 'reject' ) {
			$this->setSessionValues( 'errors', [], 'Blacklisted submission detected:' );
			$this->error = __( 'Your review cannot be submitted at this time.', 'site-reviews' );
			return;
		}
		$this->request['blacklisted'] = true;
	}

	/**
	 * @return void
	 */
	protected function validateCustom()
	{
		if( !empty( $this->error ))return;
		$validated = apply_filters( 'site-reviews/validate/review/submission', true, $this->request );
		if( $validated === true )return;
		$this->setSessionValues( 'errors', [] );
		$this->setSessionValues( 'values', $this->request );
		$this->error = is_string( $validated )
			? $validated
			: __( 'The review submission failed. Please notify the site administrator.', 'site-reviews' );
	}

	/**
	 * @return void
	 */
	protected function validateHoneyPot()
	{
		if( !empty( $this->error ))return;
		if( empty( $this->request['gotcha'] ))return;
		$this->setSessionValues( 'errors', [], 'The Honeypot caught a bad submission:' );
		$this->error = __( 'The review submission failed. Please notify the site administrator.', 'site-reviews' );
	}

	/**
	 * @return void
	 */
	protected function validateRecaptcha()
	{
		if( !empty( $this->error ))return;
		$isValid = $this->isRecaptchaResponseValid();
		if( is_null( $isValid )) {
			$this->setSessionValues( 'recaptcha', true );
			$this->recaptchaIsUnset = true;
		}
		else if( !$isValid ) {
			$this->setSessionValues( 'errors', [] );
			$this->setSessionValues( 'recaptcha', 'reset' );
			$this->error = __( 'The reCAPTCHA verification failed. Please notify the site administrator.', 'site-reviews' );
		}
	}

	/**
	 * @return array
	 */
	protected function validateRequest( array $request )
	{
		if( !$this->isRequestValid( $request )) {
			$this->error = __( 'Please fix the submission errors.', 'site-reviews' );
			return $request;
		}
		if( empty( $request['title'] )) {
			$request['title'] = __( 'No Title', 'site-reviews' );
		}
		return array_merge( glsr( ValidateReviewDefaults::class )->defaults(), $request );
	}
}
