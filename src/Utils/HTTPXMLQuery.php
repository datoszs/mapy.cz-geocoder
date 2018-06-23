<?php declare(strict_types=1);
namespace DATOSCZ\MapyCzGeocoder\Utils;

use DATOSCZ\MapyCzGeocoder\Exceptions\InvalidResultException;
use DATOSCZ\MapyCzGeocoder\Exceptions\QueryException;
use SimpleXMLElement;
use function sprintf;

class HTTPXMLQuery
{

	/**
	 * Queries given resource via HTTP and returns parsed XML object or throws en exception
	 *
	 * @param string $address
	 * @param array $params
	 *
	 * @return SimpleXMLElement
	 * @throws QueryException when error happens during quering or http code is not 200
	 * @throws InvalidResultException when given response could not been parsed and checked
	 */
	public static function performQuery(string $address, array $params = []): SimpleXMLElement
	{
		$url = sprintf('%s?%s', $address, http_build_query($params, '', '&'));


		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => 1,
		]);

		$content = curl_exec($curl);
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		if ($content === false || $httpCode !== 200) {
			throw new QueryException(sprintf('Query failed or HTTP code [%d] is not 200.', $httpCode));
		}
		try {
			libxml_use_internal_errors(TRUE);
			$parsed = new SimpleXMLElement($content);
			if (!isset($parsed->point) && $parsed->getName() !== 'rgeocode') {
				throw new QueryException('Element point or rgeocode is missing.');
			}
			return $parsed;
		} catch (\Exception $e) {
			throw new InvalidResultException(sprintf('Invalid result for %s',  $url), 0, $e);
		}
	}
}
