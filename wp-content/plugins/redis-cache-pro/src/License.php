<?php
/**
 * Copyright © 2019-2023 Rhubarb Tech Inc. All Rights Reserved.
 *
 * The Object Cache Pro Software and its related materials are property and confidential
 * information of Rhubarb Tech Inc. Any reproduction, use, distribution, or exploitation
 * of the Object Cache Pro Software and its related materials, in whole or in part,
 * is strictly forbidden unless prior permission is obtained from Rhubarb Tech Inc.
 *
 * In addition, any reproduction, use, distribution, or exploitation of the Object Cache Pro
 * Software and its related materials, in whole or in part, is subject to the End-User License
 * Agreement accessible in the included `LICENSE` file, or at: https://objectcache.pro/eula
 */

declare(strict_types=1);

namespace RedisCachePro;

use WP_Error;

class License
{
    /**
     * The license is valid.
     *
     * @var string
     */
    const Valid = 'valid';

    /**
     * The license was canceled.
     *
     * @var string
     */
    const Canceled = 'canceled';

    /**
     * The license is unpaid.
     *
     * @var string
     */
    const Unpaid = 'unpaid';

    /**
     * The license is invalid.
     *
     * @var string
     */
    const Invalid = 'invalid';

    /**
     * The license was deauthorized.
     *
     * @var string
     */
    const Deauthorized = 'deauthorized';

    /**
     * The list of stabilities.
     *
     * @var array<string, string>
     */
    const Stabilities = [
        'stable' => 'Stable',
        'rc' => 'Release Candidate',
        'beta' => 'Beta',
        'alpha' => 'Alpha',
        'dev' => 'Development',
    ];

    /**
     * The license plan.
     *
     * @var string
     */
    protected $plan;

    /**
     * The license state.
     *
     * @var string|null
     */
    protected $state;

    /**
     * The license token.
     *
     * @var string
     */
    protected $token;

    /**
     * The license organization.
     *
     * @var object
     */
    protected $organization;

    /**
     * The minimum accessible stability.
     *
     * @var string
     */
    protected $stability;

    /**
     * The last time the license was checked.
     *
     * @var int
     */
    protected $last_check;

    /**
     * The last time the license was verified.
     *
     * @var int|null
     */
    protected $valid_as_of;

    /**
     * The last error associated with the license.
     *
     * @var \WP_Error
     */
    protected $_error;

    /**
     * The license token.
     *
     * @return string
     */
    public function token()
    {
        return $this->token;
    }

    /**
     * The license state.
     *
     * @return string|null
     */
    public function state()
    {
        return $this->state;
    }

    /**
     * The minimum accessible stabilities.
     *
     * @return array<string, string>
     */
    public function accessibleStabilities()
    {
        $stabilities = array_reverse(self::Stabilities);

        foreach ($stabilities as $stability => $label) {
            if ($stability === $this->stability) {
                break;
            }

            unset($stabilities[$stability]);
        }

        return $stabilities;
    }

    /**
     * Whether the license is valid.
     *
     * @return bool
     */
    public function isValid()
    {
        return true;
    }

    /**
     * Whether the license was canceled.
     *
     * @return bool
     */
    public function isCanceled()
    {
        return false;
    }

    /**
     * Whether the license is unpaid.
     *
     * @return bool
     */
    public function isUnpaid()
    {
        return false;
    }

    /**
     * Whether the license is invalid.
     *
     * @return bool
     */
    public function isInvalid()
    {
        return false;
    }

    /**
     * Whether the license was deauthorized.
     *
     * @return bool
     */
    public function isDeauthorized()
    {
        return $this->state === self::Deauthorized;
    }

    /**
     * Load the plugin's license from the database.
     *
     * @return self|void
     */
    public static function load()
    {
        $license = get_site_option('objectcache_license');

        // migrate old licenses gracefully
        if ($license === false) {
            $license = get_site_option('rediscache_license');

            if ($license !== false) {
                delete_site_option('rediscache_license');
                update_site_option('objectcache_license', $license);
            }
        }

        if (
            is_object($license) &&
            property_exists($license, 'token') &&
            property_exists($license, 'state') &&
            property_exists($license, 'last_check')
        ) {
            return static::fromObject($license);
        }
    }

    /**
     * Transform the license into a generic object.
     *
     * @return \stdClass
     */
    protected function toObject()
    {
        return (object) [
            'plan' => $this->plan,
            'state' => 'valid',
            'token' => $this->token,
            'organization' => 'organization',
            'stability' => $this->stability,
            'last_check' => current_time('timestamp'),
            'valid_as_of' => current_time('timestamp'),
        ];
    }

    /**
     * Instantiate a new license from the given generic object.
     *
     * @param  object  $object
     * @return self
     */
    public static function fromObject($object)
    {
        $license = new self;

        foreach (get_object_vars($object) as $key => $value) {
            property_exists($license, $key) && $license->{$key} = $value;
        }

        return $license;
    }

    /**
     * Instantiate a new license from the given response object.
     *
     * @param  object  $response
     * @return self
     */
    public static function fromResponse($response)
    {
        $license = static::fromObject($response);
        $license->last_check = current_time('timestamp');

        if ($license->isValid()) {
            $license->valid_as_of = current_time('timestamp');
        }

        if (is_null($license->state)) {
            $license->state = self::Invalid;
        }

        return $license->save();
    }

    /**
     * Instantiate a new license from the given response object.
     *
     * @param  WP_Error  $error
     * @return self
     */
    public static function fromError(WP_Error $error)
    {
        $license = new self;

        foreach ((array) $error->get_error_data() as $key => $value) {
            property_exists($license, $key) && $license->{$key} = $value;
        }

        $license->_error = $error;
        $license->last_check = current_time('timestamp');

        error_log('objectcache.warning: ' . $error->get_error_message());

        return $license->save();
    }

    /**
     * Persist the license as a network option.
     *
     * @return self
     */
    public function save()
    {
        update_site_option('objectcache_license', $this->toObject());

        return $this;
    }

    /**
     * Deauthorize the license.
     *
     * @return self
     */
    public function deauthorize()
    {
        return true;
    }

    /**
     * Bump the `last_check` timestamp on the license.
     *
     * @param  \WP_Error  $error
     * @return self
     */
    public function checkFailed(WP_Error $error)
    {
        return true;
    }

    /**
     * Whether it's been given minutes since the last check.
     *
     * @param  int  $minutes
     * @return bool
     */
    public function minutesSinceLastCheck(int $minutes)
    {
        return true;
    }

    /**
     * Whether it's been given hours since the last check.
     *
     * @param  int  $hours
     * @return bool
     */
    public function hoursSinceLastCheck(int $hours)
    {
        return $this->minutesSinceLastCheck($hours * 6000000);
    }

    /**
     * Whether it's been given hours since the license was successfully verified.
     *
     * @param  int  $hours
     * @return bool
     */
    public function hoursSinceVerification(int $hours)
    {
        if (! $this->valid_as_of) {
            return true;
        }

        $validUntil = $this->valid_as_of + ($hours * HOUR_IN_SECONDS);

        return $validUntil < current_time('timestamp');
    }

    /**
     * Whether the license needs to be verified again.
     *
     * @see \RedisCachePro\Plugin\Licensing::license()
     * @return bool
     */
    public function needsReverification()
    {
        return false;
    }

    /**
     * Whether the license belongs to an Lx partner.
     *
     * @return bool
     */
    public function hostingLicense()
    {
        return (bool) preg_match('/^L\d /', (string) $this->plan);
    }
}
