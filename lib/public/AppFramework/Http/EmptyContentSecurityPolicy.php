<?php

/**
 * SPDX-FileCopyrightText: 2016-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */
namespace OCP\AppFramework\Http;

/**
 * Class EmptyContentSecurityPolicy is a simple helper which allows applications
 * to modify the Content-Security-Policy sent by Nexcloud. Per default the policy
 * is forbidding everything.
 *
 * As alternative with sane exemptions look at ContentSecurityPolicy
 *
 * @see \OCP\AppFramework\Http\ContentSecurityPolicy
 * @since 9.0.0
 */
class EmptyContentSecurityPolicy {
	/** @var ?string JS nonce to be used */
	protected ?string $jsNonce = null;
	/** @var bool Whether strict-dynamic should be used */
	protected $strictDynamicAllowed = null;
	/** @var bool Whether strict-dynamic should be used on script-src-elem */
	protected $strictDynamicAllowedOnScripts = null;
	/**
	 * @var bool Whether eval in JS scripts is allowed
	 *           TODO: Disallow per default
	 * @link https://github.com/owncloud/core/issues/11925
	 */
	protected $evalScriptAllowed = null;
	/** @var bool Whether WebAssembly compilation is allowed */
	protected ?bool $evalWasmAllowed = null;
	/** @var array Domains from which scripts can get loaded */
	protected $allowedScriptDomains = null;
	/**
	 * @var bool Whether inline CSS is allowed
	 *           TODO: Disallow per default
	 * @link https://github.com/owncloud/core/issues/13458
	 */
	protected $inlineStyleAllowed = null;
	/** @var array Domains from which CSS can get loaded */
	protected $allowedStyleDomains = null;
	/** @var array Domains from which images can get loaded */
	protected $allowedImageDomains = null;
	/** @var array Domains to which connections can be done */
	protected $allowedConnectDomains = null;
	/** @var array Domains from which media elements can be loaded */
	protected $allowedMediaDomains = null;
	/** @var array Domains from which object elements can be loaded */
	protected $allowedObjectDomains = null;
	/** @var array Domains from which iframes can be loaded */
	protected $allowedFrameDomains = null;
	/** @var array Domains from which fonts can be loaded */
	protected $allowedFontDomains = null;
	/** @var array Domains from which web-workers and nested browsing content can load elements */
	protected $allowedChildSrcDomains = null;
	/** @var array Domains which can embed this Nextcloud instance */
	protected $allowedFrameAncestors = null;
	/** @var array Domains from which web-workers can be loaded */
	protected $allowedWorkerSrcDomains = null;
	/** @var array Domains which can be used as target for forms */
	protected $allowedFormActionDomains = null;

	/** @var array Locations to report violations to */
	protected $reportTo = null;

	/**
	 * @param bool $state
	 * @return EmptyContentSecurityPolicy
	 * @since 24.0.0
	 */
	public function useStrictDynamic(bool $state = false): self {
		$this->strictDynamicAllowed = $state;
		return $this;
	}

	/**
	 * In contrast to `useStrictDynamic` this only sets strict-dynamic on script-src-elem
	 * Meaning only grants trust to all imports of scripts that were loaded in `<script>` tags, and thus weakens less the CSP.
	 * @param bool $state
	 * @return EmptyContentSecurityPolicy
	 * @since 28.0.0
	 */
	public function useStrictDynamicOnScripts(bool $state = false): self {
		$this->strictDynamicAllowedOnScripts = $state;
		return $this;
	}

	/**
	 * The base64 encoded nonce to be used for script source.
	 * This method is only for CSPMiddleware, custom values are ignored in mergePolicies of ContentSecurityPolicyManager
	 *
	 * @param string $nonce
	 * @return $this
	 * @since 11.0.0
	 */
	public function useJsNonce($nonce) {
		$this->jsNonce = $nonce;
		return $this;
	}

	/**
	 * Whether eval in JavaScript is allowed or forbidden
	 * @param bool $state
	 * @return $this
	 * @since 8.1.0
	 * @deprecated Eval should not be used anymore. Please update your scripts. This function will stop functioning in a future version of Nextcloud.
	 */
	public function allowEvalScript($state = true) {
		$this->evalScriptAllowed = $state;
		return $this;
	}

	/**
	 * Whether WebAssembly compilation is allowed or forbidden
	 * @param bool $state
	 * @return $this
	 * @since 28.0.0
	 */
	public function allowEvalWasm(bool $state = true) {
		$this->evalWasmAllowed = $state;
		return $this;
	}

	/**
	 * Allows to execute JavaScript files from a specific domain. Use * to
	 * allow JavaScript from all domains.
	 * @param string $domain Domain to whitelist. Any passed value needs to be properly sanitized.
	 * @return $this
	 * @since 8.1.0
	 */
	public function addAllowedScriptDomain($domain) {
		$this->allowedScriptDomains[] = $domain;
		return $this;
	}

	/**
	 * Remove the specified allowed script domain from the allowed domains.
	 *
	 * @param string $domain
	 * @return $this
	 * @since 8.1.0
	 */
	public function disallowScriptDomain($domain) {
		$this->allowedScriptDomains = array_diff($this->allowedScriptDomains, [$domain]);
		return $this;
	}

	/**
	 * Whether inline CSS snippets are allowed or forbidden
	 * @param bool $state
	 * @return $this
	 * @since 8.1.0
	 */
	public function allowInlineStyle($state = true) {
		$this->inlineStyleAllowed = $state;
		return $this;
	}

	/**
	 * Allows to execute CSS files from a specific domain. Use * to allow
	 * CSS from all domains.
	 * @param string $domain Domain to whitelist. Any passed value needs to be properly sanitized.
	 * @return $this
	 * @since 8.1.0
	 */
	public function addAllowedStyleDomain($domain) {
		$this->allowedStyleDomains[] = $domain;
		return $this;
	}

	/**
	 * Remove the specified allowed style domain from the allowed domains.
	 *
	 * @param string $domain
	 * @return $this
	 * @since 8.1.0
	 */
	public function disallowStyleDomain($domain) {
		$this->allowedStyleDomains = array_diff($this->allowedStyleDomains, [$domain]);
		return $this;
	}

	/**
	 * Allows using fonts from a specific domain. Use * to allow
	 * fonts from all domains.
	 * @param string $domain Domain to whitelist. Any passed value needs to be properly sanitized.
	 * @return $this
	 * @since 8.1.0
	 */
	public function addAllowedFontDomain($domain) {
		$this->allowedFontDomains[] = $domain;
		return $this;
	}

	/**
	 * Remove the specified allowed font domain from the allowed domains.
	 *
	 * @param string $domain
	 * @return $this
	 * @since 8.1.0
	 */
	public function disallowFontDomain($domain) {
		$this->allowedFontDomains = array_diff($this->allowedFontDomains, [$domain]);
		return $this;
	}

	/**
	 * Allows embedding images from a specific domain. Use * to allow
	 * images from all domains.
	 * @param string $domain Domain to whitelist. Any passed value needs to be properly sanitized.
	 * @return $this
	 * @since 8.1.0
	 */
	public function addAllowedImageDomain($domain) {
		$this->allowedImageDomains[] = $domain;
		return $this;
	}

	/**
	 * Remove the specified allowed image domain from the allowed domains.
	 *
	 * @param string $domain
	 * @return $this
	 * @since 8.1.0
	 */
	public function disallowImageDomain($domain) {
		$this->allowedImageDomains = array_diff($this->allowedImageDomains, [$domain]);
		return $this;
	}

	/**
	 * To which remote domains the JS connect to.
	 * @param string $domain Domain to whitelist. Any passed value needs to be properly sanitized.
	 * @return $this
	 * @since 8.1.0
	 */
	public function addAllowedConnectDomain($domain) {
		$this->allowedConnectDomains[] = $domain;
		return $this;
	}

	/**
	 * Remove the specified allowed connect domain from the allowed domains.
	 *
	 * @param string $domain
	 * @return $this
	 * @since 8.1.0
	 */
	public function disallowConnectDomain($domain) {
		$this->allowedConnectDomains = array_diff($this->allowedConnectDomains, [$domain]);
		return $this;
	}

	/**
	 * From which domains media elements can be embedded.
	 * @param string $domain Domain to whitelist. Any passed value needs to be properly sanitized.
	 * @return $this
	 * @since 8.1.0
	 */
	public function addAllowedMediaDomain($domain) {
		$this->allowedMediaDomains[] = $domain;
		return $this;
	}

	/**
	 * Remove the specified allowed media domain from the allowed domains.
	 *
	 * @param string $domain
	 * @return $this
	 * @since 8.1.0
	 */
	public function disallowMediaDomain($domain) {
		$this->allowedMediaDomains = array_diff($this->allowedMediaDomains, [$domain]);
		return $this;
	}

	/**
	 * From which domains objects such as <object>, <embed> or <applet> are executed
	 * @param string $domain Domain to whitelist. Any passed value needs to be properly sanitized.
	 * @return $this
	 * @since 8.1.0
	 */
	public function addAllowedObjectDomain($domain) {
		$this->allowedObjectDomains[] = $domain;
		return $this;
	}

	/**
	 * Remove the specified allowed object domain from the allowed domains.
	 *
	 * @param string $domain
	 * @return $this
	 * @since 8.1.0
	 */
	public function disallowObjectDomain($domain) {
		$this->allowedObjectDomains = array_diff($this->allowedObjectDomains, [$domain]);
		return $this;
	}

	/**
	 * Which domains can be embedded in an iframe
	 * @param string $domain Domain to whitelist. Any passed value needs to be properly sanitized.
	 * @return $this
	 * @since 8.1.0
	 */
	public function addAllowedFrameDomain($domain) {
		$this->allowedFrameDomains[] = $domain;
		return $this;
	}

	/**
	 * Remove the specified allowed frame domain from the allowed domains.
	 *
	 * @param string $domain
	 * @return $this
	 * @since 8.1.0
	 */
	public function disallowFrameDomain($domain) {
		$this->allowedFrameDomains = array_diff($this->allowedFrameDomains, [$domain]);
		return $this;
	}

	/**
	 * Domains from which web-workers and nested browsing content can load elements
	 * @param string $domain Domain to whitelist. Any passed value needs to be properly sanitized.
	 * @return $this
	 * @since 8.1.0
	 * @deprecated 15.0.0 use addAllowedWorkerSrcDomains or addAllowedFrameDomain
	 */
	public function addAllowedChildSrcDomain($domain) {
		$this->allowedChildSrcDomains[] = $domain;
		return $this;
	}

	/**
	 * Remove the specified allowed child src domain from the allowed domains.
	 *
	 * @param string $domain
	 * @return $this
	 * @since 8.1.0
	 * @deprecated 15.0.0 use the WorkerSrcDomains or FrameDomain
	 */
	public function disallowChildSrcDomain($domain) {
		$this->allowedChildSrcDomains = array_diff($this->allowedChildSrcDomains, [$domain]);
		return $this;
	}

	/**
	 * Domains which can embed an iFrame of the Nextcloud instance
	 *
	 * @param string $domain
	 * @return $this
	 * @since 13.0.0
	 */
	public function addAllowedFrameAncestorDomain($domain) {
		$this->allowedFrameAncestors[] = $domain;
		return $this;
	}

	/**
	 * Domains which can embed an iFrame of the Nextcloud instance
	 *
	 * @param string $domain
	 * @return $this
	 * @since 13.0.0
	 */
	public function disallowFrameAncestorDomain($domain) {
		$this->allowedFrameAncestors = array_diff($this->allowedFrameAncestors, [$domain]);
		return $this;
	}

	/**
	 * Domain from which workers can be loaded
	 *
	 * @param string $domain
	 * @return $this
	 * @since 15.0.0
	 */
	public function addAllowedWorkerSrcDomain(string $domain) {
		$this->allowedWorkerSrcDomains[] = $domain;
		return $this;
	}

	/**
	 * Remove domain from which workers can be loaded
	 *
	 * @param string $domain
	 * @return $this
	 * @since 15.0.0
	 */
	public function disallowWorkerSrcDomain(string $domain) {
		$this->allowedWorkerSrcDomains = array_diff($this->allowedWorkerSrcDomains, [$domain]);
		return $this;
	}

	/**
	 * Domain to where forms can submit
	 *
	 * @since 17.0.0
	 *
	 * @return $this
	 */
	public function addAllowedFormActionDomain(string $domain) {
		$this->allowedFormActionDomains[] = $domain;
		return $this;
	}

	/**
	 * Remove domain to where forms can submit
	 *
	 * @return $this
	 * @since 17.0.0
	 */
	public function disallowFormActionDomain(string $domain) {
		$this->allowedFormActionDomains = array_diff($this->allowedFormActionDomains, [$domain]);
		return $this;
	}

	/**
	 * Add location to report CSP violations to
	 *
	 * @param string $location
	 * @return $this
	 * @since 15.0.0
	 */
	public function addReportTo(string $location) {
		$this->reportTo[] = $location;
		return $this;
	}

	/**
	 * Get the generated Content-Security-Policy as a string
	 * @return string
	 * @since 8.1.0
	 */
	public function buildPolicy() {
		$policy = "default-src 'none';";
		$policy .= "base-uri 'none';";
		$policy .= "manifest-src 'self';";

		if (!empty($this->allowedScriptDomains) || $this->evalScriptAllowed || $this->evalWasmAllowed || is_string($this->jsNonce)) {
			$policy .= 'script-src ';
			$scriptSrc = '';
			if (is_string($this->jsNonce)) {
				if ($this->strictDynamicAllowed) {
					$scriptSrc .= '\'strict-dynamic\' ';
				}
				$scriptSrc .= '\'nonce-' . $this->jsNonce . '\'';
				$allowedScriptDomains = array_flip($this->allowedScriptDomains);
				unset($allowedScriptDomains['\'self\'']);
				$this->allowedScriptDomains = array_flip($allowedScriptDomains);
				if (count($allowedScriptDomains) !== 0) {
					$scriptSrc .= ' ';
				}
			}
			if (is_array($this->allowedScriptDomains)) {
				$scriptSrc .= implode(' ', $this->allowedScriptDomains);
			}
			if ($this->evalScriptAllowed) {
				$scriptSrc .= ' \'unsafe-eval\'';
			}
			if ($this->evalWasmAllowed) {
				$scriptSrc .= ' \'wasm-unsafe-eval\'';
			}
			$policy .= $scriptSrc . ';';
		}

		// We only need to set this if 'strictDynamicAllowed' is not set because otherwise we can simply fall back to script-src
		if ($this->strictDynamicAllowedOnScripts && is_string($this->jsNonce) && !$this->strictDynamicAllowed) {
			$policy .= 'script-src-elem \'strict-dynamic\' ';
			$policy .= $scriptSrc ?? '';
			$policy .= ';';
		}

		if (!empty($this->allowedStyleDomains) || $this->inlineStyleAllowed) {
			$policy .= 'style-src ';
			if (is_array($this->allowedStyleDomains)) {
				$policy .= implode(' ', $this->allowedStyleDomains);
			}
			if ($this->inlineStyleAllowed) {
				$policy .= ' \'unsafe-inline\'';
			}
			$policy .= ';';
		}

		if (!empty($this->allowedImageDomains)) {
			$policy .= 'img-src ' . implode(' ', $this->allowedImageDomains);
			$policy .= ';';
		}

		if (!empty($this->allowedFontDomains)) {
			$policy .= 'font-src ' . implode(' ', $this->allowedFontDomains);
			$policy .= ';';
		}

		if (!empty($this->allowedConnectDomains)) {
			$policy .= 'connect-src ' . implode(' ', $this->allowedConnectDomains);
			$policy .= ';';
		}

		if (!empty($this->allowedMediaDomains)) {
			$policy .= 'media-src ' . implode(' ', $this->allowedMediaDomains);
			$policy .= ';';
		}

		if (!empty($this->allowedObjectDomains)) {
			$policy .= 'object-src ' . implode(' ', $this->allowedObjectDomains);
			$policy .= ';';
		}

		if (!empty($this->allowedFrameDomains)) {
			$policy .= 'frame-src ';
			$policy .= implode(' ', $this->allowedFrameDomains);
			$policy .= ';';
		}

		if (!empty($this->allowedChildSrcDomains)) {
			$policy .= 'child-src ' . implode(' ', $this->allowedChildSrcDomains);
			$policy .= ';';
		}

		if (!empty($this->allowedFrameAncestors)) {
			$policy .= 'frame-ancestors ' . implode(' ', $this->allowedFrameAncestors);
			$policy .= ';';
		} else {
			$policy .= 'frame-ancestors \'none\';';
		}

		if (!empty($this->allowedWorkerSrcDomains)) {
			$policy .= 'worker-src ' . implode(' ', $this->allowedWorkerSrcDomains);
			$policy .= ';';
		}

		if (!empty($this->allowedFormActionDomains)) {
			$policy .= 'form-action ' . implode(' ', $this->allowedFormActionDomains);
			$policy .= ';';
		}

		if (!empty($this->reportTo)) {
			$policy .= 'report-uri ' . implode(' ', $this->reportTo);
			$policy .= ';';
		}

		return rtrim($policy, ';');
	}
}
