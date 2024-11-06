<?php
/**
 * Nextcloud - DocuSign
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Florian Klinger <florian.klinger@nextcloud.com>
 * @copyright Florian Klinger 2023
 */

declare(strict_types=1);

namespace OCA\Documenso\Controller;

use OCA\Documenso\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\DataResponse;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IRequest;
use OCP\Security\ICrypto;

class DocumensoController extends Controller {
	private $userId;
	private $config;
	/**
	 * @var IL10N
	 */
	private $l;

	public function __construct($AppName,
		IRequest $request,
		IConfig $config,
		IL10N $l,
		private ICrypto $crypto,
		?string $userId) {
		parent::__construct($AppName, $request);
		$this->config = $config;
		$this->l = $l;
		$this->userId = $userId;
	}

	/**
	 * Set config values
	 *
	 * @param array $values
	 * @return DataResponse
	 * @throws PreConditionNotMetException
	 */
	#[NoAdminRequired]
	#[FrontpageRoute(verb: 'PUT', url: '/config')]
	public function setConfig(array $values): DataResponse {
		foreach ($values as $key => $value) {
			if ($key === 'token' && $value !== '') {
				$encryptedValue = $this->crypto->encrypt(trim($value));
				$this->config->setUserValue($this->userId, Application::APP_ID, $key, $encryptedValue);
			} else {
				$this->config->setUserValue($this->userId, Application::APP_ID, $key, trim($value));
			}
		}

		return new DataResponse([]);
	}
}